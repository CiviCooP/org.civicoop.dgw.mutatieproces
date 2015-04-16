<?php
/**
 * Utils for mutatie organisaties
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Mutatieproces_MutOrgUtils {

  /**
   * Function to get custom group if exists
   * @param string $customGroupName
   * @return array
   * @access public
   * @static
   */
  public static function getCustomGroup($customGroupName) {
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $customGroupName));
      return $customGroup;
    } catch (CiviCRM_API3_Exception $ex) {
      return array();
    }
  }

  /**
   * Function to get custom field if exists
   *
   * @param int $customGroupId
   * @param string $customFieldName
   * @return array
   * @access public
   * @static
   */
  public static function getCustomField($customGroupId, $customFieldName) {
    $params = array(
      'custom_group_id' => $customGroupId,
      'name' => $customFieldName
    );
    try {
      $customField = civicrm_api3('CustomField', 'Getsingle', $params);
      return $customField;
    } catch (CiviCRM_API3_Exception $ex) {
      return array();
    }
  }

  /**
   * Function to create custom group
   *
   * @param string $customGroupName
   * @param string $customGroupTable
   * @param string $extends
   * @param string $style
   * @param int $isMultiple
   * @return array $returnGroup
   * @throws Exception when error from API CustomGroup Create
   * @access public
   * @static
   */
  public static function createCustomGroup($customGroupName, $customGroupTable, $extends, $style='Inline', $isMultiple = 0) {
    $params = array(
      'name' => $customGroupName,
      'title' => self::createLabelFromName($customGroupName),
      'extends' => $extends,
      'is_active' => 1,
      'is_reserved' => 1,
      'table_name' => $customGroupTable,
      'style' => $style,
      'is_multiple' => $isMultiple
    );
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Create', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception(ts('Could not create custom group '.$customGroupName
          .', error from API CustomGroup Create: ').$ex->getMessage());
    }
    foreach ($customGroup['values'] as $customGroupId => $returnGroup) {
      return $returnGroup;
    }
  }

  /**
   * Function to create custom field
   *
   * @param int $customGroupId
   * @param string $name
   * @param string $column
   * @param string $dataType
   * @param string $htmlType
   * @param int $isSearchable
   * @param int $isSearchRange
   * @param int $startDateYears
   * @param int $endDateYears
   * @param string $dateFormat
   * @return array $returnField
   * @throws Exception when error from API CustomField Create
   * @access public
   * @static
   */
  public static function createCustomField($customGroupId, $name, $column, $dataType, $htmlType, $isSearchable,
                                    $isSearchRange = 0, $startDateYears = 10, $endDateYears = 10, $dateFormat = 'dd-mmyy') {

    $params = array(
      'custom_group_id' => $customGroupId,
      'name' => $name,
      'label' => self::createLabelFromName($name),
      'data_type' => $dataType,
      'html_type' => $htmlType,
      'column_name' => $column,
      'is_active' => 1,
      'is_reserved' => 1,
      'is_searchable' => $isSearchable,
      'is_search_range' => $isSearchRange
    );
    if ($dataType == 'Date') {
      $params['start_date_years'] = $startDateYears;
      $params['end_date_years'] = $endDateYears;
      $params['date_format'] = $dateFormat;
    }
    try {
      $customField = civicrm_api3('CustomField', 'Create', $params);
    } catch (CiviCRM_API3_Explorer $ex) {
      throw new Exception(ts('Could not create custom field '.$name
          .' in custom group id '.$customGroupId.', error from API CustomField Create: ').$ex->getMessage());
    }
    foreach ($customField['values'] as $customFieldId => $returnField) {
      return $returnField;
    }
  }

  /**
   * Method to check if contact is an organization
   *
   * @param int $contactId
   * @return bool
   * @access public
   * @static
   */
  public static function isOrganization($contactId) {
    $params = array(
      'id' => $contactId,
      'return' => 'contact_type');
    try {
      $contactType = civicrm_api('Contact', 'Getvalue', $params);
      if ($contactType == 'Organization') {
        return TRUE;
      } else {
        return FALSE;
      }
    } catch (CiviCRM_API3_Exception $ex) {
      return FALSE;
    }
  }
}