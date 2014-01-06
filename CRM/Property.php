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
class Property {
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
     * @return $property object with data of created or updated property
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
}

?>
