<?php

/**
 * Class PropertyContract for dealing with property contracts (De Goede Woning)
 * 
 * @author Erik Hommel (erik.hommel@civicoop.org, http://www.civicoop.org)
 * @date 17 Jan 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A.
 * Licensed to De Goede Woning under the Academic Free License version 3.0.
 */
class CRM_Mutatieproces_PropertyContract {
    private $_table = "";
    public $id = 0;
    public $vge_id = 0;
    public $hoofdhuurder_id = 0;
    public $medehuurder_id = 0;
    public $start_date = "";
    public $end_date = "";
    /**
     * constructor
     */
    function __construct() {
        $this->_table = 'civicrm_property_contract';
    }
    /**
     * Function to check if there is a PropertyContract record for 
     * the incoming hov_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 7 Jan 2014
     * @param integer $hov_id
     * @return TRUE or FALSE
     * @access public
     */
    public function checkContractExists($hov_id) {
        if (empty($hov_id) || !is_numeric($hov_id)) {
            return FALSE;
        }
        $query = "SELECT COUNT(*) AS count_hov FROM ".$this->_table." WHERE hov_id = $hov_id";
        $dao = CRM_Core_DAO::executeQuery($query);
        if ($dao->fetch()) {
            if ($dao->count_hov > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    /**
     * Function to create a record in civicrm_property_contract
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param array $params
     * @return integer $id (id of the created record)
     * @access public
     */
    public function create($params) {
        $insert_fields = array();
        if (isset($params['hov_id']) && is_numeric($params['hov_id'])) {
            $insert_fields[] = "hov_id = {$params['hov_id']}";
        }
        if (isset($params['hov_vge_id']) && is_numeric($params['hov_vge_id'])) {
            $insert_fields[] = "hov_vge_id = {$params['hov_vge_id']}";
        }
        if (isset($params['hov_corr_name'])) {
            $hov_corr_name = CRM_Core_DAO::escapeString($params['hov_corr_name']);
            $insert_fields[] = "hov_corr_name = '$hov_corr_name'";
        }
        if (isset($params['hov_start_date'])) {
            if (!empty($params['hov_start_date'])) {
                $hov_start_date = date("Ymd", strtotime($params['hov_start_date']));
                $insert_fields[] = "hov_start_date = '$hov_start_date'";
            }
        }
        if (isset($params['hov_end_date'])) {
            if (!empty($params['hov_end_date'])) {
                $hov_end_date = date("Ymd", strtotime($params['hov_end_date']));
                $insert_fields[] = "hov_end_date = '$hov_end_date'";
            }
        }
        /*
         * set hoofd- and medehuurder id based on active relationships
         * hoofd- en medehuurder
         */
        $hoofd_huurder_id = $this->_getHoofdHuurderId($params['hov_id']);
        if (!empty($hoofd_huurder_id)) {
            $insert_fields[] = "hov_hoofd_huurder_id = $hoofd_huurder_id";
        }
        $mede_huurder_id = $this->_getMedeHuurderId($params['hov_id']);
        if (!empty($mede_huurder_id)) {
            $insert_fields[] = "hov_mede_huurder_id = $mede_huurder_id";
        }
        /*
         * insert record if required
         */
        if (!empty($insert_fields)) {
            $query = "INSERT INTO ".$this->_table. " SET ".implode(", ", $insert_fields);
            CRM_Core_DAO::executeQuery($query);
        }
    }
    /**
     * Function to update a record in civicrm_property_contract
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param array $params
     * @access public
     */
    public function update($params) {
        $update_fields = array();
        if (isset($params['hov_id']) && is_numeric($params['hov_id'])) {
            $update_fields[] = "hov_id = {$params['hov_id']}";
        }
        if (isset($params['hov_vge_id']) && is_numeric($params['hov_vge_id'])) {
            $update_fields[] = "hov_vge_id = {$params['hov_vge_id']}";
        }
        if (isset($params['hov_corr_name'])) {
            $hov_corr_name = CRM_Core_DAO::escapeString($params['hov_corr_name']);
            $update_fields[] = "hov_corr_name = '$hov_corr_name'";
        }
        if (isset($params['hov_start_date'])) {
            if (!empty($params['hov_start_date'])) {
                $hov_start_date = date("Ymd", strtotime($params['hov_start_date']));
                $update_fields[] = "hov_start_date = '$hov_start_date'";
            }
        }
        if (isset($params['hov_end_date'])) {
            if (!empty($params['hov_end_date'])) {
                $hov_end_date = date("Ymd", strtotime($params['hov_end_date']));
                $update_fields[] = "hov_end_date = '$hov_end_date'";
            }
        }
        /*
         * set hoofd- and medehuurder id based on active relationships
         * hoofd- en medehuurder
         */
        $hoofd_huurder_id = $this->_getHoofdHuurderId($params['hov_id']);
        if (!empty($hoofd_huurder_id)) {
            $update_fields[] = "hov_hoofd_huurder_id = $hoofd_huurder_id";
        }
        $mede_huurder_id = $this->_getMedeHuurderId($params['hov_id']);
        if (!empty($mede_huurder_id)) {
            $update_fields[] = "hov_mede_huurder_id = $mede_huurder_id";
        }
        /*
         * update record if required
         */
        if (!empty($update_fields)) {
            $query = "UPDATE ".$this->_table. " SET ".implode(", ", $update_fields);
            CRM_Core_DAO::executeQuery($query);
        }
       
    }
    /**
     * Function to get a PropertyContract by hov_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param integer $hov_id
     * @return array $property_contract
     * @access public
     * @static
     */
    public static function getPropertyContractWithHovId($hov_id) {
        $property_contract = array();
        if (empty($hov_id) || !is_numeric($hov_id)) {
            return $property_contract;
        }
        $query = "SELECT * FROM civicrm_property_contract WHERE hov_id = $hov_id";
        $dao = CRM_Core_DAO::executeQuery($query);
        if ($dao->fetch()) {
            $property_contract['id'] = $dao->id;
            $property_contract['hov_id'] = $dao->hov_id;
            $property_contract['hov_vge_id'] = $dao->hov_vge_id;
            $property_contract['hov_corr_name'] = $dao->hov_corr_name;
            $property_contract['hov_start_date'] = $dao->hov_start_date;
            $property_contract['hov_end_date'] = $dao->hov_end_date;
            $property_contract['hov_hoofd_huurder_id'] = $dao->hov_hoofd_huurder_id;
            $property_contract['hov_mede_huurder_id'] = $dao->hov_mede_huurder_id;
            $property_contract['hov_mutatie_id'] = $dao->hov_mutatie_id;
        }
        return $property_contract;
    }
    /**
     * Function to get all PropertyContracts for a contact_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param integer $contact_id
     * @return array $property_contract
     * @access public
     * @static 
     */
    public static function getPropertyContractsWithContactId($contact_id) {
        $property_contracts = array();
        if (empty($contact_id) || !is_numeric($contact_id)) {
            return $property_contracts;
        }
        /*
         * retrieve all hov_id's for contact from custom tables
         */
        return $property_contracts;
        
    }
    /**
     * Function to retrieve hoofdhuurder id for huurovereenkomst
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param integer $hov_id
     * @return integer $hoofd_huurder_id
     * @access protected
     */
    protected function _getHoofdHuurderId($hov_id) {
        $hoofd_huurder_id = 0;
        if (empty($hov_id)) {
            return $hoofd_huurder_id;
        }
        /*
         * retrieve relationship type id for Hoofdhuurder
         */
        $relationship_type = civicrm_api3('RelationshipType', 'Getsingle', array('name_a_b' => "Hoofdhuurder"));
        if (isset($relationship_type['id'])) {
            /*
             * retrieve entity_id for huurovereenkomst, check first if in huishouden
             * if not, check organisatie
             */
            $hov_hh_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (huishouden)");
            if (!civicrm_error($hov_hh_custom_group)) {
                $hov_table = $hov_hh_custom_group['table_name'];
                $hov_custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("HOV_nummer_First", $hov_hh_custom_group['id']);
                if (!civicrm_error($hov_custom_field)) {
                    $hov_field = $hov_custom_field['column_name'];
                    $query = "SELECT entity_id FROM $hov_table WHERE $hov_field = $hov_id";
                    $dao_hov = CRM_Core_DAO::executeQuery($query);
                    if ($dao_hov->fetch()) {
                        $entity_id = $dao_hov->entity_id;
                    }
                }   
            }
            if (!$entity_id) {
                $hov_org_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (organisatie)");
                if (!civicrm_error($hov_org_custom_group)) {
                    $hov_table = $hov_org_custom_group['table_name'];
                    $hov_custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hov_nummer", $hov_org_custom_group['id']);
                    if (!civicrm_error($hov_custom_field)) {
                        $hov_field = $hov_custom_field['column_name'];
                        $query = "SELECT entity_id FROM $hov_table WHERE $hov_field = $hov_id";
                        $dao_hov = CRM_Core_DAO::executeQuery($query);
                        if ($dao_hov->fetch()) {
                            return $dao_hov->entity_id;
                        }
                    }   
                }
            }
            /*
             * retrieve actieve relationship Hoofdhuurder where 
             * contact_id_b = entity_id retrieved
             */
            if ($entity_id) {
                $params = array(
                    'relationship_type_id'  =>  $relationship_type['id'],
                    'contact_id_b'          =>  $entity_id,
                    'is_active'             =>  1
                );
                $relationships = civicrm_api3('Relationship', 'Get', $params);
                if (isset($relationships['values'])) {
                    foreach($relationships['values'] as $relationship) {
                        $hoofd_huurder_id = $relationship['contact_id_a'];
                    }
                }
            }
        }
        return $hoofd_huurder_id;
    }
    /**
     * Function to retrieve medehuurder id for huurovereenkomst
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param integer $hov_id
     * @return integer $mede_huurder_id
     * @access protected
     */
    protected function _getMedeHuurderId($hov_id) {
        $mede_huurder_id = 0;
        if (empty($hov_id)) {
            return $mede_huurder_id;
        }
        /*
         * retrieve relationship type id for Medehuurder
         */
        $relationship_type = civicrm_api3('RelationshipType', 'Getsingle', array('name_a_b' => "Medehuurder"));
        if (isset($relationship_type['id'])) {
            /*
             * retrieve entity_id for huurovereenkomst, check first if in huishouden
             * if not, check organisatie
             */
            $hov_hh_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (huishouden)");
            if (!civicrm_error($hov_hh_custom_group)) {
                $hov_table = $hov_hh_custom_group['table_name'];
                $hov_custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("HOV_nummer_First", $hov_hh_custom_group['id']);
                if (!civicrm_error($hov_custom_field)) {
                    $hov_field = $hov_custom_field['column_name'];
                    $query = "SELECT entity_id FROM $hov_table WHERE $hov_field = $hov_id";
                    $dao_hov = CRM_Core_DAO::executeQuery($query);
                    if ($dao_hov->fetch()) {
                        $entity_id = $dao_hov->entity_id;
                    }
                }   
            }
            if (!$entity_id) {
                $hov_org_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (organisatie)");
                if (!civicrm_error($hov_org_custom_group)) {
                    $hov_table = $hov_org_custom_group['table_name'];
                    $hov_custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hov_nummer", $hov_org_custom_group['id']);
                    if (!civicrm_error($hov_custom_field)) {
                        $hov_field = $hov_custom_field['column_name'];
                        $query = "SELECT entity_id FROM $hov_table WHERE $hov_field = $hov_id";
                        $dao_hov = CRM_Core_DAO::executeQuery($query);
                        if ($dao_hov->fetch()) {
                            return $dao_hov->entity_id;
                        }
                    }   
                }
            }
            /*
             * retrieve actieve relationship Medehuurder where 
             * contact_id_b = entity_id retrieved
             */
            if ($entity_id) {
                $params = array(
                    'relationship_type_id'  =>  $relationship_type['id'],
                    'contact_id_b'          =>  $entity_id,
                    'is_active'             =>  1
                );
                $relationships = civicrm_api3('Relationship', 'Get', $params);
                if (isset($relationships['values'])) {
                    foreach($relationships['values'] as $relationship) {
                        $mede_huurder_id = $relationship['contact_id_a'];
                    }
                }
            }
        }
        return $mede_huurder_id;
    }
    /**
     * Funtion to set the custom fields for a huurovereenkomst
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 27 Jan 2014
     * @param type $hov_id
     * @param type $case_id
     * @return void
     * @access public
     * @static
     */
    public static function setHovFieldsCase($hov_id, $case_id, $expected_end_date) {
        /*
         * end if hov_id, case_id empty or non-numeric
         */
        if (empty($hov_id) || !is_numeric($hov_id) || empty($case_id) || !is_numeric($case_id)) {
            return;
        }
        /*
         * retrieve custom group for huuropzegging
         */
        $custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName('huuropzegging');
        $custom_table = $custom_group['table_name'];
        $hov_id_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('hov_nr', $custom_group['id']);
        $hov_id_field_name = $hov_id_field['column_name'];
        /*
         * check if already record for case and set action update or insert
         */
        $query_hov = "SELECT COUNT(*) AS count_hov  FROM $custom_table WHERE entity_id = $case_id AND $hov_id_field_name = $hov_id";
        $dao_hov = CRM_Core_DAO::executeQuery($query_hov);
        if ($dao_hov->fetch()) {
            if ($dao_hov->count_hov == 0) {
                $action = "INSERT INTO";
            } else {
                $action = "UPDATE";
            }
        }
        $fields = array();
        /*
         * retrieve hov_data
         */
        $hov_data = self::getPropertyContractWithHovId($hov_id);
        if (isset($hov_data['hov_start_date'])) {
            $start_date = CRM_Utils_DgwUtils::convertDMJString($hov_data['hov_start_date']);
            $start_date_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hov_start_datum", $custom_group['id']);
            $start_date_field_name = $start_date_field['column_name'];
            $fields[] = $start_date_field_name." = '$start_date'";
        }
        if (isset($hov_data['hov_hoofd_huurder_id'])) {
            $params = array(
                'contact_id'    =>  $hov_data['hov_hoofd_huurder_id'],
                'return'        =>  "display_name"
            );
            $contact_api = civicrm_api3('Contact', 'Getvalue', $params);
            $name = CRM_Core_DAO::escapeString($contact_api['result']);
            $name_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hoofdhuurder_name", $custom_group['id']);
            $name_field_name = $name_field['column_name'];
            $fields = $name_field_name." = '$name'";
            $persoons_nr = CRM_DgwUtils::getPersoonsnummerFirst($hov_data['hov_hoofdhuurder_id']);
            $persoons_nr_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hoofdhuurder_nr_first", $custom_group['id']);
            $persoons_nr_field_name = $persoons_nr_field['column_name'];
            $fields[] = $persoons_nr_field_name." = $persoons_nr";
        }
        if (isset($hov_data['hov_mede_huurder_id'])) {
            $params = array(
                'contact_id'    =>  $hov_data['hov_mede_huurder_id'],
                'return'        =>  "display_name"
            );
            $contact_api = civicrm_api3('Contact', 'Getvalue', $params);
            $name = CRM_Core_DAO::escapeString($contact_api['result']);
            $name_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("medehuurder_name", $custom_group['id']);
            $name_field_name = $name_field['column_name'];
            $fields = $name_field_name." = '$name'";
            $persoons_nr = CRM_DgwUtils::getPersoonsnummerFirst($hov_data['hov_medehuurder_id']);
            $persoons_nr_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("medehuurder_nr_first", $custom_group['id']);
            $persoons_nr_field_name = $persoons_nr_field['column_name'];
            $fields[] = $persoons_nr_field_name." = $persoons_nr";
        }
        $action_query = $action." $custom_table SET ".implode(", ", $fields);
        if ($action == "UPDATE") {
            $action_query_ .= " WHERE entity_id = $case_id";
        }
        if ($action == "INSERT INTO") {
            $action_query .= ", entity_id = $case_id, $hov_id_field_name = $hov_id";
        }
        CRM_Core_DAO::executeQuery($action_query);
    
    }
}