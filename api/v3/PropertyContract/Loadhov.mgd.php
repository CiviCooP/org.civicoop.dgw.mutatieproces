<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'Cron:PropertyContract.Loadhov',
    'entity' => 'Job',
    'params' => 
    array (
      'version' => 3,
      'name' => 'Call PropertyContract.Loadhov API',
      'description' => 'Call PropertyContract.Loadhov API',
      'run_frequency' => 'Daily',
      'api_entity' => 'PropertyContract',
      'api_action' => 'Loadhov',
      'parameters' => '',
    ),
  ),
);