(function ($) {
	'use strict';
	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	//
	// Updates "Select all" control in a data table
	//

	var $j = jQuery.noConflict();
	$j( document ).ready(
		function () {

			function updateDataTableSelectAllCtrl(table)
			{
				var $table            = table.table().node();
				var $chkbox_all       = $( 'tbody input[type="checkbox"]', $table );
				var $chkbox_checked   = $( 'tbody input[type="checkbox"]:checked', $table );
				var chkbox_select_all = $( 'thead input[name="select_all"]', $table ).get( 0 );
				// If none of the checkboxes are checked
				if ($chkbox_checked.length === 0) {
					chkbox_select_all.checked = false;
					if ('indeterminate' in chkbox_select_all) {
						chkbox_select_all.indeterminate = false;
					}

					// If all of the checkboxes are checked
				} else if ($chkbox_checked.length === $chkbox_all.length) {
					chkbox_select_all.checked = true;
					if ('indeterminate' in chkbox_select_all) {
						chkbox_select_all.indeterminate = false;
					}

					// If some of the checkboxes are checked
				} else {
					chkbox_select_all.checked = true;
					if ('indeterminate' in chkbox_select_all) {
						chkbox_select_all.indeterminate = true;
					}
				}
			}

			// Array holding selected row IDs
			var rows_selected = [];
			//        var table = $j('#accu_backup_backups_list').DataTable();
			var table = $j( '#accu_backup_backups_list' ).DataTable(
				{
					'columnDefs': [{
						'orderable': false,
						'targets': [0],
						'searchable': false,
						'width': '1%',
						'className': 'dt-body-center',
					}],
					'order': [1],
					'aoColumnDefs': [{
						'bSortable': false,
						'aTargets': ['nosort']

					}],
					'rowCallback': function (row, data, dataIndex) {
						// Get row ID
						var rowId = data[0];
						// If row ID is in the list of selected row IDs
						if ($.inArray( rowId, rows_selected ) !== -1) {
							$( row ).find( 'input[type="checkbox"]' ).prop( 'checked', true );
							$( row ).addClass( 'selected' );
						}
					}
				}
			);
			// Handle click on checkbox
			$( '#accu_backup_backups_list tbody' ).on(
				'click', 'input[type="checkbox"]', function (e) {
					var $row = $( this ).closest( 'tr' );
					// Get row data
					var data = table.row( $row ).data();
					// Get row ID
					var rowId = data[0];
					// Determine whether row ID is in the list of selected row IDs
					var index = $.inArray( rowId, rows_selected );
					// If checkbox is checked and row ID is not in list of selected row IDs
					if (this.checked && index === -1) {
						rows_selected.push( rowId );
						// Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
					} else if ( ! this.checked && index !== -1) {
						rows_selected.splice( index, 1 );
					}

					if (this.checked) {
						$row.addClass( 'selected' );
					} else {
						$row.removeClass( 'selected' );
					}

					// Update state of "Select all" control
					updateDataTableSelectAllCtrl( table );
					// Prevent click event from propagating to parent
					e.stopPropagation();
				}
			);
			// Handle click on table cells with checkboxes
			$( '#accu_backup_backups_list' ).on(
				'click', 'tbody td, thead th:first-child', function (e) {
					$( this ).parent().find( 'input[type="checkbox"]' ).trigger( 'click' );
				}
			);
			// Handle click on "Select all" control
			$( 'thead input[name="select_all"]', table.table().container() ).on(
				'click', function (e) {
					if (this.checked) {
						$( '#accu_backup_backups_list tbody input[type="checkbox"]:not(:checked)' ).trigger( 'click' );
					} else {
						$( '#accu_backup_backups_list tbody input[type="checkbox"]:checked' ).trigger( 'click' );
					}

					// Prevent click event from propagating to parent
					e.stopPropagation();
				}
			);
			// Handle table draw event
			table.on(
				'draw', function () {
					// Update state of "Select all" control
					//updateDataTableSelectAllCtrl(table);
				}
			);
			function check_row_count()
			{

				var get_current_rows = $( '#accu_backup_backups_list tbody input[type="checkbox"]' ).length;
				if (get_current_rows <= 0) {
					$( "div.bulkactions" ).remove();
					$( "#accu_list_bulk" ).remove();
				}

			}

			/**
		 *  Delete Single Record
		 */
			//$(".accu-btn-remove").on('click', function (event) {
			$( "#accu_backup_backups_list" ).on(
				'click','.accu-btn-remove', function (event) {
					var get_current_id = $( this ).attr( 'row_id' );
					var confim         = confirm( accu_ajax_obj.confirm_msg );
					if (confim) {
						var current_row = this;
						event.preventDefault();
						$.ajax(
							{
								url: accu_ajax_obj.ajaxurl,
								type: 'post',
								data: {
									_ajax_nonce: accu_ajax_obj.nonce, //nonce
									action: 'remove_single_backup', //action
									id: get_current_id
								},
								beforeSend: function () {
									jQuery( ".accu_notice p" ).html( accu_ajax_obj.wait_msg );
									jQuery( ".accu_notice" ).addClass( 'notice-info' );
									jQuery( ".accu_notice" ).show();
								},
								success: function (result) {
									//if (result !== '') {}

									$( current_row ).closest( "tr input[type='checkbox']:checked" ).prop( 'checked', false );
									$( current_row ).closest( "tr" ).remove();
									$( "input.accu_chk_select_all" ).prop( 'checked', false );
									//alert(accu_ajax_obj.record_delete_msg);
									$( ".accu_notice p" ).html( accu_ajax_obj.record_delete_msg );
									$( ".accu_notice" ).addClass( 'notice-success' );
									$( ".accu_notice" ).show();
									check_row_count();
									//table.draw();
								}
							}
						)
					}
				}
			);
			/**
		 *  Bulk Action - Delete
		 */
			$( "#bulk_action_submit" ).on(
				'click', function (event) {
					//accu_list_bulk
					var get_action         = $( "#accu_list_bulk" ).val();
					var selection          = $( '#accu_backup_backups_list tbody input[type="checkbox"]:checked' );
					var get_selected_count = selection.length;
					var checked            = []
					$( selection ).each(
						function () {
							checked.push( parseInt( $( this ).val() ) );
						}
					);
					if (get_action && '' !== get_action && get_action !== '-1' && get_selected_count > 0) {
						$.ajax(
							{
								url: accu_ajax_obj.ajaxurl,
								type: 'post',
								beforeSend: function () {
									if (get_action === 'delete_selected') {
										var cnrm_delete = confirm( accu_ajax_obj.confirm_msg );
										if ( ! cnrm_delete) {
											return false;
										}
									}
									jQuery( ".accu_notice p" ).html( accu_ajax_obj.wait_msg );
									jQuery( ".accu_notice" ).addClass( 'notice-info' );
									jQuery( ".accu_notice" ).show();
								},
								data: {
									_ajax_nonce: accu_ajax_obj.nonce, //nonce
									action: get_action, //action - delete_selected / download_selected
									checked: checked
								},
								success: function (result) {

									if (get_action === 'delete_selected') {

										$( selection ).each(
											function () {
												$( this ).closest( "tr input[type='checkbox']:checked" ).prop( 'checked', false );
												$( this ).closest( "tr" ).remove();
											}
										);
										$( "input.accu_chk_select_all" ).prop( 'checked', false );
										updateDataTableSelectAllCtrl( table );
										$( ".accu_notice p" ).html( accu_ajax_obj.record_delete_msg );
										$( ".accu_notice" ).addClass( 'notice-success' );
										$( ".accu_notice" ).show();
										check_row_count();
										//table.draw();

									}
								}
							}
						)
					} else {
						alert( accu_ajax_obj.select_msg );
					}
				}
			);
		}
	);
}
)( jQuery );
