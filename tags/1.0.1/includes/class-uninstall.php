<?php

/**
 * Fired during plugin uninstallation
 *
 *
 * This class defines all code necessary to run during the plugin's uninstallation.
 *
 */
class Simplest_Analytics_Uninstaller {

	/**
	 * set up options necessary for functionality of this plugin to wp_options
	 * 
	 */
	public static function uninstall() {

        global $wpdb;
        $table_name = $wpdb->prefix . 'simplest_analytics';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
        
		// delete all plugin created options
        delete_option('simplest_analytics_version');
		delete_option('simplest_analytivs_url_para');
		delete_option('simplest_analytivs_events');
		

	}

}
