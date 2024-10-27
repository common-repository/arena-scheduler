<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/admin
 */

defined('ABSPATH') || exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two example hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/admin
 * @author     Level5 <support@level5.se>
 */
class Arena_Scheduler_Admin
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Current URL of this plugin.
	 *
	 * @since     1.0.0
	 * @access   protected
	 * @var      string    $plugin_url    The URL of this plugin.
	 */
	private $plugin_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 * @param      string    $plugin_url    Current URL of this plugin.
	 */
	public function __construct($plugin_name, $version, $plugin_url)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_url = $plugin_url;
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function arena_scheduler_enqueue_styles()
	{
		// Define an array of screen IDs for which styles should be enqueued
		$allowedScreens = array(
			'toplevel_page_arena-schedule',
			'arena-scheduler_page_arena-category',
			'arena-scheduler_page_arena-knowledge-base',
			'arena-scheduler_page_arena-open-a-ticket'
		);

		// Get the current screen object
		$screen = get_current_screen();

		// Extract the screen ID
		$screen_id = $screen->id;

		// Check if the current screen ID is in the array of allowed screens
		if (in_array($screen_id, $allowedScreens)) {
			wp_enqueue_style($this->plugin_name . '-googleapis-fonts', 'https://fonts.googleapis.com/css?family=Inter', array(), $this->version, 'all');

			wp_enqueue_style($this->plugin_name . '-bootstrap-style', plugin_dir_url(__FILE__) . 'css/bootstrap5.3.3.css', array(), $this->version, 'all');

			wp_enqueue_style($this->plugin_name . 'bootstrap-datatables-style', plugin_dir_url(__FILE__) . 'css/dataTables2.1.4.bootstrap5.css', array(), $this->version, 'all');

			wp_enqueue_style($this->plugin_name . 'jquery-toast-plugin-style', plugin_dir_url(__FILE__) . 'css/jquery.toast.css', array(), $this->version, 'all');

			wp_enqueue_style($this->plugin_name . '-custom', plugin_dir_url(__FILE__) . 'css/arena-scheduler-admin.css', array(), $this->version, 'all');
		}
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function arena_scheduler_enqueue_scripts()
	{
		// Define an array of screen IDs for which styles should be enqueued
		$allowedScreens = array(
			'toplevel_page_arena-schedule',
			'arena-scheduler_page_arena-category',
			'arena-scheduler_page_arena-knowledge-base',
			'arena-scheduler_page_arena-open-a-ticket'
		);

		// Get the current screen object
		$screen = get_current_screen();

		// Extract the screen ID
		$screen_id = $screen->id;

		// Check if the current screen ID is in the array of allowed screens
		if (in_array($screen_id, $allowedScreens)) {
			wp_enqueue_script($this->plugin_name . '-bootstrap-bundle-script', plugin_dir_url(__FILE__) . 'js/bootstrap5.3.3.bundle.js', array('jquery'), $this->version, false);

			wp_enqueue_script($this->plugin_name . '-datatables-script', plugin_dir_url(__FILE__) . 'js/dataTables2.1.4.js', array('jquery'), $this->version, false);

			wp_enqueue_script($this->plugin_name . '-datatables-bootstrap-script', plugin_dir_url(__FILE__) . 'js/dataTables2.1.4.bootstrap5.js', array('jquery'), $this->version, false);

			wp_enqueue_script($this->plugin_name . '-jquery-validate', plugin_dir_url(__FILE__) . 'js/jquery.validate1.19.5.js', array('jquery'), $this->version, false);

			wp_enqueue_script($this->plugin_name . '-jquery-toast-plugin-script', plugin_dir_url(__FILE__) . 'js/jquery.toast.js', array('jquery'), $this->version, false);

			wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/arena-scheduler-admin.js', array('jquery'), $this->version, false);

			// Pass parameters to the script
			$nonce = wp_create_nonce('arena_scheduler_nonce');
			$currentPage = isset($screen) ? $screen->id : '';
			wp_localize_script($this->plugin_name . '-admin', 'arena_scheduler_admin_data', array(
				'site_url' => get_site_url(),
				'plugin_url' => $this->plugin_url,
				'current_page' => $currentPage,
				'arena_scheduler_nonce' => $nonce,
			));
		}
	}

	/**
	 * Register plugin pages in the WordPress admin menu.
	 *
	 * @since    1.0.0
	 */
	public function arena_scheduler_register_plugin_page()
	{
		// Add top-level menu page "Arena Scheduler"
		add_menu_page(
			'Arena Scheduler',	// Page title
			'Arena Scheduler',	// Menu title
			'edit_others_posts',	// Capability required
			'arena-schedule',	// Menu slug
			array($this, 'arena_scheduler_callback'),	// Callback function for rendering the page
			'dashicons-calendar-alt'	// Dashicon for the menu icon
		);

		// Add submenu page "Arena Category" under "Arena Scheduler"
		add_submenu_page(
			'arena-schedule',	// Parent slug
			'Arena Category',	// Page title
			'Arena Category',	// Menu title
			'edit_others_posts',	// Capability required
			'arena-category',	// Menu slug
			array($this, 'arena_scheduler_category_callback')	// Callback function for rendering the page
		);

		// Add submenu page "Knowledge Base" under "Arena Scheduler"
		add_submenu_page(
			'arena-schedule',	// Parent slug
			'Knowledge Base',	// Page title
			'Knowledge Base',	// Menu title
			'edit_others_posts',	// Capability required
			'arena-knowledge-base',	// Menu slug
			array($this, 'arena_scheduler_knowledge_base_callback')	// Callback function for rendering the page
		);

		// Add submenu page "Open a Ticket" under "Arena Scheduler"
		add_submenu_page(
			'arena-schedule',	// Parent slug
			'Open a Ticket',	// Page title
			'Open a Ticket',	// Menu title
			'edit_others_posts',	// Capability required
			'arena-open-a-ticket',	// Menu slug
			array($this, 'arena_scheduler_open_ticket_callback')	// Callback function for rendering the page
		);
	}

	/**
	 * Callback function for rendering the "Arena Scheduler" admin page.
	 * The content is loaded from the 'admin/partials/arena-schedule.php' file.
	 *
	 * @since 1.0.0
	 */
	public function arena_scheduler_callback()
	{
		// Prepare the data you want to pass to the included file
		$canPerformArenaAction = $this->arena_scheduler_can_perform_arena_action();

		include plugin_dir_path(dirname(__FILE__)) . 'admin/partials/arena-schedule.php';
	}

	/**
	 * Callback function for rendering the "Arena Category" admin page.
	 * The content is loaded from the 'admin/partials/arena-category.php' file.
	 *
	 * @since 1.0.0
	 */
	public function arena_scheduler_category_callback()
	{
		include plugin_dir_path(dirname(__FILE__)) . 'admin/partials/arena-category.php';
	}

	/**
	 * Callback function for rendering the "Knowledge Base" admin page.
	 * The content is loaded from the 'admin/partials/knowledge-base.php' file.
	 *
	 * @since 1.0.0
	 */
	public function arena_scheduler_knowledge_base_callback()
	{
		include plugin_dir_path(dirname(__FILE__)) . 'admin/partials/knowledge-base.php';
	}

	/**
	 * Callback function for rendering the "Open a Ticket" admin page.
	 * The content is loaded from the 'admin/partials/open-ticket.php' file.
	 *
	 * @since 1.0.0
	 */
	public function arena_scheduler_open_ticket_callback()
	{
		include plugin_dir_path(dirname(__FILE__)) . 'admin/partials/open-ticket.php';
	}

	/**
	 * Validate the incoming request data against defined rules and handle errors.
	 *
	 * This method validates the provided request data using the specified rules.
	 * If the validation fails, it sends an error response and terminates execution.
	 *
	 * @param array $request The incoming request data to be validated.
	 * @param array $rules   The validation rules to apply to the request data.
	 * @return void
	 */
	private function arena_scheduler_validate_request($request, $rules)
	{
		// Instantiate the Arena_Scheduler_Validator class to perform validation
		$validator = new Arena_Scheduler_Validator();

		// Validate the request data against the defined rules
		// The third parameter (true) indicates strict validation
		$errors = $validator->validate($request, $rules, true);

		// If validation fails (i.e., errors are found)
		if (!empty($errors)) {
			// Send the validation error response
			$validator->sendValidationResponse();

			// Terminate script execution to prevent further processing
			wp_die();
		}
	}

	/**
	 * Retrieves the total count of arenas.
	 *
	 * This function fetches the total number of records from the 'arena' table
	 * and returns the count.
	 *
	 * @return int The total count of arenas.
	 */
	public function arena_scheduler_get_arena_count()
	{
		// Get the global WordPress database class instance
		global $wpdb;

		// Query to get the total count of records
		$totalRecords = $wpdb->get_results("SELECT COUNT(*) AS count FROM {$wpdb->prefix}arena", ARRAY_A);

		// Return the total count
		return intval($totalRecords[0]['count']);
	}

	/**
	 * Retrieve all records from the 'arena' custom table and send a JSON response.
	 *
	 * @return void
	 */
	public function arena_scheduler_get_arenas()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Get the global WordPress database class instance
		global $wpdb;

		// Sanitize and retrieve pagination parameters from the POST request
		$start = isset($_POST['start']) ? sanitize_text_field($_POST['start']) : 0;
		$length = isset($_POST['length']) ? sanitize_text_field($_POST['length']) : 10;
		$draw = isset($_POST['draw']) ? sanitize_text_field($_POST['draw']) : 1;

		// Determine if the arena-related action can be performed
		$canPerformAction = $this->arena_scheduler_can_perform_arena_action();

		// Adjust the length if the user is on a free plan
		if (!$canPerformAction) {
			$length = 1;
		}

		// Query to retrieve limited records based on pagination
		$data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}arena ORDER BY id DESC LIMIT %d, %d",
				$start,
				$length
			),
			OBJECT
		);

		// Query to get the total count of records
		$recordsTotal = $wpdb->get_results("SELECT COUNT(*) AS count FROM {$wpdb->prefix}arena", ARRAY_A);

		// Prepare response data including total records for pagination
		$response = array(
			"canPerformAction" => $this->arena_scheduler_can_perform_arena_action(),
			"draw" => $draw, // Required parameter for DataTables to prevent cross-site scripting attacks
			"recordsTotal" => $recordsTotal[0]['count'],
			"recordsFiltered" => $recordsTotal[0]['count'], // For simplicity, filtering is not implemented here, so it's the same as recordsTotal
			"data" => $data // Actual data to display in the DataTable
		);

		// Send a JSON response with the retrieved data and pagination information
		wp_send_json($response);

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Creates a new arena record via AJAX.
	 *
	 * This function validates and sanitizes data from the POST request, inserts it
	 * into the custom table 'arena', and sends a JSON response with the
	 * result of the database operation.
	 *
	 * @return void
	 */
	public function arena_scheduler_create_arena()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Sanitize and assign the POST data to variables
		$name = sanitize_text_field($_POST['name']);
		$intervalTime = intval($_POST['interval_time']);
		$startTime = intval($_POST['start_time']);
		$endTime = intval($_POST['end_time']);
		$status = intval($_POST['status']);

		// Collect the necessary POST data into an associative array for the server-side validation
		$validateData = [
			'name' => $name,
			'interval_time' => $intervalTime,
			'start_time' => $startTime,
			'end_time' => $endTime,
			'status' => $status,
		];

		// Define validation rules
		$rules = [
			'name' => 'required|string',
			'interval_time' => 'required|numeric',
			'start_time' => 'required|numeric|max:2',
			'end_time' => 'required|numeric|max:2|time_greater:start_time',
			'status' => 'required|boolean'
		];

		// This handles the validation of request data
		$this->arena_scheduler_validate_request($validateData, $rules);

		// Check if the arena-related action can be performed
		if ($this->arena_scheduler_can_perform_arena_action() === true) {
			// Get the global WordPress database class instance
			global $wpdb;

			// Define the table name with the proper WordPress prefix
			$tableName = $wpdb->prefix . 'arena';

			// Insert data into the custom table
			$data = $wpdb->insert(
				$tableName,
				array('name' => $name, 'interval_time' => $intervalTime, 'start_time' => sprintf('%02d', $startTime) . ':00', 'end_time' => sprintf('%02d', $endTime) . ':00', 'is_default' => 0, 'status' => $status),
				array('%s', '%d', '%s', '%s', '%d', '%d')
			);

			// Send a JSON response with the created record details
			wp_send_json($data);
		} else {
			// Send a JSON response with an error message if the action cannot be performed
			wp_send_json([
				'error' => true,
				'message' => 'Free plan allows only up to 1 arena.'
			]);
		}

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Retrieves arena details by ID via AJAX.
	 *
	 * This function takes a record ID from the POST request, performs a database
	 * query to fetch the corresponding record details from the 'arena'
	 * table, and sends a JSON response with the retrieved record details.
	 *
	 * @return void
	 */
	public function arena_scheduler_get_arena()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Sanitize and assign the POST data to variables
		$recordID = intval($_POST['record_id']);

		// Collect the necessary POST data into an associative array for the server-side validation
		$validateData = [
			'record_id' => $recordID
		];

		// Define validation rules
		$rules = [
			'record_id' => 'required|numeric'
		];

		// This handles the validation of request data
		$this->arena_scheduler_validate_request($validateData, $rules);

		// Get the global WordPress database class instance
		global $wpdb;

		// SQL query to retrieve record details based on ID
		$data = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}arena WHERE id = %d",
			$recordID
		), ARRAY_A);

		// Send a JSON response with the retrieved record details
		wp_send_json($data);

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Handles the update of an arena record via AJAX.
	 *
	 * This function sanitizes and retrieves data from the POST request, updates
	 * the corresponding record in the 'arena' table, and sends a JSON
	 * response with the result of the database operation.
	 *
	 * @return void
	 */
	public function arena_scheduler_update_arena()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Sanitize and assign the POST data to variables
		$id = intval($_POST['id']);
		$name = sanitize_text_field($_POST['name']);
		$status = intval($_POST['status']);
		$default = sanitize_text_field($_POST['default']);

		// Collect the necessary POST data into an associative array for the server-side validation
		$validateData = [
			'id' => $id,
			'name' => $name,
			'status' => $status
		];

		// Define validation rules
		$rules = [
			'id' => 'required|numeric',
			'name' => 'required|string',
			'status' => 'required|boolean'
		];

		// This handles the validation of request data
		$this->arena_scheduler_validate_request($validateData, $rules);

		// Retrieve data from the POST request
		$isDefault = isset($default) && $default === 'on' ? 1 : 0;

		// Clear old default
		if ($isDefault == '1') {
			$this->arena_scheduler_reset_arena_default();
		}

		// Get the global WordPress database class instance
		global $wpdb;

		// Define the table name with the proper WordPress prefix
		$tableName = $wpdb->prefix . 'arena';

		// Update data in the custom table based on the record ID
		$data = $wpdb->update(
			$tableName,
			array('name' => $name, 'is_default' => $isDefault, 'status' => $status),
			array('id' => $id),
			array('%s', '%d', '%d'),
			array('%d')
		);

		// Send a JSON response with the result of the database operation
		wp_send_json($data);

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Deletes an arena category record from the custom database table.
	 * Handles the AJAX request to delete a specific record.
	 * Sends JSON response indicating the status of the deletion operation.
	 *
	 * @return void
	 */
	public function arena_scheduler_delete_arena()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Sanitize and assign the POST data to variables
		$recordID = intval($_POST['record_id']);

		// Collect the necessary POST data into an associative array for the server-side validation
		$validateData = [
			'record_id' => $recordID
		];

		// Define validation rules
		$rules = [
			'record_id' => 'required|numeric'
		];

		// This handles the validation of request data
		$this->arena_scheduler_validate_request($validateData, $rules);

		// Get the global WordPress database class instance
		global $wpdb;

		// Define the table name with the proper WordPress prefix
		$tableName = $wpdb->prefix . 'arena';

		// Delete the record from the custom table based on the record ID
		$deleted = $wpdb->delete($tableName, array('id' => $recordID), array('%d'));

		// Check if the record was successfully deleted
		if ($deleted !== false) {
			// Record deleted successfully
			wp_send_json(array('status' => 1, 'message' => 'Record deleted successfully'));
		} else {
			// Error deleting record
			wp_send_json(array('status' => 0, 'message' => 'Error deleting record'));
		}

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Retrieves the total count of arena categories.
	 *
	 * This function fetches the total number of records from the 'arena_categories' table
	 * and returns the count.
	 *
	 * @return int The total count of arena categories.
	 */
	public function arena_scheduler_get_arena_categories_count()
	{
		// Get the global WordPress database class instance
		global $wpdb;

		// Query to get the total count of records
		$totalRecords = $wpdb->get_results("SELECT COUNT(*) AS count FROM {$wpdb->prefix}arena_categories", ARRAY_A);

		// Return the total count
		return intval($totalRecords[0]['count']);
	}

	/**
	 * Retrieves all arena categories via AJAX.
	 *
	 * This function fetches all records from the 'arena_categories' table and sends
	 * a JSON response with the retrieved data.
	 *
	 * @return void
	 */
	public function arena_scheduler_get_arena_categories()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Get the global WordPress database class instance
		global $wpdb;

		// Sanitize and retrieve pagination parameters from the POST request
		$start = isset($_POST['start']) ? sanitize_text_field($_POST['start']) : 0;
		$length = isset($_POST['length']) ? sanitize_text_field($_POST['length']) : 10;

		// Determine if the category-related action can be performed
		$canPerformAction = $this->arena_scheduler_can_perform_category_action();

		// Adjust the length if the user is on a free plan
		if (!$canPerformAction) {
			$length = 2;
		}

		// Query to get the total count of records
		$totalRecords = $wpdb->get_results("SELECT COUNT(*) AS count FROM {$wpdb->prefix}arena_categories", ARRAY_A);

		// Calculate total pages
		$totalPages = ceil($totalRecords[0]['count'] / $length);

		// Ensure start is within range
		$start = max(0, min($start, $totalPages - 1));

		// Query to retrieve limited records based on pagination
		$data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}arena_categories ORDER BY id DESC LIMIT %d, %d",
				$start * $length,
				$length
			),
			OBJECT
		);

		// Prepare response data including total records for pagination
		$response = array(
			"canPerformAction" => $this->arena_scheduler_can_perform_category_action(),
			"draw" => isset($_POST['draw']) ? sanitize_text_field($_POST['draw']) : 1, // Required parameter for DataTables to prevent cross-site scripting attacks
			"recordsTotal" => $totalRecords,
			"recordsFiltered" => $totalRecords, // For simplicity, filtering is not implemented here, so it's the same as recordsTotal
			"data" => $data // Actual data to display in the DataTable
		);

		// Send a JSON response with the retrieved data and pagination information
		wp_send_json($response);

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Retrieves arena categories.
	 *
	 * This function fetches records from the 'arena_categories' table.
	 * If a limit is specified, it returns only that number of records;
	 * otherwise, it returns all records.
	 *
	 * @param int|null $limit Optional. The number of records to retrieve. Default is null.
	 * @return array An array of arena category records.
	 */
	public function arena_scheduler_get_all_arena_categories($limit = null)
	{
		// Get the global WordPress database class instance
		global $wpdb;

		// Prepare the SQL query with an optional limit
		if ($limit !== null) {
			$allRecords = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}arena_categories ORDER BY id DESC LIMIT %d",
					$limit
				)
			);
		} else {
			$allRecords = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}arena_categories ORDER BY id DESC");
		}

		// Return all records
		return $allRecords;
	}

	/**
	 * Creates a new arena category record via AJAX.
	 *
	 * This function sanitizes and retrieves data from the POST request, inserts it
	 * into the custom table 'arena_categories', and sends a JSON response with the
	 * result of the database operation.
	 *
	 * @return void
	 */
	public function arena_scheduler_create_arena_category()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Sanitize and assign the POST data to variables
		$name = sanitize_text_field($_POST['name']);
		$inputColor = sanitize_hex_color($_POST['color']);
		$inputTextColor = sanitize_hex_color($_POST['text_color']);
		$status = intval($_POST['status']);

		// Collect the necessary POST data into an associative array for the server-side validation
		$validateData = [
			'name' => $name,
			'status' => $status
		];

		// Define validation rules
		$rules = [
			'name' => 'required|string',
			'status' => 'required|boolean'
		];

		// This handles the validation of request data
		$this->arena_scheduler_validate_request($validateData, $rules);

		// Check if the category-related action can be performed
		if ($this->arena_scheduler_can_perform_category_action() === true) {
			// Retrieve data from the POST request
			$color = (Arena_Scheduler_Freemius::arena_scheduler_is_paying()) ? $inputColor : $this->arena_scheduler_determine_free_arena_category_color();
			$textColor = (Arena_Scheduler_Freemius::arena_scheduler_is_paying()) ? $inputTextColor : '#000';

			// Get the global WordPress database class instance
			global $wpdb;

			// Define the table name with the proper WordPress prefix
			$tableName = $wpdb->prefix . 'arena_categories';

			// Insert data into the custom table
			$data = $wpdb->insert(
				$tableName,
				array('name' => $name, 'color' => $color, 'text_color' => $textColor, 'is_default' => 0, 'status' => $status),
				array('%s', '%s', '%s', '%d', '%d')
			);

			// Send a JSON response with the created record details
			wp_send_json($data);
		} else {
			// Send a JSON response with an error message if the action cannot be performed
			wp_send_json([
				'error' => true,
				'message' => 'Free plan allows only up to 2 arena categories.'
			]);
		}

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Retrieves arena category details by ID via AJAX.
	 *
	 * This function takes a record ID from the POST request, performs a database
	 * query to fetch the corresponding record details from the 'arena_categories'
	 * table, and sends a JSON response with the retrieved record details.
	 *
	 * @return void
	 */
	public function arena_scheduler_get_arena_category()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Sanitize and assign the POST data to variables
		$recordID = intval($_POST['record_id']);

		// Collect the necessary POST data into an associative array for the server-side validation
		$validateData = [
			'record_id' => $recordID
		];

		// Define validation rules
		$rules = [
			'record_id' => 'required|numeric'
		];

		// This handles the validation of request data
		$this->arena_scheduler_validate_request($validateData, $rules);

		// Get the global WordPress database class instance
		global $wpdb;

		// SQL query to retrieve record details based on ID
		$data = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}arena_categories WHERE id = %d",
			$recordID
		), ARRAY_A);

		// Send a JSON response with the retrieved record details
		wp_send_json($data);

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Handles the update of an arena category record via AJAX.
	 *
	 * This function sanitizes and retrieves data from the POST request, updates
	 * the corresponding record in the 'arena_categories' table, and sends a JSON
	 * response with the result of the database operation.
	 *
	 * @return void
	 */
	public function arena_scheduler_update_arena_category()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Sanitize and assign the POST data to variables
		$id = intval($_POST['id']);
		$name = sanitize_text_field($_POST['name']);
		$inputColor = sanitize_hex_color($_POST['color']);
		$inputTextColor = sanitize_hex_color($_POST['text_color']);
		$status = intval($_POST['status']);
		$default = sanitize_text_field($_POST['default']);

		// Collect the necessary POST data into an associative array for the server-side validation
		$validateData = [
			'id' => $id,
			'name' => $name,
			'status' => $status
		];

		// Define validation rules
		$rules = [
			'id' => 'required|numeric',
			'name' => 'required|string',
			'status' => 'required|boolean'
		];

		// This handles the validation of request data
		$this->arena_scheduler_validate_request($validateData, $rules);

		// Sanitize and retrieve data from the POST request
		$color = isset($inputColor) ? $inputColor : $this->arena_scheduler_determine_free_arena_category_color();
		$textColor = isset($inputTextColor) ? $inputTextColor : '#000';
		$isDefault = isset($default) && $default === 'on' ? 1 : 0;

		// Clear old default if the current category is set as default
		if ($isDefault == 1) {
			$this->arena_scheduler_reset_arena_categories_default();
		}

		// Get the global WordPress database class instance
		global $wpdb;

		// Define the table name with the proper WordPress prefix
		$tableName = $wpdb->prefix . 'arena_categories';

		// Prepare the data array for updating
		$updateData = array('name' => $name, 'status' => $status, 'is_default' => $isDefault);
		$updateFormat = array('%s', '%d', '%d');

		// Conditionally add color and text color to the update data array if provided
		if ($color !== null) {
			$updateData['color'] = $color;
			$updateFormat[] = '%s';
		}

		if ($textColor !== null) {
			$updateData['text_color'] = $textColor;
			$updateFormat[] = '%s';
		}

		// Update data in the custom table based on the record ID
		$data = $wpdb->update(
			$tableName,
			$updateData,
			array('id' => $id),
			$updateFormat,
			array('%d')
		);

		// Send a JSON response with the result of the database operation
		wp_send_json($data);

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Deletes an arena category record from the custom database table.
	 * Handles the AJAX request to delete a specific record.
	 * Sends JSON response indicating the status of the deletion operation.
	 *
	 * @return void
	 */
	public function arena_scheduler_delete_arena_category()
	{
		// Check the nonce for security
		if (!check_ajax_referer('arena_scheduler_nonce', '_ajax_nonce', false)) {
			wp_send_json_error(array(
				'message' => 'Security check failed. Please refresh the page and try again.',
			));

			wp_die(); // Stop further execution
		}

		// Sanitize and assign the POST data to variables
		$recordID = intval($_POST['record_id']);

		// Collect the necessary POST data into an associative array for the server-side validation
		$validateData = [
			'record_id' => $recordID
		];

		// Define validation rules
		$rules = [
			'record_id' => 'required|numeric'
		];

		// This handles the validation of request data
		$this->arena_scheduler_validate_request($validateData, $rules);

		// Get the global WordPress database class instance
		global $wpdb;

		// Define the table name with the proper WordPress prefix
		$tableName = $wpdb->prefix . 'arena_categories';

		// Delete the record from the custom table based on the record ID
		$deleted = $wpdb->delete($tableName, array('id' => $recordID), array('%d'));

		// Check if the record was successfully deleted
		if ($deleted !== false) {
			// Record deleted successfully
			wp_send_json(array('status' => 1, 'message' => 'Record deleted successfully'));
		} else {
			// Error deleting record
			wp_send_json(array('status' => 0, 'message' => 'Error deleting record'));
		}

		// Always include this to terminate the AJAX request properly
		wp_die();
	}

	/**
	 * Determines the color to assign to a new free arena category.
	 *
	 * This function checks the existing records and assigns an alternate color
	 * based on the last record's color.
	 *
	 * @return string The color to assign to the new arena category.
	 */
	public function arena_scheduler_determine_free_arena_category_color()
	{
		$allCategories = $this->arena_scheduler_get_all_arena_categories(2);
		$lastColor = end($allCategories)->color;

		// Define the colors
		$color1 = '#fef982';
		$color2 = '#ff8a8a';

		// Determine the next color
		$nextColor = ($lastColor === $color1) ? $color2 : $color1;

		// Return the next color
		return $nextColor;
	}

	/**
	 * Reset the 'is_default' field to 0 for all records in the 'arena' table.
	 * This function is typically used in WordPress AJAX callbacks.
	 *
	 * @return void
	 */
	public function arena_scheduler_reset_arena_default()
	{
		global $wpdb;

		// Update query to set is_default to 0 for all records
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->prefix}arena SET is_default = %d",
			0
		));
	}

	/**
	 * Reset the 'is_default' field to 0 for all records in the 'arena_categories' table.
	 * This function is typically used in WordPress AJAX callbacks.
	 *
	 * @return void
	 */
	public function arena_scheduler_reset_arena_categories_default()
	{
		global $wpdb;

		// Update query to set is_default to 0 for all records
		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->prefix}arena_categories SET is_default = %d",
			0
		));
	}

	/**
	 * Determines if a certain arena-related action can be performed based on the plan and arena count.
	 *
	 * This function checks if the current plan allows performing a arena-related action
	 * and returns a boolean indicating whether the action can be performed.
	 *
	 * @return bool True if the action can be performed, false otherwise.
	 */
	public function arena_scheduler_can_perform_arena_action()
	{
		// Get the count of arenas
		$arenaCount = $this->arena_scheduler_get_arena_count();

		// Determine if the arena-related action can be performed
		if (Arena_Scheduler_Freemius::arena_scheduler_is_free_plan() && $arenaCount < 1) {
			return true;
		} elseif (Arena_Scheduler_Freemius::arena_scheduler_is_paying()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Determines if a certain category-related action can be performed based on the plan and arena categories count.
	 *
	 * This function checks if the current plan allows performing a category-related action
	 * and returns a boolean indicating whether the action can be performed.
	 *
	 * @return bool True if the action can be performed, false otherwise.
	 */
	public function arena_scheduler_can_perform_category_action()
	{
		// Get the count of arena categories
		$arenaCategoriesCount = $this->arena_scheduler_get_arena_categories_count();

		// Determine if the category-related action can be performed
		if (Arena_Scheduler_Freemius::arena_scheduler_is_free_plan() && $arenaCategoriesCount < 2) {
			return true;
		} elseif (Arena_Scheduler_Freemius::arena_scheduler_is_paying()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Generates HTML for a support information card.
	 *
	 * @return string HTML content for the support card.
	 */
	public function arena_scheduler_support_card_html()
	{
		$html = '<div class="card px-0 pt-0 mt-0 pb-0">
                <div class="card-header bg-red py-0 d-flex align-items-center">
                    <img src="' . esc_url($this->plugin_url) . '/admin/images/SupportIcon.svg" alt="Support & Info" class="me-2" width="25" />
                    <h3 class="wp-heading-inline text-white mb-0">Support & Info</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex align-items-center border-0">
                            <img src="' . esc_url($this->plugin_url) . '/admin/images/KnowledgeBaseIcon.svg" alt="Knowledge base" class="me-3" width="25" />
                            <a href="' . esc_url(admin_url('admin.php?page=arena-knowledge-base')) . '" class="text-black text-decoration-none ms-2">Knowledge base</a>
                        </li>
                        <li class="list-group-item px-0 d-flex align-items-center border-0">
                            <img src="' . esc_url($this->plugin_url) . '/admin/images/OpenTicketIcon.svg" alt="Open a ticket" class="me-3" width="25" />
                            <a href="' . esc_url(admin_url('admin.php?page=arena-open-a-ticket')) . '" class="text-black text-decoration-none ms-2">Open a ticket</a>
                        </li>';
		if (Arena_Scheduler_Freemius::arena_scheduler_is_free_plan('Free') === true) {
			$html .= '<li class="list-group-item px-0 d-flex align-items-center border-0">
                    <img src="' . esc_url($this->plugin_url) . '/admin/images/UpgradeProIcon.svg" alt="Upgrade to Pro" class="me-3" width="25" />
                    <a href="' . esc_url(admin_url('admin.php?page=arena-schedule-pricing')) . '" class="text-black text-decoration-none ms-2">Upgrade to Pro</a>
                </li>
            </ul>';
		}

		$html .= '</div></div>';

		return $html;
	}
}
