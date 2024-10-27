<?php

/**
 * Freemius SDK Initialization
 *
 * This file contains the Freemius SDK initialization class
 * and the hook for the uninstall cleanup function.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 */

defined('ABSPATH') || exit;

/**
 * Freemius SDK Initialization Class
 *
 * This class initializes the Freemius SDK for managing subscriptions and licensing.
 *
 * @since      1.0.0
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/includes
 * @author     Level5 <support@level5.se>
 */
class Arena_Scheduler_Freemius
{
    /**
     * Holds the instance of the Freemius SDK.
     *
     * @var object
     */
    private static $freemius;

    /**
     * Initializes the Freemius SDK.
     *
     * @return object The Freemius instance.
     */
    public static function arena_scheduler_freemius_int()
    {
        if (!isset(self::$freemius)) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/../freemius/start.php';

            self::$freemius = fs_dynamic_init(array(
                'id'                  => '15592',
                'slug'                => 'arena-schedule',
                'type'                => 'plugin',
                'public_key'          => 'pk_dc164e0d72384a4cdfeb77abf35fb',
                'is_premium'          => true,
                'premium_suffix'      => 'Pro',
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'arena-schedule',
                    'first-path'     => 'admin.php?page=arena-schedule',
                    'support'        => false
                ),
            ));
        }

        // Init Freemius.
        do_action('arena_scheduler_freemius_sdk_loaded');

        return self::$freemius;
    }

    /**
     * Check if the user is using the free version.
     *
     * @return bool True if the user is on the free version, false otherwise.
     */
    public static function arena_scheduler_is_free_plan()
    {
        $freemius_sdk = self::arena_scheduler_freemius_int();

        return $freemius_sdk->is_free_plan();
    }

    /**
     * Checks if the current user is a paying customer.
     *
     * This function uses the Freemius instance to check if the current user
     * has an active paid subscription.
     *
     * @return bool True if the user is a paying customer, false otherwise.
     */
    public static function arena_scheduler_is_paying()
    {
        $freemius_sdk = self::arena_scheduler_freemius_int();

        return $freemius_sdk->is_paying();
    }
}

// Hook the uninstall cleanup function to Freemius.
if (class_exists('Arena_Scheduler_Freemius')) {
    Arena_Scheduler_Freemius::arena_scheduler_freemius_int()->add_action('after_uninstall', array('Arena_Scheduler_Uninstaller', 'arena_scheduler_uninstall'));
}
