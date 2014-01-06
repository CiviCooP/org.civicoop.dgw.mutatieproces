<?php

require_once 'CRM/Core/Page.php';

class CRM_Mutatieproces_Page_Proptest extends CRM_Core_Page {
  function run() {
    CRM_Utils_System::setTitle(ts('Test Property Class'));
    require_once 'CRM/Mutatieproces/Property.php';
    $meldingen = CRM_Mutatieproces_Property::getByVgeId(1015);
    CRM_Core_Error::debug("meldingen", $meldingen);
    //$this->assign('meldingen', $meldingen);

    parent::run();
  }
}
