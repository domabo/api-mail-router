<?php

echo "STARTING<br";
define( 'WP_USE_THEMES', false );
# Load WordPress Core
// Assuming we're in a subdir: "~/wp-content/plugins/current_dir"
require_once( '../../../wp-load.php' );
require_once( 'api-mail-post-podio.php' );

 define( 'WP_USE_THEMES', false );
# Load WordPress Core
// Assuming we're in a subdir: "~/wp-content/plugins/current_dir"
require_once( '../../../wp-load.php' );
require_once 'Podio/PodioAPI.php';


  $PODIO_CLIENTID = get_option('PODIO_CLIENTID');
  $PODIO_CLIENTSECRET = get_option('PODIO_CLIENTSECRET');
  $PODIO_APPID = get_option('PODIO_APPID1');
  $PODIO_APPSECRET = get_option('PODIO_APPSECRET1');
  $PODIO_SPACEID = get_option('PODIO_SPACEID');


 Podio::setup($PODIO_CLIENTID, $PODIO_CLIENTSECRET);
    Podio::authenticate_with_app($PODIO_APPID, $PODIO_APPTOKEN);

 $contact_fields_index = array("name"=>"Guy Barnard", "mail"=>array("guy-facebook@barnardmail.net"));

   $existingContacts = PodioContact::get_for_app( $appid, $attributes = $contact_fields_index);

   print_r($existingContacts);
echo "<br>DONE<br";

?>   