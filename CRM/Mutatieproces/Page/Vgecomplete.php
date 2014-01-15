<?php

require_once 'CRM/Core/Page.php';

class CRM_Mutatieproces_Page_Vgecomplete extends CRM_Core_Page {
    private $_source_file = "";
    private $_source_data = array();
    
    function run() {
        require_once 'CRM/Mutatieproces/Property.php';
        CRM_Utils_System::setTitle("Laden eenheden uit First Noa");
        /**
         * check if source file has been passed in POST and is not empty
         */
        if (!isset($_POST['source_file_vge']) || empty($_POST['source_file_vge'])) {
            $this->assign('endMessage', "Laden mislukt, geen bronbestand voor eenheden geselecteerd");
        }
        $this->_source_file = "/home/erik/".$_POST['source_file_vge'];
        /*
         * read all records from the source file, expecting csv format
         */
        $sf = fopen($this->_source_file, "r");
        while (!feof($sf)) {
            $this->_source_data = fgetcsv($sf, 0, ";");
            /*
             * ignore berging
             */
            if ($this->_source_data[11] != "Berging") {
                $property = new CRM_Mutatieproces_Property();
                /*
                 * check if property exists, update if it does and
                 * create if it does not
                 */
                $property_exists = $property->checkVgeIdExists($this->_source_data[0]);
                if ($property_exists == TRUE) {
                    $property->setIdWithVgeId($this->_source_data[0]);
                    $property->update($this->_source_data);
                } else {
                    $property->create($this->_source_data);
                }
                /*
                 * update all custom fields with latest property values
                 */
                $property->setHuuropzeggingCustomFields();
            }
        }
        fclose($sf);
        parent::run();
    }
}
