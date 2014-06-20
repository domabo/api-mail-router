<?php

# No need for the template engine
define( 'WP_USE_THEMES', false );
# Load WordPress Core
// Assuming we're in a subdir: "~/wp-content/plugins/current_dir"
require_once( '../../../wp-load.php' );
require_once( 'Podio/PodioAPI.php' );
$PODIO_CLIENTID = get_option('PODIO_CLIENTID');
$PODIO_CLIENTSECRET = get_option('PODIO_CLIENTSECRET');
$PODIO_APPID = get_option('PODIO_APPID1');
$PODIO_APPSECRET = get_option('PODIO_APPSECRET1');

Podio::setup($PODIO_CLIENTID, $PODIO_CLIENTSECRET);
Podio::authenticate_with_app($PODIO_APPID, $PODIO_APPSECRET);
$items = PodioItem::filter($PODIO_APPID);

print "My app has ".count($items)." subscribers";
?>
