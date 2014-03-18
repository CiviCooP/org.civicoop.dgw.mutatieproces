<?php
/**
 * PropertyContract.Loadhov API
 * Specific De Goede Woning API - load property contract for rental data
 * Originally from project Digitaliseren Mutatieproces, but can also
 * be used in others!
 * 
 * Basic flow: 
 * - Business Object report selects relevant property contract data from First Noa
 *   in csv-file. This is scheduled daily and csv-file is mailed to
 *   standard mailaddress (bestanden@degoedewoning.nl)
 * - ICT De Goede Woning puts all files send to mailadres on CiviCRM server in
 *   path /home/beheerder/first/
 * - API checks if file exists, reads and processes records and deletes file
 *   when processing is done
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 17 Mar 2014
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception when source file does not exist
 */
function civicrm_api3_property_contract_loadhov($params) {
    set_time_limit(0);
    $countPropertyContracts = 0;
    /*
     * retrieve path from dgw_config table
     */
    $sourceFile = CRM_Utils_DgwUtils::getDgwConfigValue("kov bestandsnaam")."contracthov.csv";
    if (!file_exists($sourceFile)) {
        throw new API_Exception("Bronbestand $sourceFile niet gevonden, laden HOV-gegevens mislukt");
    } else {
        /*
         * read all records from the source file, expecting csv format
         */
        $sf = fopen($sourceFile, "r");
        while (!feof($sf)) {
            $sourceData = fgetcsv($sf, 0, ";");
            $countPropertyContracts++;
            $propertyContractHov = new CRM_Mutatieproces_PropertyContract();
            /*
             * set type to huurovereenkomst
             */
            $sourceData[14] = "h";
            /*
             * check if property exists, update if it does and
             * create if it does not
             */
            $hovExists = $propertyContractHov->checkHovIdExists($sourceData[2]);
            if ($hovExists == TRUE) {
                $propertyContractHov->setIdWithHovId($sourceData[2]);
                $propertyContractHov->update($sourceData);
            } else {
                $propertyContractHov->create($sourceData);
            }
            /*
             * update all custom fields with latest property values
             */
            $propertyContractHov->setHovLoadCustomData();
        }
        fclose($sf);
        /*
         * remove sourceFile
         */
        unlink($sourceFile);
        $returnValues[] = "Laden HOV-gegevens succesvol afgerond";
        $returnValues[] = $countPropertyContracts." huurovereenkomsten geladen in tabel civicrm_property_contract";
        return civicrm_api3_create_success($returnValues, $params, 'PropertyContract', 'Loadhov');
    }
}


