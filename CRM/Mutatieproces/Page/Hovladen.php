<?php

require_once 'CRM/Core/Page.php';

class CRM_Mutatieproces_Page_Hovladen extends CRM_Core_Page {
  function run() {
    CRM_Utils_System::setTitle(ts('Laden data uit First Noa'));
    $this->assign('loadHovFile', 'CRM/Mutatieproces/Loadhov/Control.php');
    parent::run();
  }
}
