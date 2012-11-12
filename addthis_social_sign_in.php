<?php
   /*
   Plugin Name: AddThis Social Sign In
   Plugin URI: http://www.addthis.com/
   Description: A lightweight javascript plugin helps users to sign in to the wordpress site using social media services.
   Version: 1.0.1
   Author: AddThis Team
   Author URI: http://www.addthis.com
   License: Apache
   */
?>
<?php
function addthis_ssi_activate(){ 
	
	if ( version_compare( get_bloginfo( 'version' ) , '2.9' , '<' )){
		deactivate_plugins( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
		wp_die( sprintf( __( "Require wordpress greater than 2.9")));	
	}
		
	add_option("addthis_ssi_fbid", '', '', 'yes');
	add_option("addthis_ssi_twkey", '', '', 'yes');
	add_option("addthis_ssi_googleid", '', '', 'yes');
	
	add_option("addthis_default_user_role", '', '', 'yes');
	
	add_option("addthis_ssi_redirect_enabled", '', '', 'yes');
	add_option("addthis_ssi_redirect_url", '', '', 'yes');

	add_option("addthis_ssi_welcome_enabled", '', '', 'yes');
	add_option("addthis_ssi_thumbnail_enabled", '', '', 'yes');	
}

register_activation_hook( __FILE__, 'addthis_ssi_activate' );

function addthis_ssi_render_buttons( $tmpl_mode = false ){
	
	global $addthis_addjs;
	
	wp_enqueue_style( 'frontendstyles', plugins_url('css/frontend-styles.css', __FILE__) );
	if ( version_compare( get_bloginfo( 'version' ) , '3.3' , '<' ))
		wp_head();
			
	echo '<div class="addthis_toolbox">
			<a class="addthis_login_facebook"></a>
			<a class="addthis_login_twitter"></a>
			<a class="addthis_login_google"></a>
		</div>';
	
	if( $tmpl_mode == true ){
		echo '<form method="post" action="'.get_bloginfo('wpurl').'/wp-login.php" id="loginform" name="loginform">';
		addthis_ssi_render_fields();
		echo '</form>';
	}
	
	$addthis_ssi_config =  '
var addthis_config = {
        login:{
                services:{
                        facebook:{
                                appId:"'.get_option('addthis_ssi_fbid').'",scope:"email"
                        },
                        twitter:{
                                appKey:"'.get_option('addthis_ssi_twkey').'"
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
                        document.getElementById("loginform").submit();
                }
        }
};';

$addthis_addjs->addAfterScript( $addthis_ssi_config );
$addthis_addjs->output_script();

}

add_action( 'login_form', 'addthis_ssi_render_buttons' );

function addthis_ssi_render_fields(){
	
	echo '<input type="hidden" id="addthis_signature" name="addthis_signature" value="">
<input type="hidden" id="addthis_firstname" name="addthis_firstname" value="">
<input type="hidden" id="addthis_lastname" name="addthis_lastname" value="">
<input type="hidden" id="addthis_email" name="addthis_email" value="">
<input type="hidden" id="addthis_profileurl" name="addthis_profileurl" value="">
<input type="hidden" id="addthis_avatarurl" name="addthis_avatarurl" value="">';	
}

add_action( 'login_form', 'addthis_ssi_render_fields' );

function addthis_ssi(){
	
	if ( is_user_logged_in() ) {
		
		if( get_option('addthis_ssi_welcome_enabled') ) {
		
			global $current_user;
				
        	get_currentuserinfo();
        	echo get_avatar( $current_user->ID, 30 ).'&nbsp;&nbsp;Hi&nbsp;'.$current_user->first_name;
		}			
	} else {	
		addthis_ssi_render_buttons( true );
	}
}

function addthis_ssi_getuser( $signature ) {
		
	global $wpdb;
	$sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s'";
	$user_id = $wpdb->get_var( $wpdb->prepare( $sql, 'addthis_signature', $_REQUEST[ 'addthis_signature' ] ) );
	return $user_id;	
}

function addthis_social_sign_in() {
	
	if ( version_compare( get_bloginfo( 'version' ) , '3.1' , '<' ))
		require_once(ABSPATH . WPINC . '/registration.php');
	 
	if( $_REQUEST[ 'addthis_signature' ] != "" ) {

		$user_id = addthis_ssi_getuser( $_REQUEST[ 'addthis_signature' ] );
		
		$domain = parse_url( get_site_url() );
		
		$user_email = $_REQUEST[ 'addthis_email' ] ? $_REQUEST[ 'addthis_email' ] : strtolower( $_REQUEST[ 'addthis_firstname' ].$_REQUEST[ 'addthis_lastname' ] ).'_'.time().'@'.$domain['host'];                                     
		
		if( $user_id ) {
			$user_data  = get_userdata( $user_id );
			$user_login = $user_data->user_login;
		} elseif( $user_id = email_exists( $user_email ) ) {
			if ( $user_id && is_integer( $user_id ) ) {
				update_user_meta( $user_id, 'addthis_signature', $_REQUEST[ 'addthis_signature' ] );
				update_user_meta( $user_id, 'addthis_avatarurl', $_REQUEST[ 'addthis_avatarurl' ] );
			}				
		} else {			
			$user_login = strtolower( str_replace(' ', '', $_REQUEST[ 'addthis_firstname' ]).str_replace(' ', '', $_REQUEST[ 'addthis_lastname' ]) );
			
			if ( username_exists( $user_login ) )
				$user_login = $user_login."_".time();	
			
			$userdata = array( 'user_login' => $user_login, 'user_email' => $user_email, 'first_name' => $_REQUEST[ 'addthis_firstname' ], 'last_name' => $_REQUEST[ 'addthis_lastname' ], 'user_url' => $_REQUEST[ 'addthis_profileurl' ], 'user_pass' => wp_generate_password() );
			
			if( get_option('addthis_default_user_role') ) {
				$userdata['role'] = get_option('addthis_default_user_role');
			}
			
			// Create a new user
			$user_id = wp_insert_user( $userdata );

			if ( $user_id && is_integer( $user_id ) ) {
				update_user_meta( $user_id, 'addthis_signature', $_REQUEST[ 'addthis_signature' ] );
				update_user_meta( $user_id, 'addthis_avatarurl', $_REQUEST[ 'addthis_avatarurl' ] );
			}
			
		}
		
		wp_set_auth_cookie( $user_id );
		
		$addthis_ssi_redirect_enabled = get_option('addthis_ssi_redirect_enabled');
		$addthis_ssi_redirect_url = get_option('addthis_ssi_redirect_url');
		
		// Redirect to custom URL if enabled
		if( $addthis_ssi_redirect_enabled &&  $addthis_ssi_redirect_url ) {			
			$redirect_to = $addthis_ssi_redirect_url;
		} else {		
			// Redirect to request page if redirect_to is set
			if ( isset( $_REQUEST[ 'redirect_to' ] ) && $_REQUEST[ 'redirect_to' ] != '' ) {
				$redirect_to = $_REQUEST[ 'redirect_to' ];
				// Redirect to https if user wants ssl
				if ( isset( $secure_cookie ) && $secure_cookie && false !== strpos( $redirect_to, 'wp-admin') )
					$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
			} else {
				$redirect_to = admin_url();
			}
		}
			
		wp_redirect( $redirect_to );
		exit();
	}		
}

add_action( 'init', 'addthis_social_sign_in' );


if ( is_admin() ){

	/* Settings page html code */
	add_action( 'admin_menu', 'addthis_ssi_admin_menu' );

	function addthis_ssi_admin_menu() {
		
		add_options_page('AddThis Social Sign In', 'AddThis SSI', 'administrator','addthis-social-sign-in', 'addthis_ssi_html_page');
	}
}

function addthis_ssi_html_page() {
	
	wp_enqueue_script('adminscript', plugins_url('js/admin.js', __FILE__));
	wp_enqueue_style( 'adminstyles', plugins_url('css/admin-styles.css', __FILE__) );
	
	if ( version_compare( get_bloginfo( 'version' ) , '3.3' , '<' ))
		wp_head();
	
	require("views/settings.php");
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
        require('includes/addthis_addjs.php');
        
        $addthis_options = get_option('addthis_settings');
        $addthis_addjs = new AddThis_addjs( $addthis_options );
    } elseif ( !method_exists( $addthis_addjs, 'getAtPluginPromoText' ) ){
        require('includes/addthis_addjs_extender.php');
        $addthis_addjs = new AddThis_addjs_extender( $addthis_options );
    }
}

function addthis_ssi_remove() {
	
	/* Deletes the database field */
	
	delete_option('addthis_ssi_fbid');
	delete_option('addthis_ssi_twkey');
	delete_option('addthis_ssi_googleid');
	
	delete_option('addthis_default_user_role');
	
	delete_option('addthis_ssi_redirect_enabled');
	delete_option('addthis_ssi_redirect_url');
	
	delete_option('addthis_ssi_welcome_enabled');
	delete_option('addthis_ssi_thumbnail_enabled');
}

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'addthis_ssi_remove' );

/* 2.9 compatability functions*/
if (! function_exists('get_site_url'))
{
	function get_site_url( $blog_id = null, $path = '', $scheme = null ) {
		// should the list of allowed schemes be maintained elsewhere?
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

if (! function_exists('update_user_meta'))
{
	function update_user_meta($user_id, $meta_key, $meta_value, $prev_value = '') {
		return update_metadata('user', $user_id, $meta_key, $meta_value, $prev_value);
	}
}

if (! function_exists('get_user_meta'))
{
	function get_user_meta($user_id, $key = '', $single = false) {
		return get_metadata('user', $user_id, $key, $single);
	}
}

?>
