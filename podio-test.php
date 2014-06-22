<?php

try {


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
    Podio::authenticate_with_app($PODIO_APPID, $PODIO_APPSECRET);

 $contact_fields_index = array("name"=>"Guy Barnard", "mail"=>array("guy-facebook@barnardmail.net"));
echo "STARTING<br>";
   $existingContacts = PodioContact::get_for_app( $PODIO_APPID , $attributes = $contact_fields_index);
echo "DONE<br>";

  if (count($existingContacts)>0)
  {

  	 if (count($existingContacts)>0)
      {
        $first =  $existingContacts[0];
        $ep_profile_id = $first->profile_id;
        echo $ep_profile_id;
      } 

  }
echo "<br>DONE<br";

 } catch (PodioError $e) {

     echo  "There was an error. Podio responded with the error type " . $e->body['error'] ." and the mesage " . $e->body['error_description'] . ".";

    } catch (Exception $e) {

      echo "There was a general exception: " . $$e->getMessage();

    }

?>   