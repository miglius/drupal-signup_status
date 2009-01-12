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
 * other modules to react to the event.
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
  $account = user_load(array('uid' => $uid));
  $mail = $uid ? $account->mail : $anon_mail;
  $node = node_load($nid);
  $codes = signup_status_codes(FALSE, TRUE);
  $node_types = node_get_types('names');
  $from = $from = variable_get('site_mail', ini_get('sendmail_from'));
  $msg_vars = array(
    '!username' => $account->name,
    '!title' => check_plain($node->title),
    '!site' => variable_get('site_name', 'Drupal'),
    '!mailto' => $mail,
    '!curr_status' => $codes[$curr_cid]['name'],
    '!new_status' => $codes[$new_cid]['name'],
    '!login_url' => url('user', NULL, NULL, TRUE),
    '!node_url' => url('node/'. $node->nid, NULL, NULL, TRUE),
    '!node_type' => $node_types[$node->type],
  );
  $subject = signup_status_mailer_text('subject', $msg_vars);
  $body = signup_status_mailer_text('body', $msg_vars);
  if (drupal_mail('signup_status_mailer', $mail, $subject, $body, $from)) {
    watchdog('signup', t('Signup status message sent to %name at %mail',
        array('%name' => $account->name, '%mail' => $mail)), WATCHDOG_NOTICE,
        l(t('view'), 'node/'. $node->nid .'/signups'));
  }
  else {
    watchdog('signup', t('Error sending signup status message to %name at %mail',
        array('%name' => $account->name, '%mail' => $mail)), WATCHDOG_NOTICE,
        l(t('view'), 'node/'. $node->nid .'/signups'));
  }
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
    $operations['code_'. $cid] = array(
      'label' => t('Status') .': '. $code['name'],
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
 * Provide additional content for the header area of the roster for the given
 * node.
 *
 * @param $node
 *   The full node object for which the roster is being created.
 * @return
 *   A string of HTML.
 */
function hook_signup_status_roster_content($node) {
  $output = '';
  $args = array(
    '!type' => $node->type,
    '!time' => format_date($node->created),
  );
  $output .= "<p>". t('This !type was created on !time', $args) ."</p>";
  return $output;
}


/**
 * Grant or deny the currently logged-in user access to the signup roster.
 *
 * Allow modules to determine if the currently logged in user should be
 * allowed to access the roster (signup list) for the given node.  The
 * implementation of this is that if any module returns true, the user will be
 * granted access to the roster.
 *
 * @see signup_status_access_roster()
 *
 * @param $node
 *   The node object to check access on.
 * @return
 *   Boolean TRUE if allowed, FALSE if not.
 */
function hook_signup_status_access_roster($node) {
  if (user_access('administer nodes')) {
    return TRUE;
  }
}


/**
  * Provide additional content for the header area of the certificate for the
  * given node.
  *
  * @param $node
  *   The full node object for which the certificate is being created.
  * @return
  *   A string of HTML.
 */
function hook_signup_status_cert_content($node) {
  $output = '';
  $args = array(
    '!type' => $node->type,
    '!time' => format_date($node->created),
  );
  $output .= "<p>". t('The certificate for this !type was generated on !time', $args) ."</p>";
  return $output;
}


/**
 * @} End of "addtogroup hooks".
 */
