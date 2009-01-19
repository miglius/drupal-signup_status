<?php
// $Id$

/**
 * @file
 * Module admin page callbacks.
 */

//////////////////////////////////////////////////////////////////////////////
// signup_status settings

/**
 * Implements the signup status code list.
 *
 * @return
 *   A HTML table with the codes list.
 */
function signup_status_admin_list() {
  $header = array(
    t('Name'),
    t('Description'),
    t('Modifies signup count'),
    t('Show on form'),
    array('data' => t('Operations'), 'colspan' => 2),
  );

  $rows = array();
  foreach (signup_status_codes() as $cid => $code) {
    $rows[] = array(
      $code['name'],
      $code['description'],
      empty($code['mod_signup_count']) ? t('No') : t('Yes'),
      empty($code['show_on_form']) ? t('No') : t('Yes'),
      l(t('edit'), 'admin/settings/signup_status/edit/'. $cid),
      l(t('delete'), 'admin/settings/signup_status/delete/'. $cid),
    );
  }

  return theme('table', $header, $rows);
}

/**
 * Implements the signups status code edit page.
 *
 * @param $form_state
 *   A form state array.
 * @param $op
 *   An operatin - add or edit.
 * @param $cid
 *   A signup status code ID.
 *
 * @return
 *   The form structure.
 */
function signup_status_admin_form(&$form_state, $op = NULL, $cid = NULL) {
  if (isset($cid)) {
    $form['cid'] = array(
      '#type' => 'hidden',
      '#value' => $cid,
    );
  }

  $codes = signup_status_codes();
  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('Name'),
    '#default_value' => isset($cid) ? $codes[$cid]['name'] : NULL,
    '#maxlength' => 128,
    '#required' => TRUE,
  );
  $form['description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#default_value' => isset($cid) ? $codes[$cid]['description'] : NULL,
  );
  $form['mod_signup_count'] = array(
    '#type' => 'checkbox',
    '#title' => t('Modify the total signup count'),
    '#description' => t('If selected, users signed up with this status code will be considered in addition to the standards signup count, and node creators will be able to set limits for each status code accordingly.'),
    '#default_value' => isset($cid) ? $codes[$cid]['mod_signup_count'] : NULL,
  );
  $form['show_on_form'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show on the signup form'),
    '#description' => t('If selected, users can select this status when signing up for a node.'),
    '#default_value' => isset($cid) ? $codes[$cid]['show_on_form'] : NULL,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save configuration'),
  );

  // Don't allow mod_signup_count to be changed on cid 0, 1
  if (in_array($cid, array(1, 2))) {
    $form['mod_signup_count']['#disabled'] = TRUE;
  }

  return $form;
}

/**
 * Validate hook for the signup status code form.
 */
function signup_status_admin_form_validate($form, &$form_state) {
  $values = $form_state['values'];

  $cid = isset($values['cid']) ? $values['cid'] : 0;
  if (db_fetch_object(db_query("SELECT name FROM {signup_status_codes} WHERE name = '%s' AND cid <> %d", $values['name'], $cid))) {
    form_set_error('name', t('A status code with the  name %name already exists.', array('%name' => $values['name'])));
  }
}

/**
 * Submit hook for the signup status code form.
 */
function signup_status_admin_form_submit($form, &$form_state) {
  $values = $form_state['values'];
  if (!isset($values['cid'])) {
    db_query("INSERT INTO {signup_status_codes} (name, description, mod_signup_count, show_on_form) VALUES ('%s', '%s', %d, %d)", $values['name'], $values['description'], $values['mod_signup_count'], $values['show_on_form']);
    drupal_set_message(t('Status code %name has been added.', array('%name' => $values['name'])));
    watchdog('signup_status', 'Status code %name has been added.', array('%name' => $values['name']));
  }
  else {
    db_query("UPDATE {signup_status_codes} SET name = '%s', description = '%s', mod_signup_count = %d, show_on_form = %d WHERE cid = %d", $values['name'], $values['description'], $values['mod_signup_count'], $values['show_on_form'], $values['cid']);
    drupal_set_message(t('Status code %name has been updated.', array('%name' => $values['name'])));
    watchdog('signup_status', 'Status code %name has been updated.', array('%name' => $values['name']));
  }
  $form_state['redirect'] = 'admin/settings/signup_status/list';
}

/**
 * Implements the signup status code delete page.
 *
 * @param $form_state
 *   A form state array.
 * @param $cid
 *   A signup status ID.
 *
 * @return
 *   The form structure.
 */
function signup_status_admin_delete(&$form_state, $cid) {
  $codes = signup_status_codes();
  if (array_key_exists($cid, $codes)) {
    $form['cid'] = array(
      '#type' => 'hidden',
      '#value' => $cid,
    );
    $form['name'] = array(
      '#type' => 'hidden',
      '#value' => $codes[$cid]['name'],
    );
    return confirm_form(
      $form,
      t('Are you sure you want to delete the status code %name?', array('%name' => $codes[$cid]['name'])),
      'admin/settings/signup_status/list',
      t('<p>This action cannot be undone.</p>'),
      t('Delete'),
      t('Cancel')
    );
  }
  drupal_goto('admin/settings/signup_status/list');
}

/**
 * Submit hook for the signup status code delete page.
 */
function signup_status_admin_delete_submit($form, &$form_state) {
  $values = $form_state['values'];
  if ($values['confirm'] && $values['cid']) {
    db_query("DELETE FROM {signup_status_codes} WHERE cid = %d", $values['cid']);
    drupal_set_message(t('Status code %name has been deleted.', array('%name' => $values['name'])));
    watchdog('signup_status', 'Status code %name has been deleted.', array('%name' => $values['name']));
  }
}

