<?php

require_once 'mutatieproces.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function mutatieproces_civicrm_config(&$config) {
  _mutatieproces_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function mutatieproces_civicrm_xmlMenu(&$files) {
  _mutatieproces_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function mutatieproces_civicrm_install() {
  return _mutatieproces_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function mutatieproces_civicrm_uninstall() {
  return _mutatieproces_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 * @author CiviCooP (helpdesk@civicoop.org)
 */
function mutatieproces_civicrm_enable() {
  return _mutatieproces_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function mutatieproces_civicrm_disable() {
  return _mutatieproces_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function mutatieproces_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mutatieproces_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function mutatieproces_civicrm_managed(&$entities) {
  return _mutatieproces_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook civicrm_pageRun
 * 
 * if page = CRM_Contact_Page_View_Summary
 * - determine if button huuropzeggen should be shown. 
 * 
 * @author Jaap Jansma (jaap.jansma@civicoop.org) and Erik Hommel (erik.hommel@civicoop.org)
 * @date 20 Jan 2014
 * @param type $page
 */
function mutatieproces_civicrm_pageRun(&$page) {
  $page_name = $page->getVar('_name');
  if ($page_name == "CRM_Contact_Page_View_Summary") {
    $contact_id = $page->getVar('_contactId');
    $contact_type = CRM_Contact_BAO_Contact::getContactType($contact_id);
    /*
     * determine if button Huurovereenkomst opzeggen should be shown based
     * on contact_type
     */
    $huur_opzeggen = CRM_Utils_DgwMutatieprocesUtils::checkHovOpzeggen($contact_id, $contact_type);
    /*
     * Detemerine if the user has the role for testing huurovereenkomst opzeggen
     */
    $access = false;
    if (user_access('huuropzeggen')) {
      $access = true; //user is allowed to access huuropzeggen
    }
    if ($huur_opzeggen && $access) {
      $page->assign('show_hov_opzeggen', '1');
      $page->assign('hov_opzeggen_contact_id', $contact_id);
    } else {
      $page->assign('show_hov_opzeggen', '0');
      $page->assign('hov_opzeggen_contact_id', '0');
    }
  }
}

function mutatieproces_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Case_Form_Case') {
    if ($form->getAction() == CRM_Core_Action::ADD) {
      $case_type_id = _mutatieproces_get_case_type_id('Huuropzeggingsdossier');
      if ($case_type_id && $form->elementExists('case_type_id')) {
        $cases = $form->getElement('case_type_id');
        foreach ($cases->_options as $id => $option) {
          if ($id == $case_type_id) {
            unset($cases->_options[$id]);
          }
        }
      }
    }
  }
}

function _mutatieproces_get_case_type_id($case) {
  $option_group = civicrm_api('OptionGroup', 'getsingle', array('name' => 'case_type', 'version' => '3'));
  $option_group_id = false;
  if (isset($option_group['id'])) {
    $option_group_id = $option_group['id'];
  } else {
    return false;
  }
  $option_value = civicrm_api('OptionValue', 'getsingle', array('option_group_id' => $option_group_id, 'name' => $case, 'version' => '3'));
  $option_value_id = false;
  $option_value_value = false;
  if (isset($option_value['id'])) {
    $option_value_id = $option_value['id'];
    $option_value_value = $option_value['value'];
  }

  return $option_value_value;
}

/**
 * Implementation of hook civicrm_custom 
 * If record is changed or created in custom group
 * huuirovereenkomst(huishouden) or huurovereenkomst (organisatie)\
 * then updated or add to table PropertyContract
 * 
 * @param string $op
 * @param integer $groupID
 * @param integer $entityID
 * @param array $params
 */
function mutatieproces_civicrm_custom($op, $groupID, $entityID, &$params) {
  /*
   * retrieve custom_group_id for huurovereenkomst (huishouden) en
   * huurovereenkomst (organisatie), and retrieve hov_nummer field label
   */
  $hov_hh_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (huishouden)");
  if (!civicrm_error($hov_hh_custom_group)) {
    if (isset($hov_hh_custom_group['id'])) {
      $hov_hh_custom_group_id = $hov_hh_custom_group['id'];
      $hov_id_name = "HOV_nummer_First";
      $hov_hh_custom_table = $hov_hh_custom_group['table_name'];
    }
  }
  $hov_org_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (organisatie)");
  if (!civicrm_error($hov_org_custom_group)) {
    if (isset($hov_org_custom_group['id'])) {
      $hov_org_custom_group_id = $hov_org_custom_group['id'];
      $hov_id_name = "hov_nummer";
      $hov_org_custom_table = $hov_org_custom_group['table_name'];
    }
  }

  /*
   * process only if required
   */
  if ($groupID == $hov_hh_custom_group_id || $groupID == $hov_org_custom_group_id) {
    if ($groupID == $hov_hh_custom_group_id) {
      $hov_id_name = "HOV_nummer_First";
      $hov_custom_table = $hov_hh_custom_table;
      $type = "Huishouden";
    } else {
      $hov_id_name = "hov_nummer";
      $hov_custom_table = $hov_org_custom_table;
      $type = "Organisatie";
    }
    $hov_id_custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName($hov_id_name, $groupID);
    if (!civicrm_error($hov_id_custom_field)) {
      $hov_custom_field = $hov_id_custom_field['column_name'];
    }
    $hov_query = 'SELECT ' . $hov_custom_field . " FROM " . $hov_custom_table . " WHERE entity_id = $entityID";
    $dao_hov = CRM_Core_DAO::executeQuery($hov_query);
    if ($dao_hov->fetch()) {
      $contract_data = _mutatieproces_setPropertyContractParams($params, $type);
      /*
       * update or add property contract table if required
       */
      require_once 'CRM/Mutatieproces/PropertyContract.php';
      $property_contract = new CRM_Mutatieproces_PropertyContract();
      $contract_exists = $property_contract->checkContractExists($dao_hov->$hov_custom_field);
      if ($contract_exists) {
        $retrieved_contract = CRM_Mutatieproces_PropertyContract::getPropertyContractWithHovId($dao_hov->$hov_custom_field);
        $contract_data['id'] = $retrieved_contract['id'];
        $property_contract->update($contract_data);
      } else {
        $property_contract->create($contract_data);
      }
    }
  }
}

/**
 * Function to set the fields for PropertyContract based on incoming $params
 * 
 * @author Erik Hommel (erik.hommel@civicoop.org)
 * @date 20 Jan 2014
 * @param arry $params
 * @return array $result
 * 
 */
function _mutatieproces_setPropertyContractParams($params, $type) {
  $result = array();
  if (empty($params) || empty($type)) {
    return $result;
  }
  foreach ($params as $param) {
    $custom_field = civicrm_api3('CustomField', 'Getsingle', array('id' => $param['custom_field_id']));
    $custom_field_name = $custom_field['name'];
    /*
     * set fields based on custom_group
     */
    switch (($type)) {
      case "Huishouden":
        switch ($custom_field_name) {
          case "HOV_nummer_First":
            $result['hov_id'] = $param['value'];
            break;
          case "VGE_nummer_First":
            $result['hov_vge_id'] = $param['value'];
            break;
          case "Correspondentienaam_First":
            $result['hov_corr_name'] = $param['value'];
            break;
          case "Begindatum_HOV":
            $result['hov_start_date'] = $param['value'];
            break;
          case "Einddatum_HOV":
            if ($param['value'] != "19700101") {
              $result['hov_end_date'] = $param['value'];
            }
            break;
        }
        break;
      case "Organisatie":
        switch ($custom_field_name) {
          case "hov_nummer":
            $result['hov_id'] = $param['value'];
            break;
          case "vge_nummer":
            $result['hov_vge_id'] = $param['value'];
            break;
          case "naam_op_overeenkomst":
            $result['hov_corr_name'] = $param['value'];
            break;
          case "begindatum_overeenkomst":
            $result['hov_start_date'] = $param['value'];
            break;
          case "einddatum_overeenkomst":
            if ($param['value'] != "19700101") {
              $result['hov_end_date'] = $param['value'];
            }
            break;
        }
        break;
    }
  }
  return $result;
}
