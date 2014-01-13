<?php
/**
 * @copyright Copyright (C) 2013 - CiviCooP (http://www.civicoop.org)
 * @license Licensed to CiviCRM and De Goede Woning under the Academic Free License version 3.0.
 *
 * @author Erik Hommel (erik.hommel@civicoop.org)
 * @date 13 Jan 2014
 * 
 * Page to select source file for property ('eenheden' or 'vge')
 * and start loading process
 */
require_once 'CRM/Core/Page.php';
class CRM_Mutatieproces_Page_Vgeladen extends CRM_Core_Page {
    function run() {
        CRM_Utils_System::setTitle('Laden eenheden uit First Noa');
        $this->assign('loadVgeFile', 'CRM/Mutatieproces/Page/Vgecomplete.php');
        parent::run();
    }
}
