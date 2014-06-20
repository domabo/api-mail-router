<?php

function sendMandrill($data)
{
    require_once 'Mandrill.php'; 
    echo "loading Mandrill";
 $MANDRILL_APIKEY = get_option('MANDRILL_APIKEY');
$MANDRILL_WELCOMETEMPLATE = get_option('MANDRILL_WELCOMETEMPLATE');


    $mandrill = new Mandrill($MANDRILL_APIKEY);
     echo "loeaded Mandrill for " . $MANDRILL_WELCOMETEMPLATE;

    $message = array(
        'to' => array(
            array(
                'email' => $data['email'],
                'name' =>  $data['merges']['FNAME'] . ' ' . $data['merges']['LNAME'],
                'type' => 'to'
            )
        ),
        'important' => false,
        'track_opens' => true,
        'track_clicks' => true,
        'auto_text' => true,
          'inline_css' => true,
        'tracking_domain' => null,
        'signing_domain' => null,
        'return_path_domain' => null,
        'merge' => true,
       'merge_vars' => array(
            array(
                'rcpt' => $data['email'],
                'vars' => array(
                    array(
                        'name' => 'FNAME',
                        'content' => $data['merges']['FNAME']
                    ),
                    array(
                        'name' => 'LNAME',
                        'content' => $data['merges']['LNAME']
                    )
                )
            )
        ),
        'tags' => array('subscribes')
    );
    $async = false;
    $ip_pool = 'Main Pool';
    $send_at = null;
    $result = $mandrill->messages->sendTemplate($MANDRILL_WELCOMETEMPLATE, null, $message, $async);
    /*
    Array
    (
        [0] => Array
            (
                [email] => recipient.email@example.com
                [status] => sent
                [reject_reason] => hard-bounce
                [_id] => abc123abc123abc123abc123abc123
            )
    
    )
    */
} 
?>
