<?php

class CRM_Mutatieproces_Config {
  
  private static $singleton;
  
  private $vooropname_activity_type;
  
  private $eindopname_activity_type;
  
  private function __construct() {
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
  
  /**
   * @return CRM_Mutatieproces_Config
   */
  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Mutatieproces_Config();
    }
    return self::$singleton;
  }
  
  public function getVoorOpnameActivityType() {
    return $this->vooropname_activity_type;
  }
  
  public function getEindOpnameActivityType() {
    return $this->eindopname_activity_type;
  }
}

