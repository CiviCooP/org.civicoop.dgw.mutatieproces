<?php
/**
 * PropertyContract.Create API
 * Specific De Goede Woning API - create property contract for rental data
 * Originally from project Digitaliseren Mutatieproces, but can also
 * be used in others!
 * 
 * Basic flow: 
 * - Business Object report selects relevant property contract data from First Noa
 *   in csv-file. This is scheduled daily and csv-file is mailed to
 *   standard mailaddress (bestanden@degoedewoning.nl)
 * - ICT De Goede Woning puts all files send to mailadres on CiviCRM server in
 *   path /home/beheerder/first/
 * - API PropertyContract/Loadhov loads the csv file into import table dgw_loadhov
 *   (scheduled daily)
 * - This API creates records in PropertyContract for 5000 imported hov's and deletes
 *   processed records.
 *   (scheduled hourly)
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @date 23 Mar 2014
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception when source file does not exist
 */
function civicrm_api3_property_contract_createhov($params) {
    set_time_limit(0);
    $countPropertyContracts = 0;
    /*
     * read 1000 records from import table dgw_loadhov
     */
    if (CRM_Core_DAO::checkTableExists('dgw_loadhov')) {
        $selectQuery = "SELECT * FROM dgw_loadhov ORDER BY hov_nummer LIMIT 5000";
        $daoImport = CRM_Core_DAO::executeQuery($selectQuery);
        $countPropertyContracts = 0;
        while ($daoImport->fetch()) {
            $countPropertyContracts++;
            $propertyContractHov = new CRM_Mutatieproces_PropertyContract();
            /*
             * set params for create or update query
             */
            $hovParams = array('type' => "h");
            if (isset($daoImport->hov_nummer) && !empty($daoImport->hov_nummer)) {
                $hovParams['hov_id'] = $daoImport->hov_nummer;
            }
            if (isset($daoImport->vge_nummer) && !empty($daoImport->vge_nummer)) {
                $hovParams['hov_vge_id'] = $daoImport->vge_nummer;
            }
            if (isset($daoImport->hov_naam) && !empty($daoImport->hov_naam)) {
                $hovParams['hov_name'] = $daoImport->hov_naam;
            }
            if (isset($daoImport->start_datum) && !empty($daoImport->start_datum)) {
                $hovParams['hov_start_date'] = $daoImport->start_datum;
            }
            if (isset($daoImport->eind_datum) && !empty($daoImport->eind_datum)) {
                $hovParams['hov_end_date'] = $daoImport->eind_datum;
            }
            if (isset($daoImport->mutatie_nummer) && !empty($daoImport->mutatie_nummer)) {
                if (is_numeric($daoImport->mutatie_nummer)) {
                    $hovParams['hov_mutatie_id'] = $daoImport->mutatie_nummer;
                }
            }
            $hoofdHuurderId = $propertyContractHov->getHoofdHuurderId($daoImport->hov_nummer);
            if (!empty($hoofdHuurderId)) {
                $hovParams['hov_hoofd_huurder_id'] = $hoofdHuurderId;
            }
            $medeHuurderId = $propertyContractHov->getMedeHuurderId($daoImport->hov_nummer);
            if (!empty($medeHuurderId)) {
                $hovParams['hov_mede_huurder_id'] = $medeHuurderId;
            }
            /*
             * check if property exists, update if it does and
             * create if it does not
             */
            $hovExists = $propertyContractHov->checkHovIdExists($daoImport->hov_nummer);
            if ($hovExists == TRUE) {
                $propertyContractHov->setIdWithHovId($daoImport->hov_nummer);
                $propertyContractHov->update($hovParams);
            } else {
                $propertyContractHov->create($hovParams);
            }
            /*
             * update all custom fields with latest property values
             */
            $propertyContractHov->setHovLoadCustomData();
            /*
             * delete record from import table
             */
            CRM_Core_DAO::executeQuery("DELETE FROM dgw_loadhov WHERE id = ".$daoImport->id);
        }
        $returnValues[] = "Laden HOV-gegevens succesvol afgerond";
        $returnValues[] = $countPropertyContracts." huurovereenkomsten geladen in tabel civicrm_property_contract";
        return civicrm_api3_create_success($returnValues, $params, 'PropertyContract', 'Loadhov');
    }
}


