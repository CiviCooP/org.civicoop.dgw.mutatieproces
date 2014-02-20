<?php
/**
 * @copyright Copyright (C) 2013 - CiviCooP (http://www.civicoop.org)
 * @license Licensed to CiviCRM and De Goede Woning under the Academic Free License version 3.0.
 *
 * @author Erik Hommel (erik.hommel@civicoop.org)
 * @date 17 Dec 2014
 * 
 * Page to select source file for contracts ('huurovereenkomsten')
 * and start loading process
 */
require_once 'CRM/Core/Page.php';

class CRM_Mutatieproces_Page_Hovladen extends CRM_Core_Page {
  function run() {
    CRM_Utils_System::setTitle('Laden data uit First Noa');
    $this->assign('loadHovFile', 'CRM/Mutatieproces/Page/Hovcomplete.php');
    parent::run();
  }
}
