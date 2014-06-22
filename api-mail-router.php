<?php
/*
Plugin Name: API Mail Router
Plugin URI: http://www.github.com/domabo/api-mail-router
Description: Route APIs betweenMailChimp, Mandrill, Facebook Registration, and Asasna
Version: 0.1
Author: Domabo
Author URI: http://www.github.com/domabo
License: GPLv2 or later
*/

 @ini_set( 'log_errors', 'Off' );

@ini_set( 'display_errors', 'On' );

@ini_set( 'error_reporting', E_ALL );

class amr_Plugin {
	private static $amr_instance;

	private function __construct() {
		$this->constants(); // Defines any constants used in the plugin
		$this->init();   // Sets up all the actions and filters
	}

	public static function getInstance() {
		if ( !self::$amr_instance ) {
			self::$amr_instance = new amr_Plugin();
		}

		return self::$amr_instance;
	}

	private function constants() {
		define( 'amr_VERSION', '1.0' );
	}

	private function init() {
		// Register the options with the settings API
		add_action( 'admin_init', array( $this, 'amr_register_settings' ) );

		// Add the menu page
		add_action( 'admin_menu', array( $this, 'amr_setup_admin' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),array( $this, 'amr_plugin_settings_link') );
        	add_shortcode( 'api-facebook-register', array( $this,'amr_shortcode_facebook_register') );
	}
	
	//[api-facebook-register]
	public function amr_shortcode_facebook_register( $atts ){
		
		 $a = shortcode_atts( array(
        'success' => 'subscribe-success',
        'failure' => '/',
    ), $atts );
		
        	return 
        	"
        	<script>// <![CDATA[
		window.fbAsyncInit = function() { 
			FB.init({
      				appId  : '" . get_option('FACEBOOK_APP_ID') . "',
      				status : true, // check login status
      				cookie : false, // enable cookies to allow the server to access the session
      				xfbml  : false  // parse XFBML
    				});
			FB.getLoginStatus(function(o) { 
       			if (o.status == 'connected') {
          			// USER IS LOGGED IN AND HAS AUTHORIZED APP
         			document.getElementById('registerFB').style.visibility='visible';
       			} else if (o.status == 'not_authorized') {
          			// USER IS LOGGED IN TO FACEBOOK (BUT HASN'T AUTHORIZED YOUR APP YET)
          			document.getElementById('registerFB').style.visibility='visible';
       			} else {
          			//document.getElementById('registerFB').style.visibility='hidden';
       			}
    			});};
			// ]]></script>
			<div id='registerFB' style='visibility: hidden;'>
			<h2>Or Register Using facebook</h2>
			<iframe src='https://www.facebook.com/plugins/registration?client_id=" . get_option('FACEBOOK_APP_ID'). ">&amp;redirect_uri=". plugins_url( 'api-mail-router-facebook-post.php',  __FILE__ ) . "?success=". $a['success']." &amp;fb_only=true&amp;fields=name,first_name,last_name,email' width='450' height='450'>
			</iframe>
			</div>";
		
        }

	public function amr_plugin_settings_link( $links ) {
        $links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=api-mail-router') .'">Settings</a>';
   	return $links;
	}

	public function amr_register_settings() {
		register_setting( 'amr-options', 'FACEBOOK_APP_ID' );
		register_setting( 'amr-options', 'FACEBOOK_SECRET' );
		register_setting( 'amr-options', 'MAILCHIMP_APIKEY' );
        	register_setting( 'amr-options', 'MAILCHIMP_LIST_ID' );
        	register_setting( 'amr-options', 'MAILCHIMP_WEBHOOK_KEY' );
        	register_setting( 'amr-options', 'PODIO_CLIENTID' );
        	register_setting( 'amr-options', 'PODIO_CLIENTSECRET' );
            register_setting( 'amr-options', 'PODIO_SPACEID' );
       		register_setting( 'amr-options', 'PODIO_APPID1' );
        	register_setting( 'amr-options', 'PODIO_APPSECRET1' );
        	register_setting( 'amr-options', 'MANDRILL_APIKEY' );
        	register_setting( 'amr-options', 'MANDRILL_WELCOMETEMPLATE' );
	}


	public function amr_setup_admin() {
		// Add our Menu Area
		add_options_page( 'API Mail Router', 'API Mail Router', 'administrator', 'api-mail-router', 
						  array( $this, 'amr_admin_page' ) 
						);
	   }

	public function amr_admin_page() {
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div><h2>API Mail Router Settings</h2>
			<form method="post" action="options.php">
				<?php wp_nonce_field( 'amr-options' ); ?>
				<?php settings_fields( 'amr-options' ); ?>
			
			
			 <table class="form-table">
                            <tr valign="top">
                               <th scope="row">Facebook App Id</th>
                               <td><input type="text" name="FACEBOOK_APP_ID" value="<?php echo get_option( 'FACEBOOK_APP_ID'); ?>" />
                               <br /><span class='description'>Go to <a href='https://developers.facebook.com/apps'>https://developers.facebook.com/apps</a> for App Id</span></td>
                               
        		    </tr>
        		       <tr valign="top">
                               <th scope="row">Facebook Secret</th>
                               <td><input type="text" size="80" name="FACEBOOK_SECRET" value="<?php echo get_option( 'FACEBOOK_SECRET'); ?>" />
                                    <br /><span class='description'>Go to <a href='https://developers.facebook.com/apps'>https://developers.facebook.com/apps</a> for App Secret</span></td>
              
        		    </tr>
                       	   <tr valign="top">
                               <th scope="row">MailChimp API Key</th>
                               <td><input type="text" size="80" name="MAILCHIMP_APIKEY" value="<?php echo get_option( 'MAILCHIMP_APIKEY'); ?>" />
                                    <br /><span class='description'>See <a href='http://kb.mailchimp.com/article/where-can-i-find-my-api-key'>MailChimp KB</a> for how to generate an API Key</span></td>
                          
        		    </tr>
                       	   <tr valign="top">
                               <th scope="row">MailChimp Default List Id</th>
                               <td><input type="text" name="MAILCHIMP_LIST_ID" value="<?php echo get_option( 'MAILCHIMP_LIST_ID'); ?>" />
                                        <br /><span class='description'>Enter the (numeric) id of the default list to register new users</span></td>
                          
        		    </tr>
                       	 	   <tr valign="top">
                               <th scope="row">MailChimp Webhook Key</th>
                               <td><input type="text" size="30" name="MAILCHIMP_WEBHOOK_KEY" value="<?php echo get_option( 'MAILCHIMP_WEBHOOK_KEY'); ?>" />
                                <br /><span class='description'>Create a non-obvious MAILCHIMP_WEBHOOK_KEY to use in webhook URL<br><?php echo plugins_url( 'api-mail-router-mailchimp-post.php?key=MAILCHIMP_WEBHOOK_KEY' , __FILE__ ); ?></span></td>
                 	    
        		    </tr>
        		      	 	   <tr valign="top">
                               <th scope="row">Podio Client ID</th>
                               <td><input type="text" size="30" name="PODIO_CLIENTID" value="<?php echo get_option( 'PODIO_CLIENTID'); ?>" />
                                <br /><span class='description'>The Client ID for this app associated with your Podio.com account</span></td>
                 	    
        		    </tr>
                     
                     	 	   <tr valign="top">
                               <th scope="row">Podio Client Secret</th>
                               <td><input type="text" size="80" name="PODIO_CLIENTSECRET" value="<?php echo get_option( 'PODIO_CLIENTSECRET'); ?>" />
                                 <br /><span class='description'>The Client Secret for this app associated with your Podio.com account</span></td>
        		    </tr>
                <tr valign="top">
                               <th scope="row">Podio Space ID</th>
                               <td><input type="text" size="30" name="PODIO_SPACEID" value="<?php echo get_option( 'PODIO_SPACEID'); ?>" />
                                <br /><span class='description'>The app id </span></td>
                      
                </tr>
        		         <tr valign="top">
                               <th scope="row">Podio App ID</th>
                               <td><input type="text" size="30" name="PODIO_APPID1" value="<?php echo get_option( 'PODIO_APPID1'); ?>" />
                                <br /><span class='description'>The app id </span></td>
                 	    
        		    </tr>
                     
                     	 	   <tr valign="top">
                               <th scope="row">Podio App Secret</th>
                               <td><input type="text" size="80" name="PODIO_APPSECRET1" value="<?php echo get_option( 'PODIO_APPSECRET1'); ?>" />
                                 <br /><span class='description'>The app Secret </span></td>
        		    </tr>
                  
                    
        		    	 	   <tr valign="top">
                               <th scope="row">Mandrill API Key</th>
                               <td><input type="text" size="30" name="MANDRILL_APIKEY" value="<?php echo get_option( 'MANDRILL_APIKEY'); ?>" />
                                <br /><span class='description'>The API Key associated with your Mandrillapp.com account</span></td>
                 	    
        		    </tr>
                     
                     	 	   <tr valign="top">
                               <th scope="row">Mandrill Template Id</th>
                               <td><input type="text" size="30" name="MANDRILL_WELCOMETEMPLATE" value="<?php echo get_option( 'MANDRILL_WELCOMETEMPLATE'); ?>" />
                                 <br /><span class='description'>The slug of the default Mandrill template to use for welcome emails</span></td>
        		    </tr>
                         	<input type="hidden" name="action" value="update" />
				</table>
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
			</form>
		</div>
		<?php
	}
}


$amr = amr_Plugin::getInstance();
