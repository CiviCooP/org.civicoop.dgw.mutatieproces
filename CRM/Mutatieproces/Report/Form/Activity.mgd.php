<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Mutatieproces_Report_Form_Activity',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'Uitgebreide activiteiten rapport',
      'description' => 'Dossier activiteittypes in activiteit rapport (t.b.v mutatieproces)',
      'class_name' => 'CRM_Mutatieproces_Report_Form_Activity',
      'report_url' => 'mutatie/activities',
      'component' => '',
    ),
  ),
);