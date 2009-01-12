<?php
// $Id$

/**
 * Filter signups based on their signup status.
 */
class signup_status_handler_filter_signup_status extends views_handler_filter_in_operator {
  function get_value_options() {
    $options = array();
    $codes = signup_status_codes();
    foreach ($codes as $cid => $code) {
      $options[$cid] = $code['name'];
    }
    $this->value_options = $options;
  }
}

