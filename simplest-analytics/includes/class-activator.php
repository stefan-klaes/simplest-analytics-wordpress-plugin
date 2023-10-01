<?php

/**
 * Fired during plugin activation
 *
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 */
class Simplest_Analytics_Activator {

	/**
	 * set up options necessary for functionality of this plugin to wp_options
	 */
	public static function activate() {

		// create webanalytics table
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}simplest_analytics` (
			id bigint(50) NOT NULL AUTO_INCREMENT,
			track_type varchar(255) NOT NULL,
			website varchar(255) NOT NULL,
			referrer varchar(255) NOT NULL,
			parameters varchar(255) NOT NULL,
			event_action varchar(255) NOT NULL,
			session_id varchar(255) NOT NULL,
			date_full DATETIME DEFAULT CURRENT_TIMESTAMP,
			date_year varchar(255) NOT NULL,
			date_month varchar(255) NOT NULL,
			date_week varchar(255) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);



		/**
		 * Set Up Plugin Version
		 */
		 
		 update_option( 'simplest_analytics_version', '1.2.0' );

		 /**
		 * Set Up tracking url parameter
		 */

		// check if simplest_analytivs_url_para is already set
		$para_options = get_option('simplest_analytivs_url_para');
		if ( !isset($para_options) ) {
			$example_para["campaign"] = "Campaign Name";
			update_option( 'simplest_analytivs_url_para', $example_para );
		}

		
	}

}
