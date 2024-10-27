<?php

/**
 * Define the internationalization functionality
 *
 * This file is used to load and define the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 */

defined('ABSPATH') || exit;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 * @author     Level5 <support@level5.se>
 */
class Arena_Scheduler_i18n
{
	/**
	 * Load the plugin text domain for translation.
	 *
	 * This method loads the text domain for the Arena Scheduler plugin, allowing
	 * for localization and translation of the plugin's strings.
	 *
	 * @since    1.0.0
	 */
	public function arena_scheduler_load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'arena-scheduler', // The text domain identifier
			'', // Deprecated argument, set to false
			dirname(dirname(plugin_basename(__FILE__))) . '/languages/' // Path to the language files
		);
	}
}
