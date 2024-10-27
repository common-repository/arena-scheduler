<?php

/**
 * Fired during plugin deactivation
 *
 * This file is used to define what happens when the plugin is deactivated.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 */

defined('ABSPATH') || exit;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 * @author     Level5 <support@level5.se>
 */
class Arena_Scheduler_Deactivator
{
	/**
	 * Handle the plugin deactivation process.
	 *
	 * This method runs the necessary code during plugin deactivation.
	 * Currently, no specific actions are defined for deactivation, but this
	 * method provides a placeholder for future deactivation tasks.
	 *
	 * @since    1.0.0
	 */
	public static function arena_scheduler_deactivate()
	{
		// Add deactivation tasks here if needed in the future.
	}
}
