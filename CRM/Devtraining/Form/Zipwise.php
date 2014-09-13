<?php
/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Devtraining_Form_Zipwise extends CRM_Core_Form {

  /**
   * The API key used to authenticate requests to the Zipwise service
   *
   * @var string
   */
  private $_zipwise_api_key;

  /**
   * This function sets the default values for the form. For edit/view mode
   * the default values are retrieved from the database. It's called after
   * $this->preProcess().
   *
   * @access public
   *
   * @return array
   */
  function setDefaultValues() {
    $defaults = array(
      'zipwise_api_key' => CRM_Core_BAO_Setting::getItem('com.ginkgostreet.devtraining', 'zipwise_api_key'),
    );

    return $defaults;
   }

   /**
    * This function builds the form.
    */
  function buildQuickForm() {

    // add form elements
    $this->add(
      'text', // field type
      'zipwise_api_key', // field name
      ts('Zipwise API Key'), // field label
      array('help_pre' => 'Help text'), // attributes
      TRUE // is required
    );
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

 /**
   * If your form requires special validation, add one or more callbacks here
   */
  function addRules() {
    $this->addFormRule(array(get_class($this), 'checkKeyLength'));
  }

  /**
   * Here's our custom validation callback
   */
  static function checkKeyLength($values) {
    $errors = array();
    if (strlen($values['zipwise_api_key']) <= 3) {
      $errors['zipwise_api_key'] = ts('Zipwise API key must be longer than three characters');
    }
    return empty($errors) ? TRUE : $errors;
  }

  /**
   * Stores the config in the database.
   *
   * Resist the urge to put business logic in this function; business logic belongs
   * in the BAO layer. Keep the form layer thin. For example, if you are submitting data
   * about multiple related entities through this form, do the basics here (e.g.,
   * trim whitespace, inspect the data just enough to determine which BAO function
   * should handle it, etc.), then pass it off to your BAO.
   *
   * Remember: Functions that get called automatically by CiviCRM are hard to
   * write unit tests for. If you do minimal data sanitation here then call a
   * BAO function, it's much easier to ensure new code doesn't introduce bugs.
   */
  function postProcess() {
    $values = $this->exportValues();

    if (CRM_Utils_Array::value('zipwise_api_key', $values)) {
      CRM_Core_BAO_Setting::setItem(
        $values['zipwise_api_key'], // the value to be serialized and stored
        'com.ginkgostreet.devtraining', // the group name or category for the setting
        'zipwise_api_key' // the name of the setting
      );

      CRM_Core_Session::setStatus(ts('Zipwise API key has been set to "%1"',array(
        1 => $values['zipwise_api_key']
      )), '', 'success');
    }

    parent::postProcess();
  }
}