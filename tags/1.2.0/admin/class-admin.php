<?php

/**
 *
 * The admin-specific functionality of the plugin.
 *
 */
 
 
class Simplest_Analytics_Admin {

	/** The ID of this plugin */
	private $simplest_analytics;

	/**  The version of this plugin */
	private $version;
	
	
	/** Initialize the class and set its properties. */
	public function __construct( $simplest_analytics, $version ) {
		
		$this->simplest_analytics = $simplest_analytics;
		$this->version = $version;

	}
	

	
	
	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->simplest_analytics, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area including simplest_analytics_ajax_url and simplest_analytics_ajax_nonce
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'google-charts', 'https://www.gstatic.com/charts/loader.js' );

		wp_enqueue_script( $this->simplest_analytics, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
		
		wp_register_script( $this->simplest_analytics, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
		
		wp_localize_script( $this->simplest_analytics, 'ajax_object', 
		array( 
			'simplest_analytics_ajax_url' => admin_url( 'admin-ajax.php' ),
			'simplest_analytics_ajax_nonce' => wp_create_nonce( 'secure_simplest_analytics' ) 
		) );
		
		
		
	}
	
	

	
	

	/**
	 * add amnin menu
	 */
	public function add_admin_menu() {
		
		add_menu_page(
			__( 'Webanalytics', 'simplest-analytics' ),
			__( 'Webanalytics', 'simplest-analytics' ),
			'manage_options',
			'simplest-analytics',
			array( $this, 'admin_start_page' ),
			'dashicons-chart-bar' );
			
			
		add_submenu_page( 
			'simplest-analytics',
			__( 'Settings', 'simplest-analytics' ),
			__( 'Settings', 'simplest-analytics' ),
			'manage_options',
			'simplest-analytics-settings',
			array( $this, 'admin_structure' ) );	
		
		
	}

	public function admin_start_page(){
		// require file that displays the admin settings page
		require_once plugin_dir_path( __FILE__ ) . 'dashboard.php';					
	}

	public function admin_structure(){
		// require file that displays the admin settings page
		require_once plugin_dir_path( __FILE__ ) . 'structure.php';					
	}


	/**
	 * clear database entries via ajax
	 */
	public function simplest_analytics_clear_db() {

		// check if user is allowed to do this
		if ( ! current_user_can( 'manage_options' ) ) {
			?>alert:<?php echo __( 'You are not allowed to do this. Please ask admin to upgrade your role.', 'simplest-analytics' );
			wp_die();
		}

		// check nonce: security check
		if ( check_ajax_referer( 'simplest_analytics_clear_nonce', 'nonce', false ) === false  ) {
			?>alert:<?php echo __( 'Security error: nonce not valid. please refresh the page.', 'simplest-analytics' );
			wp_die();
		}

	
		// clear type 
		$clear = isset( $_POST["clear"] ) ? sanitize_text_field( $_POST["clear"] ) : "";
		if ( $clear == ''  ) {
			?>alert:<?php echo __( 'Clear type missing. Please refresh page and try again.', 'simplest-analytics' );
			wp_die();
		}


		// request passes all tests - start clearing

		global $wpdb;
		$table_name = $wpdb->prefix . 'simplest_analytics';

		// clear all
		if ( $clear == 'all' ) {
			$sql = $wpdb->prepare( "SELECT COUNT(*) AS total_entries FROM $table_name" );
			$total_entries = $wpdb->get_var( $sql );

			if ( $total_entries == 0 ) {
				?>alert:<?php echo __( 'No entries to delete found.', 'simplest-analytics' );
				wp_die();
			}
			
			$sql = $wpdb->prepare( "DELETE FROM $table_name" );
			$wpdb->query( $sql );
			
			$success_message = sprintf( __( 'Success, %d entries deleted', 'simplest-analytics' ), $total_entries );
			echo esc_html( $success_message );
			wp_die();
		}

		// clear older than this year
		if ( $clear == 'older_than_year' ) {
			// get values, that are not in current year
			$current_year = date("Y");
			$sql = $wpdb->prepare( "SELECT COUNT(*) AS total_entries FROM $table_name WHERE YEAR(date_full) != %d", $current_year );
			$total_entries = $wpdb->get_var( $sql );

			if ( $total_entries == 0 ) {
				?>alert:<?php echo __( 'No entries to delete found.', 'simplest-analytics' );
				wp_die();
			}

			$sql = $wpdb->prepare( "DELETE FROM $table_name WHERE YEAR(date_full) != %d", $current_year );
			$wpdb->query( $sql );
			
			$success_message = sprintf( __( 'Success, %d entries deleted', 'simplest-analytics' ), $total_entries );
			echo esc_html( $success_message );
			wp_die();
		}


		// clear older than date
		if ( $clear == 'older_than_date' ) {

			// get date 
			$date = isset( $_POST["date"] ) ? sanitize_text_field( $_POST["date"] ) : "";
			if ( $date == ''  ) {
				?>alert:<?php echo __( 'Please select a date.', 'simplest-analytics' );
				wp_die();
			}

			// get values older than the date
			$sql = $wpdb->prepare( "SELECT COUNT(*) AS total_entries FROM $table_name WHERE date_full < %s", $date );
			$total_entries = $wpdb->get_var( $sql );
		
			if ( $total_entries == 0 ) {
				?>alert:<?php echo __( 'No entries to delete found.', 'simplest-analytics' );
				wp_die();
			}
		
			$sql = $wpdb->prepare( "DELETE FROM $table_name WHERE date_full < %s", $date );
			$wpdb->query( $sql );
			
			$success_message = sprintf( __( 'Success, %d entries deleted', 'simplest-analytics' ), $total_entries );
			echo esc_html( $success_message );
			wp_die();
		}
		
		// in case nothing matches
		?>alert:<?php echo __( 'Unexpected error, please reload the page and try again.', 'simplest-analytics' );
		wp_die();

	}
	

	

} // class END