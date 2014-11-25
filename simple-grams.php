<?php
/*
Plugin Name:     Simple Grams
Plugin URI:      https://github.com/kiriaze/simple-grams
Description:     Provides a shortcode and widget for displaying your most recent Instagrams. [grams account="" count="" tag="" class="" effects=""]
Version:         1.0.0
Author:          Constantine Kiriaze (@kiriaze)
Author URI:      http://getsimple.io/about
License:         GNU General Public License v2 or later
License URI:     http://www.gnu.org/licenses/gpl-2.0.html
Copyright:       (c) 2013, Constantine Kiriaze
Text Domain:     simple
GitHub Plugin URI: https://github.com/kiriaze/simple-grams
GitHub Branch:     master
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-simple-grams.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Simple_Grams', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Simple_Grams', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'Simple_Grams', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-admin.php` with the name of the plugin's admin file
 * - replace Plugin_Name_Admin with the name of the class defined in
 *   `class-simple-grams-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
// if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

// 	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-simple-grams-admin.php' );
// 	add_action( 'plugins_loaded', array( 'Simple_Grams_Admin', 'get_instance' ) );

// }