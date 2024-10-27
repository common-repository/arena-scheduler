<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://level5.se
 * @since             1.0.0
 * @package           Arena_Scheduler
 *
 * @wordpress-plugin
 * Plugin Name:       Arena Scheduler
 * Plugin URI:        https://level5.se
 * Description:       An easy and professional way to organize and schedule arena activities.
 * Version:           1.0.10
 * Author:            Level5
 * Author URI:        https://level5.se/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       arena-scheduler
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ARENA_SCHEDULER_VERSION', '1.0.10');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-arena-scheduler-activator.php
 */
function arena_scheduler_activate()
{
	// Load the activator class responsible for setting up the plugin on activation.
	require_once plugin_dir_path(__FILE__) . 'includes/class-arena-scheduler-activator.php';

	// Call the activation method.
	Arena_Scheduler_Activator::arena_scheduler_activate();
}
register_activation_hook(__FILE__, 'arena_scheduler_activate');

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-arena-scheduler-deactivator.php
 */
function arena_scheduler_deactivate()
{
	// Load the deactivator class responsible for cleaning up the plugin on deactivation.
	require_once plugin_dir_path(__FILE__) . 'includes/class-arena-scheduler-deactivator.php';

	// Call the deactivation method.
	Arena_Scheduler_Deactivator::arena_scheduler_deactivate();
}
register_deactivation_hook(__FILE__, 'arena_scheduler_deactivate');

/**
 * The core plugin class for the Arena Scheduler plugin.
 * This class defines internationalization, admin-specific hooks,
 * and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-arena-scheduler.php';

/**
 * Runs the database migration process for the Arena Scheduler plugin.
 * This ensures that any necessary database changes are applied when the plugin is updated.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-arena-scheduler-database-migration.php';

/**
 * Include the uninstaller class for the Arena Scheduler plugin.
 * This class handles the cleanup operations when the plugin is uninstalled.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-arena-scheduler-uninstaller.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function arena_scheduler_run()
{
	// Instantiate the core plugin class.
	$plugin = new Arena_Scheduler();

	// Call the run method to execute the plugin.
	$plugin->arena_scheduler_run();
}
arena_scheduler_run();
