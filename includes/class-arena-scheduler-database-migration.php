<?php

/**
 * Database migration
 *
 * This file is used to handle the database migration process for the Arena Scheduler plugin.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 */

defined('ABSPATH') || exit;

/**
 * Class Arena_Scheduler_Database_Migration
 *
 * This class handles the database migration process for the Arena Scheduler plugin.
 * It is responsible for creating or updating database tables, migrating data, etc.
 *
 * @since      1.0.0
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 * @author     Level5 <support@level5.se>
 */
class Arena_Scheduler_Database_Migration
{
	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Last released version of the plugin.
	 *
	 * @var string
	 */
	public $release = '1.0.2'; // Keep last released version

	/**
	 * Constructor for the Arena Scheduler class.
	 *
	 * This method initializes the Arena Scheduler plugin by retrieving the version from the WordPress options table.
	 * If the version is found, it sets the plugin's version property to the retrieved version.
	 * If the version is not found, it sets the plugin's version property to the first version ('1.0.0') and updates
	 * the version stored in the WordPress options table accordingly.
	 */
	public function __construct()
	{
		// Checks the plugin version and performs necessary updates if required.
		$this->arena_scheduler_check_version();
	}

	/**
	 * Checks the plugin version and performs necessary updates if required.
	 *
	 * This method retrieves the version from the WordPress options table and compares it
	 * with the release version. If the versions do not match, it triggers the arena_scheduler_release_version()
	 * method to perform necessary updates. If the version is not found in the options table,
	 * it sets the plugin's version property to the first version ('1.0.0') and updates the
	 * version stored in the options table accordingly.
	 *
	 * @return void
	 */
	public function arena_scheduler_check_version()
	{
		// Retrieve the version from the WordPress options table
		$version = $this->arena_scheduler_get_version();

		// Check if the version is found
		if ($version) {
			// If found, set the plugin's version property to the retrieved version
			$this->version = $version;

			// Check if the retrieved version matches the release version
			if ($this->version !== $this->release) {
				// If not, trigger the arena_scheduler_release_version() method to perform necessary updates
				$this->arena_scheduler_release_version();
			}
		} else {
			// If not found, set the plugin's version property to the first version ('1.0.0')
			$first_version = '1.0.0';
			$this->version = $first_version;

			// Update the version stored in the WordPress options table
			update_option('arena_scheduler_migration_version', $first_version);
		}
	}

	/**
	 * Get the database migration version of the Arena Scheduler plugin.
	 *
	 * This method retrieves the database migration version of the Arena Scheduler plugin from the WordPress options table.
	 * The database migration version indicates the version of the plugin's database schema after migration.
	 *
	 * @return string|null The database migration version of the plugin, or null if not found.
	 */
	public function arena_scheduler_get_version()
	{
		return get_option('arena_scheduler_migration_version');
	}

	/**
	 * Release the migration version of the Arena Scheduler plugin.
	 *
	 * This method sets the release migration version of the Arena Scheduler plugin and updates the version
	 * stored in the WordPress options table.
	 *
	 * @return void
	 */
	public function arena_scheduler_release_version()
	{
		// Update the plugin's version property
		$this->version = $this->release;

		// Update the version stored in the WordPress options table
		update_option('arena_scheduler_migration_version', $this->release);
	}

	/**
	 * Run the database migration process.
	 *
	 * This method is responsible for executing the database migration process
	 * for the Arena Scheduler plugin. It performs actions such as creating or
	 * updating database tables, migrating data, etc.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function arena_scheduler_run()
	{
		$version = $this->arena_scheduler_get_version();
		switch ($version) {
			case '1.0.0':
				$this->arena_scheduler_create_arena_table();
				$this->arena_scheduler_create_arena_categories_table();
				$this->arena_scheduler_create_arena_scheduled_timesheet_table();

				// Update the migration version stored in the WordPress options table
				update_option('arena_scheduler_migration_version', '1.0.0');
				break;
			case '1.0.1':
				$this->arena_scheduler_alter_arena_table();
				$this->arena_scheduler_alter_arena_categories_table();

				// Update the migration version stored in the WordPress options table
				update_option('arena_scheduler_migration_version', '1.0.1');
				break;
			case '1.0.2':
				$this->arena_scheduler_add_text_color_to_arena_categories_table();

				// Update the migration version stored in the WordPress options table
				update_option('arena_scheduler_migration_version', '1.0.2');
				break;
			default:
				// Handle tasks for other versions or if version is not found
				wp_die('Invalid');
				break;
		}
	}

	/**
	 * Create the 'arena' table in the WordPress database.
	 *
	 * This function creates a new table named 'arena' with the specified structure:
	 * - id: An auto-incrementing primary key field of type INT(10) UNSIGNED.
	 * - name: A field to store the arena name of type VARCHAR(255).
	 * - interval_time: A field to store the interval time of type VARCHAR(255), nullable.
	 * - start_time: A field to store the start time of type VARCHAR(255), nullable.
	 * - end_time: A field to store the end time of type VARCHAR(255), nullable.
	 * - is_default: A field to indicate if the arena is the default one, of type TINYINT(4), nullable.
	 * - status: A field to store the status of type INT(11) with a default value of 1.
	 * - created_at: A field to store the creation timestamp of type DATETIME with a default value of the current timestamp.
	 *
	 * @global wpdb $wpdb WordPress database access abstraction object.
	 * @return void
	 */
	public function arena_scheduler_create_arena_table()
	{
		global $wpdb; // Access WordPress database object

		$table_name = $wpdb->prefix . 'arena'; // Get the prefixed table name

		// Check if the table already exists
		$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}arena'");

		// If the table doesn't exist, create it
		if ($table_exists != $table_name) {
			// SQL query to create the 'arena' table
			$sql = "CREATE TABLE {$wpdb->prefix}arena (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            interval_time INT(11) NULL,
            start_time VARCHAR(255) NULL,
            end_time VARCHAR(255) NULL,
            is_default TINYINT(4) NULL,
            status INT(11) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP)";

			// Execute the SQL query to create the table
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

	/**
	 * Create the 'arena_categories' table in the WordPress database.
	 *
	 * This function creates a new table named 'arena_categories' with the specified structure:
	 * - id: An auto-incrementing primary key field of type INT(10) UNSIGNED.
	 * - name: A field to store the arena category name of type VARCHAR(255).
	 * - color: A field to store the category color of type VARCHAR(255).
	 * - text_color: A field to store the text color of type VARCHAR(255) with a default value of '#000000'.
	 * - is_default: A field to indicate if the category is the default one, of type TINYINT(4).
	 * - status: A field to store the status of type INT(11) with a default value of 1.
	 * - created_at: A field to store the creation timestamp of type DATETIME with a default value of the current timestamp.
	 *
	 * @global wpdb $wpdb WordPress database access abstraction object.
	 * @return void
	 */
	public function arena_scheduler_create_arena_categories_table()
	{
		global $wpdb; // Access WordPress database object

		$table_name = $wpdb->prefix . 'arena_categories'; // Get the prefixed table name

		// Check if the table already exists
		$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}arena_categories'");

		// If the table doesn't exist, create it
		if ($table_exists != $table_name) {
			// SQL query to create the 'arena_categories' table
			$sql = "CREATE TABLE {$wpdb->prefix}arena_categories (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NULL,
            color VARCHAR(255) NULL,
            text_color VARCHAR(255) DEFAULT '#000000',
            is_default TINYINT(4) NULL,
            status INT(11) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP)";

			// Execute the SQL query to create the table
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

	/**
	 * Create the 'arena_scheduled_timesheet' table in the WordPress database.
	 *
	 * This function creates a new table named 'arena_scheduled_timesheet' with the specified structure:
	 * - id: An auto-incrementing primary key field of type INT(10) UNSIGNED.
	 * - arena_id: A field to store the Arena Id of type INT(10).
	 * - timeslot_id: A field to store the dropdown slot id of type BIGINT(15) UNSIGNED.
	 * - category: A field to store the category of type INT(11).
	 * - comment: A field to store comments of type VARCHAR(255) (nullable).
	 * - scheduled_date: A field to store the slot booked date of type DATE.
	 * - created_at: A field to store the creation timestamp of type DATETIME with a default value of the current timestamp.
	 *
	 * @global wpdb $wpdb WordPress database access abstraction object.
	 * @return void
	 */
	public function arena_scheduler_create_arena_scheduled_timesheet_table()
	{
		global $wpdb; // Access WordPress database object

		$arena_table = $wpdb->prefix . 'arena'; // Get the prefixed table name
		$arena_categories = $wpdb->prefix . 'arena_categories'; // Get the prefixed table name
		$arena_scheduled_timesheet_table = $wpdb->prefix . 'arena_scheduled_timesheet'; // Get the prefixed table name

		// Check if the table already exists
		$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}arena_scheduled_timesheet'");

		// If the table doesn't exist, create it
		if ($table_exists != $arena_scheduled_timesheet_table) {
			// SQL query to create the 'arena_scheduled_timesheet' table
			$sql = "CREATE TABLE {$wpdb->prefix}arena_scheduled_timesheet (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            arena_id INT(10) UNSIGNED,
            timeslot_id BIGINT(15) UNSIGNED,
            category INT(10) UNSIGNED,
            comment VARCHAR(255) NULL,
            scheduled_date DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (arena_id) REFERENCES {$arena_table}(id) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (category) REFERENCES {$arena_categories}(id) ON DELETE CASCADE ON UPDATE CASCADE)";

			// Execute the SQL query to create the table
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

	/**
	 * Alter the 'arena' table in the database.
	 *
	 * This method checks if specific columns ('interval_time', 'start_time', 'end_time', 'is_default')
	 * exist in the 'arena' table. If any of them does not exist, it adds the missing columns.
	 *
	 * @return void
	 */
	public function arena_scheduler_alter_arena_table()
	{
		global $wpdb; // Access WordPress database object

		// Check if the columns already exist
		$interval_exists = $wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}arena LIKE 'interval_time'");
		$start_exists = $wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}arena LIKE 'start_time'");
		$end_exists = $wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}arena LIKE 'end_time'");
		$is_default_exists = $wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}arena LIKE 'is_default'");

		// Add 'interval_time' column if it does not exist
		if (!$interval_exists) {
			$wpdb->query("ALTER TABLE {$wpdb->prefix}arena ADD COLUMN interval_time INT(11) NULL AFTER name");
		}

		// Add 'start_time' column if it does not exist
		if (!$start_exists) {
			$wpdb->query("ALTER TABLE {$wpdb->prefix}arena ADD COLUMN start_time VARCHAR(255) NULL AFTER interval_time");
		}

		// Add 'end_time' column if it does not exist
		if (!$end_exists) {
			$wpdb->query("ALTER TABLE {$wpdb->prefix}arena ADD COLUMN end_time VARCHAR(255) NULL AFTER start_time");
		}

		// Add 'is_default' column if it does not exist
		if (!$is_default_exists) {
			$wpdb->query("ALTER TABLE {$wpdb->prefix}arena ADD COLUMN is_default TINYINT(4) NULL AFTER end_time");
		}
	}

	/**
	 * Alter the 'arena_categories' table in the database.
	 *
	 * This method checks if the 'is_default' column exists in the 'arena_categories' table.
	 * If it does not exist, it adds the missing column.
	 *
	 * @return void
	 */
	public function arena_scheduler_alter_arena_categories_table()
	{
		global $wpdb; // Access WordPress database object

		// Check if the 'is_default' column already exists
		$is_default_exists = $wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}arena_categories LIKE 'is_default'");

		// Add 'is_default' column if it does not exist
		if (!$is_default_exists) {
			$wpdb->query("ALTER TABLE {$wpdb->prefix}arena_categories ADD COLUMN is_default TINYINT(4) NULL AFTER color");
		}
	}

	/**
	 * Add or alter the 'text_color' column in the 'arena_categories' table in the database.
	 *
	 * This method checks if the 'text_color' column exists in the 'arena_categories' table.
	 * If it does not exist, it adds the missing column with a default value of '#000000'.
	 *
	 * @return void
	 */
	public function arena_scheduler_add_text_color_to_arena_categories_table()
	{
		global $wpdb; // Access WordPress database object

		// Check if the 'text_color' column already exists
		$text_color_exists = $wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}arena_categories LIKE 'text_color'");

		// Add 'text_color' column if it does not exist
		if (!$text_color_exists) {
			$wpdb->query("ALTER TABLE {$wpdb->prefix}arena_categories ADD COLUMN text_color VARCHAR(255) DEFAULT '#000000' AFTER color");
		}
	}
}
