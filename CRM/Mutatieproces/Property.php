<?php

/**
 * Class Property for dealing with properties (De Goede Woning)
 * 
 * @author Erik Hommel (erik.hommel@civicoop.org, http://www.civicoop.org)
 * @date 6 Jan 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A.
 * Licensed to De Goede Woning under the Academic Free License version 3.0.
 */
class CRM_Mutatieproces_Property {
    private $_table = "";
    private $_typeTable = "";
    public $id = 0;
    public $vgeId = 0;
    public $complexId = 0;
    public $subComplex = "";
    public $vgeStreetName = "";
    public $vgeStreetNumber = 0;
    public $vgeStreetUnit = "";
    public $vgePostalCode = "";
    public $vgeCity = "";
    public $vgeCountryId = 0;
    public $vgeAddressId = 0;
    public $epaLabel = "";
    public $epaPre = "";
    public $cityRegion = "";
    public $block = "";
    public $vgeTypeId = 0;
    public $strategyLabel = "";
    public $strategyBPnts = 0;
    public $strategyCPnts = 0;
    public $numberRooms = 0;
    public $outsideCode = "";
    public $buildYear = 0;
    public $stairs = 0;
    public $squareMtrs = "";
    /**
     * constructor
     */
    function __construct() {
        $this->_table = 'civicrm_property';
        $this->_typeTable = 'civicrm_property_type';
    }
    /**
     * function to add a property
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param array $params Array with parameters for field values (expecting field names as elements)
     * @access public
     */
    public function create($params) {
        $queryFields = $this->_setPropertyFields($params);
        if (!empty($queryFields)) {
            $query = "INSERT INTO ".$this->_table." SET ".implode(", ", $queryFields);
            CRM_Core_DAO::executeQuery($query);
        }
        $latestQuery = "SELECT MAX(id) AS max_id FROM ".$this->_table;
        $daoLatest = CRM_Core_DAO::executeQuery($latestQuery);
        if ($daoLatest->fetch()) {
            if (isset($daoLatest->max_id)) {
                $this->id = $daoLatest->max_id;
            }
        }        
    }
    /**
     * function to update a property
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param array $params Array with parameters for field values (expecting field names as elements)
     * @return $property object with data of created or updated property
     * @access public
     */
    public function update($params) {
        $queryFields = $this->_setPropertyFields($params);
        if (!empty($queryFields)) {
            $query = "UPDATE ".$this->_table." SET ".implode(", ", $queryFields)." WHERE id = {$this->id}";
            
            CRM_Core_DAO::executeQuery($query);
        }
    }
    /**
     * static function to retrieve property with vge_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param int $vgeId
     * @return array $result
     * @access public
     * @static
     */
    public static function getByVgeId($vgeId) {
        $result = array();

        if (empty($vgeId) || !is_numeric($vgeId)) {
            $result['is_error'] = 1;
            $result['error_message'] = "VgeId empty or not an integer";
            return $result;
        }
        
        $query = "SELECT * FROM civicrm_property WHERE vge_id = $vgeId";
        $daoProperty = CRM_Core_DAO::executeQuery($query);
        if ($daoProperty->fetch()) {
            $result = self::_propertyToArray($daoProperty);
        } else {
            $result['count'] = 0;
        }
        return $result;
    }
    /**
     * function to set fields based on incoming params
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * 
     * @param array $params expecting fields
     * @return array $result with fields
     * @access private
     */
    private function _setPropertyFields($params) {
        $result = array();
        
        if (isset($params[0])) {
            $this->vgeId = $params[0];
            $result[] = "vge_id = {$this->vgeId}";
        }
        
        if (isset($params[1])) {
            $this->complexId = CRM_Core_DAO::escapeString($params[1]);
            $result[] = "complex_id = '{$this->complexId}'";
        }
              
        if (isset($params[5])) {
            $this->vgeStreetName = CRM_Core_DAO::escapeString($params[5]);
            $result[] = "vge_street_name = '{$this->vgeStreetName}'";
        }

        if (isset($params[6])) {
            $this->vgeStreetNumber = $params[6];
            $result[] = "vge_street_number = {$this->vgeStreetNumber}";
        }
        
        if (isset($params[8])) {
            $this->vgeStreetUnit = CRM_Core_DAO::escapeString($params[8]);
            $result[] = "vge_street_unit = '{$this->vgeStreetUnit}'";
        }
        
        if (isset($params[9])) {
            $this->vgePostalCode = CRM_Core_DAO::escapeString($params[9]);
            $result[] = "vge_postal_code = '{$this->vgePostalCode}'";
        }
        
        if (isset($params[10])) {
            $this->vgeCity = CRM_Core_DAO::escapeString($params[10]);
            $result[] = "vge_city = '{$this->vgeCity}'";
        }
        
        $this->vgeCountryId = 1152;
        $result[] = "vge_country_id = {$this->vgeCountryId}";
        
        if (isset($params[13])) {
            $this->epaLabel = CRM_Core_DAO::escapeString($params[13]);
            $result[] = "epa_label = '{$this->epaLabel}'";
        }
        
        if (isset($params[14])) {
            $this->epaPre = CRM_Core_DAO::escapeString($params[14]);
            $result[] = "epa_pre = '{$this->epaPre}'";
        }
        
        if (isset($params[2])) {
            $this->cityRegion = CRM_Core_DAO::escapeString($params[2]);
            $result[] = "city_region = '{$this->cityRegion}'";
        }
        
        if (isset($params[3])) {
            $this->block = CRM_Core_DAO::escapeString($params[3]);
            $result[] = "block = '{$this->block}'";
        }
        
        if (isset($params[11]) && !empty($params[11])) {
            $typeExists = $this->_getPropertyTypeId($params[11]);
            if ($typeExists == FALSE) {
                $this->_createPropertyType($params[11]);
            }
            $result[] = "vge_type_id = {$this->vgeTypeId}";
        }
        
        if (isset($params[4])) {
            $this->squareMtrs = CRM_Core_DAO::escapeString($params[4]);
            $result[] = "square_mtrs = '{$this->squareMtrs}'";
        }
        
        if (isset($params[12])) {
            if (is_numeric($params[12])) {
                $this->buildYear = $params[12];
            }
            $result[] = "build_year = '{$this->buildYear}'";
        }
        return $result;
    }
    /**
     * function to check if the address_id exists in CiviCRM
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org
     * @date 6 Jan 2014
     * @param type $addressId
     * @return boolean true or false
     * @access public
     * @static
     */
    public static function validAddressId($addressId) {
        if (!is_integer($addressId)) {
            return FALSE;
        }
        try {
            $apiAddress = civicrm_api3('Address', 'Getcount', array('id' => $addressId));
            $countAddress = $apiAddress;
        } catch (CiviCRM_API3_Exception $e) {
            $countAddress = 0;
        }
        if ($countAddress == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * function to check if the country_id exists in CiviCRM
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org
     * @date 6 Jan 2014
     * @param type $countryId
     * @return boolean true or false
     * @access public
     * @static
     */
    public static function validCountryId($countryId) {
        if (!is_integer($countryId)) {
            return FALSE;
        }
        try {
            $apiCountry = civicrm_api3('Country', 'Getcount', array('id' => $countryId));
            $countCountry = $apiCountry;
        } catch (CiviCRM_API3_Exception $e) {
            $countCountry = 0;
        }
        if ($countCountry == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * function to set the vgeTypeId based on name
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org
     * @date 15 Jan 2014
     * @param string $vgeType
     * @return boolean TRUE/FALSE
     * @access private
     */
    private function _getPropertyTypeId($vgeType) {
        if (empty($vgeType)) {
            return FALSE;
        }
        $query = "SELECT id FROM ".$this->_typeTable." WHERE label = '$vgeType'";
        $dao = CRM_Core_DAO::executeQuery($query);
        if ($dao->fetch()) {
            $this->vgeTypeId = $dao->id;
        }
        return TRUE;
    }
    /**
     * function to store dao property into array
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param object $property
     * @return array $result
     * @access private
     * @static
     */
    private static function _propertyToArray($property) {
        $result = array();
        $propertyFields = get_object_vars($property);
        foreach ($propertyFields as $fieldName => $fieldValue) {
            if (substr($fieldName, 0, 1) != "_" && $fieldName != "N") {
                $result[$fieldName] = $fieldValue;
            }
        }
        return $result;
    }
    /**
     * Function to store property data in custom fields for
     * all cases huuropzegging en mutatie
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 9 Jan 2014
     * @return array $result (is_error, can be 1 or 0 and optional error_message)
     * @access public
     */
    public function setLoadingCustomFields() {
        $result = array();
        /*
         * vge_id is required
         */
        if (empty($this->vgeId)) {
            $result['is_error'] = 1;
            $result['error_message'] = " vge_id is empty";
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
            $result['error_message'] = "Error retrieving case_type_id for Huuropzeggingsdossier 
                with OptionValue API. Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * retrieve custom group vge that extends case for found case type
         */
        $apiParams = array(
            'name'                          =>  "vge",
            'extends'                       =>  "Case",
            'extends_entity_column_value'   =>  $caseTypeId
        );
        try {
            $customGroupApi = civicrm_api3('CustomGroup', 'Getsingle', $apiParams);
            if (isset($customGroupApi['id'])) {
                $customGroupId = $customGroupApi['id'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group vge found";
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
            $result['error_message'] = "Error retrieving customGroupId for vge with CustomGroup API. 
                Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * read records in custom group where entity_id = vge_id
         */
        $vgeIdField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_nr", $customGroupId);
        $vgeIdColumn = $vgeIdField['column_name'];
        /*
         * get custom field names
         */
        $vgeFields = array("complex_nr", "vge_straat", "vge_huis_nr", "vge_suffix", "vge_adres", "vge_postcode", "vge_plaats");
        $queryFields = array();
        foreach ($vgeFields as $vgeField) {
            $customField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName($vgeField, $customGroupId);
            $queryFields[$vgeField] = $customField['column_name'];
        }
        
        $customQuery = "SELECT * FROM $customGroupTable WHERE $vgeIdColumn = {$this->vgeId}";
        $daoVge = CRM_Core_DAO::executeQuery($customQuery);
        while ($daoVge->fetch()) {
            /*
             * add every custom field to array
             */
            $caseId = $daoVge->entity_id;
            $updateFields = array();
            $updateFields[] = $vgeIdColumn." = '".$this->vgeId."'";
            foreach ($queryFields as $label => $fieldName) {
                switch ($label) {
                    case "complex_nr":
                        $fieldValue = CRM_Core_DAO::escapeString($this->complexId);
                        break;
                    case "vge_straat":
                        $fieldValue = CRM_Core_DAO::escapeString($this->vgeStreetName);
                        break;
                    case "vge_huis_nr":
                        $fieldValue = $this->vgeStreetNumber;
                        break;
                    case "vge_street_unit":
                        $fieldValue = CRM_Core_DAO::escapeString($this->vgeStreetUnit);
                        break;
                    case "vge_adres":
                        $params = array(
                            'street_name'   => $this->vgeStreetName,
                            'street_number' => $this->vgeStreetNumber,
                            'street_unit'   => $this->vgeStreetUnit,
                            'postal_code'   => $this->vgePostalCode,
                            'city'          => $this->vgeCity
                        );
                        $address = CRM_Utils_DgwUtils::formatVgeAdres($params);
                        $fieldValue = CRM_Core_DAO::escapeString($address);
                        break;
                    case "vge_postcode":
                        $fieldValue = CRM_Core_DAO::escapeString($this->vgePostalCode);
                        break;
                    case "vge_plaats":
                        $fieldValue = CRM_Core_DAO::escapeString($this->vgeCity);
                        break;
                }
                $updateFields[] = $fieldName." = '".$fieldValue."'";
            }
            /*
             * if elements in updateFields then update record
             */
            if (!empty($updateFields)) {
                $updateQuery = "UPDATE $customGroupTable SET ".implode(", ", $updateFields)." WHERE id = $daoVge->id";
                CRM_Core_DAO::executeQuery($updateQuery);
            }
        }
        /*
         * if case_id found for vge_data, retrieve custom group woningwaardering that extends case 
         * for found case type
         */
        if ($caseId) {
            $apiParams = array(
                'name'                          =>  "woningwaardering",
                'extends'                       =>  "Case",
                'extends_entity_column_value'   =>  $caseTypeId
            );
            try {
                $customGroupApi = civicrm_api3('CustomGroup', 'Getsingle', $apiParams);
                if (isset($customGroupApi['id'])) {
                    $customGroupId = $customGroupApi['id'];
                } else {
                    $result['is_error'] = 1;
                    $result['error_message'] = "No custom group woningwaardering found";
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
                $result['error_message'] = "Error retrieving customGroupId for woningwaardering with CustomGroup API. 
                    Error returned from API : ".$e->getMessage();
                return $result;
            }
            /*
             * get custom field names
             */
            $wwFields = array("epa_label_opzegging", "epa_pre_opzegging", "woningoppervlakte");
            $queryFields = array();
            foreach ($wwFields as $wwField) {
                $customField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName($wwField, $customGroupId);
                $queryFields[$wwField] = $customField['column_name'];
            }
            /*
             * read records in custom group where entity_id = vge_id
             */
            $customQuery = "SELECT * FROM $customGroupTable WHERE entity_id = $caseId";
            $daoWoningwaardering = CRM_Core_DAO::executeQuery($customQuery);
            while ($daoWoningwaardering->fetch()) {
                foreach ($queryFields as $label => $fieldName) {
                    /*
                     * add every custom field to array
                     */
                    $updateFields = array();
                    switch ($label) {
                        case "epa_label_opzegging":
                            $fieldValue = CRM_Core_DAO::escapeString($this->epaLabel);
                            break;
                        case "epa_pre_opzegging":
                            $fieldValue = CRM_Core_DAO::escapeString($this->epaPre);
                            break;
                        case "woningoppervlakte":
                            $fieldValue = $this->squareMtrs;
                            break;
                    }
                    $updateFields[] = $fieldName." = '".$fieldValue."'";
                }
                /*
                 * if elements in update_fields then update record
                 */
                if (!empty($updateFields)) {
                    $updateQuery = "UPDATE $customGroupTable SET ".implode(", ", $updateFields)." WHERE id = $daoWoningwaardering->id";
                    CRM_Core_DAO::executeQuery($updateQuery);
                }
            }
            $result['is_error'] = 0;
        }       
    }
    /**
     * function to create a property type
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 15 Jan 2014
     * 
     * @param string $vgeType
     * @return boolean TRUE/FALSE
     * @access private
     */
    private function _createPropertyType($vgeType) {
        if (empty($vgeType)) {
            return FALSE;
        }
        $query = "INSERT INTO ".$this->_typeTable." SET label = '$vgeType'";
        CRM_Core_DAO::executeQuery($query);
        $latestQuery = "SELECT MAX(id) AS max_id FROM ".$this->_typeTable;        
        $daoLatest = CRM_Core_DAO::executeQuery($latestQuery);
        if ($daoLatest->fetch()) {
            if (isset($daoLatest->max_id)) {
                $this->_vge_type_id = $daoLatest->max_id;
            }
        }
        return TRUE;
    }
    /**
     * function to check if there is a property with the vge_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 15 Jan 2014
     * @param integer $vge_id
     * @return TRUE or FALSE
     * @access public
     */
    public function checkVgeIdExists($vgeId) {
        if (empty($vgeId) || !is_numeric($vgeId)) {
            return FALSE;
        }
        $query = "SELECT COUNT(*), id AS count_property FROM ".$this->_table." WHERE vge_id = $vgeId";
        $dao = CRM_Core_DAO::executeQuery($query);
        if ($dao->fetch()) {
            if ($dao->count_property > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }
    /**
     * function to set the id of the property based on vge_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 15 Jan 2014
     * @param integer $vge_id
     * @access public
     */
    public function setIdWithVgeId($vgeId) {
        if (!empty($vgeId) && is_numeric($vgeId)) {
            $query = "SELECT id FROM ".$this->_table." WHERE vge_id = $vgeId";
            $dao = CRM_Core_DAO::executeQuery($query);
            if ($dao->fetch()) {
                $this->id = $dao->id;
            }
        }
    }
    /**
     * Function to set the custom fields for a vge
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 27 Jan 2014
     * @param type $vge_id
     * @param type $case_id
     * @return void
     * @access public
     * @static
     */
    public static function setVgeFieldsCase($vgeId, $caseId) {
        /*
         * end if vge_id, case_id empty or non-numeric
         */
        if (empty($vgeId) || !is_numeric($vgeId) || empty($caseId) || !is_numeric($caseId)) {
            return;
        }
        /*
         * retrieve custom group for vge
         */
        $customGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName('vge');
        $customTable = $customGroup['table_name'];
        $vgeIdField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('vge_nr', $customGroup['id']);
        $vgeIdFieldName = $vgeIdField['column_name'];
        /*
         * check if already record for case and set action update or insert
         */
        $query_vge = "SELECT COUNT(*) AS count_vge  FROM $customTable WHERE entity_id = $caseId AND $vgeIdFieldName = $vgeId";
        $dao_vge = CRM_Core_DAO::executeQuery($query_vge);
        if ($dao_vge->fetch()) {
          if ($dao_vge->count_vge == 0) {
            $action = "INSERT INTO";
          } else {
            $action = "UPDATE";
            
          }
        }
        $fields = array();
        /*
         * retrieve vge_data
         */
        $vgeData = self::getByVgeId($vgeId);
        if (isset($vgeData['complex_id'])) {
            $complexId = CRM_Core_DAO::escapeString($vgeData['complex_id']);
            $complexIdField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("complex_nr", $customGroup['id']);
            $complexIdFieldName = $complexIdField['column_name'];
            $fields[] = $complexIdFieldName." = '$complexId'";
        }
        if (isset($vgeData['vge_street_name'])) {
            $streetName = CRM_Core_DAO::escapeString($vgeData['vge_street_name']);
            $streetNameField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_straat", $customGroup['id']);
            $streetNameFieldName = $streetNameField['column_name'];
            $fields[] = $streetNameFieldName." = '$streetName'";
            $formatAddressParams['street_name'] = $vgeData['vge_street_name'];
        }
        if (isset($vgeData['vge_street_number'])) {
            $streetNumberField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_huis_nr", $customGroup['id']);
            $streetNumberFieldName = $streetNumberField['column_name'];
            $fields[] = $streetNumberFieldName." = '{$vgeData['vge_street_number']}'";
            $formatAddressParams['street_number'] = $vgeData['vge_street_number'];
        }
        if (isset($vgeData['vge_street_unit'])) {
            $streetUnit = CRM_Core_DAO::escapeString($vgeData['vge_street_unit']);
            $streetUnitField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_suffix", $customGroup['id']);
            $streetUnitFieldName = $streetUnitField['column_name'];
            $fields[] = $streetUnitFieldName." = '$streetUnit'";
            $formatAddressParams['street_unit'] = $vgeData['vge_street_unit'];
        }
        if (isset($vgeData['vge_postal_code'])) {
            $postalCode = CRM_Core_DAO::escapeString($vgeData['vge_postal_code']);
            $postalCodeField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_postcode", $customGroup['id']);
            $postalCodeFieldName = $postalCodeField['column_name'];
            $fields[] = $postalCodeFieldName." = '$postalCode'";
            $formatAddressParams['postal_code'] = $vgeData['vge_postal_code'];
        }
        if (isset($vgeData['vge_city'])) {
            $city = CRM_Core_DAO::escapeString($vgeData['vge_city']);
            $cityField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_plaats", $customGroup['id']);
            $cityFieldName = $cityField['column_name'];
            $fields[] = $cityFieldName." = '$city'";
            $formatAddressParams['city'] = $vgeData['vge_city'];
        }
        if (!empty($formatAddressParams)) {
            $address = CRM_Core_DAO::escapeString(CRM_Utils_DgwUtils::formatVgeAdres($formatAddressParams));
            $addressField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_adres", $customGroup['id']);
            $addressFieldName = $addressField['column_name'];
            $fields[] = $addressFieldName." = '$address'";
        }
        
        $actionQueryVge = $action." $customTable SET ".implode(", ", $fields);
        if ($action == "UPDATE") {
          $actionQueryVge .= " WHERE entity_id = $caseId";
        } elseif ($action == "INSERT INTO") {
           if (count($fields)) {
            $actionQueryVge .= ",";
          }
          $actionQueryVge .= " entity_id = $caseId, $vgeIdFieldName = $vgeId";
        }
        CRM_Core_DAO::executeQuery($actionQueryVge);
        /*
         * retrieve custom group for woningwaardering
         */
        $customGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName('woningwaardering');
        $customTable = $customGroup['table_name'];
        /*
         * check if already record for case and set action update or insert
         */
        $queryWw = "SELECT COUNT(*) AS count_ww  FROM $customTable WHERE entity_id = $caseId";
        $daoWW = CRM_Core_DAO::executeQuery($queryWw);
        if ($daoWW->fetch()) {
          if ($daoWW->count_ww == 0) {
            $action = "INSERT INTO";
          } else {
            $action = "UPDATE";
            
          }
        }
        $fields = array();
        if (isset($vgeData['epa_label'])) {
            $epaLabel = CRM_Core_DAO::escapeString($vgeData['epa_label']);
            $epaLabelField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("epa_label_opzegging", $customGroup['id']);
            $epaLabelFieldName = $epaLabelField['column_name'];
            $fields[] = $epaLabelFieldName." = '$epaLabel'";
        }
        if (isset($vgeData['epa_pre'])) {
            $epaPre = CRM_Core_DAO::escapeString($vgeData['epa_pre']);
            $epaPreField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("epa_pre_opzegging", $customGroup['id']);
            $epaPreFieldName = $epaPreField['column_name'];
            $fields[] = $epaPreFieldName." = '$epaPre'";
        }
        if (isset($vgeData['square_mtrs'])) {
            $squareMtrs = CRM_Core_DAO::escapeString($vgeData['square_mtrs']);
            $squareMtrsField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("woningoppervlakte", $customGroup['id']);
            $squareMtrsFieldName = $squareMtrsField['column_name'];
            $fields[] = $squareMtrsFieldName." = '$squareMtrs'";
        }
        $actionQueryWw = $action." $customTable SET ".implode(", ", $fields);
        if ($action == "UPDATE") {
          $actionQueryWw .= " WHERE entity_id = $caseId";
        } elseif ($action == "INSERT INTO") {
           if (count($fields)) {
            $actionQueryWw .= ",";
          }
          $actionQueryWw .= " entity_id = $caseId";
        }
        CRM_Core_DAO::executeQuery($actionQueryWw);
        
    }
}
