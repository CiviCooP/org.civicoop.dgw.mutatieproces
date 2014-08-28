<?php
/*
+--------------------------------------------------------------------+
| Project       :   CiviCRM De Goede Woning - Upgrade CiviCRM 4.3    |
| Author        :   Jaap Jansma (CiviCooP, jaap.jansma@civicoop.org  |
| Date          :   16 April 2013                                    |
| Description   :   Class with DGW helper functions for the          |
|                   custom dgw mutatieproces                         |
+--------------------------------------------------------------------+
*/

/**
*
* @package CRM
* @copyright CiviCRM LLC (c) 2004-2013
* $Id$
*
*/
class CRM_Utils_DgwMutatieprocesUtils {
    /**
     * Function to retrieve a Custom Group by name
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param string $name
     * @return FALSE or retrieved custom group
     */
    public static function retrieveCustomGroupByName($name) {
        $civires2 = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $name));
        if (!civicrm_error($civires2)) {
            return $civires2;
        }
            return false;
    }
    /**
     * Function to retrieve CustomValuees for a contact within a custom group sorted
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param int $contact_id
     * @param int $group_id
     * @return array 
     */
    public static function retrieveCustomValuesForContactAndCustomGroupSorted($contact_id, $group_id) {
        $customValues = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomValuesForContactAndCustomGroup($contact_id, $group_id);
        $fields = array();
        if (isset($customValues['values']) && is_array($customValues['values'])) {
            foreach($customValues['values'] as $values) {
                foreach($values as $key => $v) {
                    if ($key != 'entity_id' && $key != 'id' && $key != 'latest' && $key != 'name') {
                        $fields[$key][$values['name']] = $v;
                    }
                }
            }
        }
    return $fields;
    }
    /**
     * Function to retrieve CustomValues for contact and group
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param int $contact_id
     * @param int $group_id
     * @return array
     */
    public static function retrieveCustomValuesForContactAndCustomGroup($contact_id, $group_id) {
        $return['is_error'] = '0';
        $params = array(
            'sequential' => 1,
            'entity_id' => $contact_id,
            'onlyActiveFields' => '0',
        );
        $values = civicrm_api3('CustomValue', 'Get', $params);
        if (isset($values['is_error']) && $values['is_error'] == '1') {
            return $values;
        }
        $i = 0;
        foreach($values['values'] as $value) {
            $params = array(
                'sequential' => 1,
                'id' => $value['id'],
                'custom_group_id' => $group_id
            );
          try {
            $fields = civicrm_api3('CustomField', 'Getsingle', $params);
            $return['values'][$i] = $value;
            $return['values'][$i]['name'] = $fields['name'];
            $i++;
          } catch (CiviCRM_API3_Exception $ex) {
              
          }
        }
        return $return;
    }
    /**
     * Function to get Persoonsnummer First using the relationship
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param int $huishouden_id
     * @param string $relation
     * @return string
     */
    public static function getPersoonsnummerFirstByRelation($huishouden_id, $relation) {
        $return = false;
        $cid = false;
        $result = civicrm_api3('RelationshipType', 'Getsingle', array('label_b_a'=>$relation));
        if (isset($result['id']) && $result['id']) {
            $params = array(
                'contact_id_b'          =>  $huishouden_id,
                'relationship_type_id'  =>  $result['id']
            );
            $result = civicrm_api3('Relationship', 'Getsingle', $params);
            if (isset($result['id']) && $result['id']) {
                $cid = $result['contact_id_a'];
            }	
        }
	if ($cid) {
            $pers_nr_field = CRM_Utils_DgwApiUtils::retrieveCustomFieldByName('Persoonsnummer_First');
            unset($params);
            $params = array(
                'contact_id'                            =>  $cid,
                'return.custom_'.$pers_nr_field['id']   =>  1
            );
            $result = civicrm_api3('Contact', 'Getsingle', $params);
            if (isset($result['id']) && $result['id']) {
                return $result['custom_'.$pers_nr_field['id']];
            }
        }
	return FALSE;
    }
    /**
     * Function to get ContactId using Relation
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param int $huishouden_id
     * @param int $relation
     * @return int
     */
    public static function getContactIdByRelation($huishouden_id, $relation) {
        $return = false;
        $cid = false;
        $result = civicrm_api3('RelationshipType', 'Getsingle', array('label_b_a'=>$relation));
        if (isset($result['id']) && $result['id']) {
            $params = array(
                'contact_id_b'          =>  $huishouden_id,
                'relationship_type_id'  =>  $result['id']
            );
            $result = civicrm_api3('Relationship', 'Getsingle', $params);
            if (isset($result['id']) && $result['id']) {
                $cid = $result['contact_id_a'];
            }
        }
        return $cid;
    }
    /**
     * Function to retrieve Custom Field by Name
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param string $name
     * @param int $gid
     * @return array
     */
    public static function retrieveCustomFieldByName($name, $gid=false) {
        $params['name'] = $name;
        if ($gid) {
            $params['custom_group_id'] = $gid;
        }
        $civires2 = civicrm_api3('CustomField', 'Getsingle', $params);
        if (!civicrm_error($civires2)) {
            return $civires2;
        }
        return false;
    }
    /**
     * Function to create a tag
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param string $name
     * @return int $id
     */
    public static function createTag($name) {
        $result = civicrm_api3('Tag', 'Getsingle', array('name' => $name));
        if (isset($result['id']) && $result['id']) {
            return $result['id'];
        }
        $result = civicrm_api3('Tag', 'Create', array('name' => $name));
        if (isset($result['id']) && $result['id']) {
            return $result['id'];
        }
        return false;
    }
    /**
     * Function to add a tag to a contact
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param int $tag_id
     * @param int $contact_id
     * @return array
     */
    public static function addTag($tag_id, $contact_id) {
        $params = array(
            'contact_id' => $contact_id,
            'tag_id' => $tag_id
        );
        return civicrm_api3('EntityTag', 'Create', $params);
    }
    /**
     * Function to retrieve all active huurovereenkomsten for 
     * contact (household or organization)
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param int $contact_id
     * @param string $contact_type (default Household)
     * @return int $count_hovs
     */
    public static function countActiveHovs($contact_id, $contact_type = "Household") {
        $count_hovs = 0;
        if (empty($contact_id) || empty($contact_type)) {
            return $count_hovs;
        }
        if ($contact_type == "Household") {
            $hov_custom_group = self::retrieveCustomGroupByName("Huurovereenkomst (huishouden)");
        }
        if ($contact_type == "Organization") {
            $hov_custom_group = self::retrieveCustomGroupByName("Huurovereenkomst (organisatie)");
        }
        if (!civicrm_error($hov_custom_group)) {
            $custom_table = $hov_custom_group['table_name'];
            $gid = $hov_custom_group['id'];
            $query = "SELECT * FROM $custom_table WHERE entity_id = $contact_id";
            $dao = CRM_Core_DAO::executeQuery($query);
            while ($dao->fetch()) {
                if ($contact_type == "Household") {
                    $end_date_field_array = self::retrieveCustomFieldByName('Einddatum_HOV', $gid);
                } else {
                    $end_date_field_array = self::retrieveCustomFieldByName('einddatum_overeenkomst', $gid);
                }
                if (!civicrm_error($end_date_field_array)) {
                    $end_date_field = $end_date_field_array['column_name'];
                    $end_date = date("Ymd", strtotime($dao->$end_date_field));
                    $now_date = date("Ymd");
                    /*
                     * count if end date after today or empty (19700101)
                     */
                    if ($end_date > $now_date || $end_date == "19700101") {
                        $count_hovs++;
                    }
                }
            }
        }
        return $count_hovs;
    }
    /**
     * Function to check if there is a case of type huuropzegging for contact
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param type $contact_id
     * @return boolean
     */
    public static function checkOpzeggingCase($contact_id) {
        if (empty($contact_id) || !is_numeric($contact_id)) {
            return FALSE;
        }
        $case_type_api = civicrm_api3('OptionValue', 'Get', array('option_group_id' => 26));
        if (isset($case_type_api['values'])) {
            foreach($case_type_api['values'] as $case_type) {
                if ($case_type['name'] == "Huuropzeggingsdossier") {
                    $case_type_id = $case_type['value'];
                }
            }
            if (!$case_type_id || empty($case_type_id)) {
                return FALSE;
            }
        } else {
            return FALSE;
        }
        /*
         * get all cases for contact as API does not take case_type_id into consideration
         */
        try {
            $cases = civicrm_api3('Case', 'Get', array('contact_id' => $contact_id));
            if (isset($cases['count']) && $cases['count'] == 0) {
                return FALSE;
            } else {
                foreach ($cases['values'] as $case) {
                    if ($case['is_deleted'] == 0 && $case['status_id'] != 2 && $case['case_type_id'] == $case_type_id) {
                        return TRUE;
                    }
                }
            }
        } catch(CiviCRM_API3_Exception $e) {
            return FALSE;
        }
    }
    /**
     * Function to check if the button Hov Opzeggen should be available for 
     * contact (type required to determine)
     * True will be returned if
     *   - contact_type = Organization and Organization has at least one active
     *     huurovereenkomst that does not have an associated case huuropzeggingsdossier
     *   - contact_type = Household and Household has at least one active huurovereenkomst
     *     that does not have an associated case huuropzeggingsdossier
     *   - contact_type = Individual and Individual is an active hoofdhuurder and
     *     related household has at least one active huurovereenkomst that does not
     *     have an associated case huuropzeggingsdossier * 
     * 
     * @author Erik Hommel (erik.hommel@civicoop.org)
     * @date 20 Jan 2014
     * @param int $contact_id
     * @param string $contact_type
     * @return TRUE or FALSE
     * @access public
     * @static
     */
    public static function checkHovOpzeggen($contact_id, $contact_type) {
        if (empty($contact_id) || empty($contact_type)) {
            return FALSE;
        }
        $opzeggen = FALSE;
        /*
         * further processing based on contact_type
         */
        switch($contact_type) {
            /*
             * if individual, first check if individual is active hoofdhuurder
             */
            case "Individual": 
                $hoofd_huurder = CRM_Utils_DgwUtils::checkContactHoofdHuurder($contact_id);
                if ($hoofd_huurder == FALSE) {
                    $opzeggen = FALSE;
                } else {
                    /*
                     * retrieve active huishouden(s)
                     */
                    $huis_houdens = CRM_Utils_DgwUtils::getHuishoudens($contact_id, "relatie hoofdhuurder", TRUE);
                    if (empty($huis_houdens)) {
                        $opzeggen = FALSE;
                    } else {
                        foreach ($huis_houdens as $huis_houden) {
                            $count_huishouden_hovs = self::countActiveHovs($huis_houden['huishouden_id'], "Household");
                            if ($count_huishouden_hovs == 0) {
                                $opzeggen = FALSE;
                            } else {
                                /*
                                 * check if there is a opzeggingscase for the contact
                                 */
                                $opzeggings_case = self::checkOpzeggingCase($huis_houden['huishouden_id']);
                                if ($opzeggings_case == TRUE) {
                                    $opzeggen = FALSE;
                                } else {
                                    $opzeggen = TRUE;
                                }
                            }
                        }
                    }
                }
                break;
            case "Household":
                /*
                 * retrieve active huishouden(s)
                 */
                $count_huishouden_hovs = self::countActiveHovs($contact_id, $contact_type);
                if ($count_huishouden_hovs == 0) {
                    $opzeggen = FALSE;
                } else {
                    /*
                     * check if there is a opzeggingscase for the contact
                     */
                    $opzeggings_case = self::checkOpzeggingCase($contact_id);
                    if ($opzeggings_case == TRUE) {
                        $opzeggen = FALSE;
                    } else {
                        $opzeggen = TRUE;
                    }
                }
                break;
            case "Organization":
                break;
        }
        return $opzeggen;
    }
}
