<?php
// This file declares a managed database record of type "CustomField".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 =>
  array (
    'name' => 'CountyField',
    'entity' => 'CustomField',
    'params' =>
    array (
      'version' => 3,
      'custom_group_id' => civicrm_api3('CustomGroup', 'getvalue', array(
        'name' => "constituent_information",
        'return' => "id",
      )),
      'label' => 'County',
      'name' => 'county',
      'data_type' => 'String',
      'html_type' => 'Text',
      'is_active' => 1,
      'is_required' => 0,
      'is_searchable' => 1,
      'weight' => 1,
    ),
  ),
);