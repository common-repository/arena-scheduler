(function ($) {
	"use strict";

	$(function () {
		// Retrieve necessary data from the localized script
		var siteUrl = arena_scheduler_admin_data.site_url;
		var pluginURL = arena_scheduler_admin_data.plugin_url;
		var currentPage = arena_scheduler_admin_data.current_page;
		var nonce = arena_scheduler_admin_data.arena_scheduler_nonce;

		// DataTable initialization
		// Define common options for DataTable
		var commonOptions = {
			processing: true,
			serverSide: true,
			bInfo: false,
			bFilter: false,
			bLengthChange: false,
			bAutoWidth: false,
			ordering: false,
			processing: false,
			order: [[0, "desc"]],
			drawCallback: function (settings) {
				var api = this.api();
				var recordsTotal = api.page.info().recordsTotal;

				// Show pagination only if there are more than 10 records
				if (recordsTotal > 10) {
					$(api.table().container())
						.find(".dt-paging")
						.show();
				} else {
					$(api.table().container())
						.find(".dt-paging")
						.hide();
				}
			},
			// dom: "<'row'<'col-sm-12'tr>>",
		};

		// Define specific options based on the current page
		var specificOptions = {};
		if (currentPage == "toplevel_page_arena-schedule") {
			specificOptions = {
				ajax: {
					url: siteUrl + "/wp-admin/admin-ajax.php",
					type: "POST",
					data: {
						action: "arena_scheduler_get_arenas",
						_ajax_nonce: nonce
					},
					dataType: "json",
					dataSrc: function (json) {
						// Show or hide the add button based on permission
						if (json.canPerformAction === true) {
							$('.add-button').show();
						} else {
							$('.add-button').hide();
						}

						return json.data; // Return the actual data to DataTable
					}
				},
				columns: [
					{ data: "id", width: "10%" },
					{ data: "name", width: "50%" },
					{
						data: "status",
						render: function (data, type, row) {
							return data == "1" ? "Active" : "Inactive";
						},
						width: "20%",
					},
					{
						data: "is_default",
						render: function (data, type, row) {
							if (data == "1") {
								return (
									'<img src="' + pluginURL + '/admin/images/CheckIcon.svg" alt="Default" width="18" height="18">'
								);
							} else {
								return "";
							}
						},
						width: "10%",
					},
					{
						data: null,
						render: function (data, type, row) {
							return (
								'<button class="btn btn-link btn-sm p-0 update" data-action="arena_scheduler_get_arena"><img src="' +
								pluginURL +
								'/admin/images/EditIcon.svg" alt="Edit" width="18" height="18"></button>'
							);
						},
						width: "10%",
					},
				],
			};
		} else {
			specificOptions = {
				ajax: {
					url: siteUrl + "/wp-admin/admin-ajax.php",
					type: "POST",
					data: {
						action: "arena_scheduler_get_arena_categories",
						_ajax_nonce: nonce
					},
					dataType: "json",
					dataSrc: function (json) {
						// Show or hide the add button based on permission
						$('.add-button').toggle(json.canPerformAction);

						// Correctly set recordsTotal and recordsFiltered
						json.recordsTotal = json.recordsTotal[0].count;
						json.recordsFiltered = json.recordsFiltered[0].count;

						return json.data;
					}
				},
				columns: [
					{ data: "id", width: "10%" },
					{ data: "name", width: "40%" },
					{ data: "color", width: "10%" },
					{
						data: "status",
						render: function (data, type, row) {
							return data == "1" ? "Active" : "Inactive";
						},
						width: "10%",
					},
					{
						data: null,
						render: function (data, type, row) {
							return (
								'<button class="btn btn-link btn-sm p-0 update" data-action="arena_scheduler_get_arena_category"><img src="' +
								pluginURL +
								'/admin/images/EditIcon.svg" alt="Edit" width="18" height="18"></button>'
							);
						},
						width: "10%",
					},
				],
				createdRow: function (row, data, dataIndex) {
					var colorValue = data.color;
					var textColorValue = data.text_color;

					// Apply color and text color to the specific column
					$("td:eq(2)", row).css({
						"background-color": colorValue,
						"color": textColorValue
					});
				},
			};
		}

		// Combine common and specific options
		var dataTableOptions = $.extend({}, commonOptions, specificOptions);

		// Initialize DataTable
		var dataTable = $("#datatable-" + currentPage).DataTable(dataTableOptions);

		// Show custom loader on DataTable processing
		dataTable.on('processing.dt', function (e, settings, processing) {
			if (processing) {
				$('#loader').show();
			} else {
				$('#loader').hide();
			}
		});

		// Validate the form data
		$("#frmSubmit").validate();

		// Submit form
		$("#frmSubmit").submit(function (e) {
			e.preventDefault(); // Prevent form submission

			// Serialize form data
			var formData = new FormData(this);
			formData.append('_ajax_nonce', nonce);

			// AJAX call to WordPress backend
			if ($(this).valid()) {
				$.ajax({
					type: "POST",
					url: siteUrl + "/wp-admin/admin-ajax.php", // Use the AJAX URL provided by WordPress
					data: formData,
					processData: false,
					contentType: false,
					success: function (response) {
						// Handle the success response
						if (response === 1) {
							// Refresh the DataTable
							dataTable.ajax.reload();

							// Reset form data
							$("#frmSubmit")[0].reset();

							// Hide the modal after successful update
							$("#createModal").modal("hide");
						} else {
							// Handle the error response
							$.toast({
								heading: 'Error',
								text: response.data.message,
								'icon': 'error',
								position: 'bottom-right',
								stack: false
							});
						}
					},
					error: function (error) {
						// Handle the error response
						$.toast({
							heading: 'Error',
							text: error,
							'icon': 'error',
							position: 'bottom-right',
							stack: false
						});
					},
				});
			}
		});

		// Event listener for the "Update" button click within the DataTable
		$(".datatable").on("click", ".update", function () {
			// Get the data-action attribute value from the clicked button
			var action = $(this).data("action");

			// Get the DataTable row data associated with the clicked button
			var row = dataTable.row($(this).parents("tr")).data();

			// Call the function to get record details based on the row ID and the specified action
			getRecord(row.id, action);
		});

		// Fetches record details using AJAX based on the provided record ID and action.
		function getRecord(recordId, action) {
			$.ajax({
				url: siteUrl + "/wp-admin/admin-ajax.php",
				type: "POST",
				dataType: "json",
				data: {
					record_id: recordId,
					action: action,
					_ajax_nonce: nonce
				},
				success: function (response) {
					if (response.id > 0) {
						// Populate the modal with the retrieved data
						if ($("#updateModal #id").length) {
							$("#updateModal #id").val(response.id);
						}

						if ($("#updateModal #name").length) {
							$("#updateModal #name").val(response.name);
						}

						if ($("#updateModal #interval_time").length) {
							$("#updateModal #interval_time").val(response.interval_time);
						}

						if ($("#updateModal #start_time").length) {
							$("#updateModal #start_time").val(response.start_time);
						}

						if ($("#updateModal #end_time").length) {
							$("#updateModal #end_time").val(response.end_time);
						}

						if ($("#updateModal #color").length) {
							$("#updateModal #color").val(response.color);
							$("#updateModal #color").trigger("change");
						}

						if ($("#updateModal #text_color").length) {
							$("#updateModal #text_color").val(response.text_color);
							$("#updateModal #text_color").trigger("change");
						}

						if ($("#updateModal #status").length) {
							$("#updateModal #status").val(response.status);
						}

						if ($("#updateModal #default").length) {
							if (response.is_default == 1) {
								$("#updateModal #default")
									.prop("checked", true)
									.change();
							} else {
								$("#updateModal #default")
									.prop("checked", false)
									.change();
							}
						}

						// Show the modal
						$("#updateModal").modal("show");
					}
				},
				error: function (error) {
					// Handle the error response
					$.toast({
						heading: 'Error fetching record details:',
						text: error,
						'icon': 'error',
						position: 'bottom-right',
						stack: false
					});
				},
			});
		}

		// Validate the form data
		$("#frmUpdate").validate();

		// Update form
		$("#frmUpdate").submit(function (e) {
			// Prevent the default form submission behavior
			e.preventDefault();

			// Retrieve form data, perform validation, and update record
			var formData = $("#frmUpdate").serialize();
			formData += "&_ajax_nonce=" + nonce;

			if ($(this).valid()) {
				$.ajax({
					url: siteUrl + "/wp-admin/admin-ajax.php",
					type: "POST",
					data: formData,
					success: function (response) {
						// Refresh the DataTable
						dataTable.ajax.reload();

						// Reset form data
						$("#frmUpdate")[0].reset();

						// Hide the modal after successful update
						$("#updateModal").modal("hide");
					},
					error: function (error) {
						// Handle the error response
						$.toast({
							heading: 'Error updating record:',
							text: error,
							'icon': 'error',
							position: 'bottom-right',
							stack: false
						});
					},
				});
			}
		});

		// Attach a click event listener to the delete links
		$("#delete").click(function (e) {
			e.preventDefault(); // Prevent the default link behavior

			// Get the data attributes from the link
			var recordId = $("#updateModal #id").val();
			var action = $(this).data("action");

			// Confirm deletion with the user
			var confirmDelete = confirm("Are you sure you want to delete this record?");

			if (confirmDelete) {
				// User confirmed, proceed with AJAX delete
				$.ajax({
					url: siteUrl + "/wp-admin/admin-ajax.php",
					type: "POST",
					data: {
						action: action,
						record_id: recordId,
						_ajax_nonce: nonce
					},
					success: function (response) {
						if (response.status == "1") {
							// Refresh the DataTable
							dataTable.ajax.reload();

							// Hide the modal after successful update
							$("#updateModal").modal("hide");
						}
					},
					error: function (error) {
						// Handle the error response
						$.toast({
							heading: 'Error deleting record:',
							text: error,
							'icon': 'error',
							position: 'bottom-right',
							stack: false
						});
					},
				});
			}
		});
	});
})(jQuery);
