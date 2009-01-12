<?php
// $Id$

/**
 * Field handler to display the status of a specific signup.
 */
class signup_status_handler_field_signup_status extends views_handler_field {
  function render($values) {
    // signup_status_codes() already does its own static caching.
    $status_codes = signup_status_codes();
    $cid = $values->{$this->field_alias};
    return !empty($status_codes[$cid]['name']) ? check_plain($status_codes[$cid]['name']) : t('unknown status');
  }
}

