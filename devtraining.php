<?php

require_once 'devtraining.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function devtraining_civicrm_config(&$config) {
  _devtraining_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function devtraining_civicrm_xmlMenu(&$files) {
  _devtraining_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function devtraining_civicrm_install() {
  return _devtraining_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function devtraining_civicrm_uninstall() {
  return _devtraining_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function devtraining_civicrm_enable() {
  return _devtraining_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function devtraining_civicrm_disable() {
  return _devtraining_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function devtraining_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _devtraining_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function devtraining_civicrm_managed(&$entities) {
  return _devtraining_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function devtraining_civicrm_caseTypes(&$caseTypes) {
  _devtraining_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function devtraining_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _devtraining_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_post. This is a poor man's dispatcher.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_post
 */
function devtraining_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  $f = '_' . __FUNCTION__ . '_' . $objectName;
  if (function_exists($f)){
    $f($op, $objectName, $objectId, $objectRef);
  }
}

/**
 * Delegated implementation of hook_civicrm_post for Address objects
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_post
 */
function _devtraining_civicrm_post_Address($op, $objectName, $objectId, &$objectRef) {
  // don't look up county unless address is associated with a contact, since our
  // custom field for counties is on the contact entity
  if (
    $objectRef->contact_id
    && in_array($op, array('edit', 'create'))
  ) {
    $county = _devtraining_fetch_county_by_postal_code($objectRef->postal_code);

    if ($county) {
      // look up the custom id for the county field
      $api = civicrm_api3('CustomGroup', 'getsingle', array(
        'name' => 'constituent_information',
        'api.CustomField.getvalue' => array(
            'name' => 'county',
            'return' => 'id',
        ),
      ));
      $county_field_id = $api['api.CustomField.getvalue'];

      civicrm_api3('Contact', 'create', array(
        'id' => $objectRef->contact_id,
        "custom_{$county_field_id}" => $county,
      ));
    }
  }
}

/**
 * Fetches a county name for a given postal code
 *
 * @param string $postal_code
 * @return mixed Boolean FALSE on error, county name as string on success
 */
function _devtraining_fetch_county_by_postal_code($postal_code) {
  $result = FALSE;

  $zipwise_api_key = CRM_Core_BAO_Setting::getItem('com.ginkgostreet.devtraining', 'zipwise_api_key');

  if ($zipwise_api_key) {
    $request = "https://www.zipwise.com/webservices/zipinfo.php?key={$zipwise_api_key}&zip={$postal_code}&format=json";
    $http = CRM_Utils_HttpClient::singleton()->get($request);

    if (CRM_Utils_Array::value(0, $http) === CRM_Utils_HttpClient::STATUS_OK) {
      $json = CRM_Utils_Array::value(1, $http);
      $zipwise = json_decode($json);

      if (property_exists($zipwise->results, 'error')) {
        CRM_Core_Error::debug_log_message(
          'com.ginkgostreet.devtraining - Zipwise lookup failed with: ' . $zipwise->results->error
        );
      } else {
        $result = $zipwise->results->county;
      }
    } else {
      CRM_Core_Error::debug_log_message('com.ginkgostreet.devtraining - Failed to curl Zipwise');
    }
  }

  return $result;
}