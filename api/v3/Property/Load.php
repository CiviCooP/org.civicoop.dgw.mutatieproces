<?php
/**
 * Property.Load API
 * Specific De Goede Woning API - load property data
 * Originally from project Digitaliseren Mutatieproces, but can also
 * be used in others!
 * 
 * Basic flow: 
 * - Business Object report selects relevant property data from First Noa
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
function civicrm_api3_property_load($params) {
    set_time_limit(0);
    $countProperties = 0;
    /*
     * retrieve path from dgw_config table
     */
    $sourceFile = CRM_Utils_DgwUtils::getDgwConfigValue("kov bestandsnaam")."property.csv";
    if (!file_exists($sourceFile)) {
        throw new API_Exception("Bronbestand $sourceFile niet gevonden, laden VGE-gegevens mislukt");
    } else {
        /*
         * read all records from the source file, expecting csv format
         */
        $sf = fopen($sourceFile, "r");
        while (!feof($sf)) {
            $sourceData = fgetcsv($sf, 0, ";");
            /*
             * ignore berging
             */
            if ($sourceData[11] != "Berging") {
                $countProperties++;
                $property = new CRM_Mutatieproces_Property();
                /*
                 * check if property exists, update if it does and
                 * create if it does not
                 */
                $propertyExists = $property->checkVgeIdExists($sourceData[0]);
                if ($propertyExists == TRUE) {
                    $property->setIdWithVgeId($sourceData[0]);
                    $property->update($sourceData);
                } else {
                    $property->create($sourceData);
                }
                /*
                 * update all custom fields with latest property values
                 */
                $property->setLoadingCustomFields();
            }
        }
        fclose($sf);
        /*
         * remove sourceFile
         */
        unlink($sourceFile);
        $returnValues[] = "Laden VGE-gegevens succesvol afgerond";
        $returnValues[] = $countProperties." VGEs geladen in tabel civicrm_property";
        return civicrm_api3_create_success($returnValues, $params, 'Property', 'Load');
    }
}

