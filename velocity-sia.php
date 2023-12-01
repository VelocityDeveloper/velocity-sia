<?php

/**
 * The plugin SIA (Sistem Informasi Akademik) for Velocity Developer
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/velocitydeveloper/velocity-sia
 * @since             1.0.0
 * @package           velocity-sia
 *
 * @wordpress-plugin
 * Plugin Name:       Velocity SIA
 * Plugin URI:        https://velocitydeveloper.com/
 * Description:       Plugin SIA (Sistem Informasi Akademik) by Velocity Developer
 * Version:           1.0.0
 * Author:            Velocity Developer
 * Author URI:        https://velocitydeveloper.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       velocity-sia
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Define constants
 *
 * @since 1.2.0
 */
if (!defined('VELOCITY_SIA_PLUGIN_DIR'))	define('VELOCITY_SIA_PLUGIN_DIR', plugin_dir_path(__FILE__)); // Plugin directory absolute path with the trailing slash. Useful for using with includes eg - /var/www/html/wp-content/plugins/velocity-sia/
if (!defined('VELOCITY_SIA_PLUGIN_DIR_URI'))	define('VELOCITY_SIA_PLUGIN_DIR_URI', plugin_dir_url(__FILE__)); // URL to the plugin folder with the trailing slash. Useful for referencing src eg - http://localhost/wp/wp-content/plugins/velocity-sia/

// Load everything
$includes = [
	'inc/fungsi.php',			// fungsi
	'inc/ajax.php',				// ajax
	'inc/enqueue.php',			// register css and js
	'inc/shortcode.php',		// shortcode
];
foreach ($includes as $include) {
	require_once(VELOCITY_SIA_PLUGIN_DIR.$include);
}

/**
 * The code that runs during plugin activation.
 * This action is documented in classes/class-velocity-sia-activator.php
 */
function activate_velocity_sia()
{
	require_once VELOCITY_SIA_PLUGIN_DIR . 'classes/class-velocity-sia-activator.php';
	Velocity_SIA_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in classes/class-velocity-sia-deactivator.php
 */
function deactivate_velocity_sia()
{
	require_once VELOCITY_SIA_PLUGIN_DIR . 'classes/class-velocity-sia-deactivator.php';
	Velocity_SIA_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_velocity_sia');
register_deactivation_hook(__FILE__, 'deactivate_velocity_sia');


//mulai session
function velv_sesi() {
	if( !session_id() ) {
		session_start();
	}
}
add_action('init', 'velv_sesi');


// remove admin bar
add_filter( 'show_admin_bar' , 'velocity_sia_admin_bar');
function velocity_sia_admin_bar() {
	if (current_user_can('mahasiswa') || current_user_can('pending') || current_user_can('dosen') || wp_get_current_user()->user_login == "admindemo") {		
		return false;
	} else {		
		return true;
	}
}


// Limit media library access
add_filter( 'ajax_query_attachments_args', 'wpb_show_current_user_attachments' );
function wpb_show_current_user_attachments( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts
') ) {
        $query['author'] = $user_id;
    }
    return $query;
}



//register page template
add_filter( 'template_include', 'velocitysia_register_page_template' );
function velocitysia_register_page_template( $template ) {
    if ( is_singular() ) {
        $page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
        if ( 'velocity-sia-template' === $page_template ) {
            $template = VELOCITY_SIA_PLUGIN_DIR . 'inc/page-sia.php';
		}
    }
    return $template;
}
function velocitysia_templates_page($post_templates) {
    $post_templates['velocity-sia-template'] = __( 'Velocity SIA', 'velocity-sia' );
    return $post_templates;
}
add_filter( "theme_page_templates", 'velocitysia_templates_page' );

// Create Page
function create_page_elearning() {
	$post_id = -1;
	$slug = 'sia';
	$title = 'SIA';
	if( null == get_page_by_title( $title ) ) {
		$post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	'1',
				'post_name'			=>	$slug,
				'post_title'		=>	$title,
				'post_status'		=>	'publish',
				'post_type'			=>	'page',
				'page_template'		=> 'velocity-sia-template',
			)
		);
	} else {
    	$post_id = -2;
	}
}
add_filter( 'after_setup_theme', 'create_page_elearning' );
