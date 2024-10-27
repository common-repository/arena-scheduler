<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/public
 */


defined('ABSPATH') || exit;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/public
 * @author     Level5 <support@level5.se>
 */
class Arena_Scheduler_Public
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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Add shortcodes for front-end and app views
		add_shortcode('arena_scheduler_view_calendar', array($this, 'arena_scheduler_render'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function arena_scheduler_enqueue_styles()
	{
		if (!is_admin()) {
			// Enqueue the public-facing stylesheet
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/arena-scheduler-public.css', array(), $this->version, 'all');
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function arena_scheduler_enqueue_scripts()
	{
		//
	}

	/**
	 * Initializes a React app by registering its JavaScript and CSS files.
	 */
	public function arena_scheduler_app_initialize()
	{
		// Register the JavaScript file for the React app
		wp_register_script("arena_scheduler_app_js", plugin_dir_url(__FILE__) . 'build/static/js/main.js', array(), "1.0", true);

		// Register the CSS file for the React app
		wp_register_style("arena_scheduler_app_css", plugin_dir_url(__FILE__) . 'build/static/css/main.css', array(), "1.0", "all");
	}

	/**
	 * Render DOM.
	 *
	 * Enqueues the JavaScript and CSS files for the React app and returns
	 * a placeholder div with an ID where the React app will be mounted.
	 *
	 * @return string HTML code for the placeholder div.
	 */
	public function arena_scheduler_render()
	{
		// Enqueue the JavaScript file for the React app
		wp_enqueue_script("arena_scheduler_app_js", '1.0', true);

		// Enqueue the CSS file for the React app
		wp_enqueue_style("arena_scheduler_app_css");

		// Return a placeholder div with an ID where the React app will be mounted
		return "<div id=\"arena-scheduler-root\"></div>";
	}

	/**
	 * Initializes custom REST API routes for Arena and Timesheet data.
	 * Registers REST routes for retrieving specific data using GET requests.
	 */
	public function arena_scheduler_register_routes()
	{
		// Register REST route for retrieving arena data
		register_rest_route('custom/v1', '/arena', array(
			'methods' => 'GET',
			'callback' => array($this, 'arena_scheduler_get_wp_arena_data'),
			'permission_callback' => '__return_true',
		));

		// Register REST route for retrieving arena category data
		register_rest_route('custom/v1', '/arena/category', array(
			'methods' => 'GET',
			'callback' => array($this, 'arena_scheduler_get_wp_arena_category_data'),
			'permission_callback' => '__return_true',
		));

		// Register REST route for retrieving timesheet data
		register_rest_route('custom/v1', '/timesheet', array(
			'methods' => 'GET',
			'callback' => array($this, 'arena_scheduler_get_arena_timesheet_data'),
			'permission_callback' => '__return_true',
		));

		// Register REST route for saving arena scheduler data
		register_rest_route('custom/v1', '/arena/save/schedule', array(
			'methods' => 'POST',
			'callback' => array($this, 'arena_scheduler_save_data'),
			'permission_callback' => '__return_true',
		));

		// Register REST route for copying arena scheduler data
		register_rest_route('custom/v1', '/arena/copy/schedule', array(
			'methods' => 'POST',
			'callback' => array($this, 'arena_scheduler_copy_data'),
			'permission_callback' => '__return_true',
		));

		// Register REST route for saving arena scheduler comment
		register_rest_route('custom/v1', '/arena/save/schedule/comment', array(
			'methods' => 'POST',
			'callback' => array($this, 'arena_scheduler_save_comment'),
			'permission_callback' => '__return_true',
		));

		// Register REST route for copying arena week data
		register_rest_route('custom/v1', '/arena/copy/schedule/week', array(
			'methods' => 'POST',
			'callback' => array($this, 'arena_scheduler_copy_arena_week_data'),
			'permission_callback' => '__return_true',
		));
	}

	/**
	 * Adds a nonce to the header for API requests.
	 *
	 * This method generates a nonce and adds it to the HTML head section
	 * as a meta tag for use with API requests.
	 *
	 * @since    1.0.0
	 */
	public function add_nonce_to_header()
	{
		if (!is_admin()) { // Ensure it's only added on the front end
			// Generate a nonce for the API request
			$api_nonce = wp_create_nonce('wp_rest');

			// Output the nonce in a meta tag, alongside other meta tags
			echo '<meta name="api-nonce" content="' . esc_attr($api_nonce) . '">' . "\n";
		}
	}

	/**
	 * Verifies the security nonce for REST API requests.
	 *
	 * This method checks the validity of the nonce to ensure that the request is legitimate.
	 * If the nonce is invalid, it returns a WP_Error object with a 403 status code, which
	 * indicates that the request is forbidden due to a failed security check.
	 *
	 * @return bool|WP_Error Returns true if the nonce check passes;
	 *                       Returns a WP_Error object if the nonce check fails.
	 */
	private function arena_scheduler_verify_nonce($nonce)
	{
		if (!wp_verify_nonce($nonce, 'wp_rest')) {
			return new WP_Error(
				'rest_forbidden',
				'Security check failed. Please refresh the page and try again.',
				array('status' => 403)
			);
		}

		return true; // Return true if the nonce check passes
	}

	/**
	 * Retrieves the total count of arena.
	 *
	 * This function fetches the total number of records from the 'arena' table
	 * and returns the count.
	 *
	 * @return int The total count of arena.
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
	 * Callback function for retrieving Arena data via the REST API.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|array Response containing Arena data or an empty array.
	 */
	public function arena_scheduler_get_wp_arena_data($request)
	{
		// Check the nonce for security
		$nonce_check = $this->arena_scheduler_verify_nonce(sanitize_text_field($request['_nonce']));
		if (is_wp_error($nonce_check)) {
			return $nonce_check; // Return the error response if nonce check fails
		}

		global $wpdb;

		// Determine if the arena-related action can be performed
		$canPerformAction = $this->arena_scheduler_can_perform_arena_action();

		// Perform the query with the prepared statement
		if (!$canPerformAction) {
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, name, interval_time, start_time, end_time, is_default, status
					FROM {$wpdb->prefix}arena
					WHERE status = %d
					ORDER BY is_default DESC LIMIT %d",
					1,
					1
				)
			);
		} else {
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, name, interval_time, start_time, end_time, is_default, status
					FROM {$wpdb->prefix}arena
					WHERE status = %d
					ORDER BY is_default DESC",
					1
				)
			);
		}

		// Return the results
		return rest_ensure_response($results);
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
	 * Callback function for retrieving Arena category data via the REST API.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|array Response containing Arena category data or an empty array.
	 */
	public function arena_scheduler_get_wp_arena_category_data($request)
	{
		// Check the nonce for security
		$nonce_check = $this->arena_scheduler_verify_nonce(sanitize_text_field($request['_nonce']));
		if (is_wp_error($nonce_check)) {
			return $nonce_check; // Return the error response if nonce check fails
		}

		global $wpdb;

		// Determine if the category-related action can be performed
		$canPerformAction = $this->arena_scheduler_can_perform_category_action();

		// Perform the query with the prepared statement
		if (!$canPerformAction) {
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, name, color, text_color, is_default, status
					FROM {$wpdb->prefix}arena_categories
					WHERE status = %d
					GROUP BY name
					ORDER BY is_default DESC LIMIT %d",
					1,
					2
				)
			);
		} else {
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, name, color, text_color, is_default, status
					FROM {$wpdb->prefix}arena_categories
					WHERE status = %d
					GROUP BY name
					ORDER BY is_default DESC",
					1
				)
			);
		}

		// Return the results
		return rest_ensure_response($results);
	}

	/**
	 * Callback function for retrieving Arena timesheet data via the REST API.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|array Response containing timesheet data or an empty array.
	 */
	public function arena_scheduler_get_arena_timesheet_data($request)
	{
		// Check the nonce for security
		$nonce_check = $this->arena_scheduler_verify_nonce(sanitize_text_field($request['_nonce']));
		if (is_wp_error($nonce_check)) {
			return $nonce_check; // Return the error response if nonce check fails
		}

		global $wpdb;

		// Get start and end dates from the request
		$arenaID = sanitize_text_field($request->get_param('active_tab'));
		$startDate = sanitize_text_field($request->get_param('start_date'));
		$endDate = sanitize_text_field($request->get_param('end_date'));

		// Perform the query with JOIN operation using dynamic table names
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT timesheet.id, timesheet.arena_id, timesheet.timeslot_id, timesheet.category, timesheet.comment, timesheet.scheduled_date, categories.name, categories.color, categories.text_color
            	FROM {$wpdb->prefix}arena_scheduled_timesheet AS timesheet
            	INNER JOIN {$wpdb->prefix}arena_categories AS categories ON timesheet.category = categories.id
            	WHERE scheduled_date BETWEEN %s AND %s
            	AND arena_id = %d
            	ORDER BY scheduled_date ASC",
				$startDate,
				$endDate,
				$arenaID
			)
		);

		// Return the results
		return rest_ensure_response($results);
	}

	/**
	 * Callback function to save arena scheduler data via the REST API.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|array Response containing timesheet data or an empty array.
	 */
	public function arena_scheduler_save_data($request)
	{
		// Check the nonce for security
		$nonce_check = $this->arena_scheduler_verify_nonce(sanitize_text_field($request['_nonce']));
		if (is_wp_error($nonce_check)) {
			return $nonce_check; // Return the error response if nonce check fails
		}

		global $wpdb;

		$arenaID = sanitize_text_field($request->get_param('arena_id'));
		$scheduledDate = sanitize_text_field($request->get_param('scheduled_date'));
		$timeslotID = sanitize_text_field($request->get_param('timeslot_id'));
		$category = sanitize_text_field($request->get_param('category'));

		$tableName = $wpdb->prefix . 'arena_scheduled_timesheet';

		// query to check if record exist or not
		$query = $wpdb->get_row(
			$wpdb->prepare("SELECT id FROM {$wpdb->prefix}arena_scheduled_timesheet WHERE arena_id=%d AND timeslot_id=%d", $arenaID, $timeslotID),
			ARRAY_A
		);

		if (isset($query) && $query['id'] > 0) {
			// Update it
			$result =  $wpdb->update($tableName, array('category' => $category), array('id' => $query['id']));
			if (is_wp_error($result)) {
				$response = array(
					'status' => 0,
					'message' => 'Failed to update the record'
				);

				return rest_ensure_response($response);
			} else {
				return $this->arena_scheduler_fetch_timesheet_data($query['id']);
			}
		} else {
			// Insert it
			$result = $wpdb->insert($tableName, array(
				'arena_id' => $arenaID,
				'timeslot_id' => $timeslotID,
				'category' => $category,
				'scheduled_date' => $scheduledDate
			));

			if (is_wp_error($result)) {
				$response = array(
					'status' => 0,
					'message' => 'Failed to create the record'
				);

				return rest_ensure_response($response);
			} else {
				return $this->arena_scheduler_fetch_timesheet_data($wpdb->insert_id);
			}
		}
	}

	/**
	 * Callback function to copy arena scheduler data via the REST API.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|array Response containing timesheet data or an empty array.
	 */
	public function arena_scheduler_copy_data($request)
	{
		// Check the nonce for security
		$nonce_check = $this->arena_scheduler_verify_nonce(sanitize_text_field($request['_nonce']));
		if (is_wp_error($nonce_check)) {
			return $nonce_check; // Return the error response if nonce check fails
		}

		global $wpdb;

		$arenaID = sanitize_text_field($request->get_param('arena_id'));
		$scheduledDate = sanitize_text_field($request->get_param('scheduled_date'));
		$timeslotID = sanitize_text_field($request->get_param('timeslot_id'));
		$endTimeslot = sanitize_text_field($request->get_param('end_timeslot'));
		$category = sanitize_text_field($request->get_param('category'));
		$intervalTime = sanitize_text_field($request->get_param('interval_time'));

		// Copy Slot
		if ($endTimeslot !== '') {
			$startTime = substr_replace(substr($timeslotID, -4), ':', -2, -2);
			$endTime = substr_replace(substr($endTimeslot, -4), ':', -2, -2);
			$slots = $this->arena_scheduler_get_timeslot($intervalTime, $startTime, $endTime);
			$dateRaw =  preg_replace('/[^0-9]/', '', $scheduledDate);

			$tableName = $wpdb->prefix . 'arena_scheduled_timesheet';

			foreach ($slots as $slot) {
				$timeslotID = $dateRaw . $slot['slot_raw'];

				// query to check if record exist or not
				$query = $wpdb->get_row(
					$wpdb->prepare("SELECT id FROM {$wpdb->prefix}arena_scheduled_timesheet WHERE arena_id=%d AND timeslot_id=%d", $arenaID, $timeslotID),
					ARRAY_A
				);

				if (isset($query) && $query['id'] > 0) {
					// Update it
					$wpdb->update($tableName, array('category' => $category), array('id' => $query['id']));
				} else {
					// Insert it
					$wpdb->insert($tableName, array(
						'arena_id' => $arenaID,
						'timeslot_id' => $timeslotID,
						'category' => $category,
						'scheduled_date' => $scheduledDate
					));
				}
			}
		}

		// Retrieve the records
		$records = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT timesheet.id, timesheet.arena_id, timesheet.timeslot_id, timesheet.category, timesheet.comment, timesheet.scheduled_date, categories.name, categories.color, categories.text_color
            	FROM {$wpdb->prefix}arena_scheduled_timesheet AS timesheet
            	INNER JOIN {$wpdb->prefix}arena_categories AS categories ON timesheet.category = categories.id
            	WHERE timesheet.scheduled_date = %s
				AND timesheet.arena_id = %d
				AND timesheet.category = %d",
				$scheduledDate,
				$arenaID,
				$category
			)
		);

		return rest_ensure_response($records);
	}

	/**
	 * Save comment for an arena scheduler.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 */
	public function arena_scheduler_save_comment($request)
	{
		// Check the nonce for security
		$nonce_check = $this->arena_scheduler_verify_nonce(sanitize_text_field($request['_nonce']));
		if (is_wp_error($nonce_check)) {
			return $nonce_check; // Return the error response if nonce check fails
		}

		global $wpdb;

		// Get parameters from the request
		$arenaID = sanitize_text_field($request->get_param('arena_id'));
		$timeslotID = sanitize_text_field($request->get_param('timeslot_id'));
		$comment = sanitize_text_field($request->get_param('comment'));

		// Table name for arena scheduled timesheet
		$arenaScheduledTimesheetTable = $wpdb->prefix . 'arena_scheduled_timesheet';

		// Query to check if the record exists
		$query = $wpdb->get_row(
			$wpdb->prepare("SELECT id FROM {$wpdb->prefix}arena_scheduled_timesheet WHERE arena_id=%d AND timeslot_id=%d", $arenaID, $timeslotID),
			ARRAY_A
		);

		// Update or insert the comment based on the record existence
		if (isset($query) && $query['id'] > 0) {
			// Update existing record with the comment
			$updateResult = $wpdb->update(
				$arenaScheduledTimesheetTable,
				array('comment' => $comment),
				array('id' => $query['id'])
			);

			if ($updateResult !== false) {
				// Get the updated row after the update operation
				$updatedRow = $wpdb->get_row(
					$wpdb->prepare("SELECT * FROM {$wpdb->prefix}arena_scheduled_timesheet WHERE id = %d", $query['id']),
					ARRAY_A
				);

				if ($updatedRow) {
					return $this->arena_scheduler_fetch_timesheet_data($updatedRow['id']);
				}
			} else {
				$response = array(
					'status' => 0,
					'message' => 'Failed to update the record'
				);

				return rest_ensure_response($response);
			}
		} else {
			$response = array(
				'status' => 0,
				'message' => 'No matching record found for the provided ID'
			);

			return rest_ensure_response($response);
		}
	}

	/**
	 * Fetches timesheet data based on a specific record ID.
	 *
	 * @param int $recordID The ID of the record to fetch.
	 * @return WP_REST_Response The fetched data or an error response if no data found.
	 */
	public function arena_scheduler_fetch_timesheet_data($recordID)
	{
		global $wpdb;

		// Table name for arena scheduled timesheet
		$tableName = $wpdb->prefix . 'arena_scheduled_timesheet';
		$tableCategories = $wpdb->prefix . 'arena_categories';

		// Retrieve data based on the provided record ID
		$record = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT timesheet.id, timesheet.arena_id, timesheet.timeslot_id, timesheet.category, timesheet.comment, timesheet.scheduled_date, categories.name, categories.color, categories.text_color
				FROM {$wpdb->prefix}arena_scheduled_timesheet AS timesheet
				INNER JOIN {$wpdb->prefix}arena_categories AS categories ON timesheet.category = categories.id
				WHERE timesheet.id = %d",
				$recordID
			)
		);

		// Check if data was fetched, and return response accordingly
		if ($record) {
			$response = array(
				'status' => 1,
				'data' => $record,
				'message' => 'Record retrieved successfully'
			);

			return rest_ensure_response($response);
		} else {
			$response = array(
				'status' => 0,
				'message' => 'No record found for the provided ID'
			);

			return rest_ensure_response($response);
		}
	}

	/**
	 * Generate time slots within a specified time range based on an interval.
	 *
	 * @param int $interval The duration of each time slot in minutes.
	 * @param string $starTime The start time for generating time slots.
	 * @param string $endTime The end time for generating time slots.
	 * @return array An array containing information about the generated time slots.
	 */
	public function arena_scheduler_get_timeslot($interval, $starTime, $endTime)
	{
		// Create DateTime objects for start and end times
		$start = new DateTime($starTime);
		$end = new DateTime($endTime);

		// Format start and end times to 'H:i' format (hours:minutes)
		$startTime = $start->format('H:i');
		$endTime = $end->format('H:i');

		// Initialize variables
		$i = 0;
		$time = [];

		// Loop through time intervals until end time is reached
		while (strtotime($startTime) <= strtotime($endTime)) {
			$start = $startTime; // Store current start time
			$end = gmdate('H:i', strtotime('+' . $interval . ' minutes', strtotime($startTime))); // Calculate end time based on interval
			$startTime = gmdate('H:i', strtotime('+' . $interval . ' minutes', strtotime($startTime))); // Update start time by adding interval
			$i++; // Increment counter

			// If the updated start time is within the end time
			if (strtotime($startTime) <= strtotime($endTime)) {
				// Store slot start and end times
				$time[$i]['slot_start_time'] = $start;
				$time[$i]['slot_end_time'] = $end;

				// Create a display string for the time slot
				$time[$i]['display_slot'] = $start . '-' . $end;

				// Create a raw string by removing non-numeric characters for comparison
				$time[$i]['slot_raw'] = preg_replace('/[^0-9]/', '', $start) . preg_replace('/[^0-9]/', '', $end);
			}
		}

		return $time; // Return the array of time slots
	}

	/**
	 * Copies arena week data to a specified week.
	 *
	 * This function manages the copying of records from one week of an arena to another.
	 * It relies on the getSlotRecords and getStartAndEndDateByWeekNo functions to handle date manipulations.
	 * The function then proceeds to process and copy records to the designated week within the database.
	 *
	 * @param WP_REST_Request $request The WordPress REST request object.
	 */
	public function arena_scheduler_copy_arena_week_data($request)
	{
		// Check the nonce for security
		$nonce_check = $this->arena_scheduler_verify_nonce(sanitize_text_field($request['_nonce']));
		if (is_wp_error($nonce_check)) {
			return $nonce_check; // Return the error response if nonce check fails
		}

		global $wpdb;

		// Get parameters from the request
		$begin = sanitize_text_field($request->get_param('begin'));
		$arenaID = sanitize_text_field($request->get_param('arena_id'));
		$copyToWeek = sanitize_text_field($request->get_param('copy_to_week'));
		$copyWeekNo = sanitize_text_field($request->get_param('copy_week_no'));

		// Get start and end dates of the source week
		$firstDay = gmdate('Y-m-d', strtotime($begin));
		$lastDay = gmdate('Y-m-d', strtotime($begin . '+ 6 days'));
		list($rsSlots, $rsComm) = $this->arena_scheduler_get_slot_records($arenaID, strtotime($firstDay), strtotime($lastDay));
		$arenaScheduledTimesheetTable = $wpdb->prefix . 'arena_scheduled_timesheet';

		// Get start and end dates of the destination week
		$rsWeek = $this->arena_scheduler_get_start_and_end_date_by_week_no($copyToWeek, gmdate('Y'));
		$scheduleStartDate = $rsWeek['week_start'];
		$scheduleEndDate = $rsWeek['week_end'];

		// Calculate the week difference for copying
		$addWeek = $copyToWeek - $copyWeekNo;

		$arrNewCopy = [];
		// Loop through slots of the source week and copy them to the destination week
		foreach ($rsSlots as $key => $val) {
			// Extracting year, month, day, and timeslot information
			$year = substr($key, 0, 4);
			$month = substr($key, 4, 2);
			$day = substr($key, 6, 2);
			$timeslot = substr($key, 8, 8);

			// Calculate scheduled date for the destination week
			$scheduledDate = gmdate('Y-m-d', strtotime("$year-$month-$day" . '+' . $addWeek . ' week'));
			$newKey = gmdate('Ymd', strtotime("$year-$month-$day" . '+ ' . $addWeek . ' week')) . $timeslot;

			// Create data for copying to the destination week
			$arrNewCopy[$newKey] = array('category' => $val, 'comment' => $rsComm[$key], 'scheduled_date' => $scheduledDate);
		}

		// Save the copied records to the database
		$status = 1;
		if (count($arrNewCopy) > 0) {
			// Insert new records to the destination week
			foreach ($arrNewCopy as $nKey => $nVal) {
				if (isset($arenaID) && $arenaID > 0 && isset($nVal['category']) && $nVal['category'] >= 0 && isset($nVal['scheduled_date']) && !empty($nVal['scheduled_date'])) {
					// Check if a record exists with specific criteria
					$findRecord = $this->arena_scheduler_is_scheduled_timesheet_exist($arenaID, $nKey, $nVal['scheduled_date']);
					if (isset($findRecord[0]->id) && !empty($findRecord[0]->id)) {
						// If a record is found, update the existing record
						$resultCheck = $wpdb->update(
							$arenaScheduledTimesheetTable,
							array(
								'arena_id' => $arenaID,
								'timeslot_id' => $nKey,
								'category' => $nVal['category'],
								'scheduled_date' => $nVal['scheduled_date'],
								'comment' => $nVal['comment']
							),
							array(
								'id' => $findRecord[0]->id // Use the found record's ID for updating
							)
						);
					} else {
						// If no record is found, perform an insert operation
						$resultCheck = $wpdb->insert(
							$arenaScheduledTimesheetTable,
							array(
								'arena_id' => $arenaID,
								'timeslot_id' => $nKey,
								'category' => $nVal['category'],
								'scheduled_date' => $nVal['scheduled_date'],
								'comment' => $nVal['comment']
							)
						);
					}

					if (is_wp_error($resultCheck)) {
						$status = 0;
					} else {
						$status = 1;
					}
				}
			}
		}

		$response = array(
			'status' => $status
		);

		return rest_ensure_response($response);
	}

	/**
	 * Get slot records within a specified date range for a particular arena.
	 *
	 * @param int    $arenaID    The ID of the arena.
	 * @param string $startDate  The start date in 'Y-m-d' format.
	 * @param string $endDate    The end date in 'Y-m-d' format.
	 * @param int    $arenaCat   Optional. The category ID of the arena (default is 0).
	 *
	 * @global wpdb $wpdb WordPress database access abstraction object.
	 *
	 * @return array An array containing two arrays: dataArr and commArr.
	 *               - dataArr contains timeslot IDs as keys and categories as values.
	 *               - commArr contains timeslot IDs as keys and comments as values.
	 */
	public function arena_scheduler_get_slot_records($arenaID, $startDate, $endDate, $arenaCat = 0)
	{
		global $wpdb;

		// Format start and end dates to 'Y-m-d' format
		$startDate = gmdate('Y-m-d', $startDate);
		$endDate = gmdate('Y-m-d', $endDate);

		$dataArr = [];
		$commArr = [];

		// Prepare and execute the database query based on provided parameters
		if ($arenaCat > 0) {
			$query = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}arena_scheduled_timesheet WHERE arena_id=%d AND scheduled_date BETWEEN %s and %s AND category=%d GROUP BY timeslot_id",
					$arenaID,
					$startDate,
					$endDate,
					$arenaCat
				),
				ARRAY_A
			);
		} else {
			$query = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}arena_scheduled_timesheet WHERE arena_id=%d AND scheduled_date BETWEEN %s and %s GROUP BY timeslot_id",
					$arenaID,
					$startDate,
					$endDate
				),
				ARRAY_A
			);
		}

		// Process the query results
		if (count($query) > 0) {
			foreach ($query as $val) {
				// Populate dataArr and commArr with timeslot data
				$dataArr[$val['timeslot_id']] = $val['category'];
				$commArr[$val['timeslot_id']] = $val['comment'];
			}
		}

		// Return an array containing dataArr and commArr
		return array($dataArr, $commArr);
	}

	/**
	 * Get the start and end date of a week based on the week number and year.
	 *
	 * @param int $week The week number.
	 * @param int $year The year.
	 *
	 * @return array An associative array containing 'week_start' and 'week_end' dates.
	 */
	public function arena_scheduler_get_start_and_end_date_by_week_no($week, $year)
	{
		// Create a new DateTime object
		$dto = new DateTime();

		// Set the ISO date based on the provided week and year
		$dto->setISODate($year, $week);

		// Get the start date of the week
		$ret['week_start'] = $dto->format('Y-m-d');

		// Modify the date to get the end of the week (6 days later for a full week)
		$dto->modify('+6 days');

		// Get the end date of the week
		$ret['week_end'] = $dto->format('Y-m-d');

		// Return the start and end dates of the week
		return $ret;
	}

	/**
	 * Check if a scheduled timesheet exists for the given arena, timeslot, and date.
	 *
	 * @param int $arenaID The ID of the arena.
	 * @param int $timeslotID The ID of the timeslot.
	 * @param string $scheduledDate The scheduled date in YYYY-MM-DD format.
	 * @return array|null The latest scheduled timesheet if found, or null if not found.
	 */
	public function arena_scheduler_is_scheduled_timesheet_exist($arenaID, $timeslotID, $scheduledDate)
	{
		global $wpdb;

		// Retrieve the latest scheduled timesheet based on arena ID, timeslot ID, and scheduled date
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, arena_id, timeslot_id, category, comment, scheduled_date
            	FROM {$wpdb->prefix}arena_scheduled_timesheet
				WHERE arena_id = %d AND timeslot_id = %d AND scheduled_date = %s
				ORDER BY id ASC
				LIMIT 1",
				$arenaID,
				$timeslotID,
				$scheduledDate
			)
		);

		// Return the results
		return $results;
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
		// Get the count of arena
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
}
