<?php
/***********************************************

This script assumes you use a key to secure the script and configure a URL like this in MailChimp:

http://www.mydomain.com/mc-webhook.php?key=mykey

***********************************************/

# No need for the template engine
define( 'WP_USE_THEMES', false );
# Load WordPress Core
// Assuming we're in a subdir: "~/wp-content/plugins/current_dir"
require_once( '../../../wp-load.php' );
require_once( 'api-mail-post-mandrill.php' );
require_once( 'api-mail-post-podio.php' );

$MAILCHIMP_WEBHOOK_KEY = get_option('MAILCHIMP_WEBHOOK_KEY');

if ( !isset($_GET['key']) ){
//  No security key specified, ignoring request
} elseif ($_GET['key'] != $MAILCHIMP_WEBHOOK_KEY) {
//  Security key specified, but not correct
} else {
//process the request
  switch($_POST['type']){
    case 'subscribe'  : subscribe($_POST['data']);   break;
    case 'unsubscribe': unsubscribe($_POST['data']); break;
    case 'cleaned'    : cleaned($_POST['data']);     break;
    case 'upemail'    : upemail($_POST['data']);     break;
    case 'profile'    : profile($_POST['data']);     break;
    default:
//  Request type unknown, ignoring
  }
}

/***********************************************
Helper Functions
***********************************************/

function subscribe($data){
// $data['email'] just subscribed
  echo 'RECEIVED SUBSCRIBE';
  sendMandrill($data);
  echo 'SENT WELCOME EMAIL';
  $retval = sendPodio($data);
  if (empty($retval))
    echo 'PROCESSED SUBSCRIBE';
  else
    echo $retval;
}
function unsubscribe($data){
// $data['email'] just unsubscribed

}
function cleaned($data){
// $data['email']  was cleaned from  list

}
function upemail($data){
// $data['old_email'] changed their email address to  $data['new_email']

}
function profile($data){
  sendMandrill($data);
// $data['email']  updated their profile
}


?>
