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
  var_dump($entities); exit();
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
  $nieuwVgeCustomGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("nieuw_vge");
  if (!civicrm_error($nieuwVgeCustomGroup)) {
    if (isset($nieuwVgeCustomGroup['id'])) {
      $nieuwVgeCustomGroupId = $nieuwVgeCustomGroup['id'];
      $nieuwVgeCustomTable = $nieuwVgeCustomGroup['table_name'];
    }
  }
  $nieuwWwCustomGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("nieuw_woningwaardering");
  if (!civicrm_error($nieuwWwCustomGroup)) {
    if (isset($nieuwWwCustomGroup['id'])) {
      $nieuwWwCustomGroupId = $nieuwWwCustomGroup['id'];
      $nieuwWwCustomTable = $nieuwWwCustomGroup['table_name'];
    }
  }
  
  /*
   * process only if required
   */
  if ($groupID == $hov_hh_custom_group_id || $groupID == $hov_org_custom_group_id || $groupID == $nieuwVgeCustomGroupId) {
      
      
    /*
     * Custom Group vge bij Nieuwehuurdersdossier
     */  
    if ($groupID == $nieuwVgeCustomGroupId) {
        if ($op == "create" || $op == "edit") {
            $vgeNummerField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_vge_nr', $groupID);
            if (!civicrm_error($vgeNummerField)) {
                $vgeNummerColumn = $vgeNummerField['column_name'];
            }
            $complexNummerField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_complex_nr', $groupID);
            if (!civicrm_error($complexNummerField)) {
                $complexNummerColumn = $complexNummerField['column_name'];
            }
            $vgeAdresField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_vge_adres', $groupID);
            if (!civicrm_error($vgeAdresField)) {
                $vgeAdresColumn = $vgeAdresField['column_name'];
            }
            $vgeStraatField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_vge_straat', $groupID);
            if (!civicrm_error($vgeStraatField)) {
                $vgeStraatColumn = $vgeStraatField['column_name'];
            }
            $vgeHuisNrField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_vge_huis_nr', $groupID);
            if (!civicrm_error($vgeHuisNrField)) {
                $vgeHuisNrColumn = $vgeHuisNrField['column_name'];
            }
            $vgeSuffixField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_vge_suffix', $groupID);
            if (!civicrm_error($vgeSuffixField)) {
                $vgeSuffixColumn = $vgeSuffixField['column_name'];
            }
            $vgePostcodeField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_vge_postcode', $groupID);
            if (!civicrm_error($vgePostcodeField)) {
                $vgePostcodeColumn = $vgePostcodeField['column_name'];
            }
            $vgePlaatsField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_vge_plaats', $groupID);
            if (!civicrm_error($vgePlaatsField)) {
                $vgePlaatsColumn = $vgePlaatsField['column_name'];
            }
            $epaLabelField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_epa_label_opzegging', $nieuwWwCustomGroupId);
            if (!civicrm_error($epaLabelField)) {
                $epaLabelColumn = $epaLabelField['column_name'];
            }
            $epaPreField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_epa_pre_opzegging', $nieuwWwCustomGroupId);
            if (!civicrm_error($epaPreField)) {
                $epaPreColumn = $epaPreField['column_name'];
            }
            $oppervlakteField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('nw_woningoppervlakte', $nieuwWwCustomGroupId);
            if (!civicrm_error($oppervlakteField)) {
                $oppervlakteColumn = $oppervlakteField['column_name'];
            }
            /*
             * retrieve vge_id from existing record in custom table
             */
            $getVgeQuery = "SELECT $vgeNummerColumn FROM $nieuwVgeCustomTable WHERE entity_id = $entityID";
            $daoNwVge = CRM_Core_DAO::executeQuery($getVgeQuery);
            if ($daoNwVge->fetch()) {
                /*
                 * retrieve property data with vgeId
                 */
                $property = CRM_Mutatieproces_Property::getByVgeId($daoNwVge->$vgeNummerColumn);
                if (!civicrm_error($property)) {
                    $updateVgeFields = array();
                    $addressParts = array();
                    $cityParts = array();
                    if (isset($property['complex_id']) && !empty($property['complex_id'])) {
                        $updateVgeFields[] = "$complexNummerColumn = '{$property['complex_id']}'";
                    }
                    if (isset($property['vge_street_name']) && !empty($property['vge_street_name'])) {
                        $updateVgeFields[] = "$vgeStraatColumn = '".CRM_Core_DAO::escapeString($property['vge_street_name'])."'";
                        $addressParts[] = $property['vge_street_name'];
                    }
                    if (isset($property['vge_street_number']) && !empty($property['vge_street_number'])) {
                        $updateVgeFields[] = "$vgeHuisNrColumn = '{$property['vge_street_number']}'";
                        $addressParts[] = $property['vge_street_number'];
                    }
                    if (isset($property['vge_street_unit']) && !empty($property['vge_street_unit'])) {
                        $updateVgeFields[] = "$vgeSuffixColumn = '".CRM_Core_DAO::escapeString($property['vge_street_unit'])."'";
                        $addressParts[] = $property['vge_street_unit'];
                    }
                    if (isset($property['vge_postal_code']) && !empty($property['vge_postal_code'])) {
                        if (!empty($addressParts)) {
                            $cityParts[] = $property['vge_postal_code'];
                        }
                        $updateVgeFields[] = "$vgePostcodeColumn = '{$property['vge_postal_code']}'";
                    }
                    if (isset($property['vge_city']) && !empty($property['vge_city'])) {
                        if (!empty($addressParts)) {
                            $cityParts[] = $property['vge_city'];
                        }
                        $updateVgeFields[] = "$vgePlaatsColumn = '".CRM_Core_DAO::escapeString($property['vge_city'])."'";
                    }
                    if (!empty($addressParts)) {
                        $vgeAdres = implode(" ", $addressParts);
                        if (!empty($cityParts)) {
                            $vgeAdres .= ", ".implode(" ", $cityParts);
                        }
                        $updateVgeFields[] = "$vgeAdresColumn = '".CRM_Core_DAO::escapeString($vgeAdres)."'";
                    }
                    if (!empty($updateVgeFields)) {
                        $updVgeQuery = "UPDATE $nieuwVgeCustomTable SET ".implode(", ", $updateVgeFields)." WHERE entity_id = $entityID";
                        CRM_Core_DAO::executeQuery($updVgeQuery);
                    }
                    $updateWwFields = array();
                    if (isset($property['epa_label']) && !empty($property['epa_label'])) {
                        $updateWwFields[] = "$epaLabelColumn = '{$property['epa_label']}'";
                    }
                    if (isset($property['epa_pre']) && !empty($property['epa_pre'])) {
                        $updateWwFields[] = "$epaPreColumn = '{$property['epa_pre']}'";
                    }
                    if (isset($property['square_mtrs']) && !empty($property['square_mtrs'])) {
                        $updateWwFields[] = "$oppervlakteColumn = '{$property['square_mtrs']}'";
                    }
                    if (!empty($updateWwFields)) {
                        $updWwQuery = "UPDATE $nieuwWwCustomTable SET ".implode(", ", $updateWwFields)." WHERE entity_id = $entityID";
                        CRM_Core_DAO::executeQuery($updWwQuery);
                    }                    
                }
            }
        }
        
    }
    /*
     * Custom group huurovereenkomst with individual or organization
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
          $contract_exists = $property_contract->checkHovIdExists($dao_hov->$hov_custom_field);
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
            $result['hov_name'] = $param['value'];
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
            $result['hov_name'] = $param['value'];
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
    $result['type'] = "h";
  }
  return $result;
}
/**
 * Implementation of hook civicrm_validateForm
 * 
 * Validate CRM_Case_Form_Case
 * - validate if vge_id has been entered and is valid
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org)
 * @date 21 Mar 2014
 */
function mutatieproces_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
    if ($formName == "CRM_Case_Form_Case") {
        $contactId = $form->getVar('_currentlyViewedContactId');
        /*
         * validation for case type Nieuwehuurdersdossier
         */
        $nieuweHuurderTypeId = _mutatieproces_get_case_type_id("Nieuwehuurdersdossier");
        if ($fields['case_type_id'] == $nieuweHuurderTypeId) {
            /*
             * only one active Nieuwehuurdersdossier at a time allowed
             */
            $casesContact = civicrm_api3('Case', 'Get', array('contact_id' => $contactId));
            foreach ($casesContact['values'] as $caseId => $caseContact) {
                if ($caseContact['is_deleted'] == 0 && $caseContact['case_type_id'] == $nieuweHuurderTypeId) {
                    /*
                     * retrieve case status for closed
                     */
                    $caseStatusGroupParams = array(
                        'name'  =>  'case_status',
                        'return'=>  'id'
                    );
                    try {
                        $caseStatusGroupId = civicrm_api3('OptionGroup', 'Getvalue', $caseStatusGroupParams);
                        $caseStatusFieldParams = array(
                            'option_group_id'   =>  $caseStatusGroupId,
                            'name'              =>  'closed',
                            'return'            =>  'value'
                        );
                        try {
                            $caseStatusClosedId = civicrm_api3('OptionValue', 'Getvalue', $caseStatusFieldParams);
                            if ($caseContact['status_id'] != $caseStatusClosedId) {
                                $errors['case_type_id'] = 'Er is al een open Nieuwehuurdersdossier voor dit contact, er kan slechts 1 open Nieuwehuurdersdossier zijn.';
                            }
                        } catch (CiviCRM_API3_Exception $e) {
                            throw new Exception('Geen value gevonden in option group case_status met de name closed, melding van API OptionValue Getvalue : '.$e->getMessage());
                        }
                        
                    } catch (CiviCRM_API3_Exception $e) {
                        throw new Exception('Geen option group met name case_status gevonden, melding van API OptionGroup Getvalue : '.$e->getMessge());
                    }
                } 
            }
            /*
             * build form field name of vge_id by retrieving custom_id and adding '-1'
             */
            $customGroupParams = array(
                'name'  =>  'nieuw_vge',
                'return'=>  'id'
            );
            try {
                $customGroupId = civicrm_api3('CustomGroup', 'Getvalue', $customGroupParams);
                $customFieldParams = array(
                    'custom_group_id'   =>  $customGroupId,
                    'name'              =>  'nw_vge_nr',
                    'return'            =>  'id'
                );
                try {
                    $customFieldId = civicrm_api3('CustomField', 'Getvalue', $customFieldParams);
                    $vgeIdFieldName = "custom_".$customFieldId."_-1";
                    /*
                     * field can not be empty
                     */
                    if (empty($fields[$vgeIdFieldName])) {
                        $errors[$vgeIdFieldName] = "VGE nummer mag niet leeg zijn.";
                    } else {
                        /*
                         * has to be a valid property
                         */
                        $property = CRM_Mutatieproces_Property::getByVgeId($fields[$vgeIdFieldName]);
                        if (civicrm_error($property)) {
                            $errors[$vgeIdFieldName] = "VGE nummer ".$fields[$vgeIdFieldName]." niet gevonden in bestand met eenheden.";
                        } else {
                            if (isset($property['count']) && $property['count'] == 0) {
                                $errors[$vgeIdFieldName] = "VGE nummer ".$fields[$vgeIdFieldName]." niet gevonden in bestand met eenheden.";                        
                            } 
                        }
                    }

                } catch (CiviCRM_API3_Exception $e) {
                    throw new Exception(ts('Could not find custom field with name nw_vge_nr in custom group nieuw_vge, error from API CustomField Getvalue : '.$e->getMessage()));
                }
            } catch (CiviCRM_API3_Exception $e) {
                throw new Exception(ts('Could not find custom group with name nieuw_vge, error from API CustomGroup Getvalue : '.$e->getMessage()));
            }
        }
    }
    return;
}
/**
 * Implementation of hook_civicrm_tokens
 * Deze hook gebruiken we om de volgende tokens kenbaar te maken aan het systeem
 * - mutatie.eindopname: datum van de eindopname/onbekend als die niet gevonden kan worden
 * 
 * 
 * @param array $tokens
 */
function mutatieproces_civicrm_tokens(&$tokens) {
  $tokens['mutatieproces'] = array (
    'mutatieproces.eindopname' => 'Datum eindopname (indien bekend)',
  );
}

/**
 * implementation of hook_civicrm_tokenValues
 * 
 * This function deletegates the tokens to the desired functions
 * 
 * @param type $values
 * @param type $cids
 * @param type $job
 * @param type $tokens
 * @param type $context
 */
function mutatieproces_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  if (!empty($tokens['mutatieproces'])) {
    if (in_array('eindopname', $tokens['mutatieproces'])) {
       mutatieproces_token_eindopname($values, $cids, $job, $tokens, $context);
    }
  } elseif (is_array($tokens) && count($tokens) == 0) {
    mutatieproces_token_eindopname($values, $cids, $job, $tokens, $context);
  }
}

/**
 * implementation of hook: mutatieproces.eindopname
 * 
 * This function deletegates the tokens to the desired functions
 * 
 * @param type $values
 * @param type $cids
 * @param type $job
 * @param type $tokens
 * @param type $context
 */
function mutatieproces_token_eindopname(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  $contacts = $cids;
  $use_array = true;
  if (!is_array($contacts) && !empty($cids)) {
    $contacts = array($cids);
    $use_array = false;
  }
  if (count($contacts) == 0) {
    return;
  }
  
  $act_type_group = civicrm_api3('OptionGroup', 'getsingle', array('name' => 'activity_type'));
  $gid = $act_type_group['id'];
  $activity_type = civicrm_api3('OptionValue', 'getsingle', array('option_group_id' => $gid, 'name' => 'eindgesprek_huuropzegging'));
  $activity_type_id = $activity_type['value'];
  
  if (!$use_array) {
    $values['mutatieproces.eindopname'] = 'Onbekend';
  } else {
    foreach($contacts as $cid) {
      $values[$cid]['mutatieproces.eindopname'] = 'Onbekend';
    }
  }
  
  $sql = "SELECT MIN(`a`.`activity_date_time`) AS `activity_date_time`, `cc`.`contact_id` AS `contact_id` FROM `civicrm_activity` `a` "
      . "INNER JOIN `civicrm_case_activity` `ca` ON `a`.`id` = `ca`.`activity_id` "
      . "INNER JOIN `civicrm_case_contact` `cc` ON `ca`.`case_id` = `cc`.`case_id`"
      . "WHERE `a`.`activity_type_id` = '".$activity_type_id."' AND `a`.`status_id` = '1' AND `cc`.`contact_id` IN (".implode(",", $contacts).") AND `a`.`is_current_revision` = '1' GROUP BY `cc`.`contact_id`";

  $dao = CRM_Core_DAO::executeQuery($sql);
  while($dao->fetch()) {
    $cid = $dao->contact_id;
    if (in_array($cid, $contacts)) {
        $date = new DateTime($dao->activity_date_time);
        $days = array ('zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag');
        $months = array('januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december');
        $dateStr = $days[$date->format('w')];
        $dateStr .= ' '.$date->format('j');
        $dateStr .= ' '.$months[$date->format('n')-1];
        $dateStr .= ' '.$date->format('Y');
        
        if (!$use_array) { 
          $values['mutatieproces.eindopname'] = $dateStr;
        } else {
          $values[$cid]['mutatieproces.eindopname'] = $dateStr;
        }
      }
    }  
}