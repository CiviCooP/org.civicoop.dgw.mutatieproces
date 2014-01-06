<?php

require_once 'CRM/Core/Page.php';
require_once 'CRM/Property.php';

class CRM_Mutatieproces_Page_Proptest extends CRM_Core_Page {
  function run() {
    CRM_Utils_System::setTitle(ts('Test Property Class'));
    $property = new Property();
    $params = array(
        'vge_id'            =>  1015,
        'complex_id'        =>  1300,
        'subcomplex'        =>  "SC1300A",
        'vge_street_name'   =>  "Ambachtstraat"
    );
    $property->create($params);
   
    //$this->assign('meldingen', $melding);

    parent::run();
  }
}
