<?php
// $Id$

/**
 * @file
 * These are the hooks that are invoked by the Signup Status module.
 */

/**
 * @addtogroup hooks
 * @{
 */
 
/**
 * Act upon status change of a user's signup.
 *
 * When the status of a user's signup to a node is changed this hook allows
 * other modules to react to the event.  A potential use case would be a 
 * module that emails users when their status is changed.
 *
 * @param $uid
 *   The uid of the account being acted upon.
 * @param $nid
 *   The nid of the node for which the user's signup status is being altered.
 * @param $curr_cid
 *   The current status code (an integer) of the user's signup.
 * @param $new_cid
 *   The new status code (an integer) of the user's signup.
 * @param $anon_mail
 *   The email address provided by the user when she registered.  Can be NULL,
 *   for registered users, so it is important that modules account for that
 *   value.
 * @return 
 *   None.
 */
function hook_update_signup_status($uid, $nid, $curr_cid, $new_cid, $anon_mail = NULL) {
  $codes = signup_status_codes();
  $node = node_load(array('nid' => $nid));
  
  $mail = '';
  if ($anon_mail) {
    $mail = $anon_mail;
  }
  else {
    $account = user_load(array('uid' => $uid));
    $mail = $account->mail;
  }
  
  $message = t('Your signup for %title has been changed from %old to %new.  To view your current signup information for %title, go to %url');
  
  // Rest of mail code goes here...
}

/**
 * Add signup status mass operations.
 * 
 * The hook enables modules to inject custom operations into the mass 
 * operations dropdown found on the signup status tab of signup enabled nodes,
 * by associating a callback function with the operation, which is then 
 * called when the form is submitted. The callback function receives one 
 * initial argument, which is an array of the checked nodes.  This hook is 
 * very similar to hook_node_operations(). See signup_status_operations() for
 * an implementation example of handling this submission via the supplied
 * callback.
 *
 * @return 
 *   An array of operations. Each operation is an associative array that may
 *   contain the following key-value pairs:
 *   - "label": Required. The label for the operation, displayed in the 
 *     dropdown menu.
 *   - "callback": Required. The function to call for the operation.
 *   - "callback arguments": Optional. An array of additional arguments to 
 *     pass to the callback function.
 */
function hook_signup_status_operations() {
  $codes = signup_status_codes();
  foreach ($codes as $cid => $code) {
    $operations['code_' . $cid] = array(
      'label' => t('Status') . ': ' . $code['name'],
      'callback' => 'signup_status_operations',
      'callback arguments' => array('code', $cid),
    );
  }
  $operations['cancel'] = array(
    'label' => t('Cancel Signup'),
    'callback' => 'signup_status_operations',
    'callback arguments' => array('cancel'),
  );
  return $operations;
}

/**
 * @} End of "addtogroup hooks".
 */