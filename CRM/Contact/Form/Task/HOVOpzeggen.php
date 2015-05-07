<?php
/**
 * Class to process huuropzegging
 * 
 * @author Jaap Jansma (jaap.jansma@civicoop.org, http://www.civicoop.org)
 * @date Oct 2013
 * 
 * Copyright (C) 2014 Coöperatieve CiviCooP U.A.
 * Licensed to De Goede Woning under the Academic Free License version 3.0.
 */
class CRM_Contact_Form_Task_HOVOpzeggen extends CRM_Contact_Form_Task {
  protected $caseType = "";
  protected $caseTypeId = 0;
  protected $contactId;
  protected $contactType = "";
  protected $customGroup = 0;
  protected $customValues = array();
  protected $vgeNrFieldName = "";
  protected $hovNrFieldName = "";
  protected $adresFieldName = "";
    
  /**
   * Method to build all the data structures needed to build the form
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $this->contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
    $this->contactType = CRM_Contact_BAO_Contact::getContactType($this->contactId);
    if ($this->contactId) {
      $this->_contactIds = array($this->contactId);
      $this->assign('totalSelectedContacts', 1);
    } else {
      parent::preProcess();
    }
  }
  /**
   * Method to build the form
   *
   * BOS1502861 16 april 2015 - add organizations
   *
   * @access public
   * @return void
   */
  public function buildQuickForm() {
    $label = ts('Huurcontract opzeggen');
    if (isset($this->_contactIds[0])) {
      $session = CRM_Core_Session::singleton();
      $session->replaceUserContext(CRM_Utils_System::url('civicrm/contact/view',
        'reset=1&cid=' . $this->_contactIds[0]));

      $this->addSelectOther('hov', 'Huurovereenkomst', array(), array(), true);
      $hovs = $this->getElement('hov_id');
      /*
       * get huishouden for hoofdhuurder if contact_type = Individual
       */
      switch ($this->contactType) {
        case 'Individual':
          $huishoudenId = CRM_Utils_DgwUtils::getHuishoudenHoofdhuurder($this->_contactIds[0]);
          $hovs = $this->addHovToForm($huishoudenId, $hovs);
          break;
        case 'Household':
          $hovs = $this->addHovToForm($this->_contactIds[0], $hovs);
          break;
        case 'Organization':
          $hovs = $this->addHovToForm($this->_contactIds[0], $hovs);
          break;
      }

      $this->addDate('verwachte_einddatum', 'Verwachte einddatum', true, array('formatType' => 'activityDate'));
      $this->addDefaultButtons($label, 'done', 'cancel');
    } else {
        $this->addDefaultButtons($label, 'done');
    }
  }

  /**
   * Method to set default values
   *
   * @return mixed
   * @access public
   */
  public function setDefaultValues() {
    $today = new DateTime();
    $today->modify('+1 month');
    $values['verwachte_einddatum'] = $today->format('m/d/Y');
    return $values;
  }

  /**
   * Method to get Property contracts for contact
   *
   * BOS1502861 16 april 2015 - add organizations
   *
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @author for BOS1502861 Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
   * @param integer $contactId
   * @param object $hovs
   * @return object $hovs
   * @access protected
   */
  protected function addHovToForm($contactId, &$hovs) {
    $hovs->addOption( '- Selecteer een huurovereenkomst -', '');
    /*
     * process based on contact type (huurovereenkomst huishouden or
     * huurovereenkomst organisatie)
     */
    switch ($this->contactType) {
      case 'Organization':
        $orgHovs = CRM_Mutatieproces_MutOrg::getActiveOrgHov($contactId);
        foreach ($orgHovs as $hovId => $hovData) {
          $hovs->addOption($hovData['vge_adres'].' (HOV: '.$hovId.', VGE: '.$hovData['vge_nummer'].')', $hovId);
        }
        break;

      default:
        $this->customGroup = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName('Huurovereenkomst (huishouden)');
        $this->customValues = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomValuesForContactAndCustomGroupSorted($contactId,
          $this->customGroup['id']);
        $vgeField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('VGE_nummer_First', $this->customGroup['id']);
        if (isset($vgeField['name'])) {
          $this->vgeNrFieldName = $vgeField['name'];
        }
        $hovField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('HOV_nummer_First', $this->customGroup['id']);
        if (isset($hovField['name'])) {
          $this->hovNrFieldName = $hovField['name'];
        }
        $adresField = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("VGE_adres_First", $this->customGroup['id']);
        if (isset($adresField['name'])) {
          $this->adresFieldName = $adresField['name'];
        }
        foreach($this->customValues as $id => $value) {
          /*
           * only if allowed
           */
          if (empty($value['Einddatum_HOV'])) {
            $hovs->addOption($value['VGE_adres_First'].' (HOV: '.$value['HOV_nummer_First'].', VGE: '.$value['VGE_nummer_First'].')', $id);
          }
        }
        break;
    }
  }
  /**
   * Method to get the case type for the name
   *
   * @author Jaap Jansma (jaap.jansma@civicoop.org)
   * @param string $case (name of case type)
   * @return integer $optionValueValue
   * @access protected
   */
  protected function getCaseTypeId($case) {
    $optionGroup = civicrm_api('OptionGroup', 'getsingle', array('name' => 'case_type', 'version' => '3'));
    if (isset($optionGroup['id'])) {
      $optionGroupId = $optionGroup['id'];
    } else {
      return false;
    }
    $optionValue = civicrm_api('OptionValue', 'getsingle', array('option_group_id' => $optionGroupId, 'name' => $case, 'version' => '3'));
    $optionValueValue = false;
    if (isset($optionValue['id'])) {
      $optionValueValue = $optionValue['value'];
    }
    return $optionValueValue;
  }
  /**
   * Method to process the form after the input has been submitted and validated
   *
   * BOS1502861 16 april 2015 - add organizations
   *
   * @access public
   * @return void
   */
  public function postProcess() {
    $hovId = $this->getSubmitValue('hov_id');
    $caseType = 'Huuropzeggingsdossier';
    $urlParams = "reset=1";
    $urlString = 'civicrm/dashboard';
    $expectedEndDate = date('Ymd', strtotime($this->getSubmitValue('verwachte_einddatum')));

    switch ($this->contactType) {

      case 'Organization':
        $hovData = CRM_Mutatieproces_MutOrg::getHov($hovId);
        if (isset($this->_contactIds[0])) {
          $caseParams = array(
            'contact_id' => $this->_contactIds[0],
            'case_type' => $caseType,
            'subject' => 'Opzegging huurcontact ' . $hovId . ' (' . $hovData['vge_adres'] . ')');
          $createdCase = civicrm_api3('Case', 'Create', $caseParams);
          if (isset($createdCase['id'])) {
            CRM_Mutatieproces_Property::setVgeFieldsCase($hovData['vge_nummer'], $createdCase['id']);
            CRM_Mutatieproces_PropertyContract::setHovFieldsCase($hovId, $createdCase['id'], $expectedEndDate);
            $this->updateFutureAddress($createdCase['id'], $this->_contactIds[0]);
            $urlString = 'civicrm/contact/view/case';
            $urlParams = "reset=1&id=".$createdCase['id']."&cid=".$this->_contactIds[0]."&action=view";
          }
        }
        break;

      default:
        foreach ($this->customValues as $entityId => $hovData) {
          if ($entityId == $hovId) {
            $vgeNr = $hovData[$this->vgeNrFieldName];
            $hovNr = $hovData[$this->hovNrFieldName];
            $vgeAdres = $hovData[$this->adresFieldName];
            //generate dossier opzeggen huurovereenkomst
            if (isset($this->_contactIds[0])) {
              $cid = $this->_contactIds[0];

              $params = array(
                'contact_id'  =>  $cid,
                'case_type'   =>  $caseType,
                'subject'     =>  "Opzegging huurcontract ".$hovNr." (".$vgeAdres.")"
              );
              $result = civicrm_api3('Case', 'Create', $params);
              if (isset($result['id'])) {
                /*
                 * update vge and huurovereenkomst fields
                 */
                CRM_Mutatieproces_Property::setVgeFieldsCase($vgeNr, $result['id']);
                CRM_Mutatieproces_PropertyContract::setHovFieldsCase($hovNr, $result['id'], $expectedEndDate);
                $this->updateFutureAddress($result['id'], $cid);
                $urlString = 'civicrm/contact/view/case';
                $urlParams = "reset=1&id=".$result['id']."&cid=".$cid."&action=view";
              }
            }
          }
        }
        break;
    }
    $session = CRM_Core_Session::singleton();
    $session->replaceUserContext(CRM_Utils_System::url($urlString, $urlParams));
  }

  /**
   * Update the field Future Address in First with future address data
   *
   * @param $case_id
   * @param $contact_id
   * @throws \CiviCRM_API3_Exception
   */
  protected function updateFutureAddress($case_id, $contact_id) {
    $toekomst = civicrm_api3('LocationType', 'getvalue', array('name' => 'Toekomst', 'return' => 'id'));
    try {
      $address = civicrm_api3('Address', 'getsingle', array('location_type_id' => $toekomst, 'contact_id' => $contact_id));
    } catch (Exception $e) {
      return;
    }

    $future_address = $address['street_address']."\r\n".$address['postal_code']." ".$address['city'];
    $future_address = trim($future_address);

    $customGroup = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'info_afd_verhuur'));
    $customField = civicrm_api3('CustomField', 'getsingle', array('name' => 'future_address_in_first', 'custom_group_id' => $customGroup['id']));

    $id = CRM_Core_DAO::singleValueQuery("SELECT `id` FROM `".$customGroup['table_name']."` WHERE `entity_id` = %1", array(1=>array($case_id, 'Integer')));
    if ($id) {
      $sql = "UPDATE `".$customGroup['table_name']."` SET `".$customField['column_name']."`  = %1 WHERE `id` = %1";
      $params[1] = array($future_address, 'String');
      $params[2] = array($id, 'Integer');
      CRM_Core_DAO::executeQuery($sql, $params);
    } else {
      $sql = "INSERT INTO `".$customGroup['table_name']."` (`entity_id`, `".$customField['column_name']."`) VALUES (%1, %2)";
      $params[1] = array($case_id, 'Integer');
      $params[2] = array($future_address, 'String');
      CRM_Core_DAO::executeQuery($sql, $params);
    }
  }
}