<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 */

defined('ABSPATH') || exit;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 * @author     Level5 <support@level5.se>
 */
class Arena_Scheduler
{
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Arena_Scheduler_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Current URL of this plugin.
	 *
	 * @since     1.0.0
	 * @access   protected
	 * @var      string    $plugin_url    The URL of this plugin.
	 */
	protected $plugin_url;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('ARENA_SCHEDULER_VERSION')) {
			$this->version = ARENA_SCHEDULER_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		//  Update plugin version
		update_option('arena_scheduler_version', $this->version);

		$this->plugin_name = 'arena-scheduler';

		$this->arena_scheduler_load_dependencies();
		$this->arena_scheduler_set_locale();
		$this->arena_scheduler_define_admin_hooks();
		$this->arena_scheduler_define_public_hooks();
		$this->arena_scheduler_freemius();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Arena_Scheduler_Loader. Orchestrates the hooks of the plugin.
	 * - Arena_Scheduler_i18n. Defines internationalization functionality.
	 * - Arena_Scheduler_Admin. Defines all hooks for the admin area.
	 * - Arena_Scheduler_Public. Defines all hooks for the public side of the site.
	 * - Arena_Scheduler_Freemius. Initializes the Freemius SDK.
	 * - Arena_Scheduler_Validator. Defines validation functionality.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function arena_scheduler_load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-arena-scheduler-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-arena-scheduler-i18n.php';

		/**
		 * The class responsible for initializing Freemius SDK.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-arena-scheduler-freemius.php';

		/**
		 * The class responsible for validation functionality.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-arena-scheduler-validator.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-arena-scheduler-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-arena-scheduler-public.php';

		$this->loader = new Arena_Scheduler_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Arena_Scheduler_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function arena_scheduler_set_locale()
	{
		$plugin_i18n = new Arena_Scheduler_i18n();

		$this->loader->arena_scheduler_add_action('plugins_loaded', $plugin_i18n, 'arena_scheduler_load_plugin_textdomain');
	}

	/**
	 * Initialize Freemius SDK.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function arena_scheduler_freemius()
	{
		$plugin_freemius = new Arena_Scheduler_Freemius();
		$this->loader->arena_scheduler_add_action('plugins_loaded', $plugin_freemius, 'arena_scheduler_freemius_int');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function arena_scheduler_define_admin_hooks()
	{
		// Instantiate the admin class for the Arena Scheduler plugin, passing the plugin name, version, and URL
		$plugin_admin = new Arena_Scheduler_Admin($this->arena_scheduler_get_plugin_name(), $this->arena_scheduler_get_version(), $this->arena_scheduler_get_plugin_url());

		// Add action to enqueue styles in the WordPress admin
		$this->loader->arena_scheduler_add_action('admin_enqueue_scripts', $plugin_admin, 'arena_scheduler_enqueue_styles');

		// Add action to enqueue scripts in the WordPress admin
		$this->loader->arena_scheduler_add_action('admin_enqueue_scripts', $plugin_admin, 'arena_scheduler_enqueue_scripts');

		// Add actions to register plugin pages in the WordPress admin menu
		$this->loader->arena_scheduler_add_action('admin_menu', $plugin_admin, 'arena_scheduler_register_plugin_page');

		// Ajax request to get arenas
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_get_arenas', $plugin_admin, 'arena_scheduler_get_arenas');

		// Ajax request to create a new arena
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_create_arena', $plugin_admin, 'arena_scheduler_create_arena');

		// Ajax request to get details of a specific arena
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_get_arena', $plugin_admin, 'arena_scheduler_get_arena');

		// Ajax request to update an existing arena
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_update_arena', $plugin_admin, 'arena_scheduler_update_arena');

		// Ajax request to delete an arena
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_delete_arena', $plugin_admin, 'arena_scheduler_delete_arena');

		// Ajax request to get arena categories
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_get_arena_categories', $plugin_admin, 'arena_scheduler_get_arena_categories');

		// Ajax request to create a new arena category
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_create_arena_category', $plugin_admin, 'arena_scheduler_create_arena_category');

		// Ajax request to get details of a specific arena category
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_get_arena_category', $plugin_admin, 'arena_scheduler_get_arena_category');

		// Ajax request to update an existing arena category
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_update_arena_category', $plugin_admin, 'arena_scheduler_update_arena_category');

		// Ajax request to delete an arena category
		$this->loader->arena_scheduler_add_action('wp_ajax_arena_scheduler_delete_arena_category', $plugin_admin, 'arena_scheduler_delete_arena_category');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function arena_scheduler_define_public_hooks()
	{
		// Instantiate the public-facing class for the Arena Scheduler plugin, passing the plugin name and version
		$plugin_public = new Arena_Scheduler_Public($this->arena_scheduler_get_plugin_name(), $this->arena_scheduler_get_version());

		// Add action to enqueue styles on the front end
		$this->loader->arena_scheduler_add_action('wp_enqueue_scripts', $plugin_public, 'arena_scheduler_enqueue_styles');

		// Add action to enqueue scripts on the front end
		$this->loader->arena_scheduler_add_action('wp_enqueue_scripts', $plugin_public, 'arena_scheduler_enqueue_scripts');

		// Add action to register custom REST API routes
		$this->loader->arena_scheduler_add_action('rest_api_init', $plugin_public, 'arena_scheduler_register_routes');

		// Add action to initialize the React app during WordPress initialization
		$this->loader->arena_scheduler_add_action('init', $plugin_public, 'arena_scheduler_app_initialize');

		// Add action to include a nonce in the <head> section of the front end for API requests
		$this->loader->arena_scheduler_add_action('wp_head', $plugin_public, 'add_nonce_to_header');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function arena_scheduler_run()
	{
		$this->loader->arena_scheduler_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function arena_scheduler_get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Arena_Scheduler_Loader    Orchestrates the hooks of the plugin.
	 */
	public function arena_scheduler_get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function arena_scheduler_get_version()
	{
		return $this->version;
	}

	/**
	 * Retrieves the URL of this plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The URL of this plugin.
	 */
	public function arena_scheduler_get_plugin_url()
	{
		return plugins_url($this->plugin_name);
	}
}
