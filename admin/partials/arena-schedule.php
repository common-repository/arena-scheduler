<?php

/**
 * Provide a admin area view for the plugin
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
                <h3 class="wp-heading-inline text-white">Arena</h3>
            </div>
            <div class="px-3 py-2 bg-white">
                <div class="d-flex justify-content-end align-items-center">
                    <?php if ($canPerformArenaAction) : ?>
                        <!-- Button to open the Create Modal -->
                        <button type="button" data-bs-toggle="modal" data-bs-target="#createModal" class="btn btn-link action-button add-button pe-0">
                            <img src="<?php echo esc_url($this->plugin_url); ?>/admin/images/AddIcon.svg" alt="Add" />
                            <span class="ps-2 align-text-top">Add</span>
                        </button>
                    <?php endif; ?>
                </div>
                <!-- Loader animation -->
                <div id="loader">
                    <img src="<?php echo esc_url($this->plugin_url); ?>/admin/images/Spin.gif" alt="Loading..." width="68">
                </div>
                <!-- DataTable for displaying arenas -->
                <table id="datatable-toplevel_page_arena-schedule" class="datatable table table-responsive table-bordered border table-striped" style="display: inline-table;">
                    <thead>
                        <tr>
                            <th class="text-center">Number</th>
                            <th class="text-start">Name</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Default</th>
                            <th class="text-center">Edit</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
            <!-- Support card with links to knowledge base and ticket system -->
            <?php echo wp_kses_post($this->arena_scheduler_support_card_html()); ?>
        </div>
    </div>
</section>

<!-- Create Modal -->
<div id="createModal" class="arena-action-modal modal fade" tabindex="-1" aria-labelledby="createModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-0">
            <form method="post" id="frmSubmit">
                <div class="modal-header py-0 pe-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Create Arena</h4>
                    <button type="button" class="modal-close close border-0" data-bs-dismiss="modal" aria-label="Close">
                        <img src="<?php echo esc_url($this->plugin_url); ?>/admin/images/RectangleIcon.svg" class="p-2" height="40" alt="Close" />
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required" maxlength="30">
                    </div>
                    <div class="form-group mb-3">
                        <label for="interval_time" class="form-label">Interval Time</label>
                        <select class="form-control" id="interval_time" name="interval_time" required="required">
                            <option value="15">15</option>
                            <option value="30">30</option>
                            <option value="60">60</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="number" class="form-control" id="start_time" name="start_time" placeholder="00" min="1" max="24" required="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="number" class="form-control" id="end_time" name="end_time" placeholder="00" min="1" max="24" required="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required="required">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer pt-0">
                    <input type="hidden" name="action" id="action" value="arena_scheduler_create_arena" />
                    <input type="submit" name="create" id="create" class="btn action-button" value="Save" />
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Modal -->
<div id="updateModal" class="arena-action-modal modal fade" tabindex="-1" aria-labelledby="updateModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-0">
            <form method="post" id="frmUpdate">
                <div class="modal-header py-0 pe-0 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Arena</h4>
                    <button type="button" class="modal-close close border-0" data-bs-dismiss="modal" aria-label="Close">
                        <img src="<?php echo esc_url($this->plugin_url); ?>/admin/images/RectangleIcon.svg" class="p-2" height="40" alt="Close" />
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
                    </div>
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required="required">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group d-flex align-items-center mt-3">
                        <label for="default" class="form-check-label me-2 mt-0">Set as default</label>
                        <input type="checkbox" class="form-check-input mt-0" name="default" id="default">
                    </div>
                </div>
                <div class="modal-footer pt-0 d-flex justify-content-between">
                    <input type="hidden" name="id" id="id" value="" readonly />
                    <input type="hidden" name="action" id="action" value="arena_scheduler_update_arena" readonly />
                    <!-- Left side: Delete button -->
                    <button type="button" id="delete" class="btn btn-link text-decoration-none action-remove p-0" data-action="arena_scheduler_delete_arena">
                        <img src="<?php echo esc_url($this->plugin_url); ?>/admin/images/DeleteIcon.svg" class="text-decoration-underline" alt="Delete" />
                        <span class="align-middle ps-1">Delete</span>
                    </button>
                    <!-- Right side: Save button -->
                    <input type="submit" name="save" id="save" class="btn action-button" value="Save" />
                </div>
            </form>
        </div>
    </div>
</div>
