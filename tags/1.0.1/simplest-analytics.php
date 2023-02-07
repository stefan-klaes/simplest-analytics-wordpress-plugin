<?php

/**
 * Welcome to Simplest Analytics
 *
 *
 * @link              https://www.coden-lassen.de
 * @since             1.0.1
 * @package           Simplest_Analytics
 *
 * @wordpress-plugin
 * Plugin Name:       Simplest Analytics
 * Plugin URI:        https://www.coden-lassen.de
 * Description:       Serverside and cookieless webanalytics.
 * Version:           1.0.1
 * Author:            Stefan Klaes
 * Author URI:        https://www.coden-lassen.de/wordpress-freelancer
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simplest-anlytics
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}




/**
 * Currently plugin version:
 * version 1.0.1
 */
 
define( 'SIMPLEST_ANALYTICS_VERSION', '1.0.1' );




/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
 
function activate_simplest_analytics() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	Simplest_Analytics_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_simplest_analytics' );



/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
 
function deactivate_simplest_analytics() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	Simplest_Analytics_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_simplest_analytics' );


/**
 * The code that runs if plugin will be uninstalled.
 * This action is documented in includes/class-uninstall.php
 */
function uninstall_simplest_analytics() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-uninstall.php';
	Simplest_Analytics_Uninstaller::uninstall();
}
register_uninstall_hook( __FILE__, 'uninstall_simplest_analytics' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
 
require plugin_dir_path( __FILE__ ) . 'includes/class-simplest-analytics.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Check if Woocommerce is activ
 */
 


	
$plugin = new Simplest_Analytics();
$plugin->run();
	