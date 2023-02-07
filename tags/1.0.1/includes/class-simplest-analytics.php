<?php

/**
 * The core plugin class.
 */
class Simplest_Analytics {

	// The loader that's responsible for maintaining and registering all hooks that power the plugin.
	protected $loader;

	// The unique identifier of this plugin.
	protected $simplest_analytics;

	// The current version of the plugin.
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the admin-option-settings-facing side of the site.
	 *
	 */
	public function __construct() {
		if ( defined( 'SIMPLEST_ANALYTICS_VERSION' ) ) {
			$this->version = SIMPLEST_ANALYTICS_VERSION;
		} else {
			$this->version = '1.0.1';
		}
		$this->simplest_analytics = 'simplest-analytics';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}


	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Simplest_Analytics_Loader. Orchestrates the hooks of the plugin.
	 * - Simplest_Analytics_i18n. Defines internationalization functionality.
	 * - Simplest_Analytics_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 */
	private function load_dependencies() {

		// The class responsible for orchestrating the actions and filters of the core plugin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-loader.php';

		// The class responsible for defining internationalization functionality of the plugin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-i18n.php';

		// The class responsible for defining all actions that occur in the admin area.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin.php';

		// The class responsible for defining all actions that occur in public website.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-public.php';
		
		
		$this->loader = new Simplest_Analytics_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Simplest_Analytics_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 */
	private function set_locale() {

		$plugin_i18n = new Simplest_Analytics_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}


	
	

	// Register all of the hooks related to the admin and woocommerce order area functionality of the plugin.
	private function define_admin_hooks() {

		$plugin_admin = new Simplest_Analytics_Admin( $this->get_simplest_analytics(), $this->get_version() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );

		
		$this->loader->add_action( 'wp_ajax_simplest_analytics_structure_save_action', $plugin_admin, 'simplest_analytics_structure_save_action' );		

		// load css and js only when needed
		$current_page = sanitize_url($_SERVER['REQUEST_URI']);
		
		if ( strpos($current_page,'simplest-analytics') > 0 || 1 == 1) {
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		}
		
	}


	// Register all of the hooks related to the public website.
	private function define_public_hooks() {

		$plugin_public = new Simplest_Analytics_Public( $this->get_simplest_analytics(), $this->get_version() );

		// tracking ajax action to store data in database
		$this->loader->add_action( 'wp_ajax_nopriv_simplest_analytics_tracking_action', $plugin_public, 'simplest_analytics_tracking_action' );		
		$this->loader->add_action( 'wp_ajax_simplest_analytics_tracking_action', $plugin_public, 'simplest_analytics_tracking_action' );		

		// tracking in wc thank you page
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'tracking_wc_ty_page' );		

		
		// tracking in footer
		$this->loader->add_action( 'wp_footer', $plugin_public, 'tracking_in_footer' );
		
		
	}
	

	
	 

	// Run the loader to execute all of the hooks with WordPress.
	public function run() {
		$this->loader->run();
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 */
	public function get_simplest_analytics() {
		return $this->simplest_analytics;
	}


	// The reference to the class that orchestrates the hooks with the plugin.
	public function get_loader() {
		return $this->loader;
	}


	// Retrieve the version number of the plugin.
	public function get_version() {
		return $this->version;
	}

	

}
