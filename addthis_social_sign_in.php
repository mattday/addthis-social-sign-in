<?php
   /*
   Plugin Name: AddThis Social Sign In
   Plugin URI: http://www.addthis.com/
   Description: A lightweight javascript plugin helps users to sign in to the wordpress site using social media services.
   Version: 2.0.1
   Author: AddThis Team
   Author URI: http://www.addthis.com
   License: Apache
   */

define(ADDTHIS_SSI_PRODUCT_CODE, 'wpssi-201');
define(ADDTHIS_SSI_AT_VERSION, 300);
		
if ( version_compare( get_bloginfo( 'version' ) , '3.1' , '<' ) )
	require_once( ABSPATH . WPINC . '/registration.php' );

wp_enqueue_script('jquery');

function addthis_ssi_activate() { 
	
	if ( version_compare( get_bloginfo( 'version' ) , '2.9' , '<' ) ) {
		deactivate_plugins( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
		wp_die( sprintf( __( "Require wordpress greater than 2.9") ) );	
	}
		
	add_option( "addthis_ssi_fbid", '', '', 'yes' );
	add_option( "addthis_ssi_twkey", '', '', 'yes' );
	add_option( "addthis_ssi_tw_secret", '', '', 'yes' );
	add_option( "addthis_ssi_googleid", '', '', 'yes' );
	add_option( "addthis_ssi_linkedin_key", '', '', 'yes' );
	add_option( "addthis_ssi_linkedin_secret", '', '', 'yes' );
	add_option( "addthis_ssi_yahoo_enabled", '', '', 'yes' );	
		
	add_option( "addthis_default_user_role", '', '', 'yes' );
		
	add_option( "addthis_ssi_redirect_url", '', '', 'yes' );

	add_option( "addthis_ssi_welcome_enabled", '', '', 'yes' );
	add_option( "addthis_ssi_thumbnail_enabled", '', '', 'yes' );
	add_option( "addthis_ssi_button_text", 'Click one of the buttons below to sign in with your favorite service', '', 'yes' );
	add_option( "addthis_ssi_popup_enabled", '', '', 'yes' );
	add_option( "addthis_ssi_email_exist_text", 'The email address [email], is already registered.  Please login with your existing credentials or use the "Lost your password?" link below.', '', 'yes' );	
}

function at_force_user_fill_credentials(){
	
	$message = ( $_REQUEST['page'] == "addthis-social-sign-in" ? 'any of the service fields with corresponding keys / IDs ' : 'the <b><a href="'.admin_url( 'options-general.php?page=addthis-social-sign-in' ).'">settings</a></b> page with the credentials' );
	echo '<div class="error"><p>Fill '.$message.' to enable AddThis Social Sign In.</p></div>';	
}

if( ( substr( $_SERVER["PHP_SELF"], -11 ) == 'plugins.php' || $_REQUEST['page'] == "addthis-social-sign-in" ) && !(get_option( 'addthis_ssi_fbid' ) || get_option( 'addthis_ssi_googleid' ) || ( get_option( 'addthis_ssi_twkey' )  && get_option( 'addthis_ssi_tw_secret' ) ) || ( get_option( 'addthis_ssi_linkedin_key' ) && get_option( 'addthis_ssi_linkedin_secret' ) ) || get_option( 'addthis_ssi_yahoo_enabled' ) ) )
	add_action( 'admin_notices', 'at_force_user_fill_credentials' );

register_activation_hook( __FILE__, 'addthis_ssi_activate' );

function addthis_ssi_render_buttons( $tmpl_mode = false ){
	
	wp_enqueue_style( 'frontendstyles', plugins_url('css/frontend-styles.css', __FILE__) );
	
	if( get_option( 'addthis_ssi_fbid' ) || get_option( 'addthis_ssi_twkey' ) || get_option( 'addthis_ssi_googleid' ) || ( get_option( 'addthis_ssi_linkedin_key' ) && get_option( 'addthis_ssi_linkedin_secret' ) ) || get_option( 'addthis_ssi_yahoo_enabled' ) ) {
		
		global $addthis_addjs;
		
		$at_ssi_button = '<label class="at_button_label">'. get_option('addthis_ssi_button_text') .'</label>
				<div class="addthis_toolbox">
				<a class="addthis_login_facebook"></a>';				

		if( get_option('addthis_ssi_twkey') && get_option('addthis_ssi_tw_secret') && extension_loaded('curl') ) {		
			$at_ssi_button .=  '<a class="addthis_login_twitter"><span id="at-twitter-connect" class="ssi-button"></span></a>';
			wp_enqueue_script( 'tw_script', plugins_url('js/at_twitter.js', __FILE__) );
		}

		$at_ssi_button .= '<a class="addthis_login_google"></a>';
		
		if( get_option('addthis_ssi_linkedin_key') && get_option('addthis_ssi_linkedin_secret') && extension_loaded('curl') ) {		
			$at_ssi_button .= '<a class="addthis_login_linkedin"><span id="at-linkedin-connect" class="ssi-button"></span></a>';
			wp_enqueue_script( 'ln_script', plugins_url('js/at_linkedin.js', __FILE__) );
		}
		
		if( get_option('addthis_ssi_yahoo_enabled') && extension_loaded('curl') ){		
			$at_ssi_button .= '<a class="addthis_login_yahoo"><span id="at-yahoo-connect" class="ssi-button" at_login_url='.wp_login_url().'></span></a>';
			wp_enqueue_script( 'y_script', plugins_url('js/at_yahoo.js', __FILE__) );
		}
				
		$at_ssi_button .= '</div>';

		echo $at_ssi_button;
			
		if ( version_compare( get_bloginfo( 'version' ) , '3.3' , '<' ) )
			wp_head();
		
		if( $tmpl_mode == true ){
			echo '<form method="post" action="'.get_bloginfo('wpurl').'/wp-login.php" id="loginform" name="loginform">';
			addthis_ssi_render_fields();
			$redirect_req = ( isset( $_REQUEST[ 'redirect_to' ] ) ) ? $_REQUEST[ 'redirect_to' ] : '';
			echo '<input type="hidden" name="redirect_to" value="'.$redirect_req.'" />';
			echo '</form>';
		}
		
		$addthis_ssi_config =  '
		if (typeof(addthis_config) == "undefined") { var addthis_config = {}; }	
		addthis_config.login = {
	                services:{
	                        facebook:{
	                                appId:"'.get_option('addthis_ssi_fbid').'",scope:"email"
	                        },
	                        google:{
	                                clientId:"'.get_option('addthis_ssi_googleid').'"
	                        }
	                },
	                callback:function(user){
	
	                        document.getElementById("addthis_signature").value = user.addthis_signature;
	                        document.getElementById("addthis_firstname").value = user.firstName;
	                        document.getElementById("addthis_lastname").value = user.lastName;
	                        document.getElementById("addthis_email").value = user.email;
	                        document.getElementById("addthis_profileurl").value = user.profileURL;
	                        document.getElementById("addthis_avatarurl").value = user.thumbnailURL;
	                        document.getElementById("addthis_service").value = user.service;
	                        try { document.getElementById("loginform").submit(); } 
	                        	catch (err) { document.getElementById("registerform").submit(); }
	                }
	        };';
	
		$addthis_addjs->addAfterScript( $addthis_ssi_config );
		$addthis_addjs->output_script();
	}	
}

add_action( 'login_form', 'addthis_ssi_render_buttons' );
add_action( 'register_form', 'addthis_ssi_render_buttons' );

function addthis_ssi_render_fields(){
	
	echo '<input type="hidden" id="addthis_signature" name="addthis_signature" value=""/>
<input type="hidden" id="addthis_firstname" name="addthis_firstname" value=""/>
<input type="hidden" id="addthis_lastname" name="addthis_lastname" value=""/>
<input type="hidden" id="addthis_email" name="addthis_email" value=""/>
<input type="hidden" id="addthis_profileurl" name="addthis_profileurl" value=""/>
<input type="hidden" id="addthis_avatarurl" name="addthis_avatarurl" value=""/>
<input type="hidden" id="addthis_service" name="addthis_service" value=""/>
<input type="hidden" id="addthis_redirect" name="addthis_redirect" value="'. addthis_get_current_url() .'">';	
}

add_action( 'login_form', 'addthis_ssi_render_fields' );
add_action( 'register_form', 'addthis_ssi_render_fields' );

/*Append email existing message to login form*/
function addthis_ssi_email_exist_message(){
	
	$at_exist_userdata = unserialize( stripslashes( $_COOKIE[ 'at_ssi_exist_userdata' ] ) );
	$at_exist_userdata_email = !empty( $at_exist_userdata["addthis_email"] ) ? "<i>".$at_exist_userdata["addthis_email"]."</i>" : "<i>".$at_exist_userdata["at_custom_email"]."</i>";

	echo '<p class="email-exist-message">'. str_replace( "[email]", $at_exist_userdata_email, get_option('addthis_ssi_email_exist_text')  ).'</p><br/>';	
}

if( $_REQUEST[ 'action' ] == "at_email_exist" ) {
	add_action( 'login_form', 'addthis_ssi_email_exist_message', 0 );
}

/*Template Tag function function*/
function addthis_ssi(){
	
	if ( is_user_logged_in() ) {
		
		if( get_option('addthis_ssi_welcome_enabled') ) {
		
			global $current_user;
				
        	get_currentuserinfo();
        	echo get_avatar( $current_user->ID, 30 ).'&nbsp;&nbsp;Hi&nbsp;'.$current_user->first_name.'&nbsp;&nbsp;<a href="'. wp_logout_url().'" title="Logout">Logout</a>';
		}			
	} else {	
		addthis_ssi_render_buttons( true );
	}
}

function addthis_ssi_getuser() {
		
	global $wpdb;
	$sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s'";
	$at_signature_meta = "addthis_signature_".$_REQUEST[ 'addthis_service' ];
	$user_id = $wpdb->get_var( $wpdb->prepare( $sql, $at_signature_meta, $_REQUEST[ 'addthis_signature' ] ) );
	return $user_id;	
}

function addthis_social_sign_in() {
	
	if( isset( $_COOKIE[ 'at_ssi_exist_userdata' ] ) && is_user_logged_in() ){
		
		global $current_user;
		
		$at_exist_userdata = unserialize( stripslashes( $_COOKIE[ 'at_ssi_exist_userdata' ] ) );
		
		$at_exist_userdata_email = !empty( $at_exist_userdata["addthis_email"] ) ? $at_exist_userdata["addthis_email"] : $at_exist_userdata["at_custom_email"];
		$at_exist_userdata_signature = !empty( $at_exist_userdata["addthis_signature"] ) ? $at_exist_userdata["addthis_signature"] : $at_exist_userdata["at_custom_signature"];
		$at_exist_userdata_avatar = !empty( $at_exist_userdata["addthis_avatarurl"] ) ? $at_exist_userdata["addthis_avatarurl"] : $at_exist_userdata["at_custom_avatarurl"];
		$at_exist_userdata_service = !empty( $at_exist_userdata["addthis_service"] ) ? $at_exist_userdata["addthis_service"] : $at_exist_userdata["at_custom_service"];
		
		if( $at_exist_userdata_email == $current_user->user_email ){ 
			addthis_update_meta( $current_user->ID, $at_exist_userdata_service, $at_exist_userdata_signature, $at_exist_userdata_avatar );
			setcookie( "at_ssi_exist_userdata", "", time()-1000, "/" );
			add_action('admin_notices', 'at_exist_user_success');			
		}		
	}

	if( get_option( 'addthis_ssi_twkey' ) && get_option( 'addthis_ssi_tw_secret' ) && isset( $_REQUEST[ 'tOauth' ] ) && $_REQUEST[ 'tOauth' ] == "initiate" ) {
		addthis_ssi_twitter();
	}
		
	if( get_option( 'addthis_ssi_linkedin_key' ) && get_option( 'addthis_ssi_linkedin_secret' ) && isset( $_REQUEST[ 'lType' ] ) && $_REQUEST[ 'lType' ] == "initiate" ) {
		addthis_ssi_linkedin();
	}

	if( get_option( 'addthis_ssi_yahoo_enabled' ) ) {	
		addthis_ssi_yahoo();
	}	
	
	if ( empty ( $_REQUEST[ 'redirect_to' ] ) ) {
		
		if( isset( $_REQUEST[ 'addthis_redirect' ] ) && $_REQUEST[ 'addthis_redirect' ] != "" ) { 
			$_REQUEST[ 'redirect_to' ] = $_REQUEST[ 'addthis_redirect' ];
		}	
		if( isset( $_REQUEST[ 'at_custom_redirect' ] ) &&  $_REQUEST[ 'at_custom_redirect' ] != "" ) {
			$_REQUEST[ 'redirect_to' ] = $_REQUEST[ 'at_custom_redirect' ];
		}	
	}

	if( isset( $_REQUEST[ 'at_custom_submit' ] ) && $_REQUEST[ 'at_custom_submit' ] == true ) {
		
		$user_id = addthis_ssi_insertuser( $_REQUEST[ 'at_custom_userlogin' ], $_REQUEST[ 'at_custom_email' ], $_REQUEST[ 'at_custom_firstname' ], $_REQUEST[ 'at_custom_lastname' ], $_REQUEST[ 'at_custom_profileurl' ], $_REQUEST[ 'at_custom_service' ], $_REQUEST[ 'at_custom_signature' ], $_REQUEST[ 'at_custom_avatarurl' ] );
		
		addthis_sign_in_process( $user_id, $_REQUEST[ 'redirect_to' ] );				
	}	

	if( isset( $_REQUEST[ 'addthis_signature' ] ) && $_REQUEST[ 'addthis_signature' ] != "" ) {		
			
		unset( $_POST['log'] );
		unset( $_POST[ 'pwd' ] );	

		$user_id = addthis_ssi_getuser();
		
		if( $user_id ) {			
			$user_data  = get_userdata( $user_id );
			$user_login = $user_data->user_login;
			
			addthis_update_meta( $user_id, $_REQUEST[ 'addthis_service' ], $_REQUEST[ 'addthis_signature' ], $_REQUEST[ 'addthis_avatarurl' ] );

			addthis_sign_in_process( $user_id, $_REQUEST[ 'redirect_to' ] );
		}
		elseif( get_option('addthis_ssi_popup_enabled') ) {	
			add_action( 'login_form', 'addthis_ssi_enablepopup' );			
			add_action( 'register_form', 'addthis_ssi_enablepopup');			
		} else {			
			$user_id = addthis_ssi_insertuser( '', $_REQUEST[ 'addthis_email' ], $_REQUEST[ 'addthis_firstname' ], $_REQUEST[ 'addthis_lastname' ], $_REQUEST[ 'addthis_profileurl' ], $_REQUEST[ 'addthis_service' ], $_REQUEST[ 'addthis_signature' ], $_REQUEST[ 'addthis_avatarurl' ] );
			
			if( is_integer( $user_id ) ) {
				addthis_sign_in_process( $user_id, $_REQUEST[ 'redirect_to' ] );
			}			
		}		
	}	
}

add_action( 'init', 'addthis_social_sign_in', 0 );

/*Additional services exclusively offered to wp addthis ssi*/

function addthis_ssi_twitter(){ 
	
	require_once( 'includes/twitterOauth.php' );
	
	if( !empty($_GET['oauth_verifier']) &&  !empty($_GET['tOauth'] )  ) {

		$at_tw_token = unserialize( stripslashes( $_COOKIE[ 'addthis_ssi_twitter' ] ) );
		$at_tw_auth = new TwitterOAuth( get_option( 'addthis_ssi_twkey' ), get_option( 'addthis_ssi_tw_secret' ), $at_tw_token['oauth_token'], $at_tw_token['oauth_token_secret']);  
		$at_access_token = $at_tw_auth->getAccessToken( $_GET['oauth_verifier'] );
    	$at_tw_user_info = $at_tw_auth->get( 'account/verify_credentials' );

		$_REQUEST[ 'addthis_signature' ] = md5( "twitter_".get_option( 'addthis_ssi_tw_secret' ).$at_tw_user_info->id );
		$_REQUEST[ 'addthis_firstname' ] = $at_tw_user_info->name;
		$_REQUEST[ 'addthis_avatarurl' ] = $at_tw_user_info->profile_image_url_https;
		$_REQUEST[ 'addthis_profileurl' ] = "http://twitter.com/".$at_tw_user_info->screen_name;
		
		$_REQUEST[ 'addthis_service' ] = 'twitter';
								
			$at_tw_redirect = str_replace("?tOauth=initiate", "", $_COOKIE[ 'addthis_tw_redirect' ] );
			$at_tw_redirect = str_replace("&tOauth=initiate", "", $at_tw_redirect );
			$_REQUEST[ 'addthis_redirect' ] = $at_tw_redirect;					
			 
			setcookie( "addthis_ssi_twitter", "", time()-10, "/" );
			setcookie( "addthis_tw_redirect", "", time()-10, "/" );		

	} else {
			
		$at_tw_auth = new TwitterOAuth( get_option( 'addthis_ssi_twkey' ), get_option( 'addthis_ssi_tw_secret' ) );	
		$at_tw_callback_url = wp_login_url() . '?tOauth=initiate';	
		$at_req_token = $at_tw_auth->getRequestToken( $at_tw_callback_url );
		setcookie( "addthis_ssi_twitter", serialize( $at_req_token ), time()+3600, "/" );
		
		$at_current_url = addthis_get_current_url();			
		setcookie( "addthis_tw_redirect", $at_current_url, time()+3600, "/" );
	
		// If everything goes well..  
		if( $at_tw_auth->http_code == 200 ){  
		    // Generate the URL and redirect  
		    $at_tw_authurl = $at_tw_auth->getAuthorizeURL( $at_req_token['oauth_token'] ); 
		    header( 'Location: '. $at_tw_authurl );
		    exit(); 
		} else {
			add_action( 'login_form', 'at_invalid_ids', 0 );
			add_action( 'register_form', 'at_invalid_ids', 0 );			
		}		
	}	
}

function addthis_ssi_linkedin(){
		
	require_once( 'includes/linkedin_3.2.0.class.php' );
	
	$API_CONFIG = array( 'appKey' => get_option( 'addthis_ssi_linkedin_key' ), 'appSecret' => get_option( 'addthis_ssi_linkedin_secret' ), 'callbackUrl'  => NULL  );

	// set the callback url
	$API_CONFIG[ 'callbackUrl' ] = wp_login_url() . '?lType=initiate&' . LINKEDIN::_GET_RESPONSE . '=1';
	
	$OBJ_linkedin = new LinkedIn($API_CONFIG);
      
	// check for response from LinkedIn
	$_GET[LINKEDIN::_GET_RESPONSE] = (isset($_GET[LINKEDIN::_GET_RESPONSE])) ? $_GET[LINKEDIN::_GET_RESPONSE] : '';
	
	if( !$_GET[LINKEDIN::_GET_RESPONSE] ) { 
      	
        // send a request for a LinkedIn access token
        $response = $OBJ_linkedin->retrieveTokenRequest();
		if( $response[ 'success' ] === TRUE ) {
          // store the request token

			$addthis_linkedin_resp[ 'oauth' ][ 'linkedin' ][ 'request' ] = $response[ 'linkedin' ];

			setcookie( "addthis_ssi_linkedin", serialize( $addthis_linkedin_resp ), time()+3600, "/" );
         
			$at_current_url = addthis_get_current_url();
			setcookie( "addthis_ln_redirect", $at_current_url, time()+3600, "/" );

          	// redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
          	header( 'Location: ' . LINKEDIN::_URL_AUTH . $response[ 'linkedin' ][ 'oauth_token' ] );
          	exit();
        } else {
        	add_action( 'login_form', 'at_invalid_ids', 0 );
			add_action( 'register_form', 'at_invalid_ids', 0 );        	
        }
    } else { 

      	$addthis_linkedin_oauth_token = unserialize( stripslashes( $_COOKIE[ 'addthis_ssi_linkedin' ] ) );
      
        // LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
        $response = $OBJ_linkedin->retrieveTokenAccess( $addthis_linkedin_oauth_token[ 'oauth' ][ 'linkedin' ][ 'request' ][ 'oauth_token' ], $addthis_linkedin_oauth_token[ 'oauth' ][ 'linkedin' ][ 'request' ][ 'oauth_token_secret' ], $_GET[ 'oauth_verifier' ] );
        
	if( $response[ 'success' ] === TRUE ) {
         
        // the request went through without an error, gather user's 'access' tokens
        $addthis_linkedin_oauth_token[ 'oauth' ][ 'linkedin' ][ 'access' ] = $response[ 'linkedin' ];
          
		// set the user as authorized for future quick reference
		$addthis_linkedin_oauth_token[ 'oauth' ][ 'linkedin' ][ 'authorized' ] = TRUE;
          
		$OBJ_linkedin = new LinkedIn( $API_CONFIG );
		$OBJ_linkedin->setTokenAccess( $addthis_linkedin_oauth_token[ 'oauth' ][ 'linkedin' ][ 'access' ] );
		$OBJ_linkedin->setResponseFormat( LINKEDIN::_RESPONSE_XML );         
          
		$response = $OBJ_linkedin->profile('~:(id,first-name,last-name,public-profile-url,picture-url)');
            
		if( $response[ 'success' ] === TRUE ) {
             
			$ln_prof_data = (array) new SimpleXMLElement( $response[ 'linkedin' ] );              
			$_REQUEST[ 'addthis_signature' ] = $ln_prof_data[ 'id' ];
			$_REQUEST[ 'addthis_firstname' ] = $ln_prof_data[ 'first-name' ];
			$_REQUEST[ 'addthis_lastname' ] = $ln_prof_data[ 'last-name' ];
			$_REQUEST[ 'addthis_avatarurl' ] = $ln_prof_data[ 'picture-url' ];
			$_REQUEST[ 'addthis_profileurl' ] = $ln_prof_data[ 'public-profile-url' ];
			
			$_REQUEST[ 'addthis_service' ] = 'linkedin';
								
			$at_ln_redirect = str_replace("?lType=initiate", "", $_COOKIE[ 'addthis_ln_redirect' ] );
			$at_ln_redirect = str_replace("&lType=initiate", "", $at_ln_redirect );
			$_REQUEST[ 'addthis_redirect' ] = $at_ln_redirect;					
			 
			setcookie( "addthis_ln_redirect", "", time()-10, "/");					    
        }         
     }
   }	
}

function addthis_ssi_yahoo() {
	//Handling openid auth
	require_once( 'includes/openid.php' );
	
	$openid = new LightOpenID( $_SERVER[ 'HTTP_HOST' ] );
	
	if( isset( $_REQUEST[ 'yOauth' ] ) && $_REQUEST[ 'yOauth' ] == "initiate" ){		
	
		$openid->identity = 'https://me.yahoo.com';			
	
		$openid->required = array( 'contact/email' , 'namePerson', 'namePerson/friendly', 'media/image/default' );			
		
		header( 'Location: ' . $openid->authUrl() );
		exit();
	} 	

	if( isset( $_GET[ 'openid_mode' ] ) )
	{			 
		$is_yahoo = substr_count( urldecode( $_GET[ 'openid_identity' ] ), "https://me.yahoo.com" );
		
		if( ( $is_yahoo > 0 ) && $openid->validate() )
		{				
			//User logged in
			$y_resp = $openid->getAttributes();
			
			$name = $y_resp[ 'namePerson' ];
			$username = $y_resp[ 'namePerson/friendly' ];
			$email = $y_resp[ 'contact/email' ];
			
			$_REQUEST[ 'addthis_signature' ] = md5( $openid->identity );
			$_REQUEST[ 'addthis_email' ] = $y_resp[ 'contact/email' ];
			
			if ( trim( $y_resp[ 'namePerson' ] ) == '') {
				$names = explode("@", $y_resp[ 'contact/email' ] );					
				$_REQUEST[ 'addthis_firstname' ] = $names[0];
				$_REQUEST[ 'addthis_lastname' ] = '';
			} else {
				$names = explode(" ", $y_resp[ 'namePerson' ] );
				$_REQUEST[ 'addthis_firstname' ] = $names[0];
				$_REQUEST[ 'addthis_lastname' ] = $names[1];
			}
			
			$_REQUEST[ 'addthis_avatarurl' ] = $y_resp[ 'media/image/default' ];
			$_REQUEST[ 'addthis_profileurl' ] = '';
			
			$_REQUEST[ 'addthis_service' ] = 'yahoo';
			
			$at_yh_redirect = str_replace("?yOauth=initiate", "", $_COOKIE[ 'addthis_yh_redirect' ] );
			$at_yh_redirect = str_replace("&yOauth=initiate", "", $at_yh_redirect );
			$_REQUEST[ 'addthis_redirect' ] = $at_yh_redirect;					
			
			setcookie( "addthis_yh_redirect", "", time()-10);				
		}
	}	
}

/*Notice for Invalid AppIds*/  
function at_invalid_ids(){	
	echo '<div class="error"><p>Credentials are not valid.</p></div>';	
}

/*Notification to the existing user who connects the account with any of the social media services. */
function at_exist_user_success(){
	$at_exist_userdata = unserialize( stripslashes( $_COOKIE[ 'at_ssi_exist_userdata' ] ) );
	$at_exist_userdata_service = !empty( $at_exist_userdata["addthis_service"] ) ? $at_exist_userdata["addthis_service"] : $at_exist_userdata["at_custom_service"];
    echo '<div class="updated"><p>You have successfully connected the existing account with the '.ucfirst($at_exist_userdata_service).'.</p></div>'; 
}

function addthis_ssi_insertuser( $user_login = "", $user_email, $firstname = "", $lastname = "", $prof_url = "", $service = "", $signature, $avatar = "" ) {
	
	if( !$user_login ){
		$user_login = strtolower($firstname.$lastname );
	}
	
	if ( username_exists( $user_login ) ) {
		$user_login = $user_login."_".time();
	}

	if( !$user_email ){
		$domain = parse_url( get_site_url() );		
		$user_email = $user_email ? $user_email : strtolower( $firstname.$lastname ).'_'.time().'@'.$_SERVER['HTTP_HOST']; 
	}
				
	if( email_exists( $user_email ) ) {
		setcookie( "at_ssi_exist_userdata", serialize( $_REQUEST ), time()+900, "/" );
		$login_url_email_exist = wp_login_url()."?action=at_email_exist";
		wp_redirect( $login_url_email_exist );
		exit();		
	}

	$at_userdata = array( 'user_login' => $user_login, 'user_email' => $user_email, 'first_name' => $firstname, 'last_name' => $lastname, 'user_url' => $prof_url, 'user_pass' => wp_generate_password() );
		
	if( get_option( 'addthis_default_user_role' ) ) {
		$at_userdata[ 'role' ] = get_option( 'addthis_default_user_role' );
	}

	// Create a new user
	$user_id = wp_insert_user( $at_userdata );

	if ( $user_id && is_integer( $user_id ) ) {
		addthis_update_meta( $user_id, $service, $signature, $avatar );
	}
		
	return $user_id;
}

function addthis_ssi_enablepopup( ) {
	
	echo '<div class="at3lb-light"></div>
<div class="at3win">
	<div class="at3win-wrapper">
		<div class="at3win-header">
			<h3>Confirm Authentication</h3>
			<a class="at3-close" href="javascript:void(0);">X</a>
		</div>
		<div class="at3win-content">
			
			<!-- error! -->
			<div id="at_feedback"></div>

			<p><b>Thank you for authenticating through '.$_REQUEST[ 'addthis_service' ].'.</b></p>
			<p>Please verify that the information below is correct to complete the registration process.</p>

			<table width="100%">
				<tr>
					<td>
						<label for="">First Name</label>
						<input type="text" id ="at_custom_firstname" name="at_custom_firstname" value="'.$_REQUEST[ 'addthis_firstname' ].'"/>
					</td>
					<td>
						<label for="">Last Name</label>
						<input type="text" id="at_custom_lastname" name="at_custom_lastname" value="'.$_REQUEST[ 'addthis_lastname' ].'"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<label for="">User Name <sup>*</sup></label>
						<input type="text" id="at_custom_userlogin" name="at_custom_userlogin" value="'.strtolower(trim($_REQUEST[ 'addthis_firstname' ]).trim($_REQUEST[ 'addthis_lastname' ])).'"/>
					</td>	
				</tr>				
				<tr>
					<td colspan="2">
						<label for="">Your Email <sup>*</sup></label>
						<input type="text" id="at_custom_email" name="at_custom_email" value="'.$_REQUEST[ 'addthis_email' ].'"/>
						<input type="hidden" name="action" value="addthis_check_user_exists"/>
        				<input type="hidden" id="at_custom_signature" name="at_custom_signature" value="'.$_REQUEST[ 'addthis_signature' ].'">
        				<input type="hidden" id="at_custom_service" name="at_custom_service" value="'.$_REQUEST[ 'addthis_service' ].'">
        				<input type="hidden" id="at_custom_avatarurl" name="at_custom_avatarurl" value="'.$_REQUEST[ 'addthis_avatarurl' ].'">
        				<input type="hidden" id="at_custom_profileurl" name="at_custom_profileurl" value="'.$_REQUEST[ 'addthis_profileurl' ].'">
        				<input type="hidden" id="at_custom_redirect" name="at_custom_redirect" value="'.$_REQUEST[ 'addthis_redirect' ].'">
        				<input type="hidden" id="at_custom_submit" name="at_custom_submit" value="">
					</td>	
				</tr>
			</table>
			<p class="req">
				<sup>*</sup> = required
			</p>
			<p>
				<input class="btn-blue" type="button" value="Continue" id="at-continue" name="at-continue"/>
			</p>

		</div>
	</div>	
</div>	
<script type="text/javascript">
				
				setTimeout(function(){
        			jQuery("#at_custom_email").focus();
        		},300);
	 			jQuery("#at-continue").click(ajaxSubmit);
				function ajaxSubmit(){ 
					var loginForm = jQuery("#loginform").serialize();				
					jQuery.ajax({
					type:"POST",
					url: "'.admin_url( 'admin-ajax.php' ).'",
					data: loginForm,
					success:function(data){
					
						if(data != "")
						{
							jQuery("#at_feedback").html(data);
							if (data.indexOf("Username") >= 0) {
								jQuery("#at_custom_userlogin").focus();
							}
							if (data.indexOf("Email") >= 0 || data.indexOf("email") >= 0) {
								jQuery("#at_custom_email").focus();
							}							
						}	
						else
						{
							jQuery("#at_custom_submit").val(true);
							jQuery("#loginform").submit();
						}	
					}
					});	 
					return false;
					}
					
					jQuery(".at3-close").click(function() {
						jQuery(".at3lb-light").css("display", "none");
						jQuery(".at3win").css("display", "none");
					});
					jQuery(".at3win").keydown(function(e) {
	    				if (e.keyCode == 13) {
	        				ajaxSubmit();
	        				return false;
	    				}
					});
</script>';
}

/*check whether a user exists with same email id*/
function addthis_check_user_exists(){

	if ( empty( $_REQUEST['at_custom_userlogin'] ) || trim( $_REQUEST['at_custom_userlogin'] ) == "" || !validate_username( $_REQUEST['at_custom_userlogin'] ) )
		echo '<div class="error-alert">Invalid Username</div>';	
	
	if ( isset( $_REQUEST['at_custom_userlogin'] ) && username_exists( $_REQUEST['at_custom_userlogin'] ) )	
		echo '<div class="error-alert">Username exists</div>';	
	
	if( email_exists( $_REQUEST['at_custom_email'] ) ) {
		
		setcookie( "at_ssi_exist_userdata", serialize( $_REQUEST ), time()+900, "/" );
		echo '<div class="email-exist-alert">The given emailid is already exists. Do you want to connect that account with this social authentication? If so please 
		<a href="'.wp_login_url().'?action=at_email_exist">login</a> to your account with the credentials you already have, we will automatically connect this authentication to your existing account. </div>';
	}	
		
	 if( !eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,4}(\.[a-zA-Z]{2,3})?(\.[a-zA-Z]{2,3})?$', $_REQUEST['at_custom_email'])) {
    	echo '<div class="error-alert">Invalid Email</div>';
	}	

	die();
}

add_action('wp_ajax_nopriv_addthis_check_user_exists', 'addthis_check_user_exists');

function addthis_update_meta( $user_id, $service, $signature, $avatar_url ) {
	
	$at_signature_meta = "addthis_signature_".$service;
	update_user_meta( $user_id, $at_signature_meta, $signature );
	update_user_meta( $user_id, 'addthis_avatarurl', $avatar_url );
}

function addthis_sign_in_process( $user_id, $redirect = "" ) {
		
		wp_set_auth_cookie( $user_id );		
		
		$addthis_ssi_redirect_url = get_option( 'addthis_ssi_redirect_url' );
		
		// Redirect to custom URL if enabled
		if( $addthis_ssi_redirect_url ) {			
			$at_redirect_to = $addthis_ssi_redirect_url;
		} else {

			if( strpos( $redirect, "wp-login" ) ||  strpos( $redirect, "wp-admin" ) ||  $redirect == "" ) {
				$at_redirect_to = admin_url();
			} else {
				$at_redirect_to = $redirect;
				if ( isset( $secure_cookie ) && $secure_cookie ) {					
					$at_redirect_to = preg_replace( '|^http://|', 'https://', $at_redirect_to );
				}
			}		
		}			
	
		wp_redirect( $at_redirect_to );
		exit();
}

function addthis_get_current_url() {

      // check for the correct http protocol (i.e. is this script being served via http or https)
      if( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) {
        $protocol = 'https';
      } else {
        $protocol = 'http';
      }
      
	// set the callback url
	$current_url = $protocol . '://' . $_SERVER[ 'SERVER_NAME' ] . $_SERVER["REQUEST_URI"];
	return $current_url;
}

if ( is_admin() ){

	/* Settings page html code */
	add_action( 'admin_menu', 'addthis_ssi_admin_menu' );

	function addthis_ssi_admin_menu() {
		
		add_options_page( 'AddThis Social Sign In', 'AddThis SSI', 'administrator','addthis-social-sign-in', 'addthis_ssi_html_page' );
	}
}

function addthis_ssi_html_page() {
	
	wp_enqueue_script( 'adminscript', plugins_url('js/admin.js', __FILE__) );
	wp_enqueue_style( 'adminstyles', plugins_url('css/admin-styles.css', __FILE__) );
	
	if ( version_compare( get_bloginfo( 'version' ) , '3.3' , '<' ) )
		wp_head();
	
	require( "views/settings.php" );
}

function addthis_custom_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
	global $comment;

	if( ! empty ( $avatar ) ) {
		if( ! empty ( $id_or_email ) ) {
			if ( is_numeric( $id_or_email ) ) {
				$user_id = ( int ) $id_or_email;
			}
			elseif ( is_string( $id_or_email ) && ( $user = get_user_by( 'email', $id_or_email ) ) ) {
				$user_id = $user->ID;
			}
			elseif ( is_object( $id_or_email ) && ! empty( $id_or_email->user_id ) ) {
				$user_id = ( int ) $id_or_email->user_id;
			}
		} 

		//Check if we are in a comment
		if ( ! is_null ( $comment ) && ! empty ( $comment->user_id ) ) {
			$user_id = $comment->user_id;
		}

		if ( $user_id ) {
			if ( ( $user_thumbnail = get_user_meta ( $user_id, 'addthis_avatarurl', true ) ) !== false ) {
				if ( strlen ( trim ( $user_thumbnail ) ) > 0) {
					$user_thumbnail = preg_replace ( '#src=([\'"])([^\\1]+)\\1#Ui', "src=\\1" . $user_thumbnail . "\\1", $avatar );

					return $user_thumbnail;
				}
			}
		}
	}
	return $avatar;
}

if( get_option('addthis_ssi_thumbnail_enabled') ) {
	add_filter ( 'get_avatar', 'addthis_custom_avatar', 10, 5 );	
}

function get_role_names() {

	global $wp_roles;

	if ( ! isset( $wp_roles ) )
    	$wp_roles = new WP_Roles();

	return $wp_roles->get_names();
}

// Setup our shared resources early
add_action( 'init', 'addthis_ssi_shared', 0 );
function addthis_ssi_shared() {
	
    global $addthis_addjs;
    
    if ( !isset( $addthis_addjs ) ){
        require( 'includes/addthis_addjs.php' );
        
        $addthis_options = get_option( 'addthis_settings' );
        $addthis_addjs = new AddThis_addjs( $addthis_options );
    } elseif ( !method_exists( $addthis_addjs, 'getAtPluginPromoText' ) ){
        require( 'includes/addthis_addjs_extender.php' );
        $addthis_addjs = new AddThis_addjs_extender( $addthis_options );
    }
}

function addthis_ssi_remove() {
	
	/* Deletes the database field */	
	delete_option( 'addthis_ssi_fbid' );
	delete_option( 'addthis_ssi_twkey' );
	delete_option( 'addthis_ssi_tw_secret' );
	delete_option( 'addthis_ssi_googleid' );
	delete_option( 'addthis_ssi_linkedin_key' );
	delete_option( 'addthis_ssi_linkedin_secret' );
	delete_option( 'addthis_ssi_yahoo_enabled' );
		
	delete_option( 'addthis_default_user_role' );
	
	delete_option( 'addthis_ssi_redirect_url' );
	
	delete_option( 'addthis_ssi_welcome_enabled' );
	delete_option( 'addthis_ssi_thumbnail_enabled' );
	delete_option( 'addthis_ssi_button_text' );
	delete_option( 'addthis_ssi_popup_enabled' );
	delete_option( 'addthis_ssi_email_exist_text' );
}

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'addthis_ssi_remove' );

/* 2.9 compatability functions*/
if (! function_exists( 'get_site_url' ) )
{
	function get_site_url( $blog_id = null, $path = '', $scheme = null ) {

		$orig_scheme = $scheme;
		if ( !in_array( $scheme, array( 'http', 'https', 'relative' ) ) ) {
			if ( ( 'login_post' == $scheme || 'rpc' == $scheme ) && ( force_ssl_login() || force_ssl_admin() ) )
				$scheme = 'https';
			elseif ( ( 'login' == $scheme ) && force_ssl_admin() )
				$scheme = 'https';
			elseif ( ( 'admin' == $scheme ) && force_ssl_admin() )
				$scheme = 'https';
			else
				$scheme = ( is_ssl() ? 'https' : 'http' );
		}
	
		if ( empty( $blog_id ) || !is_multisite() )
			$url = get_option( 'siteurl' );
		else
			$url = get_blog_option( $blog_id, 'siteurl' );
	
		if ( 'relative' == $scheme )
			$url = preg_replace( '#^.+://[^/]*#', '', $url );
		elseif ( 'http' != $scheme )
			$url = str_replace( 'http://', "{$scheme}://", $url );
	
		if ( !empty( $path ) && is_string( $path ) && strpos( $path, '..' ) === false )
			$url .= '/' . ltrim( $path, '/' );
	
		return apply_filters( 'site_url', $url, $path, $orig_scheme, $blog_id );
	}
}

if (! function_exists( 'update_user_meta' ) )
{
	function update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_metadata( 'user', $user_id, $meta_key, $meta_value, $prev_value );
	}
}

if (! function_exists( 'get_user_meta' ) )
{
	function get_user_meta( $user_id, $key = '', $single = false ) {
		return get_metadata( 'user', $user_id, $key, $single );
	}
}
?>