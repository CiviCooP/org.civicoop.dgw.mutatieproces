<?php
require_once 'CRM/Core/Page.php';

class _Page_Hovcomplete extends CRM_Core_Page {
    function run() {
        CRM_Utils_System::setTitle("Laden data uit First Noa");
        /**
         * check if source file has been passed in POST and is not empty
         */
        if (!isset($_POST['source_file_hov']) || empty($_POST['source_file_hov'])) {
            $this->assign('endMessage', "Laden mislukt, geen bronbestand voor huurovereenkomsten geselecteerd");
        }
        $source_file_hov = $_POST['source_file_hov'];
        /*
         * read all records from the source file, expecting csv format
         */
        $sf = fopen($source_file_hov, "r");
        while (!feof($sf)) {
            
        }
        fclose($sf);
        
        parent::run();
    }
}
