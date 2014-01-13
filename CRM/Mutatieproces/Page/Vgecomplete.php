<?php

require_once 'CRM/Core/Page.php';

class _Page_Vgecomplete extends CRM_Core_Page {
    private $_source_file = "";
    private $_source_data = array();
    
    function run() {
        CRM_Utils_System::setTitle("Laden eenheden uit First Noa");
        /**
         * check if source file has been passed in POST and is not empty
         */
        if (!isset($_POST['source_file_vge']) || empty($_POST['source_file_vge'])) {
            $this->assign('endMessage', "Laden mislukt, geen bronbestand voor eenheden geselecteerd");
        }
        $this->_source_file = $_POST['source_file_vge'];
        CRM_Core_Error::debug("vge source file", $this->_source_file);
        /*
         * read all records from the source file, expecting csv format
         */
        $temp_index = 0;
        $sf = fopen($this->_source_file, "r");
        while (!feof($sf)) {
            $this->_source_data = fgetc($sf);
            if ($temp_index < 3) {
                CRM_Core_Error::debug("source data", $this->_source_data);           
                $temp_index++;
            }
        }
        fclose($sf);
        parent::run();
    }
}
