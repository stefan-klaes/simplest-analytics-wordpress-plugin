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
			'admin_start_page',
			'dashicons-chart-bar' );
			
		function admin_start_page(){
				// require file that displays the admin settings page
				require_once plugin_dir_path( __FILE__ ) . 'dashboard.php';					
		}
			
			
		add_submenu_page( 
			'simplest-analytics',
			__( 'Settings', 'simplest-analytics' ),
			__( 'Settings', 'simplest-analytics' ),
			'manage_options',
			'simplest-analytics-settings',
			'admin_structure' );
			
			
		function admin_structure(){
				// require file that displays the admin settings page
				require_once plugin_dir_path( __FILE__ ) . 'structure.php';					
		}
		
		
		
	}
	

	
	 

	



} // class END