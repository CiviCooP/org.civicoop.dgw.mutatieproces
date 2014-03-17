<?php

/**
 * Collection of upgrade steps
 */
class CRM_Mutatieproces_Upgrader extends CRM_Mutatieproces_Upgrader_Base {
    public function install() {
        /**
         * create tables for civicrm_property (VGE), civicrm_property_type and civicrm_property_contract (HOV)
         * during install if they do not exist yet
         */
        if (!CRM_Core_DAO::checkTableExists("civicrm_property_contract")) {
            $this->executeSqlFile('sql/createPropertyContract.sql');
        } else {
            CRM_Core_Session::setStatus("Table civicrm_property_contract already exists, please check if table needs cleaning.", "info");
        }
        if (!CRM_Core_DAO::checkTableExists("civicrm_property_type")) {
            $this->executeSqlFile('sql/createPropertyType.sql');
        } else {
            CRM_Core_Session::setStatus("Table civicrm_property_type already exists, please check if table needs cleaning", "info");
        }
        if (!CRM_Core_DAO::checkTableExists("civicrm_property")) {
            $this->executeSqlFile('sql/createProperty.sql');
        } else {
            CRM_Core_Session::setStatus("Table civicrm_property already exists, please check if table needs cleaning", "info");
        }
    }
    /**
     * Upgrade function to modify column build_year
     * 
     * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
     * @date 17 Mar 2014
     */
   public function upgrade_1001() {
       $this->ctx->log->info('Applying update 1001');
       if (CRM_Core_DAO::checkTableExists('civicrm_propery')) {
           CRM_Core_DAO::executeQuery("ALTER TABLE civicrm_property MODIFY COLUMN build_year CHAR(4)");
       }
       return TRUE;
   }
}