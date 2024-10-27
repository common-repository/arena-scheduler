<?php

/**
 * Fired during plugin activation
 *
 * This file is used to define what happens when the plugin is activated.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 */

defined('ABSPATH') || exit;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 * @author     Level5 <support@level5.se>
 */
class Arena_Scheduler_Activator
{
	/**
	 * Handle the plugin activation process.
	 *
	 * This method runs the database migration necessary during activation.
	 *
	 * @since    1.0.0
	 */
	public static function arena_scheduler_activate()
	{
		// Instantiate the database migration class
		$db_migration = new Arena_Scheduler_Database_Migration();

		// Run the database migration to set up necessary tables
		$db_migration->arena_scheduler_run();
	}
}
