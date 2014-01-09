<?php

require_once 'CRM/Core/Page.php';

class CRM_Mutatieproces_Page_Proptest extends CRM_Core_Page {
  function run() {
    CRM_Utils_System::setTitle(ts('Test retrieveCustomGroupFields function'));
    $fields = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupFields(24);
    CRM_Core_Error::debug("fields", $fields);
    //$this->assign('meldingen', $meldingen);

    parent::run();
  }
}
