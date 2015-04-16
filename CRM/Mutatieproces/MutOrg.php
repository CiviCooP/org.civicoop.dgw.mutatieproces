<?php
/**
 * Class for Mutatieproces Organisaties De Goede Woning
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Mutatieproces_MutOrg {
  /**
   * Method to check if the organization has active 'huurovereenkomst'
   *
   * @param int $orgId
   * @return bool
   * @access public
   * @static
   */
  public static function hasActiveHov($orgId) {
    if (self::countActiveHovs($orgId) > 0) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Method to count the number of active hovs for an org
   * active is when einddatum is empty or <= today
   * and begindatum is empty or >= today
   *
   * @param int $orgId
   * @return int $countHov
   * @access public
   * @static
   */
  public static function countActiveHovs($orgId) {
    $countHov = 0;
    if (empty($orgId)) {
      return $countHov;
    }
    $orgHovs = self::getOrgHov($orgId);
    foreach ($orgHovs as $hovId => $hovData) {
      if (self::isActiveHov($hovData['begindatum_overeenkomst'], $hovData['einddatum_overeenkomst']) == TRUE
        && self::hasOpzeggingsDossier($hovId) == FALSE) {
        $countHov++;
      }
    }
    return $countHov;
  }

  /**
   * Method to check if beginDatum and eindDatum construct an active range
   *
   * @param string $beginDatum
   * @param string $eindDatum
   * @return bool $isActive
   * @access public
   * @static
   */

  public static function isActiveHov($beginDatum, $eindDatum) {
    $isActive = FALSE;
    if (empty($beginDatum) && empty($eindDatum)) {
      $isActive = TRUE;
    } else {
      if (empty($eindDatum)) {
        if (date('Ymd', strtotime($beginDatum)) <= date('Ymd')) {
          $isActive = TRUE;
        }
      } else {
        if (date('Ymd', strtotime($beginDatum)) <= date('Ymd') && date('Ymd', strtotime($eindDatum)) >= date('Ymd')) {
          $isActive = TRUE;
        }
      }
    }
    return $isActive;
  }
  /**
   * Method to get active huurovereenkomsten for organization
   *
   * @param int $orgId
   * @return array $orgHovs
   * @access public
   * @static
   */
  public static function getActiveOrgHov($orgId) {
    $activeHovs = array();
    if (!empty($orgId)) {
      $orgHovs = self::getOrgHov($orgId);
      foreach ($orgHovs as $hovId => $hovData) {
        if (self::isActiveHov($hovData['begindatum_overeenkomst'], $hovData['einddatum_overeenkomst']) == TRUE
          && self::hasOpzeggingsDossier($hovId) == FALSE) {
          $activeHovs[$hovId] = $hovData;
        }
      }
    }
    return $activeHovs;
  }

  /**
   * Method to check if there is an active huuropzeggingsdossier for the hov
   *
   * @param int $hovId
   * @return bool $hasOpzeggsingsDossier
   * @access public
   * @static
   */
  public static function hasOpzeggingsDossier($hovId) {
    $hasOpzeggingsDossier = FALSE;
    $customGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName('huur_opzegging');
    if ($customGroup != FALSE) {
      $customField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('hov_nr', $customGroup['id']);
      if ($customField != FALSE) {
        $query = 'SELECT entity_id FROM '.$customGroup['table_name'].' WHERE '.$customField['column_name'].' = %1';
        $dao = CRM_Core_DAO::executeQuery($query, array(1 => array($hovId, 'Integer')));
        while ($dao->fetch()) {
          $caseParams = array(
            'id' => $dao->entity_id,
            'return' => 'is_deleted');
          try {
            $caseIsDeleted = civicrm_api3('Case', 'Getvalue', $caseParams);
            if ($caseIsDeleted == 0) {
              $hasOpzeggingsDossier = TRUE;
            }
          } catch (CiviCRM_API3_Exception $ex) {}
        }
      }
    }
    return $hasOpzeggingsDossier;
  }

  /**
   * Method to get huurovereenkomsten for organization
   *
   * @param int $orgId
   * @return array $orgHovs
   * @access public
   * @static
   */
  public static function getOrgHov($orgId) {
    $orgHovs = array();
    if (!empty($orgId)) {
      $configMutOrg = CRM_Mutatieproces_MutOrgConfig::singleton();
      $query = 'SELECT * FROM '.$configMutOrg->getHovOrgCustomGroup('table_name').' WHERE entity_id = %1';
      $queryParams = array(1 => array($orgId, 'Integer'));
      $dao = CRM_Core_DAO::executeQuery($query, $queryParams);
      while ($dao->fetch()) {
        $orgHov = self::buildArrayFromDao($dao);
        $orgHovs[$orgHov['hov_nummer']] = $orgHov;
      }
    }
    return $orgHovs;
  }

  /**
   * Method to build hov array from dao
   *
   * @param object $dao
   * @return array $orgHov
   * @access protected
   * @static
   */
  protected static function buildArrayFromDao($dao) {
    $configMutOrg = CRM_Mutatieproces_MutOrgConfig::singleton();
    $hovOrgFields = array(
      'id' => 'id',
      'entity_id' => 'entity_id',
      'hov_nummer' => $configMutOrg->getHovNummerCustomField('column_name'),
      'vge_nummer' => $configMutOrg->getVgeNummerCustomField('column_name'),
      'vge_adres' => $configMutOrg->getVgeAdresCustomField('column_name'),
      'begindatum_overeenkomst' => $configMutOrg->getBeginDatumCustomField('column_name'),
      'einddatum_overeenkomst' => $configMutOrg->getEindDatumCustomField('column_name'),
      'naam_op_overeenkomst' => $configMutOrg->getNaamOpOvereenkomstCustomField('column_name')
    );
    $orgHov = array();
    foreach ($hovOrgFields as $key => $value) {
      if (isset($dao->$value)) {
        $orgHov[$key] = $dao->$value;
      } else {
        $orgHov[$key] = null;
      }
    }
    return $orgHov;
  }

  /**
   * Method to get single huurovereenkomst bij hovId
   *
   * @param int $hovId
   * @return array $hovData
   * @access public
   * @static
   */
  public static function getHov($hovId) {
    $hovData = array();
    if (!empty($hovId)) {
      $configMutOrg = CRM_Mutatieproces_MutOrgConfig::singleton();
      $query = 'SELECT * FROM '.$configMutOrg->getHovOrgCustomGroup('table_name').' WHERE '
        .$configMutOrg->getHovNummerCustomField('column_name').'= %1';
      $queryParams = array(1 => array($hovId, 'Integer'));
      $dao = CRM_Core_DAO::executeQuery($query, $queryParams);
      CRM_Core_Error::debug('dao', $dao);
      if ($dao->fetch()) {
        $hovData = self::buildArrayFromDao($dao);
      }
    }
    return $hovData;
  }
}