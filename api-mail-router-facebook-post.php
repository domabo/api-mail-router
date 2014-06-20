<?php

# No need for the template engine
define( 'WP_USE_THEMES', false );
# Load WordPress Core
// Assuming we're in a subdir: "~/wp-content/plugins/current_dir"
require_once( '../../../wp-load.php' );
$FACEBOOK_APP_ID = get_option('FACEBOOK_APP_ID');
$FACEBOOK_SECRET = get_option('FACEBOOK_SECRET');
$MAILCHIMP_APIKEY = get_option('MAILCHIMP_APIKEY');
$MAILCHIMP_LIST_ID = get_option('MAILCHIMP_LIST_ID');

// No need to change the function body
function parse_signed_request($signed_request, $secret) 
{
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);
// decode the data
    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);
    if (strtoupper($data['algorithm']) !== 'HMAC-SHA256')
    {
        error_log('Unknown algorithm. Expected HMAC-SHA256');
        return null;
    }

// check sig
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    if ($sig !== $expected_sig) 
    {
        error_log('Bad Signed JSON signature!');
        return null;
    }
    return $data;
}

function base64_url_decode($input) 
{
    return base64_decode(strtr($input, '-_', '+/'));
}

if ($_REQUEST) 
{
    $response = parse_signed_request($_REQUEST['signed_request'],
        $FACEBOOK_SECRET);

    require_once('MCAPI.class.php');  // same directory as store-address.php

    $api = new MCAPI($MAILCHIMP_APIKEY);

$ip = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');

    $merge_vars = array(
        'EMAIL' => $response["registration"]["email"],
        'FNAME' => $response["registration"]["first_name"],
        'LNAME' => $response["registration"]["last_name"],
        'OPTIN_IP' => $ip,
        'FB_ID' =>  $response["user_id"]
    );

     if($api->listSubscribe($MAILCHIMP_LIST_ID, $response["registration"]["email"], $merge_vars, 'html', false, true, false, true  )) {
        // It worked!   
/* Redirect browser */
header("Location: ". get_home_url(null, $_GET['success']));
 
/* Make sure that code below does not get executed when we redirect. */
exit;
    }else{
        // An error ocurred, return error message   
        echo '<html><head></head><body><b>Error:</b>&nbsp; ' . $api->errorMessage . '</body></html>';
    }
}
else 
{
    echo '$_REQUEST is empty';
}
?>
