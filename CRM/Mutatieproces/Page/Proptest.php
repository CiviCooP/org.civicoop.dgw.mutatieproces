<?php

require_once 'CRM/Core/Page.php';
require_once 'CRM/Property.php';

class CRM_Mutatieproces_Page_Proptest extends CRM_Core_Page {
  function run() {
    CRM_Utils_System::setTitle(ts('Test Property Class'));
    $property = new Property();
    $params = array(
        'vge_id'            =>  1015,
        'complex_id'        =>  1300,
        'subcomplex'        =>  "SC1300A",
        'vge_street_name'   =>  "Ambachtstraat",
        'vge_street_number' =>  21,
        'vge_city'          =>  "Brummen",
        'vge_street_unit'   =>  "bis",
        'vge_postal_code'   =>  "6971 BN",
        'vge_country_id'    =>  1152,
        'vge_address_id'    =>  52171,
        'epa_label'         =>  "A",
        'epa_pre'           =>  "B",
        'city_region'       =>  "Centrum",
        'block'             =>  "Zuid",
        'vge_type_id'       =>  1,
        'strategy_label'    =>  "Continueren 1",
        'strategy_b_pnts'   =>  2,
        'strategy_c_pnts'   =>  3,
        'number_rooms'      =>  5,
        'outside_code'      =>  "Enorme tuin",
        'stairs'            =>  1,
        'square_mtrs'       =>  124.5
    );
    $property->create($params);
    //$this->assign('meldingen', $melding);

    parent::run();
  }
}
