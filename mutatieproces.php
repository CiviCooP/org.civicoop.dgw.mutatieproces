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
    /*
     * remove custom groups for extension
     */
    _mutatieproces_delete_custom_group('huur_opzegging');
    _mutatieproces_delete_custom_group('vge');
    _mutatieproces_delete_custom_group('woningwaardering');
    return _mutatieproces_civix_civicrm_uninstall();
}
/**
 * Implementation of hook_civicrm_enable
 * @author CiviCooP (helpdesk@civicoop.org)
 */
function mutatieproces_civicrm_enable() {
    _mutatieproces_add_relationship_type('Technisch woonconsulent is', 'Technisch woonconsulent', '', '');
    _mutatieproces_add_activity_type('Adviesgesprek', 'Inplannen van een adviesgesprek');
    $dossier = _mutatieproces_add_case('DossierOpzeggingHuurcontract');
    $gid = false;
    if ($dossier) {
        /*
         * create custom data sets and fields for case type Huuropzegging
         * This is the case where the tenant ends his rental period
         */
        $gid = _mutatieproces_add_custom_group('huur_opzegging', 'Contract en opzegging gegevens', $dossier, 'Case');
        if ($gid) {
            _mutatieproces_add_custom_field($gid, 'mutatie_nr', 'Mutatienummer First Noa', 'String', 'Text', '1', 1);
            _mutatieproces_add_custom_field($gid, 'hov_nr', 'Huurovereenkomstnummer First Noa', 'String', 'Text', '1', 2);
            _mutatieproces_add_custom_field($gid, 'hov_start_datum', 'Startdatum huurovereenkomst', 'Date', 'Select Date', '1', '3');
            _mutatieproces_add_custom_field($gid, 'hoofdhuurder_nr_first', 'Persoonsnummer hoofdhuurder', 'String', 'Text', '1', '4');
            _mutatieproces_add_custom_field($gid, 'hoofdhuurder_name', 'Naam hoofdhuurder', 'String', 'Text', '1', '5');
            _mutatieproces_add_custom_field($gid, 'medehuurder_nr_first', 'Persoonsnummer medehuurder', 'String', 'Text', '1', '6');
            _mutatieproces_add_custom_field($gid, 'medehuurder_name', 'Naam medehuurder', 'String', 'Text', '1', '7');
            _mutatieproces_add_custom_field($gid, 'verwachte_eind_datum', 'Verwachte einddatum', 'Date', 'Select Date', '1','8');
            _mutatieproces_add_custom_field($gid, 'plattegrond_opzegging', 'Plattegrond bij vorige mutatie', 'File', 'File' , '1', '9');
            _mutatieproces_add_custom_field($gid, 'opnamerapport_opzegging', 'Opnamerapport bij vorige mutatie', 'File', 'File', '1', '10');
            _mutatieproces_add_custom_field($gid, 'staat_oplevering_opzegging', 'Bijlage staat van oplevering', 'File', 'File', '1', '11');
        }
        $gid = _mutatieproces_add_custom_group('vge', 'VGE gegevens', $dossier, 'Case');
        if ($gid) {
            _mutatieproces_add_custom_field($gid, 'vge_nr', 'VGE nummer First', 'String', 'Text', '1', '1');
            _mutatieproces_add_custom_field($gid, 'complex_nr', 'Complexnummer First', 'String', 'Text', '1', '2');
            _mutatieproces_add_custom_field($gid, 'vge_adres', 'VGE adres', 'String', 'Text', '1', '3');
            _mutatieproces_add_custom_field($gid, 'vge_straat', 'Straat', 'String', 'Text', '0', '4');
            _mutatieproces_add_custom_field($gid, 'vge_huis_nr', 'Huisnummer', 'String', 'Text', '0', '5');
            _mutatieproces_add_custom_field($gid, 'vge_suffix', 'Toevoeging', 'String', 'Text', '0', '6');
            _mutatieproces_add_custom_field($gid, 'vge_postcode', 'Postcode', 'String', 'Text', '1', '7');
            _mutatieproces_add_custom_field($gid, 'vge_plaats', 'Plaats', 'String', 'Text', '1', '8');
        }
        $gid = _mutatieproces_add_custom_group('woningwaardering', 'Woningwaardering', $dossier, 'Case');
        if ($gid) {
            _mutatieproces_add_custom_field($gid, 'epa_label_opzegging', 'EPA label', 'String', 'Text', '1', '1');
            _mutatieproces_add_custom_field($gid, 'epa_pre_opzegging', 'EPA prelabel', 'String', 'Text', '1', '2');
            _mutatieproces_add_custom_field($gid, 'woningoppervlakte', 'Totale woonoppervlakte', 'String', '1', '3');
        }
        /*
         * create custom data sets and fields for case type Nieuwe huurder
         * This is the case where a new tenant moves into the house
         */
    }

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
 * Implementation of hook civicrm_navigationMenu
 * to create menu items
 * 
 * @author Erik Hommel (erik.hommel@civicoop.org http://www.civicoop.org)
 * @date 6 Jan 2014
 * @param array $params
 */
function mutatieproces_civicrm_navigationMenu( &$params ) {
    $maxKey = ( max( array_keys($params) ) );
    $params[$maxKey+1] = array (
        'attributes' => array (
            'label'      => 'Testen Erik',
            'name'       => 'Testen Erik',
            'url'        => null,
            'permission' => null,
            'operator'   => null,
            'separator'  => null,
            'parentID'   => null,
            'navID'      => $maxKey+1,
            'active'     => 1
    ),
        'child' =>  array (
            '1' => array (
                'attributes' => array (
                    'label'      => 'Testen Property class',
                    'name'       => 'Testen Property class',
                    'url'        => 'civicrm/proptest',
                    'operator'   => null,
                    'separator'  => 0,
                    'parentID'   => $maxKey+1,
                    'navID'      => 1,
                    'active'     => 1
                ),
                'child' => null
            ) 
        ) 
    );
    $params[$maxKey+2] = array (
        'attributes' => array (
            'label'      => 'Laden gegevens vanuit First',
            'name'       => 'Laden gegevens vanuit First',
            'url'        => null,
            'permission' => null,
            'operator'   => null,
            'separator'  => null,
            'parentID'   => null,
            'navID'      => $maxKey+2,
            'active'     => 1
    ),
        'child' =>  array (
            '1' => array (
                'attributes' => array (
                    'label'      => 'Laden vge gegevens',
                    'name'       => 'Laden vge gegevens',
                    'url'        => 'civicrm/vgeladen',
                    'operator'   => null,
                    'separator'  => 0,
                    'parentID'   => $maxKey+2,
                    'navID'      => 1,
                    'active'     => 1
                ),
                'child' => null
            ) 
        ) 
    );
}
function _mutatieproces_add_activity_type($type, $description) {
    $componentCase = 7; //activity type for civi case
    $param = array(
        'label'         =>  $type,
        'description'   =>  $description,
        'component_id'  =>  $componentCase,
        'is_reserved'   =>  true,
        'is_active'     =>  1,
        'weight'        =>  1,
	);
    civicrm_api3('ActivityType', 'Create', $param);
}

function _mutatieproces_add_relationship_type($name_a_b, $name_b_a, $contact_type_a, $contact_type_b) {
    $params['name_a_b'] = $name_a_b;
    $params['name_b_a'] = $name_b_a;
    if (strlen($contact_type_a)) {
        $params['contact_type_a'] = $contact_type_a;
    }
    if (strlen($contact_type_b)) {
        $params['contact_type_b'] = $contact_type_b;
    }
    $result = civicrm_api('relationship_type', 'get', $params);
    if ($result['is_error'] == 1 || $result['count'] == 0) {
        $result = civicrm_api3('RelationshipType', 'Create', $params);
    }
}

function _mutatieproces_add_case($case) {
    $option_group = civicrm_api3('OptionGroup', 'Getsingle', array('name' => 'case_type'));
    $option_group_id = false;
    if (isset($option_group['id'])) {
        $option_group_id = $option_group['id'];
    }
    if (!$option_group_id) {
        return false;
    }

    $params = array(
        'option_group_id'   =>  $option_group_id,
        'name'              =>  $case        
    );
    $option_value = civicrm_api3('OptionValue', 'Getsingle', $params);
    $option_value_id = false;
    $option_value_value = false;
    if (isset($option_value['id'])) {
        $option_value_id = $option_value['id'];
        $option_value_value = $option_value['value'];
    }
    if (!$option_value_id) {
        $option_value = civicrm_api3('OptionValue', 'Create', $params);
        if (isset($option_value['id']) && is_array($option_value['values']) && count($option_value['values'])) {
            $v = reset($option_value['values']);
            $option_value_id = $option_value['id'];
            $option_value_value = $v['value'];
        }
    }
    return $option_value_value;
}

function _mutatieproces_add_custom_group($group, $group_title, $case_id, $extends) {
    $result = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $group));
    if (!isset($result['id'])) {
        $params = array(
            'name'                          =>  $group,
            'title'                         =>  $group_title,
            'extends'                       =>  $extends,
            'extends_entity_column_value'   =>  $case_id,
            'is_active'                     =>  1
        );
        $result = civicrm_api3('CustomGroup', 'Create', $params);
    }
    $gid = false;
    if (isset($result['id'])) {
        $gid = $result['id'];
    }
    return $gid;
}

function _mutatieproces_add_custom_field($gid, $name, $label, $data_type, $html_type, $active, $weight = 0) {
    $params = array(
        'custom_group_id'   =>  $gid,
        'label'             =>  $label
    );
    $result = civicrm_api3('CustomField', 'Getsingle', $params);
    if (!isset($result['id'])) {
        unset($params);
        $params = array(
            'custom_group_id'   =>  $gid,
            'name'              =>  $name,
            'label'             =>  $name,
            'html_type'         =>  $html_type,
            'data_type'         =>  $data_type,
            'weight'            =>  $weight,
            'is_active'         =>  $active
        );
        $result = civicrm_api3('CustomField', 'Create', $params);
        
        $params2= array(
            'label'     =>  $label,
            'active'    =>  $active,
            'id'        =>  $result['id']
        );
        civicrm_api3('CustomField', 'Create', $params2);
    }
}

function _mutatieproces_delete_custom_group($name) {
    $result = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $name));
    if (isset($result['id'])) {
        $gid = $result['id'];
        $result = civicrm_api3('CustomField', 'Get', array('custom_group_id' => $gid));
        if (isset($result['values']) && is_array($result['values'])) {
            foreach($result['values']  as $field) {
                unset($params);
                civicrm_api3('CustomField', 'Delete', array('id' => $field['id']));
            }
        }
        $result = civicrm_api3('CustomGroup', 'Delete', array('id' => $gid));
    }
}

function _mutatieproces_enable_custom_group($name, $enable) {
    $result = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $name));
    if (isset($result['id'])) {
        $gid = $result['id'];
        $params = array(
            'id'        =>  $gid,
            'is_active' =>  $enable ? '1' : '0'
        );
	$result = civicrm_api3('CustomGroup', 'update', $params);
    }
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
function mutatieproces_civicrm_pageRun( &$page ) {
    $page_name = $page->getVar('_name');
    if ($page_name == "CRM_Contact_Page_View_Summary") {
        $contact_id = $page->getVar('_contact_id');
        $contact_type = CRM_Contact_BAO_Contact::getContactType($contact_id);
        /*
         * determine if button Huurovereenkomst opzeggen should be shown based
         * on contact_type
         */
        $huur_opzeggen = _mutatieproces_checkHovOpzeggen($contact_id, $contact_type);
        if ($huur_opzeggen) {
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
            $case_type_id = _mutatieproces_get_case_type_id('DossierOpzeggingHuurcontract');
            if ($case_type_id && $form->elementExists('case_type_id')) {
                $cases = $form->getElement('case_type_id');
                foreach($cases->_options as $id => $option) {
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
function mutatieproces_civicrm_custom( $op, $groupID, $entityID, &$params ) {
    /*
     * retrieve custom_group_id for huurovereenkomst (huishouden) en
     * huurovereenkomst (organisatie), and retrieve hov_nummer field label
     */
    $hov_hh_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (huishouden)");
    if (!civicrm_error($hov_hh_custom_group)) {
        if (isset($hov_hh_custom_group[ 'id'])) {
            $hov_hh_custom_group_id = $hov_hh_custom_group['id'];
            $hov_id_name = "HOV_nummer_First";
            $hov_hh_custom_table = $hov_hh_custom_group['table_name'];
        }
    }
    $hov_org_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (organisatie)");
    if (!civicrm_error($hov_org_custom_group)) {
        if (isset($hov_org_custom_group[ 'id'])) {
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
        $hov_query = 'SELECT '.$hov_custom_field." FROM ".$hov_custom_table." WHERE entity_id = $entityID";
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
    foreach($params as $param) {
        $custom_field = civicrm_api3('CustomField', 'Getsingle', array('id' =>$param['custom_field_id']));
        $custom_field_name = $custom_field['name'];
        /*
         * set fields based on custom_group
         */
        switch(($type)) {
            case "Huishouden":
                switch($custom_field_name) {
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
                switch($custom_field_name) {
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
/**
 * Function to check if the button Hov Opzeggen should be available for 
 * contact (type required to determine)
 * True will be returned if
 *   - contact_type = Organization and Organization has at least one active
 *     huurovereenkomst that does not have an associated case huuropzeggingsdossier
 *   - contact_type = Household and Household has at least one active huurovereenkomst
 *     that does not have an associated case huuropzeggingsdossier
 *   - contact_type = Individual and Individual is an active hoofdhuurder and
 *     related household has at least one active huurovereenkomst that does not
 *     have an associated case huuropzeggingsdossier * 
 * 
 * @author Erik Hommel (erik.hommel@civicoop.org)
 * @date 20 Jan 2014
 * @param int $contact_id
 * @param string $contact_type
 * @return TRUE or FALSE
 */
function _mutatieproces_checkHovOpzeggen($contact_id, $contact_type) {
    if (empty($contact_id) || empty($contact_type)) {
        return FALSE;
    }
    $opzeggen = FALSE;
    /*
     * further processing based on contact_type
     */
    switch($contact_type) {
        /*
         * if individual, first check if individual is active hoofdhuurder
         */
        case "Individual": 
            $hoofd_huurder = CRM_Utils_DgwUtils::checkContactHoofdHuurder($contact_id);
            if ($hoofd_huurder == FALSE) {
                $opzeggen = FALSE;
            } else {
                /*
                 * retrieve active huishouden(s)
                 */
                $huis_houdens = CRM_Utils_DgwUtils::getHuishoudens($contact_id, "relatie hoofdhuurder", TRUE);
                if (empty($huis_houdens)) {
                    $opzeggen = FALSE;
                } else {
                    foreach ($huis_houdens as $huis_houden) {
                        $count_huishouden_hovs = CRM_Utils_DgwMutatieprocesUtils::countActiveHovs($huis_houden['huishouden_id'], $contact_type);
                        if ($count_huishouden_hovs == 0) {
                            $opzeggen = FALSE;
                        } else {
                            /*
                             * check if there is a opzeggingscase for the contact
                             */
                            $opzeggings_case = CRM_Utils_DgwMutatieprocesUtils::checkOpzeggingCase($huis_houden['huishouden_id']);
                            $opzeggen = $opzeggings_case;
                        }
                    }
                }
            }
            break;
        case "Household":
            break;
        case "Organization":
            break;
    }
    return $opzeggen;
}
