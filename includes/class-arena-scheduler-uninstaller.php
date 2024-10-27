<?php

/**
 * The uninstaller class for Arena Scheduler plugin.
 *
 * This class defines all code necessary to run during the plugin's uninstallation.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 */

defined('ABSPATH') || exit;

/**
 * Class Arena_Scheduler_Uninstaller
 *
 * This class handles the uninstallation process for the Arena Scheduler plugin.
 * It performs tasks such as deleting custom database tables and removing options from the WordPress database.
 *
 * @since      1.0.0
 * @package    Arena_Scheduler
 */
class Arena_Scheduler_Uninstaller
{

	/**
	 * Cleanup tasks to be performed on plugin uninstall.
	 *
	 * This method handles the uninstallation process, which includes deleting custom tables from the database
	 * and removing plugin-specific options from the WordPress options table.
	 *
	 * @since    1.0.0
	 */
	public static function arena_scheduler_uninstall()
	{
		global $wpdb;

		// Delete the custom tables if they exist
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}arena_scheduled_timesheet");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}arena_categories");
		$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}arena");

		// Remove the migration version option
		delete_option('ARENA_SCHEDULER_MIGRATION_VERSION');

		// Remove the plugin version option
		delete_option('ARENA_SCHEDULER_VERSION');
	}
}
