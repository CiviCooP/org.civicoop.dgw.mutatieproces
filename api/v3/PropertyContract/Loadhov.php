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
 * - This API PropertyContract loads the csv file into import table dgw_loadhov
 *   (scheduled daily)
 * - API PropertyContract Createhov creates records in PropertyContract for 5000 
 *   imported hov's and deletes processed records.
 *   (scheduled hourly)
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
    if (!CRM_Core_DAO::checkTableExists('dgw_loadhov')) {
        $createLoadTable = 
        "CREATE TABLE `dgw_loadhov` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `persoon_nummer` varchar(25) DEFAULT NULL,
          `vge_nummer` varchar(25) DEFAULT NULL,
          `hov_nummer` varchar(25) DEFAULT NULL,
          `hov_naam` varchar(128) DEFAULT NULL,
          `start_datum` varchar(25) DEFAULT NULL,
          `eind_datum` varchar(25) DEFAULT NULL,
          `verwachte_eind_datum` varchar(25) DEFAULT NULL,
          `mutatie_nummer` varchar(25) DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `id_UNIQUE` (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8";
        CRM_Core_DAO::executeQuery($createLoadTable);
    } else {
        CRM_Core_DAO::executeQuery("TRUNCATE TABLE dgw_loadhov");
    }
    $sourceFile = CRM_Utils_DgwUtils::getDgwConfigValue("kov bestandsnaam")."contracthov.csv";

    if (!file_exists($sourceFile)) {
        throw new API_Exception("Bronbestand $sourceFile niet gevonden, laden HOV-gegevens mislukt");
    } else {
        $csvSeparator = _check_separator($sourceFile);
        /*
         * read all records from the source file, expecting csv format
         */
        $hovCount = 0;
        $sf = fopen($sourceFile, "r", $csvSeparator);
        while (!feof($sf)) {
            $sourceData = fgetcsv($sf, 0, ",");
            $insertFields = setInsertFields($sourceData);
            if (!empty($insertFields)) {
                $insertQry = "INSERT INTO dgw_loadhov SET ".implode(", ", $insertFields);
                CRM_Core_DAO::executeQuery($insertQry);
                $hovCount++;
            }
        }
        fclose($sf);
        $returnValues = array();
        $returnValues[] = "Laden huurovereenkomsten succesvol afgerond";
        $returnValues[] = $hovCount." huurovereenkomsten geladen";
    }
    return civicrm_api3_create_success($returnValues, $params, 'PropertyContract', 'Loadeh');    
}
/**
 * Function to set the insert fields
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 22 Mar 2014
 * @param array $sourceData
 * @return array $insertFields
 * @access public
 */
function setInsertFields($sourceData) {
    if (isset($sourceData[0])&& !empty($sourceData[0])) {
        $insertFields[] = "persoon_nummer = '{$sourceData[0]}'";
    }
    if (isset($sourceData[1]) && !empty($sourceData[1])) {
        $insertFields[] = "vge_nummer = '{$sourceData[1]}'";
    }
    if (isset($sourceData[2]) && !empty($sourceData[2])) {
        $insertFields[] = "hov_nummer = '{$sourceData[2]}'";
    }
    if (isset($sourceData[7]) && !empty($sourceData[7])) {
        $hovNaam = CRM_Core_DAO::escapeString($sourceData[7]);
        $insertFields[] = "hov_naam = '$hovNaam'";
    }
    if (isset($sourceData[10]) && !empty($sourceData[10])) {
        $insertFields[] = "start_datum = '{$sourceData[10]}'";
    }
    if (isset($sourceData[11]) && !empty($sourceData[11])) {
        $insertFields[] = "eind_datum = '{$sourceData[11]}'";
    }
    if (isset($sourceData[12]) && !empty($sourceData[12])) {
        $insertFields[] = "verwachte_eind_datum = '{$sourceData[12]}'";
    }
    if (isset($sourceData[13]) && !empty($sourceData[13])) {
        $insertFields[] = "mutatie_nummer = '{$sourceData[13]}'";
    }
    return $insertFields;
}
/**
 * Function to check which csv separator to use. Assumption is that
 * separator is ';', if reading first record return record with only 
 * 1 field, then ',' should be used
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 22 Apr 2014
 */
function _check_separator($sourceFile) {
  $testSeparator = fopen($sourceFile, 'r');
  /*
   * first test if semi-colon or comma separated, based on assumption that
   * it is semi-colon and it should be comma if I only get one record then
   */
  if ($testRow = fgetcsv($testSeparator, 0, ';')) {
    if (!isset($testRow[1])) {
      $csvSeparator = ",";
    } else {
      $csvSeparator = ";";
    }
  }
  fclose($testSeparator);
  return $csvSeparator;  
}
