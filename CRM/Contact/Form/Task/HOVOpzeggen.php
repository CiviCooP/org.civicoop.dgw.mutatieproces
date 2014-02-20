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
    protected $_case_type = "";
    protected $_case_type_id = 0;
    protected $_contact_id;
    protected $_contact_type = "";
    protected $_custom_group = 0;
    protected $_custom_values = "";
    protected $_vge_nr_fieldname = "";
    protected $_hov_nr_fieldname = "";
    protected $_adres_fieldname = "";
    
    /**
     * function to build all the data structures needed to build the form
     * 
     * @return void
     * @access public
     */
    public function preProcess() {
        $this->_contact_id = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
        $this->_contact_type = CRM_Contact_BAO_Contact::getContactType($this->_contact_id);
        if ($this->_contact_id) {
            $this->_contactIds = array($this->_contact_id);
            $this->assign('totalSelectedContacts', 1);
        } else {
            parent::preProcess();
        }
    }
    /**
     * Build the form
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
            if ($this->_contact_type == "Individual") {
                $huishouden_id = CRM_Utils_DgwUtils::getHuishoudenHoofdhuurder($this->_contactIds[0]);
                $hovs = $this->addHovToForm($huishouden_id, $hovs);
            } else {
                $hovs = $this->addHovToForm($this->_contactIds[0], $hovs);
            }
            
            $this->addDate('verwachte_einddatum', 'Verwachte einddatum', true, array('formatType' => 'activityDate'));
            $this->addDefaultButtons($label, 'done', 'cancel');
        } else {
            $this->addDefaultButtons($label, 'done');
        }
    }
    /**
     * function to get Property contracts for contact
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param integer $contact_id
     * @param object $hovs
     * @return object $hovs
     * @access protected
     */
    protected function addHovToForm($contact_id, $hovs) {
        $hovs->addOption( '- Selecteer een huurovereenkomst -', '');
        $case_type_id = $this->getCaseTypeId('Huuropzeggingsdossier');
        /*
         * process based on contact type (huurovereenkomst huishouden or
         * huurovereenkomst organisatie)
         */
        if ($this->_contact_type != "Organization") {
            $this->_custom_group = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomGroupByName('Huurovereenkomst (huishouden)');
            $this->_custom_values = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomValuesForContactAndCustomGroupSorted($contact_id, $this->_custom_group['id']);
            $vge_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('VGE_nummer_First', $this->_custom_group['id']);
            if (isset($vge_field['name'])) {
                $this->_vge_nr_fieldname = $vge_field['name'];
            }
            $hov_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName('HOV_nummer_First', $this->_custom_group['id']);
            if (isset($hov_field['name'])) {
                $this->_hov_nr_fieldname = $hov_field['name'];
            }
            $adres_field = CRM_Utils_DgwMutatieprocesUtils::retrieveCustomFieldByName("VGE_adres_First", $this->_custom_group['id']);
            if (isset($adres_field['name'])) {
                $this->_adres_fieldname = $adres_field['name'];
            }
            foreach($this->_custom_values as $id => $value) {
                /*
                 * only if allowed
                 */
                $add_option = CRM_Utils_DgwMutatieprocesUtils::checkHovOpzeggen($contact_id, "Household");
                if ($add_option == TRUE) {
                    $hovs->addOption($value['VGE_adres_First'].' (HOV: '.$value['HOV_nummer_First'].', VGE: '.$value['VGE_nummer_First'].')', $id);
                }
            }
        }
        return $hovs;
    }
    /**
     * function to get the case type for the name
     * 
     * @author Jaap Jansma (jaap.jansma@civicoop.org)
     * @param string $case (name of case type)
     * @return integer $option_value_value
     * @access protected
     */
    protected function getCaseTypeId($case) {
        $option_group = civicrm_api('OptionGroup', 'getsingle', array('name' => 'case_type', 'version' => '3'));
        $option_group_id = false;
        if (isset($option_group['id'])) {
            $option_group_id = $option_group['id'];
        } else {
            return false;
        }
        $option_value = civicrm_api('OptionValue', 'getsingle', array('option_group_id' => $option_group_id, 'name' => $case, 'version' => '3'));
        $option_value_id = false;
        $option_value_value = false;
        if (isset($option_value['id'])) {
            $option_value_id = $option_value['id'];
            $option_value_value = $option_value['value'];
        }
        return $option_value_value;
    }
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        $session = CRM_Core_Session::singleton();
        $hov_id = $this->getSubmitValue('hov_id');
        $urlParams = "reset=1";
        $urlString = 'civicrm/dashboard';
        foreach ($this->_custom_values as $entity_id => $hov_data) {
            if ($entity_id == $hov_id) {
                $vge_nr = $hov_data[$this->_vge_nr_fieldname];
                $hov_nr = $hov_data[$this->_hov_nr_fieldname];
                $vge_adres = $hov_data[$this->_adres_fieldname];
                
            }
        }
        //generate dossier opzeggen huurovereenkomst
        if (isset($this->_contactIds[0])) {
            $cid = $this->_contactIds[0];
            $expected_end_date = date('Ymd', strtotime($this->getSubmitValue('verwachte_einddatum')));
            $urlParams = "reset=1&cid=".$cid;
            $urlString = 'civicrm/contact/view';
          
            $params = array(
                'contact_id'  =>  $cid,
                'case_type'   =>  "Huuropzeggingsdossier",
                'subject'     =>  "Opzegging huurcontract ".$hov_nr." (".$vge_adres.")"
            );
            $result = civicrm_api3('Case', 'Create', $params);
            if (isset($result['id'])) {
                /*
                 * update vge and huurovereenkomst fields
                 */
                CRM_Mutatieproces_Property::setVgeFieldsCase($vge_nr, $result['id']);
                CRM_Mutatieproces_PropertyContract::setHovFieldsCase($hov_nr, $result['id'], $expected_end_date);
            }
            $urlString = 'civicrm/contact/view/case';
            $urlParams = "reset=1&id=".$result['id']."&cid=".$cid."&action=view";
        }
        $session->replaceUserContext(CRM_Utils_System::url($urlString, $urlParams));
    }
}

