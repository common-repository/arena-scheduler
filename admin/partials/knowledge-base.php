<?php

/**
 * Provide a knowledge base view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://level5.se
 * @since      1.0.0
 *
 * @package    Arena_Scheduler
 * @subpackage Arena_Scheduler/admin/partials
 */

defined('ABSPATH') || exit;
?>

<section class="wrap manage-arena container pt-3">
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-12 mb-3">
            <div class="d-flex justify-content-between align-items-center bg-red mb-3 px-3">
                <h3 class="wp-heading-inline text-white">Knowledge Base</h3>
            </div>
            <!-- <div class="px-3 py-2 bg-white"></div> -->
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
            <!-- Support card with links to knowledge base and ticket system -->
            <?php echo wp_kses_post($this->arena_scheduler_support_card_html()); ?>
        </div>
    </div>
</section>
