<?php

/**
 * Class configuration singleton
 * 
 * @client De Goede Woning (http://www.degoedewoning.nl)
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 12 May 2014
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A. <http://www.civicoop.org>
 * Licensed to De Goede Woning <http://www.degoedewoning.nl> and CiviCRM under AGPL-3.0
 */
class CRM_Mutatieproces_Config {
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
    
  public $hoofdhuurderRelationshipTypeId = NULL;
    
  /*
   * array with case types that are available for M&B reporting
   */
  public $validCaseTypes = array();
  
  private $vooropname_activity_type;
  
  private $eindopname_activity_type;

  /**
   * Constructor function
   */
  function __construct() {
    $this->setHoofdhuurderRelationshipTypeId();
    
    $this->vooropname_activity_type = CRM_Core_OptionGroup::getValue('activity_type',
      'adviesgesprek_huuropzegging',
      $labelField = 'name',
      $labelType  = 'String',
      $valueField = 'value');
    $this->eindopname_activity_type = CRM_Core_OptionGroup::getValue('activity_type',
      'eindgesprek_huuropzegging',
      $labelField = 'name',
      $labelType  = 'String',
      $valueField = 'value');
  }
    
  private function setHoofdhuurderRelationshiptypeId() {
    $params = array(
      'name_a_b'  =>  'Hoofdhuurder',
      'return'    =>  'id');
    try {
      $this->hoofdhuurderRelationshipTypeId = civicrm_api3('RelationshipType', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->hoofdhuurderRelationshipTypeId = 0;
    }
  }
  
  /**
   * Function to return singleton object
   * 
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Mutatieproces_Config();
    }
    return self::$_singleton;
  }
  
  public function getVoorOpnameActivityType() {
    return $this->vooropname_activity_type;
  }
  
  public function getEindOpnameActivityType() {
    return $this->eindopname_activity_type;
  }
}
