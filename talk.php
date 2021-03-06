<?php
/**
 * Plugin Name: Coral Project Talk - Daily Maverick
 * Plugin URI: https://coralproject.net
 * Description: A plugin to replace stock WP commenting with Talk from the Coral Project
 * Version: 0.1.0
 * Author: Alley Interactive, The Coral Project, Jason Norwood-Young
 * Author URI: https://www.alleyinteractive.com, https://10layer.com
 * License: Apache 2.0
 *
 * @package Talk_Plugin
 */

define( 'CORAL_PROJECT_TALK_DIR', dirname( __FILE__ ) );
require("vendor/autoload.php");
use Firebase\JWT\JWT;

/**
 * Class Talk_Plugin
 */
class Talk_Plugin {
	static $key = "0C945O*iPo9^";
	static $talk_mode = "testing";
	static $talk_test_article = "curatorially-speaking-heres-how-to-start-your-very-own-art-collection";
	/**
	 * Talk_Plugin constructor.
	 */
	public function __construct() {
		require_once( CORAL_PROJECT_TALK_DIR . '/inc/helper-functions.php' );
		require_once( CORAL_PROJECT_TALK_DIR . '/inc/class-talk-settings-page.php' );
		require_once( CORAL_PROJECT_TALK_DIR . '/inc/class-talk-default-comments-settings.php' );
		add_filter( 'comments_template', function( $default_template_path ) {
			return coral_talk_plugin_is_usable() ?
				coral_talk_get_comments_template_path() :
				$default_template_path;
		}, 99 );
		add_action( 'admin_notices', function() {
			if ( ! coral_talk_plugin_is_usable() ) {
				coral_talk_print_admin_notice(
					'warning',
					__( 'The Base URL is required in %sCoral Talk settings%s', 'coral-project-talk' )
				);
			}
		} );
		add_action( 'show_user_profile', 'talk_user_id_field' );
		add_action( 'edit_user_profile', 'talk_user_id_field' );
	}
}

/**
 * Assuming that the plugin is active (otherwise this function won't be available)
 * determine if the required Talk instance URL option is set
 *
 * @since 0.0.2
 * @return bool
 */
function coral_talk_plugin_is_usable() {
	$talk_url = get_option( 'coral_talk_base_url' );
	return ! empty( $talk_url );
}

/**
 * Get absolute path to comments template file
 *
 * @since 0.0.2
 * @return string File path
 */
function coral_talk_get_comments_template_path() {
	return ( CORAL_PROJECT_TALK_DIR . '/inc/comments-template.php' );
}

/**
 * Template tag to render the Coral Talk template without the performance hit of
 * filtering comments_template()
 *
 * @since 0.0.1
 */
function coral_talk_comments_template($user_id = false) {
	if ($user_id === false) {
		ob_start();
		require( coral_talk_get_comments_template_path() );
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
	}
	$talk_url = get_option( 'coral_talk_base_url' );
	$user = get_userdata($user_id);
	$last_name = get_user_meta(get_current_user_id(), 'last_name', true);
	$first_name = get_user_meta(get_current_user_id(), 'first_name', true);
	$name = $user->data->display_name;
	if (!empty($first_name) && !empty($last_name)) {
		$name = $first_name . " " . $last_name;
	}
	if (strpos($name, "@") !== false) {
		$name = ucfirst(substr($name, 0, strpos($name, "@")));
	}
	$token = array(
		"jti" => uniqid(),
		"exp" => time() + (7 * 24 * 60 * 60),
		"iss" => $talk_url,
		"aud" => "talk",
		"sub" => "wordpress-" . $user_id,
		"name" => $name,
		"email" => $user->data->user_email
	);
	// print "<pre>";
	// print_r($token);
	// print "</pre>";
	$auth_token = JWT::encode($token, Talk_Plugin::$key);
	ob_start();
	require( coral_talk_get_comments_template_path() );
	$s = ob_get_contents();
	ob_end_clean();
	return $s;
}

/**
 * Construct asset_id based on current post type and ID.
 * Must be used inside The Loop.
 *
 * @return string asset_id
 * @since 0.0.2
 */
function coral_talk_get_asset_id() {
	return get_post_type() . '-' . get_the_ID();
}

function talk_user_id_field( $user ) {
	$talk_id = get_the_author_meta( 'talk_id', $user->ID );
	?>
	<h3><?php esc_html_e( 'Talk ID', 'crf' ); ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="year_of_birth"><?php esc_html_e( 'Talk ID', 'crf' ); ?></label></th>
			<td>
				<input type="text"
			       value="<?php echo esc_attr( $talk_id ); ?>"
			       class="regular-text"
				/>
			</td>
		</tr>
	</table>
	<?php
}

function talk_embed() {
	if (Talk_Plugin::$talk_mode == "testing") {
		global $post;
    	$post_slug = $post->post_name;
		if ($post_slug !== Talk_Plugin::$talk_test_article) {
			return "";
		}
		// $user = get_userdata(get_current_user_id());
		// $user_meta = get_user_meta(get_current_user_id());
		// print "<pre>";
		// print_r($user);
		// print_r($user_meta);
		// print "</pre>";
	}
	$s = "<h3>Comments</h3>\n";
	$user_id = get_current_user_id();
	if (!empty($user_id)) {
	    if (function_exists( 'wc_memberships' )) {
	        if (isDMMembershipActive()) {
	                $s .= coral_talk_comments_template($user_id);
	        } else {
				$s .= "<p>Please note you must be a <a href='/insider/?utm_source=DM_Website&utm_medium=Comments'>Maverick Insider</a> to comment. <a href='/insider/?utm_source=DM_Website&utm_medium=Comments'>Sign up here</a>.</p>\n";
	            $s .= coral_talk_comments_template();
	        }
	    }
	} else {
		$s .= "<p>Please <a href='#' class='login-alert triggeredFromLoginPopup'>sign in</a> or <a href='/create-account/'>create an account</a> to view the comments. To join the conversation, sign up as a <a href='/insider/?utm_source=DM_Website&utm_medium=Comments'>Maverick Insider</a>.</p>\n";
	}
	return $s;
}

new Talk_Plugin;
