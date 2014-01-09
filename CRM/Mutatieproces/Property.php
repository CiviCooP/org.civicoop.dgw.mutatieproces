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
    private $_type_table = "";
    public $id = 0;
    public $vge_id = 0;
    public $complex_id = 0;
    public $subcomplex = "";
    public $vge_street_name = "";
    public $vge_street_number = 0;
    public $vge_street_unit = "";
    public $vge_postal_code = "";
    public $vge_city = "";
    public $vge_country_id = 0;
    public $vge_address_id = 0;
    public $epa_label = "";
    public $epa_pre = "";
    public $city_region = "";
    public $block = "";
    public $vge_type_id = 0;
    public $strategy_label = "";
    public $strategy_b_pnts = 0;
    public $strategy_c_pnts = 0;
    public $number_rooms = 0;
    public $outside_code = "";
    public $stairs = 0;
    public $square_mtrs = 0;
    /**
     * constructor
     */
    function __construct() {
        $this->_table = 'civicrm_property';
        $this->_type_table = 'civicrm_property_type';
    }
    /**
     * function to add a property
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param array $params Array with parameters for field values (expecting field names as elements)
     */
    function create($params) {
        /*
         * required parameters are vge_id, vge_street, vge_street_number and vge_city
         */
        $required_params = array("vge_id", "vge_street_name", "vge_street_number", "vge_city");
        foreach ($required_params as $required_param) {
            if (!isset($params[$required_param])) {
                throw new Exception("Required parameter ".$required_param." not found in parameter array.");
            }
        }

        $query_fields = $this->_setPropertyFields($params);
        if (!empty($query_fields)) {
            $query = "INSERT INTO ".$this->_table." SET ".implode(", ", $query_fields);
            CRM_Core_DAO::executeQuery($query);
        }
        $latest_query = "SELECT MAX(id) AS max_id FROM ".$this->_table;
        $dao_latest = CRM_Core_DAO::executeQuery($latest_query);
        if ($dao_latest->fetch()) {
            if (isset($dao_latest->max_id)) {
                $this->id = $dao_latest->max_id;
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
     */
    function update($params) {
        /*
         * required parameters are id, vge_id, vge_street, vge_street_number and vge_city
         */
        $required_params = array("id", "vge_id", "vge_street_name", "vge_street_number", "vge_city");
        foreach ($required_params as $required_param) {
            if (!isset($params[$required_param])) {
                throw new Exception("Required parameter ".$required_param." not found in parameter array.");
            }
        }
        /*
         * id has to be integer
         */
        if (empty($params['id']) || !is_integer($params['id'])) {
            throw new Exception("Id can not be empty and has to be an integer");
        }
        $this->id = $params['id'];

        $query_fields = $this->_setPropertyFields($params);
        if (!empty($query_fields)) {
            $query = "UPDATE ".$this->_table." SET ".implode(", ", $query_fields)." WHERE id = {$this->id}";
            CRM_Core_DAO::executeQuery($query);
        }
    }
    /**
     * static function to retrieve property with vge_id
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param integer $vge_id
     * @return array $result
     */
    static function getByVgeId($vge_id) {
        $result = array();
        
        if (empty($vge_id) || !is_integer($vge_id)) {
            $result['is_error'] = 1;
            $result['error_message'] = "Vge_id empty or not an integer";
            return $result;
        }
        
        $query = "SELECT * FROM civicrm_property WHERE vge_id = $vge_id";
        $dao_property = CRM_Core_DAO::executeQuery($query);
        if ($dao_property->fetch()) {
            $result = self::_propertyToArray($dao_property);
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
     */
    private function _setPropertyFields($params) {
        $result = array();
        
        if (isset($params['vge_id'])) {
            if (!is_integer($params['vge_id'])) {
                throw new Exception("Parameter vge_id has to be numeric");
            }
            $this->vge_id = $params['vge_id'];
            $result[] = "vge_id = {$this->vge_id}";
        }
        
        if (isset($params['complex_id'])) {
            if (!is_integer($params['complex_id'])) {
                throw new Exception("Parameter complex_id has to be numeric");
            }
            $this->complex_id = $params['complex_id'];
            $result[] = "complex_id = {$this->complex_id}";
        }
        
        if (isset($params['subcomplex'])) {
            $this->subcomplex = CRM_Core_DAO::escapeString($params['subcomplex']);
            $result[] = "subcomplex = '{$this->subcomplex}'";
        }
        
        if (isset($params['vge_street_name'])) {
            $this->vge_street_name = CRM_Core_DAO::escapeString($params['vge_street_name']);
            $result[] = "vge_street_name = '{$this->vge_street_name}'";
        }

        if (isset($params['vge_street_number'])) {
            if (!is_integer($params['vge_street_number'])) {
                throw new Exception("Parameter vge_street_number has to be numeric");
            }
            $this->vge_street_number = $params['vge_street_number'];
            $result[] = "vge_street_number = {$this->vge_street_number}";
        }
        
        if (isset($params['vge_street_unit'])) {
            $this->vge_street_unit = CRM_Core_DAO::escapeString($params['vge_street_unit']);
            $result[] = "vge_street_unit = '{$this->vge_street_unit}'";
        }
        
        if (isset($params['vge_postal_code'])) {
            $this->vge_postal_code = self::formatPostalCode($params['vge_postal_code']);
            $result[] = "vge_postal_code = '{$this->vge_postal_code}'";
        }
        
        if (isset($params['vge_city'])) {
            $this->vge_city = CRM_Core_DAO::escapeString($params['vge_city']);
            $result[] = "vge_city = '{$this->vge_city}'";
        }
        
        if (isset($params['vge_country_id'])) {
            if (!self::validCountryId($params['vge_country_id'])) {
                throw new Exception("Vge_country_id {$params['vge_country_id']} is not valid");
            }
            $this->vge_country_id = $params['vge_country_id'];
            $result[] = "vge_country_id = {$this->vge_country_id}";
        }
        
        if (isset($params['vge_address_id'])) {
            if (!self::validAddressId($params['vge_address_id'])) {
                throw new Exception("Vge_address_id {$params['vge_address_id']} is not valid");
            }
            $this->vge_address_id = $params['vge_address_id'];
            $result[] = "vge_address_id = {$this->vge_address_id}";
        }
        
        if (isset($params['epa_label'])) {
            $this->epa_label = CRM_Core_DAO::escapeString($params['epa_label']);
            $result[] = "epa_label = '{$this->epa_label}'";
        }
        
        if (isset($params['epa_pre'])) {
            $this->epa_pre = CRM_Core_DAO::escapeString($params['epa_pre']);
            $result[] = "epa_pre = '{$this->epa_pre}'";
        }
        
        if (isset($params['city_region'])) {
            $this->city_region = CRM_Core_DAO::escapeString($params['city_region']);
            $result[] = "city_region = '{$this->city_region}'";
        }
        
        if (isset($params['block'])) {
            $this->block = CRM_Core_DAO::escapeString($params['block']);
            $result[] = "block = '{$this->block}'";
        }
        
        if (isset($params['vge_type_id'])) {
            if (!$this->validTypeId($params['vge_type_id'])) {
                throw new Exception("Vge_type_id {$params['vge_type_id']} is not valid");
            }
            $this->vge_type_id = $params['vge_type_id'];
            $result[] = "vge_type_id = {$this->vge_type_id}";
        }

        if(isset($params['strategy_label'])) {
            $this->strategy_label = CRM_Core_DAO::escapeString($params['strategy_label']);
            
            $result[] = "strategy_label = '{$this->strategy_label}'";
        }
        
         if (isset($params['strategy_b_pnts'])) {
            if (!is_integer($params['strategy_b_pnts'])) {
                throw new Exception("Parameter strategy_b_pnts has to be numeric");
            }
            $this->strategy_b_pnts = $params['strategy_b_pnts'];
            $result[] = "strategy_b_pnts = {$this->strategy_b_pnts}";
        }
        
        if (isset($params['strategy_c_pnts'])) {
            if (!is_integer($params['strategy_c_pnts'])) {
                throw new Exception("Parameter strategy_c_pnts has to be numeric");
            }
            $this->strategy_c_pnts = $params['strategy_c_pnts'];
            $result[] = "strategy_c_pnts = {$this->strategy_c_pnts}";
        }

         if (isset($params['number_rooms'])) {
            if (!is_integer($params['number_rooms'])) {
                throw new Exception("Parameter number_rooms has to be numeric");
            }
            $this->number_rooms = $params['number_rooms'];
            $result[] = "number_rooms = {$this->number_rooms}";
        }

        if (isset($params['outside_code'])) {
            $this->outside_code = CRM_Core_DAO::escapeString($params['outside_code']);
            $result[] = "outside_code = '{$this->outside_code}'";
        }
        
        if (isset($params['stairs'])) {
            if ($params['stairs'] != 0 && $params['stairs'] != 1) {
                throw new Exception("Parameter stairs can only be 0 or 1");
            }
            $this->stairs = $params['stairs'];
            $result[] = "stairs = '{$this->stairs}'";
        }
        
        if (isset($params['square_mtrs'])) {
            if (!is_numeric($params['square_mtrs'])) {
                throw new Exception("Parameter square_mtrs has to be numeric");
            }
            $this->square_mtrs = $params['square_mtrs'];
            $result[] = "square_mtrs = {$this->square_mtrs}";
        }
       
        return $result;
    }
    /**
     * static function to format the postal code 1234 AA
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param string $postal_code
     * @return string $formatted_postal_code
     */
    static function formatPostalCode($postal_code) {
        $formatted_postal_code = $postal_code;
        return $formatted_postal_code;
    }
    /**
     * static function to check if the address_id exists in CiviCRM
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org
     * @date 6 Jan 2014
     * @param type $address_id
     * @return boolean true or false
     */
    static function validAddressId($address_id) {
        if (!is_integer($address_id)) {
            return FALSE;
        }
        try {
            $api_address = civicrm_api3('Address', 'Getcount', array('id' => $address_id));
            $count_address = $api_address;
        } catch (CiviCRM_API3_Exception $e) {
            $count_address = 0;
        }
        if ($count_address == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * static function to check if the country_id exists in CiviCRM
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org
     * @date 6 Jan 2014
     * @param type $country_id
     * @return boolean true or false
     */
    static function validCountryId($country_id) {
        if (!is_integer($country_id)) {
            return FALSE;
        }
        try {
            $api_country = civicrm_api3('Country', 'Getcount', array('id' => $country_id));
            $count_country = $api_country;
        } catch (CiviCRM_API3_Exception $e) {
            $count_country = 0;
        }
        if ($count_country == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * static function to check if the vge_type_id exists
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org
     * @date 6 Jan 2014
     * @param type $vge_type_id
     * @return boolean true or false
     */
    private function validTypeId($vge_type_id) {
        if (!is_integer($vge_type_id)) {
            return FALSE;
        }
        $count_query = "SELECT COUNT(*) AS type_count FROM ".$this->_type_table." WHERE id = $vge_type_id";
        $count_type = 0;
        $dao_type = CRM_Core_DAO::executeQuery($count_query);
        if ($dao_type->fetch()) {
            if (isset($dao_type->type_count)) {
                $count_type = $dao_type->type_count;
            }
        }
        if ($count_type == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * private function to store dao property into array
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 6 Jan 2014
     * @param object $property
     * @return array $result
     */
    static function _propertyToArray($property) {
        $result = array();
        $property_fields = get_object_vars($property);
        foreach ($property_fields as $field_name => $field_value) {
            if (substr($field_name, 0, 1) != "_" && $field_name != "N") {
                $result[$field_name] = $field_value;
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
     * @param array $params
     * @return array $result (is_error, can be 1 or 0 and optional error_message)
     */
    public function setHuuropzeggingCustomFields() {
        $result = array();
        /*
         * vge_id is required
         */
        if (empty($this->vge_id)) {
            $result['is_error'] = 1;
            $result['error_message'] = " vge_id is empty";
            return $result;
        }
        /*
         * retrieve CaseType id for Huuropzegging
         */
        try {
            $case_type_api = civicrm_api3('OptionValue', 'Get', array('option_group_id' => 26));
            if (isset($case_type_api['values'])) {
                foreach($case_type_api as $case_type) {
                    if ($case_type['name'] == "Dossier Huuropzegging") {
                        $case_type_id = $case_type['value'];
                    }
                }
                if (!$case_type_id || empty($case_type_id)) {
                    $result['is_error'] = 1;
                    $result['error message'] = "No case type Dossier Huuropzegging found";
                    return $result;
                }
            }
        } catch (CiviCRM_API3_Exception $e) {
            $result['is_error'] = 1;
            $result['error_message'] = "Error retrieving case_type_id for Dossier 
                Huuropzegging with OptionValue API. Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * retrieve custom group vge that extends case for found case type
         */
        $api_params = array(
            'name'                          =>  "vge",
            'extends'                       =>  "Case",
            'extends_entity_column_value'   =>  $case_type_id
        );
        try {
            $custom_group_api = civicrm_api3('CustomGroup', 'Getsingle', $api_params);
            if (isset($custom_group_api['id'])) {
                $custom_group_id = $custom_group_api['id'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group vge found";
                return $result;
            }
            if (isset($custom_group_api['table_name'])) {
                $custom_group_table = $custom_group_api['table_name'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group table name found";
                return $result;
            }
        } catch (CiviCRM_API3_Exception $e) {
            $result['is_error'] = 1;
            $result['error_message'] = "Error retrieving custom_group_id for vge with CustomGroup API. 
                Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * read records in custom group where entity_id = vge_id
         */
        $custom_query = "SELECT * FROM $custom_group_table WHERE entity_id = {$this->vge_id}";
        $dao_vge = CRM_Core_DAO::executeQuery($custom_query);
        while ($dao_vge->fetch()) {
            /*
             * add every custom field to array
             */
            $update_fields = array();
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("complex_id", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $complex_id = CRM_Core_DAO::escapeString($this->complex_id);
                $update_fields[] = $custom_field['column_name']." = '$complex_id'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_straat", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_straat = CRM_Core_DAO::escapeString($this->vge_street_name);
                $update_fields[] = $custom_field['column_name']." = '$vge_straat'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_huis_nr", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $update_fields[] = $custom_field['column_name']." = '".$this->vge_street_number."'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_suffix", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_suffix = CRM_Core_DAO::escapeString($this->vge_street_unit);
                $update_fields[] = $custom_field['column_name']." = '$vge_suffix'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_adres", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_adres = CRM_Core_DAO::escapeString($this->_formatVgeAdres());
                $update_fields[] = $custom_field['column_name']." = '$vge_adres'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_postcode", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_postcode = CRM_Core_DAO::escapeString($this->vge_postal_code);
                $update_fields[] = $custom_field['column_name']." = '$vge_postcode'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_plaats", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $vge_plaats = CRM_Core_DAO::escapeString($this->vge_city);
                $update_fields[] = $custom_field['column_name']." = '$vge_plaats'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("vge_nr", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $update_fields[] = $custom_field['column_name']." = '".$this->vge_id."'";
            }
            /*
             * if elements in update_fields then update record
             */
            if (!empty($update_fields)) {
                $update_query = "UPDATE $custom_group_table SET ".implode(", ".$update_fields)." WHERE id = $dao_vge->id";
                CRM_Core_DAO::executeQuery($update_query);
            }
        }       
        /*
         * retrieve custom group woningwaardering that extends case for found case type
         */
        $api_params = array(
            'name'                          =>  "woningwaardering",
            'extends'                       =>  "Case",
            'extends_entity_column_value'   =>  $case_type_id
        );
        try {
            $custom_group_api = civicrm_api3('CustomGroup', 'Getsingle', $api_params);
            if (isset($custom_group_api['id'])) {
                $custom_group_id = $custom_group_api['id'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group woningwaardering found";
                return $result;
            }
            if (isset($custom_group_api['table_name'])) {
                $custom_group_table = $custom_group_api['table_name'];
            } else {
                $result['is_error'] = 1;
                $result['error_message'] = "No custom group table name found";
                return $result;
            }
        } catch (CiviCRM_API3_Exception $e) {
            $result['is_error'] = 1;
            $result['error_message'] = "Error retrieving custom_group_id for woningwaardering with CustomGroup API. 
                Error returned from API : ".$e->getMessage();
            return $result;
        }
        /*
         * read records in custom group where entity_id = vge_id
         */
        $custom_query = "SELECT * FROM $custom_group_table WHERE entity_id = {$this->vge_id}";
        $dao_woningwaardering = CRM_Core_DAO::executeQuery($custom_query);
        while ($dao_woningwaardering->fetch()) {
            /*
             * add every custom field to array
             */
            $update_fields = array();
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("epa_label_opzegging", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $epa_label = CRM_Core_DAO::escapeString($this->epa_label);
                $update_fields[] = $custom_field['column_name']." = '$epa_label'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("epa_pre_opzegging", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $epa_pre = CRM_Core_DAO::escapeString($this->epa_pre);
                $update_fields[] = $custom_field['column_name']." = '$epa_pre'";
            }
            $custom_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("woningoppervlakte", $custom_group_id);
            if (!civicrm_error($custom_field)) {
                $update_fields[] = $custom_field['column_name']." = '".$this->square_mtrs."'";
            }
            /*
             * if elements in update_fields then update record
             */
            if (!empty($update_fields)) {
                $update_query = "UPDATE $custom_group_table SET ".implode(", ".$update_fields)." WHERE id = $dao_woningwaardering->id";
                CRM_Core_DAO::executeQuery($update_query);
            }
        }       
    }
    /**
     * private function to glue formatted address
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 9 Jan 2014
     * @return string $result
     */
    private function _formatVgeAdres() {
        $formatted_address = array();
        if (!empty($this->vge_street_name)) {
            $formatted_address[] = $this->vge_street_name;
        }
        if (!empty($this->vge_street_number)) {
            $formatted_address[] = $this->vge_street_number;
        }
        if (!empty($this->vge_street_unit)) {
            $formatted_address = $this->vge_street_unit;
        }
        $result = implode(" ", $formatted_address);
        if (!empty($this->vge_postal_code)) {
            $result .= ", ".$this->vge_postal_code;
            if (!empty($this->vge_city)) {
                $result .= " ".$this->vge_city;
            }
        } else {
            if (!empty($this->vge_city)) {
                $result .= ", ".$this->vge_city;
            }
        }
        return $result;
    }
}

?>
