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
    public $hovId = 0;
    public $vgeId = 0;
    public $hoofdHuurderId = 0;
    public $medeHuurderId = 0;
    public $startDate = "";
    public $endDate = "";
    public $expectedEndDate = "";
    public $type = "";
    public $hovName = "";
    public $mutatieId = 0;
    /**
     * constructor
     */
    function __construct() {
        $this->_table = 'civicrm_property_contract';
    }
    /**
     * Function to check if there is a PropertyContract record for 
     * the incoming hovId
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 7 Jan 2014
     * @param integer $hov_id
     * @return TRUE or FALSE
     * @access public
     */
    public function checkHovIdExists($hovId) {
        if (empty($hovId) || !is_numeric($hovId)) {
            return FALSE;
        }
        $query = "SELECT COUNT(*) AS count_hov FROM " . $this->_table . " WHERE hov_id = $hovId";
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
     * @return integer $newId (id of the created record)
     * @throws Exception if $params is not an array
     * @access public
     */
    public function create($params) {
        /*
         * error if params is not an array
         */
        if (!is_array($params)) {
            throw new Exception("Params is not array in function PropertyContract->create");
        }
        $insertFields = array();
        $numericFields = array();
        $daoColumn = CRM_Core_DAO::executeQuery("SHOW FULL COLUMNS FROM ".$this->_table);
        while ($daoColumn->fetch()) {
            if (substr($daoColumn->Type, 0, 3) == "int") {
                $numericFields[] = $daoColumn->Field;
            }
        }
        foreach ($params as $key => $value) {
            if (!empty($value)) {
                if ($key == "hov_start_date" || $key == "hov_end_date") {
                    $value = date("Ymd", strtotime($value));
                }
                if (in_array($key, $numericFields)) {
                    $insertFields[] = $key." = ".$value;
                } else {
                    $insertFields[] = $key." = '".CRM_Core_DAO::escapeString($value). "'";
                }
            }
        }
        /*
         * insert record if required
         */
        if (!empty($insertFields)) {
            $query = "INSERT INTO " . $this->_table . " SET " . implode(", ", $insertFields);
            CRM_Core_DAO::executeQuery($query);
        }
        $latestQuery = "SELECT MAX(id) AS max_id FROM ".$this->_table;
        $daoLatest = CRM_Core_DAO::executeQuery($latestQuery);
        if ($daoLatest->fetch()) {
            if (isset($daoLatest->max_id)) {
                $this->id = $daoLatest->max_id;
            }
        }        
        return $this->id;
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
        /*
         * error if params is not an array
         */
        if (!is_array($params)) {
            throw new Exception("Params is not array in function PropertyContract->create");
        }
        $updateFields = array();
        $numericFields = array();
        $daoColumn = CRM_Core_DAO::executeQuery("SHOW FULL COLUMNS FROM ".$this->_table);
        while ($daoColumn->fetch()) {
            if (substr($daoColumn->Type, 0, 3) == "int") {
                $numericFields[] = $daoColumn->Field;
            }
        }
        foreach ($params as $key => $value) {
            if ($key == "hov_start_date" || $key == "hov_end_date" || $key == "hov_expected_end_date") {
                $value = date("Ymd", strtotime($value));
            }
            if (in_array($key, $numericFields)) {
                $updateFields[] = $key." = ".$value;
            } else {
                $updateFields[] = $key." = '".CRM_Core_DAO::escapeString($value). "'";
            }
        }
        /*
         * update record if required
         */
        if (!empty($updateFields)) {
            $query = "UPDATE " . $this->_table . " SET " . implode(", ", $updateFields)." WHERE id = {$this->id}";
            CRM_Core_DAO::executeQuery($query);
        }
    }
    /**
     * Function to get a PropertyContract by hov_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param integer $hovId
     * @return array $property_contract
     * @access public
     * @static
     */
    public static function getPropertyContractWithHovId($hovId) {
        $propertyContract = array();
        if (empty($hovId) || !is_numeric($hovId)) {
        return $propertyContract;
        }
     $query = "SELECT * FROM civicrm_property_contract WHERE hov_id = $hovId";
        $dao = CRM_Core_DAO::executeQuery($query);
        if ($dao->fetch()) {
            $propertyContract['id'] = $dao->id;
            $propertyContract['hov_id'] = $dao->hov_id;
            $propertyContract['hov_vge_id'] = $dao->hov_vge_id;
            $propertyContract['hov_name'] = $dao->hov_name;
            $propertyContract['hov_start_date'] = $dao->hov_start_date;
            $propertyContract['hov_end_date'] = $dao->hov_end_date;
            $propertyContract['hov_hoofd_huurder_id'] = $dao->hov_hoofd_huurder_id;
            $propertyContract['hov_mede_huurder_id'] = $dao->hov_mede_huurder_id;
            $propertyContract['hov_mutatie_id'] = $dao->hov_mutatie_id;
            $propertyContract['type'] = $dao->type;
        }
        return $propertyContract;
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
    public static function getPropertyContractsWithContactId($contactId) {
        $propertyContracts = array();
        if (empty($contactId) || !is_numeric($contactId)) {
            return $propertyContracts;
        }
        /*
         * retrieve all hovId's for contact from custom tables
         */
        return $propertyContracts;
    }
    /**
     * Function to retrieve hoofdhuurder id for huurovereenkomst
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param integer $hovId
     * @return integer $hoofdHuurderId
     * @access public
     */
    public function getHoofdHuurderId($hovId) {
        $hoofdHuurderId = 0;
        if (empty($hovId)) {
            return $hoofdHuurderId;
        }
        /*
         * retrieve relationship type id for Hoofdhuurder
         */
        $relationshipType = civicrm_api3('RelationshipType', 'Getsingle', array('name_a_b' => "Hoofdhuurder"));
        if (isset($relationshipType['id'])) {
            /*
             * retrieve entity_id for huurovereenkomst, check first if in huishouden
             * if not, check organisatie
             */
            $hovHhCustomGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (huishouden)");
            if (!civicrm_error($hovHhCustomGroup)) {
                $hovTable = $hovHhCustomGroup['table_name'];
                $hovCustomField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("HOV_nummer_First", $hovHhCustomGroup['id']);
                if (!civicrm_error($hovCustomField)) {
                    $hovField = $hovCustomField['column_name'];
                    $query = "SELECT entity_id FROM $hovTable WHERE $hovField = $hovId";
                    $daoHov = CRM_Core_DAO::executeQuery($query);
                    if ($daoHov->fetch()) {
                        $entityId = $daoHov->entity_id;
                    }
                }
            }
            if (!$entityId) {
                $hovOrgCustomGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (organisatie)");
                if (!civicrm_error($hovOrgCustomGroup)) {
                    $hovTable = $hovOrgCustomGroup['table_name'];
                    $hovCustomField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hov_nummer", $hovOrgCustomGroup['id']);
                    if (!civicrm_error($hovCustomField)) {
                        $hovField = $hovCustomField['column_name'];
                        $query = "SELECT entity_id FROM $hovTable WHERE $hovField = $hovId";
                        $daoHov = CRM_Core_DAO::executeQuery($query);
                        if ($daoHov->fetch()) {
                            return $daoHov->entity_id;
                        }
                    }
                }
            }
            /*
             * retrieve actieve relationship Hoofdhuurder where 
             * contact_id_b = entity_id retrieved
             */
            if ($entityId) {
                $params = array(
                    'relationship_type_id' => $relationshipType['id'],
                    'contact_id_b' => $entityId,
                );
                $relationships = civicrm_api3('Relationship', 'Get', $params);
                if (isset($relationships['values'])) {
                    foreach ($relationships['values'] as $relationship) {
                        $hoofdHuurderId = $relationship['contact_id_a'];
                    }
                }
            }
        }
        return $hoofdHuurderId;
    }
    /**
     * Function to retrieve medehuurder id for huurovereenkomst
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param integer $hovId
     * @return integer $medeHuurderId
     * @access public
     */
    public function getMedeHuurderId($hovId) {
        $medeHuurderId = 0;
        if (empty($hovId)) {
            return $medeHuurderId;
        }
        /*
         * retrieve relationship type id for Medehuurder
         */
        $relationshipType = civicrm_api3('RelationshipType', 'Getsingle', array('name_a_b' => "Medehuurder"));
        if (isset($relationshipType['id'])) {
            /*
             * retrieve entity_id for huurovereenkomst, check first if in huishouden
             * if not, check organisatie
             */
            $hovHhCustomGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (huishouden)");
            if (!civicrm_error($hovHhCustomGroup)) {
                $hovTable = $hovHhCustomGroup['table_name'];
                $hovCustomField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("HOV_nummer_First", $hovHhCustomGroup['id']);
                if (!civicrm_error($hovCustomField)) {
                    $hovField = $hovCustomField['column_name'];
                    $query = "SELECT entity_id FROM $hovTable WHERE $hovField = $hovId";
                    $daoHov = CRM_Core_DAO::executeQuery($query);
                    if ($daoHov->fetch()) {
                        $entityId = $daoHov->entity_id;
                    }
                }
            }
            if (!$entityId) {
                $hov_org_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName("Huurovereenkomst (organisatie)");
                if (!civicrm_error($hov_org_custom_group)) {
                    $hovTable = $hov_org_custom_group['table_name'];
                    $hovCustomField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hov_nummer", $hov_org_custom_group['id']);
                    if (!civicrm_error($hovCustomField)) {
                        $hovField = $hovCustomField['column_name'];
                        $query = "SELECT entity_id FROM $hovTable WHERE $hovField = $hovId";
                        $daoHov = CRM_Core_DAO::executeQuery($query);
                        if ($daoHov->fetch()) {
                            return $daoHov->entity_id;
                        }
                    }
                }   
            }
            /*
             * retrieve actieve relationship Medehuurder where 
             * contact_id_b = entity_id retrieved
             */
            if (isset($entityId) && !empty($entityId)) {
                $params = array(
                    'relationship_type_id' => $relationshipType['id'],
                    'contact_id_b' => $entityId,
                );
                $relationships = civicrm_api3('Relationship', 'Get', $params);
                if (isset($relationships['values'])) {
                    foreach ($relationships['values'] as $relationship) {
                        $medeHuurderId = $relationship['contact_id_a'];
                    }
                }   
            }
        }
        return $medeHuurderId;
    }
    /**
     * Funtion to set the custom fields for a huurovereenkomst
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 27 Jan 2014
     * @param int $hovId
     * @param int $caseId
     * @param string $expectedEndDate
     * @return void
     * @access public
     * @static
     */
    public static function setHovFieldsCase($hovId, $caseId, $expectedEndDate) {
      CRM_Core_Error::debug('hovId', $hovId);
      CRM_Core_Error::debug('caseId', $caseId);
      CRM_Core_Error::debug('end', $expectedEndDate);
        /*
         * end if hov_id, case_id empty or non-numeric
         */
        if (empty($hovId) || !is_numeric($hovId) || empty($caseId) || !is_numeric($caseId)) {
            return;
        }
        /*
         * retrieve custom group for huuropzegging
         */
        $customGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName('huur_opzegging');
        $customTable = $customGroup['table_name'];
        $hovIdField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('hov_nr', $customGroup['id']);
        $hovIdFieldName = $hovIdField['column_name'];
        /*
         * check if already record for case and set action update or insert
         */
        $queryOpzegging = "SELECT COUNT(*) AS count_opzegging  FROM $customTable WHERE entity_id = $caseId AND $hovIdFieldName = $hovId";
        $daoOpzegging = CRM_Core_DAO::executeQuery($queryOpzegging);
        if ($daoOpzegging->fetch()) {
          if ($queryOpzegging->count_opzegging == 0) {
            $action = "INSERT INTO";
          } else {
            $action = "UPDATE";
          }
        }
        /*
         * retrieve hov_data
         */
        $queryFields = array();
        $hovData = self::getPropertyContractWithHovId($hovId);
        if (!empty($hovData)) {
          CRM_Core_Error::debug('hovData', $hovData);
            if (isset($hovData['hov_start_date'])) {
                $startDate = CRM_Utils_DgwUtils::convertDMJString(date("d-m-Y", strtotime($hovData['hov_start_date'])));
                $startDateField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hov_start_datum", $customGroup['id']);
                $startDateFieldName = $startDateField['column_name'];
                $queryFields[] = $startDateFieldName . " = '$startDate'";
            }

            if (isset($hovData['hov_hoofd_huurder_id'])) {
                $params = array(
                    'contact_id' => $hovData['hov_hoofd_huurder_id'],
                    'return' => "display_name"
                );
                $name = civicrm_api3('Contact', 'Getvalue', $params);
                if (!empty($name)) {
                    $name = CRM_Core_DAO::escapeString($name);
                    $nameField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hoofdhuurder_name", $customGroup['id']);
                    $nameFieldName = $nameField['column_name'];
                    $queryFields[] = $nameFieldName . " = '$name'";
                }
                require_once 'CRM/Utils/DgwUtils.php';
                $persoonsNr = CRM_Utils_DgwUtils::getPersoonsnummerFirst($hovData['hov_hoofd_huurder_id']);
                $persoonsNrField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hoofdhuurder_nr_first", $customGroup['id']);
                $persoonsNrFieldName = $persoonsNrField['column_name'];
                $queryFields[] = $persoonsNrFieldName . " = $persoonsNr";
            }
            if (isset($hovData['hov_mede_huurder_id']) && !empty($hovData['hov_mede_huurder_id'])) {
                $params = array(
                    'contact_id' => $hovData['hov_mede_huurder_id'],
                    'return' => "display_name"
                );
                $name = civicrm_api3('Contact', 'Getvalue', $params);
                if (!empty($name)) {
                    $name = CRM_Core_DAO::escapeString($name);
                    $nameField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("medehuurder_name", $customGroup['id']);
                    $nameFieldName = $nameField['column_name'];
                    $queryFields[] = $nameFieldName . " = '$name'";
                }
                $persoonsNr = CRM_Utils_DgwUtils::getPersoonsnummerFirst($hovData['hov_mede_huurder_id']);
                $persoonsNrField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("medehuurder_nr_first", $customGroup['id']);
                $persoonsNrFieldName = $persoonsNrField['column_name'];
                $queryFields[] = $persoonsNrFieldName . " = $persoonsNr";
            }
            if (isset($expectedEndDate) && !empty($expectedEndDate)) {
                $expectedEndDate = CRM_Utils_DgwUtils::convertDMJString(date("d-m-Y", strtotime($expectedEndDate)));
                $expectedEndDateField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("verwachte_eind_datum", $customGroup['id']);
                $expectedEndDateFieldName = $expectedEndDateField['column_name'];
                $queryFields[] = $expectedEndDateFieldName . " = '$expectedEndDate'";
            }
        }
      if (!empty($queryFields)) {
        $actionQuery = $action . " $customTable SET " . implode(", ", $queryFields);
        if ($action == "UPDATE") {
          $actionQuery .= " WHERE entity_id = $caseId";
        } elseif ($action == "INSERT INTO") {
          if (count($queryFields)) {
            $actionQuery .= ",";
          }
          $actionQuery .= " entity_id = $caseId, $hovIdFieldName = $hovId";
        }
        CRM_Core_DAO::executeQuery($actionQuery);
      }
    }
    /**
     * Function to update custom records when loading property contract
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 18 Mar 2014
     * @param void
     * @return array $result (is_error, can be 1 or 0 and optional error_message)
     * @access public
     */
    public function setHovLoadCustomData() {
        $result = array();
        /*
         * hovId is required
         */
        if (empty($this->hovId)) {
            $result['is_error'] = 1;
            $result['error_message'] = " hovId is empty";
            return $result;
        }
        /*
         * retrieve CaseType id for Huuropzegging
         */
        try {
            $caseTypeApi = civicrm_api3('OptionValue', 'Get', array('option_group_id' => 26));
            if (isset($caseTypeApi['values'])) {
                foreach($caseTypeApi['values'] as $caseType) {
                    if ($caseType['name'] == "Huuropzeggingsdossier") {
                        $caseTypeId = $caseType['value'];
                    }
                }
                if (!$caseTypeId || empty($caseTypeId)) {
                    $result['is_error'] = 1;
                    $result['error message'] = "No case type Huuropzeggingsdossier found";
                    return $result;
                }
            }
        } catch (CiviCRM_API3_Exception $e) {
            $result['is_error'] = 1;
            $result['error_message'] = "Error retrieving caseTypeId for Huuropzeggingsdossier 
                with OptionValue API. Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * retrieve custom group contract that extends case for found case type
         */
        $apiParams = array(
            'name'                          =>  "huur_opzegging",
            'extends'                       =>  "Case",
            'extends_entity_column_value'   =>  $caseTypeId
        );
        try {
            $customGroupApi = civicrm_api3('CustomGroup', 'Getsingle', $apiParams);
            if (isset($customGroupApi['id'])) {
                $customGroupId = $customGroupApi['id'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group huur_opzegging found";
                return $result;
            }
            if (isset($customGroupApi['table_name'])) {
                $customGroupTable = $customGroupApi['table_name'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group table name found";
                return $result;
            }
        } catch (CiviCRM_API3_Exception $e) {
            $result['is_error'] = 1;
            $result['error_message'] = "Error retrieving customGroupId for huur_opzegging with CustomGroup API. 
                Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * read records in custom group where hovId
         */
        $hovIdField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("hov_nr", $customGroupId);
        $hovIdColumn = $hovIdField['column_name'];
        /*
         * get custom field names
         */
        $hovFields = array("hov_start_datum", "mutatie_nr", "hoofdhuurder_nr_first", "hoofdhuurder_name", "medehuurder_nr_first", "medehuurder_name");
        $queryFields = array();
        foreach ($hovFields as $hovField) {
            $customField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName($hovField, $customGroupId);
            $queryFields[$hovField] = $customField['column_name'];
        }
        $customQuery = "SELECT * FROM $customGroupTable WHERE $hovIdColumn = {$this->hovId}";
        $daoHov = CRM_Core_DAO::executeQuery($customQuery);
        while ($daoHov->fetch()) {
            /*
             * add every custom field to array
             */
            $updateFields = array();
            foreach ($queryFields as $label => $fieldName) {
                switch ($label) {
                    case "mutatie_nr":
                        $fieldValue = CRM_Core_DAO::escapeString($this->mutatieId);
                        break;
                    case "hov_start_datum":
                        $fieldValue = CRM_Utils_DgwUtils::convertDMJString(date("d-m-Y", strtotime($this->startDate)));
                        break;
                    case "hoofdhuurder_nr_first":
                        $fieldValue = CRM_Core_DAO::escapeString($this->hoofdHuurderId);
                        break;
                    case "hoofdhuurder_name":
                        $hhPersoonsNr = CRM_Utils_DgwUtils::getPersoonsnummerFirst($this->hoofdHuurderId);
                        if ($hhPersoonsNr == 0) {
                            $fieldValue = "";
                        } else {
                            $hhNameParams = array(
                                'contact_id'    =>  $this->hoofdHuurderId,
                                'return'        =>  'display_name'
                            );
                            try {
                                $fieldValue = civicrm_api3('Contact', 'Getvalue', $hhNameParams);
                            } catch (CiviCRM_API3_Exception $e) {
                                $fieldValue = "";
                            }
                        }
                        break;
                    case "medehuurder_nr_first":
                        $fieldValue = CRM_Core_DAO::escapeString($this->medeHuurderId);
                        break;
                    case "medehuurder_name":
                        $mhPersoonsNr = CRM_Utils_DgwUtils::getPersoonsnummerFirst($this->medeHuurderId);
                        if ($mhPersoonsNr == 0) {
                            $fieldValue = "";
                        } else {
                            $mhNameParams = array(
                                'contact_id'    =>  $this->medeHuurderId,
                                'return'        =>  'display_name'
                            );
                            try {
                                $fieldValue = civicrm_api3('Contact', 'Getvalue', $mhNameParams);
                            } catch (CiviCRM_API3_Exception $e) {
                                $fieldValue = "";
                            }
                        }
                        break;
                }
                $updateFields[] = $fieldName." = '".$fieldValue."'";
            }
            /*
             * if elements in updateFields then update record
             */
            if (!empty($updateFields)) {
                $updateQuery = "UPDATE $customGroupTable SET ".implode(", ", $updateFields)." WHERE id = $daoHov->id";
                CRM_Core_DAO::executeQuery($updateQuery);
            }
        }
        $result['is_error'] = 0;
        return $result;
    }
    /**
     * Function to set id with hovId
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 18 Mar 2014
     * @param int $hovId
     * @return void
     * @access public
     */
    public function setIdWithHovId($hovId) {
        if (!empty($hovId) && is_numeric($hovId)) {
            $query = "SELECT id FROM ".$this->_table." WHERE hov_id = $hovId";
            $dao = CRM_Core_DAO::executeQuery($query);
            if ($dao->fetch()) {
                $this->id = $dao->id;
            }
        }
       
    }
}