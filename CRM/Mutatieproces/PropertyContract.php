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
        
    }
}