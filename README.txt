/* $Id$ */

The Signup Status module extends the Signup module's functionality by allowing 
users with permission to administer signups on signup-enabled nodes to set the 
status of a user's signup. This can be used, for example, to mark users as 
"Paid" or "Completed." By default, the module comes with two default statuses, 
"Approved" (the default status) and "Wait listed."

This module features:
* Limit number of signups per status.
* Wait list functionality for the signup module with optional auto-transition
  to "Registered" status.
* Printable rosters as HTML, PDF or CSV.
* Modifications to the Signup Broadcast form that allows signup creators to
  email a group of signups based on signup status.
* Views module support through several fields and filters.

Several optional modules are included in this package, which include:
* Signup Status Log: An administrative log of signup status changes.
* Signup Status Certificates: Grant certificates to users based on their 
  status.  See "Configuring Certificate Templates" below.
* Signup Status Mailer: Email users when their signup status has changed.


Requirements
------------------------
* Signup module (2.4 or greater) must be installed (http://drupal.org/project/signup)
* If you would like users to auto-transition from status-to-status (i.e. from
  "Wait Listed" to "Registered"), cron must be configured.  See 
  http://drupal.org/cron for details.

Signup Status Certificates module requires:
* Token (http://drupal.org/project/token)


Setup
------------------------
* Enable the module(s) and setup new permissions, if necessary:
  - manage signup status codes: Add, edit, delete signup status codes
  - print any signup certificate: Print other user's certificates using a
    certificate number.
* Navigate to Administer, Settings, Signup Status.
* Add or remove status codes as desired.

The signups tab for any signup-enabled node now contains an interface for
managing the signup status of users and the node edit form for these nodes
now contains fields for setting the enrollment limit for the various statuses.


Configuring Certificate Templates
---------------------------------
Certificate templates come in the form of nodes that use the token module to
substitute in profile and signup data about a given user's "completion" of a
signup.  This is useful for events like training sessions where the attendees
need certificates verifying their attendance and / or completion of the 
training.

* Create a new content type for certificate templates (i.e. "Certificate 
  Template").
* Create a new signup status code that will be used as the identifying status
  for certificated users, i.e. "Completed."
* Click on the "Certificates" tab on the Signup Status administration page.
* Choose the content type that you created for templates.
* Choose the certificate granting status code and save the settings form.
* Create a new Certificate Template.  In the "Available tokens and directives" 
  rollout, there will be the standard list of tokens, plus several 
  signup-specific tokens.  These can be used to, for example, list the user's 
  profile first and last name, the title of the event, the certificate number, 
  etc.
* Navigate to a signup-enabled node, or edit an existing one.  You will now 
  see an option for which certificate to use for completions of this event.  
  Set it to the new template.
* Change a signed-up user's status to the certificate granting status.  This
  user will now see an option to print their certificate when viewing the
  node.


Support
------------------------
Please submit any feature requests or bug reports to this project's issue
queue at http://drupal.org/project/issues/signup_status


Author / Maintainer
------------------------
Jeff Beeman: http://drupal.org/user/16734
