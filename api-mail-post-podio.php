<?php
require_once 'Podio/PodioAPI.php';

function sendPodio($data)
{
$PODIO_CLIENTID = get_option('PODIO_CLIENTID');
$PODIO_CLIENTSECRET = get_option('PODIO_CLIENTSECRET');
$PODIO_APPID = get_option('PODIO_APPID1');
$PODIO_APPSECRET = get_option('PODIO_APPSECRET1');
$PODIO_SPACEID = get_option('PODIO_SPACEID');

try
{

Podio::setup($PODIO_CLIENTID, $PODIO_CLIENTSECRET);
Podio::authenticate_with_app($PODIO_APPID, $PODIO_APPSECRET);

$contact_fields = array(
    "name"=>($data['merges']['FNAME'] . ' ' . $data['merges']['LNAME']),
    "mail"=>array($data['email'])
    );

$ep_profile_id = PodioContact::create( $PODIO_SPACEID, $contact_fields);


if (!empty($data['merges']['FB_ID']))
{
$item_fields =  array(
          "contact" => $ep_profile_id,
          "facebook-id" => $data['merges']['FB_ID'],
           "source" => 1 );
} else
{
    $item_fields =  array(
          "contact" => $ep_profile_id,
           "source" => 1 );
}
           
PodioItem::create( $PODIO_APPID,  array('fields' => $item_fields));

} catch (PodioError $e) {
  echo 'Caught Pod exception: ' . $e->body['error_description'] ;

} catch (Exception $e) {
    echo 'Caught exception: ' .  $e->getMessage(), "\n";
}

}
?>
