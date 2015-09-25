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
  /**
   * BOSW1509263 insite - vooropname mail aanhef
   * $medehuurderRelationshipTypeId is called in mutatieproces.php in
   * the function mutatieproces_civicrm_tokenValues, but did not exists
   */
  public $medehuurderRelationshipTypeId = NULL;
    
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
    $this->setMedehuurderRelationshipTypeId();
    
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
   * BOSW1509263 insite - vooropname mail aanhef
   * $medehuurderRelationshipTypeId is called in mutatieproces.php in
   * the function mutatieproces_civicrm_tokenValues, but did not exists
   */
  private function setMedehuurderRelationshipTypeId() {
    $params = array(
      'name_a_b'  =>  'Medehuurder',
      'return'    =>  'id');
    try {
      $this->medehuurderRelationshipTypeId = civicrm_api3('RelationshipType', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $this->medehuurderRelationshipTypeId = 0;
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
