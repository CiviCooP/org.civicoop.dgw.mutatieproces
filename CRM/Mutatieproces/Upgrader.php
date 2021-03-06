<?php

/**
 * Collection of upgrade steps
 */
class CRM_Mutatieproces_Upgrader extends CRM_Mutatieproces_Upgrader_Base {

  public function install() {
    /**
     * create tables for civicrm_property (VGE), civicrm_property_type and civicrm_property_contract (HOV)
     * during install if they do not exist yet
     */
    if (!CRM_Core_DAO::checkTableExists("civicrm_property_contract")) {
      $this->executeSqlFile('sql/createPropertyContract.sql');
    } else {
      CRM_Core_Session::setStatus("Table civicrm_property_contract already exists, please check if table needs cleaning.", "info");
    }
    if (!CRM_Core_DAO::checkTableExists("civicrm_property_type")) {
      $this->executeSqlFile('sql/createPropertyType.sql');
    } else {
      CRM_Core_Session::setStatus("Table civicrm_property_type already exists, please check if table needs cleaning", "info");
    }
    if (!CRM_Core_DAO::checkTableExists("civicrm_property")) {
      $this->executeSqlFile('sql/createProperty.sql');
    } else {
      CRM_Core_Session::setStatus("Table civicrm_property already exists, please check if table needs cleaning", "info");
    }

    $this->installHuuropzeggingsDossier();
    
    /**
     * Install the dossier for nieuwe huurder
     */
    $this->installNieuweHuurderDossier();
    
    /**
     * Install activity for woning waardering nakijken
     */
    $this->installInfoAfdVerhuur();
  }
  
  /**
   * Install custom fields for afdeling verhuur. Those fields are used for common remarks
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014 
   */
  protected function installInfoAfdVerhuur() {    
    $huuropzegging = $this->get_case_type_by_name('Huuropzeggingsdossier');
    $extends_entity_ids[] = $huuropzegging['value'];
   
    //$extends_entity_ids = implode(CRM_Core_DAO::VALUE_SEPARATOR, $extends_entity_ids);
    $extends_entity_ids = implode(",", $extends_entity_ids);
    $gid = $this->add_custom_group('info_afd_verhuur', 'Info tbv afdeling verhuur', $extends_entity_ids, 'Case', false);
    if ($gid) {
      $this->add_custom_field($gid, 'huuropzeg_rapport', 'Opm. vanuit advies- of eindgesprek', 'Memo', 'TextArea', 1, 1);
      $this->add_custom_field($gid, 'future_address_in_first', 'Toekomstig adres (bij opzegging)', 'Memo', 'TextArea', 1, 1);
      $this->add_custom_field($gid, 'future_address', 'Toekomstig adres (tijdens inspectie)', 'Memo', 'TextArea', 1, 1);
    }
  }

  
  /**
   * Install the dossier for Nieuwe huurder
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014 
   */
  protected function installNieuweHuurderDossier() {
    /*
   * create custom data sets and fields for case type Nieuwehuurder
   * This is the case where a new tenant moves into the house
   */
  $this->add_relationship_type('Verhuurconsulent is', 'Verhuurconsulent', 'Individual', '');
  $this->add_activity_type('Plannen bezichtiging', 'Plannen van een bezichtiging met vertrekkende huurder of consulent');
  $this->add_activity_type('Versturen aanbiedingsbrief', 'Maken en versturen van de aanbiedingsbrief');
  $this->add_activity_type('Controleren gegevens compleet', 'Controleren of alle gegevens compleet zijn');
  $this->add_activity_type('Voorbereiden contract', 'Huurcontract voorbereiden');
  $this->add_activity_type('Tekenen contract', 'Huurcontract tekenen met nieuwe huurder');
  $this->add_activity_type('Afmelden woonkeus', 'Eenheid afmelden bij woonkeus');
  $nw_dossier = $this->add_case('Nieuwehuurdersdossier');
  $nw_gid = false;
  if ($nw_dossier) {
    /*
     * create custom data sets and fields for case type Nieuwehuurder
     */
    $nw_gid = $this->add_custom_group('nieuw_vge', 'VGE gegevens nieuw contract', $nw_dossier, 'Case');
    if ($nw_gid) {
      $this->add_custom_field($nw_gid, 'nw_vge_nr', 'VGE nummer First', 'String', 'Text', '1', '1');
      $this->add_custom_field($nw_gid, 'nw_complex_nr', 'Complexnummer First', 'String', 'Text', '1', '2');
      $this->add_custom_field($nw_gid, 'nw_vge_adres', 'VGE adres', 'String', 'Text', '1', '3');
      $this->add_custom_field($nw_gid, 'nw_vge_straat', 'Straat', 'String', 'Text', '1', '4');
      $this->add_custom_field($nw_gid, 'nw_vge_huis_nr', 'Huisnummer', 'String', 'Text', '1', '5');
      $this->add_custom_field($nw_gid, 'nw_vge_suffix', 'Toevoeging', 'String', 'Text', '1', '6');
      $this->add_custom_field($nw_gid, 'nw_vge_postcode', 'Postcode', 'String', 'Text', '1', '7');
      $this->add_custom_field($nw_gid, 'nw_vge_plaats', 'Plaats', 'String', 'Text', '1', '8');
    }
    $nw_gid = $this->add_custom_group('nieuw_woningwaardering', 'Woningwaardering nieuw contract', $nw_dossier, 'Case');
    if ($nw_gid) {
      $this->add_custom_field($nw_gid, 'nw_epa_label_opzegging', 'EPA label', 'String', 'Text', '1', '1');
      $this->add_custom_field($nw_gid, 'nw_epa_pre_opzegging', 'EPA prelabel', 'String', 'Text', '1', '2');
      $this->add_custom_field($nw_gid, 'nw_woningoppervlakte', 'Totale woonoppervlakte', 'String', 'Text', '1', '3');
    }
  }
  }

  /**
   * Install the huuropzeggings doosier and the relevant custom fields, activities etc...
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014
   */
  protected function installHuuropzeggingsDossier() {
    $this->add_relationship_type('Technisch woonconsulent is', 'Technisch woonconsulent', 'Individual', '');
    $this->add_activity_type('adviesgesprek_huuropzegging', 'Vooropname', 'Inplannen en afhandelen van een vooropname');
    $this->add_activity_type('Nakijken puntprijs', 'Nakijken puntprijs', 'Puntprijs nakijken door HAI');
    $this->add_activity_type('Afronden huuropzegging', 'Afronden huuropzegging', "Afronden van de huuropzegging");
    $this->add_activity_type('eindgesprek_huuropzegging', 'Eindopname', 'Inplannen en afhandelen van een eindopname');
    $dossier = $this->add_case('Huuropzeggingsdossier');
    $gid = false;
    if ($dossier) {
      /*
       * create custom data sets and fields for case type Huuropzegging
       * This is the case where the tenant ends his rental period
       */
      $gid = $this->add_custom_group('huur_opzegging', 'Contract en opzegging gegevens', $dossier, 'Case');
      if ($gid) {
        $this->add_custom_field($gid, 'mutatie_nr', 'Mutatienummer First Noa', 'String', 'Text', '1', 1);
        $this->add_custom_field($gid, 'hov_nr', 'Huurovereenkomstnummer First Noa', 'String', 'Text', '1', 2);
        $this->add_custom_field($gid, 'hov_start_datum', 'Startdatum huurovereenkomst', 'Date', 'Select Date', '1', '3');
        $this->add_custom_field($gid, 'hoofdhuurder_nr_first', 'Persoonsnummer hoofdhuurder', 'String', 'Text', '1', '4');
        $this->add_custom_field($gid, 'hoofdhuurder_name', 'Naam hoofdhuurder', 'String', 'Text', '1', '5');
        $this->add_custom_field($gid, 'medehuurder_nr_first', 'Persoonsnummer medehuurder', 'String', 'Text', '1', '6');
        $this->add_custom_field($gid, 'medehuurder_name', 'Naam medehuurder', 'String', 'Text', '1', '7');
        $this->add_custom_field($gid, 'verwachte_eind_datum', 'Verwachte einddatum', 'Date', 'Select Date', '1', '8');
        $this->add_custom_field($gid, 'plattegrond_opzegging', 'Plattegrond bij vorige mutatie', 'File', 'File', '1', '9');
        $this->add_custom_field($gid, 'opnamerapport_opzegging', 'Opnamerapport bij vorige mutatie', 'File', 'File', '1', '10');
        $this->add_custom_field($gid, 'staat_oplevering_opzegging', 'Bijlage staat van oplevering', 'File', 'File', '1', '11');
      }
      $gid = $this->add_custom_group('vge', 'VGE gegevens', $dossier, 'Case');
      if ($gid) {
        $this->add_custom_field($gid, 'vge_nr', 'VGE nummer First', 'String', 'Text', '1', '1');
        $this->add_custom_field($gid, 'complex_nr', 'Complexnummer First', 'String', 'Text', '1', '2');
        $this->add_custom_field($gid, 'vge_adres', 'VGE adres', 'String', 'Text', '1', '3');
        $this->add_custom_field($gid, 'vge_straat', 'Straat', 'String', 'Text', '1', '4');
        $this->add_custom_field($gid, 'vge_huis_nr', 'Huisnummer', 'String', 'Text', '1', '5');
        $this->add_custom_field($gid, 'vge_suffix', 'Toevoeging', 'String', 'Text', '1', '6');
        $this->add_custom_field($gid, 'vge_postcode', 'Postcode', 'String', 'Text', '1', '7');
        $this->add_custom_field($gid, 'vge_plaats', 'Plaats', 'String', 'Text', '1', '8');
      }
      $gid = $this->add_custom_group('woningwaardering', 'Woningwaardering', $dossier, 'Case');
      if ($gid) {
        $this->add_custom_field($gid, 'epa_label_opzegging', 'EPA label', 'String', 'Text', '1', '1');
        $this->add_custom_field($gid, 'epa_pre_opzegging', 'EPA prelabel', 'String', 'Text', '1', '2');
        $this->add_custom_field($gid, 'woningoppervlakte', 'Totale woonoppervlakte', 'String', 'Text', '1', '3');
      }
    }
  }
  
  /**
   * Alter an activity type to the system
   * 
   * @param string $name
   * @param string $label
   * @param string $description
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 20 Mar 2014
   */
  protected function alter_activity_type($name, $label, $description) {
    $option_group = 2; //activity type
    $param = array(
      'label' => $label,
      'description' => $description,
      'option_group_id' => $option_group,
    );
    
    $getParams['name'] = $name;
    $getParams['option_group_id'] = $option_group;
    $result = civicrm_api3('OptionValue', 'get', $getParams);
    if ($result['count'] == 0) {
      return;
    }
    if (isset($result['id'])) {
      $param['id'] = $result['id'];
      civicrm_api3('OptionValue', 'Create', $param);
    }
  }

  /**
   * Add an activity type to the system
   * 
   * @param string $name
   * @param string $label
   * @param string $description
   * @param bool $caseActivity - optional, default true, when set this activity type is used for case activities
   * @return int|false activity type id on success, false on failure
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014
   */
  protected function add_activity_type($name, $label, $description='', $caseActivity=true) {
    $option_group = 2; //activity type
    $componentCase = 7; //activity type for civi case
    $param = array(
      'label' => $label,
      'name' => $name,
      'description' => $description,
      'option_group_id' => $option_group,
      'is_reserved' => true,
      'is_active' => 1,
      'weight' => 1,
    );
    if ($caseActivity) {
      $param['component_id'] = $componentCase;
    }
    
    $getParams['name'] = $name;
    $getParams['option_group_id'] = $option_group;
    if ($caseActivity) {
      $getParams['component_id'] = $componentCase;
    }
    $result = civicrm_api3('OptionValue', 'get', $getParams);
    if ($result['count'] == 0) {
      $result = civicrm_api3('OptionValue', 'Create', $param);
    }
    if (isset($result['id'])) {
      return $result['id'];
    }
    return false;
  }
  
  /**
   * Returns the details of an activity type from the option value api
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 18 Mar 2014
   * @param string $name
   * @return array
   */
  protected function get_activity_type_by_name($name) {
    $option_group = 2; //activity type
    $getParams['name'] = $name;
    $getParams['option_group_id'] = $option_group;
    $result = civicrm_api3('OptionValue', 'getsingle', $getParams);
    return $result;
  }
  
  /**
   * Returns the details of a case type from the option value api.
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 19 Mar 2014
   * @param string $name
   * @return array
   */
  protected function get_case_type_by_name($name) {
    $option_group_result = civicrm_api3('OptionGroup', 'getsingle', array('name' => 'case_type'));
    $option_group = false;
    if (isset($option_group_result['id'])) {
      $getParams['option_group_id'] = $option_group_result['id'];
    }
    $getParams['name'] = $name;
    $result = civicrm_api3('OptionValue', 'getsingle', $getParams);
    return $result;
  }

  /**
   * Add a relationship type to the system
   * 
   * @param type $name_a_b
   * @param type $name_b_a
   * @param type $contact_type_a
   * @param type $contact_type_b
   *
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014
   */
  protected function add_relationship_type($name_a_b, $name_b_a, $contact_type_a = "", $contact_type_b = "") {
    $params['name_a_b'] = $name_a_b;
    $params['name_b_a'] = $name_b_a;
    if (strlen($contact_type_a)) {
      $params['contact_type_a'] = $contact_type_a;
    }
    if (strlen($contact_type_b)) {
      $params['contact_type_b'] = $contact_type_b;
    }
    try {
      $result = civicrm_api3('relationship_type', 'get', $params);
      if (isset($result['count']) && $result['count'] == 0) {
        civicrm_api3('RelationshipType', 'Create', $params);
      }
    } catch (CiviCRM_API3_Exception $e) {
      
    }
  }

  /**
   * Add a case to the system
   * 
   * @param type $case
   * @return boolean
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014
   */
  protected function add_case($case) {
    $option_group = civicrm_api3('OptionGroup', 'Getsingle', array('name' => 'case_type'));
    $option_group_id = false;
    if (isset($option_group['id'])) {
      $option_group_id = $option_group['id'];
    }
    if (!$option_group_id) {
      return false;
    }

    $params = array(
      'option_group_id' => $option_group_id,
      'name' => $case
    );
    $option_value_id = false;
    $option_value_value = false;
    try {
      $option_value = civicrm_api3('OptionValue', 'Getsingle', $params);
      if (isset($option_value['id'])) {
        $option_value_id = $option_value['id'];
        $option_value_value = $option_value['value'];
      }
    } catch (CiviCRM_API3_Exception $e) {
      $option_value = civicrm_api3('OptionValue', 'Create', $params);
      if (isset($option_value['id']) && is_array($option_value['values']) && count($option_value['values'])) {
        $v = reset($option_value['values']);
        $option_value_id = $option_value['id'];
        $option_value_value = $v['value'];
      }
    }
    return $option_value_value;
  }

  /**
   * Add a custom group to the system and return the ID
   * When the custom group exist it returns the id of the existing group
   * 
   * @param type $group
   * @param type $group_title
   * @param type $extends_entity_ids
   * @param type $extends
   * @return type
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014
   */
  protected function add_custom_group($group, $group_title, $extends_entity_ids, $extends, $collapse=true) {
    try {
      $result = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $group));
      $gid = $result['id'];
    } catch (CiviCRM_API3_Exception $e) {
      $params = array(
        'name' => $group,
        'title' => $group_title,
        'extends' => $extends,
        'extends_entity_column_value' => $extends_entity_ids,
        'collapse_display' => $collapse ? '1' : '0',
        'is_active' => 1
      );
      $result = civicrm_api3('CustomGroup', 'Create', $params);
      $gid = $result['id'];
    }
    return $gid;
  }

  /**
   * Add a custom field to the system
   * 
   * @param type $gid
   * @param type $name
   * @param type $label
   * @param type $data_type
   * @param type $html_type
   * @param type $active
   * @param type $weight
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014
   */
  protected function add_custom_field($gid, $name, $label, $data_type, $html_type, $active, $weight = 0) {
    $params = array(
      'custom_group_id' => $gid,
      'label' => $label
    );
    try {
      $result = civicrm_api3('CustomField', 'Getsingle', $params);
    } catch (CiviCRM_API3_Exception $e) {
      unset($params);
      $params = array(
        'custom_group_id' => $gid,
        'name' => $name,
        'label' => $name,
        'html_type' => $html_type,
        'data_type' => $data_type,
        'weight' => $weight,
        'is_active' => $active
      );
      $result = civicrm_api3('CustomField', 'Create', $params);

      $params2 = array(
        'label' => $label,
        'is_active' => $active,
        'id' => $result['id']
      );
      civicrm_api3('CustomField', 'Create', $params2);
    }
  }

  /**
   * Delete a custom group from the system
   * 
   * @param type $name
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014
   */
  protected function delete_custom_group($name) {
    $result = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $name));
    if (isset($result['id'])) {
      $gid = $result['id'];
      $result = civicrm_api3('CustomField', 'Get', array('custom_group_id' => $gid));
      if (isset($result['values']) && is_array($result['values'])) {
        foreach ($result['values'] as $field) {
          unset($params);
          civicrm_api3('CustomField', 'Delete', array('id' => $field['id']));
        }
      }
      $result = civicrm_api3('CustomGroup', 'Delete', array('id' => $gid));
    }
  }

  /**
   * Enable/disable a customgroup
   * 
   * @param type $name
   * @param type $enable
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 17 Mar 2014
   */
  protected function enable_custom_group($name, $enable) {
    $result = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $name));
    if (isset($result['id'])) {
      $gid = $result['id'];
      $params = array(
        'id' => $gid,
        'is_active' => $enable ? '1' : '0'
      );
      $result = civicrm_api3('CustomGroup', 'update', $params);
    }
  }
    /**
     * Update 1001
     * 
     * @author Erik Hommel (CiviCooP)<erik.hommel@civicoop.org>
     * @date 17 Mar 2014
     */
   /*public function upgrade_1001() {
       $this->ctx->log->info('Applying update 1001');
       if (CRM_Core_DAO::checkTableExists('civicrm_property')) {
           if (CRM_Core_DAO::checkFieldExists('civicrm_property', 'build_year')) {
               CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_property MODIFY COLUMN build_year CHAR(4)");
           }
       }
       if (CRM_Core_DAO::checkTableExists('civicrm_property_contract')) {
           if (!CRM_Core_DAO::checkFieldExists('civicrm_property_contract', 'type')) {
               CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_property_contract ADD COLUMN type CHAR(1)");
           }
       }
       return TRUE;
   }*/
    /**
     * Update 1002
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 17 Mar 2014
     */
   /*public function upgrade_1002() {
       $this->ctx->log->info('Applying update 1002');
       if (CRM_Core_DAO::checkTableExists('civicrm_property_contract')) {
           if (CRM_Core_DAO::checkFieldExists('civicrm_property_contract', 'hov_corr_name')) {
               CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_property_contract CHANGE hov_corr_name hov_name VARCHAR(128)");
           }
           if (!CRM_Core_DAO::checkFieldExists('civicrm_property_contract', 'hov_expected_end_date')) {
               CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_property_contract ADD COLUMN hov_expected_end_date DATE");
           }
       }
       return TRUE;
   }*/
   
   /**
    * Update 1003
    * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
    * @date 18 Mar 2014
    */
   /*public function upgrade_1003() {
     $this->ctx->log->info('Applying update 1003: installing activity for woning waardering');
     $this->installInfoAfdVerhuur();
     return TRUE;
   }*/

  /**
   * Update 1004: toevoegen velden toekomstig adres
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 18 Mar 2014
   */
  public function upgrade_1004() {
    $this->ctx->log->info('Applying update 1014: installing future address fields');
    $this->installInfoAfdVerhuur();
    return TRUE;
  }
}
