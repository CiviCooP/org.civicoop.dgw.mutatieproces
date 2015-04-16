<?php
/**
 * Config with singleton pattern for Mutatieproces Organisaties De Goede Woning
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Mutatieproces_MutOrgConfig {
  static private $_singleton = NULL;
  /*
   * properties for custom group and fields huurovereenkomst organisatie
   * and additional data organisatie
   */
  protected $hovOrgCustomGroup = array();
  protected $hovNummerCustomField = array();
  protected $vgeNummerCustomField = array();
  protected $vgeAdresCustomField = array();
  protected $beginDatumCustomField = array();
  protected $eindDatumCustomField = array();
  protected $naamOpOvereenkomstCustomField = array();

  protected $additionalDataCustomGroup = array();
  protected $nrInFirstCustomField = array();

  /**
   * Method to get a specific value for the custom field nr in first
   *
   * @param string $key
   * @return mixed
   * @access public
   */
  public function getNrInFirstCustomField($key = 'id') {
    return $this->nrInFirstCustomField[$key];
  }

  /**
   * Method to get a specific value for the custom field naam op overeenkomst
   *
   * @param string $key
   * @return mixed
   * @access public
   */
  public function getNaamOpOvereenkomstCustomField($key = 'id') {
    return $this->naamOpOvereenkomstCustomField[$key];
  }

  /**
   * Method to get a specific value for the custom field eind datum
   *
   * @param string $key
   * @return mixed
   * @access public
   */
  public function getEindDatumCustomField($key = 'id') {
    return $this->eindDatumCustomField[$key];
  }

  /**
   * Method to get a specific value for the custom field begin datum
   *
   * @param string $key
   * @return mixed
   * @access public
   */
  public function getBeginDatumCustomField($key = 'id') {
    return $this->beginDatumCustomField[$key];
  }

  /**
   * Method to get a specific value for the custom field vge adres
   *
   * @param string $key
   * @return mixed
   * @access public
   */
  public function getVgeAdresCustomField($key = 'id') {
    return $this->vgeAdresCustomField[$key];
  }

  /**
   * Method to get a specific value for the custom field vge nummer
   *
   * @param string $key
   * @return mixed
   * @access public
   */
  public function getVgeNummerCustomField($key = 'id') {
    return $this->vgeNummerCustomField[$key];
  }

  /**
   * Method to get a specific value for the custom field hov nummer
   *
   * @param string $key
   * @return mixed
   * @access public
   */
  public function getHovNummerCustomField($key = 'id') {
    return $this->hovNummerCustomField[$key];
  }

  /**
   * Method to get a specific value for the custom group additional data first
   *
   * @param string $key
   * @return mixed
   * @access public
   */
  public function getAdditionalDataCustomGroup($key = 'id') {
    return $this->additionalDataCustomGroup[$key];
  }

  /**
   * Method to get a specific value for the custom group huurovereenkomst organisatie
   *
   * @param string $key
   * @return mixed
   * @access public
   */
  public function getHovOrgCustomGroup($key = 'id') {
    return $this->hovOrgCustomGroup[$key];
  }

  /**
   * Constructor method
   */
  public function __construct() {
    $this->setCustomGroup();
  }

  /**
   * Method to set or the custom groups and fields for mutatie organisatie
   *
   * @access protected
   */
  protected function setCustomGroup() {
    $customGroupHov = CRM_Mutatieproces_MutOrgUtils::getCustomGroup('Huurovereenkomst (organisatie)');
    if (empty($customGroupHov)) {
      $customGroupHov = CRM_Mutatieproces_MutOrgUtils::createCustomGroup('Huurovereenkomst (organisatie)', 'civicrm_value_hovorg',
        'Organization', 'Tab', 1);
    }
    $this->hovOrgCustomGroup = $customGroupHov;
    $this->setHovCustomFields();

    $customGroupFirst = CRM_Mutatieproces_MutOrgUtils::getCustomGroup('Gegevens_uit_First');
    if (empty($customGroupFirst)) {
      $customGroupFirst = CRM_Mutatieproces_MutOrgUtils::createCustomGroup('Gegevens_uit_First', 'civicrm_value_firstorg',
        'Organization');
    }
    $this->additionalDataCustomGroup = $customGroupFirst;
    $this->setFirstCustomFields();
  }

  /**
   * Method to get or create custom fields for first data
   *
   * @access protected
   */
  protected function setFirstCustomFields() {
    $nrInFirst = CRM_Mutatieproces_MutOrgUtils::getCustomField($this->additionalDataCustomGroup['id'], 'Nr_in_First');
    if (empty($nrInFirst)) {
      $nrInFirst = CRM_Mutatieproces_MutOrgUtils::createCustomField($this->hovOrgCustomGroup['id'],
        'Nr_in_First', 'Nr_in_First', 'String', 'Text', 1);
    }
    $this->nrInFirstCustomField = $nrInFirst;
  }

  /**
   * Method to get or create custom fields for hov organisatie
   *
   * @access protected
   */
  protected function setHovCustomFields() {
    $hovNummer = CRM_Mutatieproces_MutOrgUtils::getCustomField($this->hovOrgCustomGroup['id'], 'hov_nummer');
    if (empty($hovNummer)) {
      $hovNummer = CRM_Mutatieproces_MutOrgUtils::createCustomField($this->hovOrgCustomGroup['id'],
        'hov_nummer', 'hov_nummer', 'String', 'Text', 1);
    }
    $this->hovNummerCustomField = $hovNummer;

    $vgeNummer = CRM_Mutatieproces_MutOrgUtils::getCustomField($this->hovOrgCustomGroup['id'], 'vge_nummer');
    if (empty($vgeNummer)) {
      $vgeNummer = CRM_Mutatieproces_MutOrgUtils::createCustomField($this->hovOrgCustomGroup['id'], 'vge_nummer',
        'vge_nummer', 'String', 'Text', 1);
    }
    $this->vgeNummerCustomField = $vgeNummer;

    $vgeAdres = CRM_Mutatieproces_MutOrgUtils::getCustomField($this->hovOrgCustomGroup['id'], 'vge_adres');
    if (empty($vgeAdres)) {
      $vgeAdres = CRM_Mutatieproces_MutOrgUtils::createCustomField($this->hovOrgCustomGroup['id'], 'vge_adres',
        'vge_adres', 'String', 'Text', 1);
    }
    $this->vgeAdresCustomField = $vgeAdres;

    $beginDatum = CRM_Mutatieproces_MutOrgUtils::getCustomField($this->hovOrgCustomGroup['id'], 'begindatum_overeenkomst');
    if (empty($beginDatum)) {
      $beginDatum = CRM_Mutatieproces_MutOrgUtils::createCustomField($this->hovOrgCustomGroup['id'], 'begindatum_overeenkomst',
        'begindatum_overeenkomst', 'String', 'Text', 1, 1);
    }
    $this->beginDatumCustomField = $beginDatum;

    $eindDatum = CRM_Mutatieproces_MutOrgUtils::getCustomField($this->hovOrgCustomGroup['id'], 'einddatum_overeenkomst');
    if (empty($eindDatum)) {
      $eindDatum = CRM_Mutatieproces_MutOrgUtils::createCustomField($this->hovOrgCustomGroup['id'], 'einddatum_overeenkomst',
        'einddatum_overeenkomst', 'String', 'Text', 1, 1);
    }
    $this->eindDatumCustomField = $eindDatum;


    $naamOpOvereenkomst = CRM_Mutatieproces_MutOrgUtils::getCustomField($this->hovOrgCustomGroup['id'], 'naam_op_overeenkomst');
    if (empty($naamOpOvereenkomst)) {
      $naamOpOvereenkomst = CRM_Mutatieproces_MutOrgUtils::createCustomField($this->hovOrgCustomGroup['id'], 'naam_op_overeenkomst',
        'naam_op_overeenkomst', 'String', 'Text', 1, 1);
    }
    $this->naamOpOvereenkomstCustomField = $naamOpOvereenkomst;

  }

  /**
   * Method to return singleton object
   *
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Mutatieproces_MutOrgConfig();
    }
    return self::$_singleton;
  }
}