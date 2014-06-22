<?php

require_once 'amrPodio.php';
require_once 'amrFacebook.php';

function sendPodio($data)
{
  $PODIO_CLIENTID = get_option('PODIO_CLIENTID');
  $PODIO_CLIENTSECRET = get_option('PODIO_CLIENTSECRET');
  $PODIO_APPID = get_option('PODIO_APPID1');
  $PODIO_APPSECRET = get_option('PODIO_APPSECRET1');
  $PODIO_SPACEID = get_option('PODIO_SPACEID');

  $contact_name = $data['merges']['FNAME'] . ' ' . $data['merges']['LNAME'];
  $contact_email = $data['email'];

  $item_fields =  array(
    "source" => 1 );

  if (!empty($data['merges']['FB_ID']))
  {
    $contact_facebook = $data['merges']['FB_ID'];

    $item_fields["facebook-id"] = $contact_facebook;
  }
  else
  {
    $contact_facebook = "";
  }

  amrPodio::authenticate($PODIO_CLIENTID, $PODIO_CLIENTSECRET, $PODIO_APPID, $PODIO_APPSECRET);

  return amrPodio::createContactItem($PODIO_APPID, $PODIO_SPACEID, $contact_name, $contact_email, $contact_facebook, $item_fields, "contact");

}

?>
