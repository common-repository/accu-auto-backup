<?php
/**
 * The admin-specific database functionality of the plugin.
 *
 * PHP version 5
 *
 * This file is used to handle all database related functions.
 *
 * LICENSE: GPL-2.0+
 *
 * @category   Module
 * @package    Accu_Auto_Backup
 * @subpackage Accu_Auto_Backup/admin
 * @author     Dhanashree Inc <business@dhanashree.com>
 * @copyright  2018 Dhanashree Inc
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL License
 * @version    SVN: 1.0.0
 * @link       http://www.dhanashree.com/
 * @since      File available since Release 1.0.0
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Accu_Auto_Backup_Db_Functions' ) ) {

	/**
	 * The admin-specific database functionality of the plugin.
	 *
	 * PHP version 5
	 *
	 * This file is used to handle all database related functions.
	 *
	 * LICENSE: GPL-2.0+
	 *
	 * @category   Module
	 * @package    Accu_Auto_Backup
	 * @subpackage Accu_Auto_Backup/admin
	 * @author     Dhanashree Inc <business@dhanashree.com>
	 * @copyright  2018 Dhanashree Inc
	 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL License
	 * @version    Release: 1.0.0
	 * @link       http://www.dhanashree.com/
	 * @since      File available since Release 1.0.0
	 */
	class Accu_Auto_Backup_Db_Functions {

		protected $accu_tbl;

		/**
		 * Constructor of DB Function.
		 *
		 * @global type $wpdb
		 */
		public function __construct() {
			// create action to backup
			global $wpdb;

			$this->accu_tbl = $wpdb->prefix . 'accubackup';
		}

		/**
		 * Get Single Row from Table
		 *
		 * @param type $backup_id Pass Backup ID of table
		 *
		 * @global type $wpdb
		 * @return type
		 */
		public function get_single_accu_backup_tbl( $backup_id ) {
			if ( ! is_null( $backup_id ) && '' !== $backup_id ) {
				global $wpdb;

				$results = $wpdb->get_row( "SELECT * FROM $this->accu_tbl WHERE backup_id = $backup_id", ARRAY_A );

				return $results;
			}
		}

		/**
		 * Display Admin Notices
		 *
		 * @return null
		 */
		public function accu_backup_admin_notice() {
			$screen = get_current_screen();

			if ( 'tools_page_accu_auto_backup' === $screen->id ) {

				//-- check folder creations
				$this->check_db_directory();
			}
		}

		/**
		 * Remove Single Backup - AJAX Call
		 *
		 * @return null
		 */
		public function remove_single_backup_handler() {
			if ( 1 === check_ajax_referer( 'accu_auto_backup', '_ajax_nonce' ) ) {
				$bkp_id = (int) $_POST['id'];

				$get_data = $this->get_single_accu_backup_tbl( $bkp_id );

				if ( $get_data && is_array( $get_data ) ) {
					$get_file_path = ACCU_AUTO_BACKUP_DIR . $get_data['filename'];

					try {
						$this->delete_backup_file( $bkp_id, $get_file_path );
					} catch ( Exception $ex ) {
					}
				}
			}
			wp_die();
		}

		/**
		 * Delete/Remove Database Backup - AJAX Call
		 *
		 * @return null
		 */
		public function delete_selected_handler() {
			if ( 1 === check_ajax_referer( 'accu_auto_backup', '_ajax_nonce' ) && 'delete_selected' === $_POST['action'] && is_array( $_POST['checked'] ) && null !== $_POST['checked'] ) {
				$checked_list = $_POST['checked'];
				if ( count( $checked_list ) > 0 ) {
					foreach ( $checked_list as $key => $single_id ) {
						$bkp_id = $single_id;

						$get_data = $this->get_single_accu_backup_tbl( $bkp_id );

						if ( $get_data && is_array( $get_data ) ) {
							$fpath = ACCU_AUTO_BACKUP_DIR . $get_data['filename'];

							$this->delete_backup_file( $bkp_id, $fpath );
						}
					}
				}
			}
			wp_die();
		}
		/**
		 * Download Database Backup - AJAX Call
		 *
		 * @return null
		 */
		public function download_selected_handler() {
			if ( 1 === check_ajax_referer( 'accu_auto_backup', '_ajax_nonce' ) && 'download_selected' === $_POST['action'] && is_array( $_POST['checked'] ) && null !== $_POST['checked'] ) {
				$checked_list = $_POST['checked'];
				if ( count( $checked_list ) > 0 ) {
					$zip_file_list = array();

					foreach ( $checked_list as $key => $single_id ) {
						$bkp_id = $single_id;

						$get_data = $this->get_single_accu_backup_tbl( $bkp_id );

						if ( $get_data && is_array( $get_data ) ) {
							$get_file_path = $get_data['dir'];

							$file_name = $get_data['filename'];

							//$this->delete_backup_file($bkp_id, $get_file_path);

							if ( file_exists( $get_file_path ) ) {
								$zip_file_list[ $single_id ]['dir']      = $get_file_path;
								$zip_file_list[ $single_id ]['filename'] = $file_name;
							}
						}
					}
					/**
					 *  Now create the zip file
					 */
					if ( isset( $zip_file_list ) && null !== $zip_file_list && is_array( $zip_file_list ) ) {
						$sitename = preg_replace( '/[^A-Za-z0-9\_]/', '_', get_bloginfo( 'name' ) );

						$zipfilename = $sitename . '_' . Date( 'Y_m_d' ) . '_' . Time( 'H:M:S' ) . '_' . 'accu_auto_backup_collection' . '.zip';

						$arcname = ACCU_AUTO_BACKUP_DIR . $zipfilename;

						if ( class_exists( 'ZipArchive' ) ) {
							$zip = new ZipArchive;
							if ( $zip->open( $arcname, ZipArchive::CREATE ) === true ) {
								// Add file to the zip file
								foreach ( $zip_file_list as $key => $single_file_data ) {
									$zip->addFile( $single_file_data['dir'], $single_file_data['filename'] );
								}

								$zip->close();
							}
						} else {
							error_log( 'Class ZipArchive Not Present' );
						}
					}
				}
			}
			wp_die();
		}

		/**
		 * Add Backquote for database table name.
		 *
		 * @param type $a_name will be table name
		 *
		 * @return string
		 */
		public function backquote( $a_name ) {
			if ( ! empty( $a_name ) && '*' != $a_name ) {
				if ( is_array( $a_name ) ) {
					$result = array();
					foreach ( $a_name as $key => $val ) {
						$result[ $key ] = '`' . $val . '`';
					}
					return $result;
				} else {
					return '`' . $a_name . '`';
				}
			} else {
				return $a_name;
			}
		}

		/**
		 * Will Remove Directrory Trailing Slashes
		 *
		 * @param type $string Direcotry Name
		 *
		 * @return related string
		 */
		public function strip_dir_slash( $string ) {
			return preg_replace( '#/+(,|$)#', '$1', $string );
		}

		/**
		 * Will Create Mysql Database Backup.
		 *
		 * @global type $wpdb
		 * @return string
		 */
		public function accu_auto_backup_create_mysql_backup() {
			global $wpdb;
			/* BEGIN : Prevent saving backup plugin settings in the database dump */

			//delete_option('wp_db_backup_options');
			/* END : Prevent saving backup plugin settings in the database dump */
			$accu_db_exclude_table = array();
			$accu_db_exclude_table = get_option( 'accu_auto_backup_exclude_table' );
			$tables                = $wpdb->get_col( 'SHOW TABLES' );
			$output                = '';
			foreach ( $tables as $table ) {
				if ( empty( $accu_db_exclude_table ) || ( ! ( in_array( $table, $accu_db_exclude_table ) ) ) ) {
					$result   = $wpdb->get_results( "SELECT * FROM {$table}", ARRAY_N );
					$row_drop = 'DROP TABLE IF EXISTS ' . $table . ";\n";
					$row2     = $wpdb->get_row( 'SHOW CREATE TABLE ' . $table, ARRAY_N );
					$output  .= "\n\n" . $row_drop . "\n\n" . $row2[1] . ";\n\n";
					for ( $i = 0; $i < count( $result ); $i++ ) {
						$row     = $result[ $i ];
						$output .= 'INSERT INTO ' . $table . ' VALUES(';
						for ( $j = 0; $j < count( $result[0] ); $j++ ) {
							$row[ $j ] = $wpdb->_real_escape( $row[ $j ] );
							$output   .= ( isset( $row[ $j ] ) ) ? '"' . $row[ $j ] . '"' : '""';
							if ( $j < ( count( $result[0] ) - 1 ) ) {
								$output .= ',';
							}
						}
						$output .= ");\n";
					}
					$output .= "\n";
				}
			}
			$wpdb->flush();
			/* BEGIN : Prevent saving backup plugin settings in the database dump */

			//add_option('wp_db_backup_options', $settings_backup);
			/* END : Prevent saving backup plugin settings in the database dump */
			return $output;
		}

		/**
		 * Generate the archive file of backup.
		 *
		 * @return string uploaded path
		 */
		public function accu_auto_backup_create_archive() {

			/* Begin : Setup Upload Directory, Secure it and generate a random file name */

			$this->check_db_directory();

			$path_info = wp_upload_dir();

			$htassestext = '';

			fclose( fopen( ACCU_AUTO_BACKUP_DIR . 'index.html', 'w' ) );

			/* Begin : Generate SQL DUMP and save to file database.sql */
			$sitename = preg_replace( '/[^A-Za-z0-9\_]/', '_', get_bloginfo( 'name' ) );

			$wpdbfilename = $sitename . '_' . Date( 'Y_m_d' ) . '_' . Time( 'H:M:S' ) . '_' . substr( md5( AUTH_KEY ), 0, 7 ) . '_accu_auto_backup';

			$sqlfilename = $wpdbfilename . '.sql';

			$filename = $wpdbfilename . '.zip';

			/**
			 * CREATE SQL **
			 */
			$handle = fopen( ACCU_AUTO_BACKUP_DIR . $sqlfilename, 'w+' );

			fwrite( $handle, $this->accu_auto_backup_create_mysql_backup() );

			fclose( $handle );

			$upload_path = array(
				'filename' => ( $filename ),
				'dir'      => ( ACCU_AUTO_BACKUP_DIR . $filename ),
				'url'      => ( ACCU_AUTO_BACKUP_DIR_URL . $filename ),
				'size'     => 0,
			);

			$arcname = ACCU_AUTO_BACKUP_DIR . $wpdbfilename . '.zip';

			if ( class_exists( 'ZipArchive' ) ) {
				$zip = new ZipArchive;
				$zip->open( $arcname, ZipArchive::CREATE );
				$zip->addFile( ACCU_AUTO_BACKUP_DIR . $sqlfilename, $sqlfilename );
				$zip->close();
			} else {
				error_log( 'Accu Auto Backup :  Class ZipArchive Not Present' );
			}

			$logmessage = 'Database File Name :' . $filename;

			$upload_path['sqlfile'] = ACCU_AUTO_BACKUP_DIR . $sqlfilename;

			$upload_path['size'] = filesize( $upload_path['sqlfile'] );

			$accu_auto_backup_log = get_option( 'accu_auto_backup_log' );

			if ( 1 == $accu_auto_backup_log ) {
				$accu_db_exclude_table = get_option( 'accu_auto_backup_exclude_table' );
				if ( ! empty( $accu_db_exclude_table ) ) {
					$logmessage .= '<br> Exclude Table : ' . implode( ', ', $accu_db_exclude_table );
				}
				$upload_path['log'] = $logmessage;
			}
			unlink( ACCU_AUTO_BACKUP_DIR . $sqlfilename );
			return $upload_path;
		}

		/**
		 * Get list of table rows of accu table from database.
		 *
		 * @global type $wpdb
		 * @return type
		 */
		public function get_accu_auto_backup_backps() {
			global $wpdb;
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}accubackup WHERE 1 and filename like '%_accu_auto_backup%' ORDER BY backup_id DESC", ARRAY_A );
			return $results;
		}

		/**
		 * Get all rows of accu table from database - AJAX Call
		 *
		 * @global type $wpdb
		 *
		 * @return list
		 */
		public function get_accu_auto_backup_backps_list() {
			global $wpdb;

			if ( 1 === check_ajax_referer( 'accu_auto_backup', '_ajax_nonce' ) ) {
				$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}accubackup WHERE 1 and filename like '%_accu_auto_backup%' ORDER BY backup_id DESC", ARRAY_A );

				if ( isset( $results ) ) {
					$new_array = array();

					foreach ( $results as $key => $single_row ) {
						//$new_array []
						$new_array [ $key ] = $single_row;
					}
				}

				wp_send_json( $new_array );
				;
			}
			wp_die();
		}

		/**
		 * Will Remove Database from Directory & Table.
		 *
		 * @param type $backup_id Backup ID from Table
		 * @param type $dir       Directory Path
		 *
		 * @global type $wpdb
		 *
		 * @return null
		 */
		public function delete_backup_file( $backup_id, $dir ) {
			if ( isset( $backup_id ) && isset( $dir ) && file_exists( $dir ) ) {
				$delete_file = unlink( $dir );
				if ( true === $delete_file ) {
					global $wpdb;
					// Using where formatting.
					try {
						$wpdb->delete( $this->accu_tbl, array( 'backup_id' => $backup_id ), array( '%d' ) );
					} catch ( Exception $e ) {
						$log_message = 'Exception (' . get_class( $e ) . ') occurred during deletion of backup : ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ', line ' . $e->getLine() . ' in ' . $e->getFile() . ')';
						error_log( $log_message );
					}
					//put log
				}
			}
		}

		/**
		 * Make the zip file and fire the backup archive event.
		 *
		 * @return string Reponse of all backup process.
		 */
		public function accu_auto_backup_bkp_process() {

			//added in v.3.9.5
			$chk_cron_enable = get_option( 'accu_auto_backup_sc_auto_enabled' );

			if ( isset( $chk_cron_enable ) && 'yes' != $chk_cron_enable ) {
				return __( 'Cron is disabled,please enable cron from settings.', 'accu-auto-backup' );
			}

			ini_set( 'max_execution_time', '5000' );
			ini_set( 'max_input_time', '5000' );
			ini_set( 'memory_limit', '1000M' );
			set_time_limit( 0 );

			$details = $this->accu_auto_backup_create_archive();

			if ( '' == $details || null === $details ) {
				return __( 'Backup create archive has error.', 'accu-auto-backup' );
			}

			$options = array();

			$accu_auto_backup_log = get_option( 'accu_auto_backup_log' );
			if ( 1 == $accu_auto_backup_log ) {
				$logmessage = $details['log'];
			} else {
				$logmessage = '';
			}

			//REMOVE BACKUP ABOVE LIMIT
			$number_of_backups_limit = (int) get_option( 'accu_auto_backup_bkp_store_limit' );

			if ( 0 !== $number_of_backups_limit ) {
				$files = $this->get_accu_auto_backup_backps();
				if ( $files ) {
					foreach ( $files as $key => $file_list ) {
						if ( ++$key >= $number_of_backups_limit ) {
							$file_directory = ACCU_AUTO_BACKUP_DIR . $file_list['filename'];
							$this->delete_backup_file( $file_list['backup_id'], $file_directory );
						}
					}
				}
			}

			$options[] = array(
				'date'     => time(),
				'filename' => $details['filename'],
				'url'      => $details['url'],
				'dir'      => $details['dir'],
				'log'      => $logmessage,
				'sqlfile'  => $details['sqlfile'],
				'size'     => $details['size'],
			);

			$db_tbl_insert = array(
				'filename'   => $details['filename'],
				'log'        => $logmessage,
				'size'       => $details['size'],
				'date_added' => current_time( 'mysql', 1 ),
			);

			global $wpdb;

			try {
				$result = $wpdb->insert( $this->accu_tbl, $db_tbl_insert );
			} catch ( Exception $ex ) {
				$log_message = 'Accu Auto Backp : Exception (' . get_class( $ex ) . ') occurred during create archive: ' . $ex->getMessage() . ' (Code: ' . $ex->getCode() . ', line ' . $ex->getLine() . ' in ' . $ex->getFile() . ')';
				return $log_message;
				error_log( $logmessage );
			}

			update_option( 'accu_last_backup', current_time( 'timestamp', 1 ) );

			//$args = array($details['filename'], $details['dir'], $logMessage, $details['size']);
			//return sprintf(__("Success! Accu Auto Backup successfully done at %s having file name %s and size %d on disk.",'accu_auto_backup'),current_time('timestamp'),$db_tbl_insert['filename'],(float)$db_tbl_insert['size']);
			$db_response = sprintf( /* translators: %1$s datetime %2$s filename %3$d size */ __( 'Success! Accu Auto Backup successfully done on %1$s having file name %2$s and size %3$d on disk.', 'accu-auto-backup' ), date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ), $db_tbl_insert['filename'], (float) $db_tbl_insert['size'] );

			return $db_response;

			//do_action_ref_array('wp_db_backup_completed', array(&$args));
		}

		/**
		 * Will Clear or Delete any unexcepted files in folder as well in table.
		 *
		 * @global type $wpdb
		 * @return null
		 */
		public function clear_undeleted_backups() {
			$options           = $this->get_accu_auto_backup_backps();
			$newoptions        = array();
			$backup_check_list = array( '.htaccess', 'index.html' );
			$deletemessage     = 'ACCU AUTO BACKUP : Deleted Files:';
			foreach ( $options as $option ) {
				$backup_check_list[] = $option['filename'];
			}
			$path_info                  = wp_upload_dir();
			$accu_backup_db_backup_path = ACCU_AUTO_BACKUP_DIR;

			$file_list = array();

			// Open a directory, and read its contents
			if ( is_dir( $accu_backup_db_backup_path ) ) {
				$dh = opendir( $accu_backup_db_backup_path );
				if ( $dh ) {
					while ( $file = readdir( $dh ) !== false ) {
						$file_list[] = $file;
						if ( ! ( in_array( $file, $backup_check_list ) ) ) {
							if ( '.' == $file || '..' == $file ) {
								continue;
							}
							if ( is_file( $accu_backup_db_backup_path . $file ) ) {
								unlink( $accu_backup_db_backup_path . $file );
								$deletemessage .= ' ' . $file;
							}
						}
					}
					closedir( $dh );
				}
				//error_log($deleteMessage);
			}

			/*             * ****
			 *  Now check for exists in database and not in folder
			 * *** */

			if ( is_array( $file_list ) && is_dir( $accu_backup_db_backup_path ) && is_array( $options ) && is_array( $backup_check_list ) ) {
				foreach ( $options as $key => $single_file_info ) {
					if ( ! in_array( $single_file_info['filename'], $file_list ) ) {
						$backup_id = $single_file_info['backup_id'];
						if ( ! empty( $backup_id ) && '' !== $backup_id ) {
							global $wpdb;
							try {
								$wpdb->delete( $this->accu_tbl, array( 'backup_id' => $backup_id ), array( '%d' ) );
							} catch ( Exception $e ) {
								$log_message = 'Exception (' . get_class( $e ) . ') occurred during deletion of backup : ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ', line ' . $e->getLine() . ' in ' . $e->getFile() . ')';
								error_log( $log_message );
							}
						}
					}
				}
			}
		}

		/**
		 * Will check and create default directory if not exists.
		 *
		 * @return null
		 */
		public function check_db_directory() {
			$upload_dir = wp_upload_dir();

			$dir = ACCU_AUTO_BACKUP_DIR;

			if ( false === is_dir( $dir ) ) {
				wp_mkdir_p( ACCU_AUTO_BACKUP_DIR );

				touch( ACCU_AUTO_BACKUP_DIR . 'index.html' );
			}

			if ( is_dir( $dir ) && ! is_writable( $dir ) ) {
				error_log( 'Accu Auto Backp : Error - Permission denied make sure you have write permission for folder writable.' ); ?>
				<div class="notice notice-error is-dismissible display">
					<h4><?php _e( 'Accu Auto Backup', 'accu-auto-backup' ); ?></h4>
					<p><?php printf( /* translators: %s directory */ __( 'Error: Permission denied, make sure you have write permission for %s folder', 'accu-auto-backup' ), $dir ); ?> </p>
				</div>
				<?php
				return false;
			}
		}

		/**
		 *  Formate file size according to size like KB, MB and GB.
		 *
		 * @param type $bytes     will have bytes
		 * @param type $precision will check precision
		 *
		 * @return string formated size
		 */
		public function accu_auto_backup_format_bytes( $bytes, $precision = 2 ) {
			$units  = array( 'B', 'KB', 'MB', 'GB', 'TB' );
			$bytes  = max( $bytes, 0 );
			$pow    = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
			$pow    = min( $pow, count( $units ) - 1 );
			$bytes /= pow( 1024, $pow );
			return round( $bytes, $precision ) . ' ' . $units[ $pow ];
		}

		/**
		 * Downlaod DB
		 *
		 * @param type $query_vars query string where
		 *
		 * @return null
		 */
		public function download_db( $query_vars ) {
			if ( isset( $query_vars ) && null !== $query_vars && '' !== $query_vars ) {
				if ( isset( $query_vars['attachment'] ) && ! empty( $query_vars['attachment'] ) ) {
					//$get_query_string = explode("&",$query_vars['attachment']);
				}
			}
		}

		/**
		 * Responsible for listing stored backups with Datatable.
		 *
		 * @return list of backups
		 */
		public function list_backups() {
			$this->clear_undeleted_backups();

			$options = $this->get_accu_auto_backup_backps();

			$bulk_action = __( 'Bulk Actions', 'accu-auto-backup' );
			$delete      = __( 'Delete', 'accu-auto-backup' );
			$download    = __( 'Download', 'accu-auto-backup' );
			$apply       = __( 'Apply', 'accu-auto-backup' );

			//$no = __("No.", 'accu-auto-backup');
			$heading_date = __( 'Date', 'accu-auto-backup' );
			$size         = __( 'Size', 'accu-auto-backup' );
			$backup_file  = __( 'Backup File', 'accu-auto-backup' );
			$remove_file  = __( 'Remove File', 'accu-auto-backup' );

			$no_backup = __( 'No backups are currently available.', 'accu-auto-backup' );

			//$nonce = wp_nonce_field('accu_auto_backup');
			//            echo "<pre>";
			//            print_r($options);
			//            echo "</pre>";
			//            exit();
			//var_dump(get_option('accu_auto_backup_backups'));
			if ( $options ) {
				echo '<h2 class="accu-screen-reader-text"> ' . __( 'List of database backups', 'accu-auto-backup' ) . '</h2> 
                  <div class="table-responsive">
                                <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline" role="grid">                               
                                
                                <div class="bult_action_div actions bulkactions">
                                    <select id="accu_list_bulk" >
                                        <option selected value="-1"> ' . $bulk_action . '</option>
                                        <option value="delete_selected">' . $delete . '</option>
                                        <!--<option value="download_selected">' . $download . '</option>--->
                                     </select>
                                     <input type="button" id="bulk_action_submit" class="button action" name="bulk_action_submit" value="' . $apply . '" />
                                </div>

                                <table class="wp-list-table widefat fixed striped pages" id="accu_backup_backups_list">
                                    <thead>';
				echo '<tr class="accu-bkp-header">';
				echo '<th  width="5%" class="nosort"><input name="select_all" class="accu_chk_select_all" value="1" type="checkbox"></th>';
				//echo '<th  width="5%" class="manage-column" scope="col"  style="text-align: center;">' . $no . '</th>';
				echo '<th class="manage-column" scope="col" width="25%">' . $heading_date . '</th>';
				//echo '<th class="manage-column" scope="col" width="5%"></th>';
				echo '<th class="manage-column" scope="col" width="10%">' . $size . '</th>';
				echo '<th class="manage-column nosort " scope="col" width="15%">' . $backup_file . '</th>';
				echo '<th class="manage-column nosort sorting_disabled" scope="col" width="15%">' . $remove_file . '</th>';
				//echo '<th class="manage-column" scope="col" width="15%"></th>';
				echo '</tr>';
				echo '</thead>';

				echo '<tfoot>';
				echo '<tr class="">';
				echo '<th  width="5%" class="nosort">&nbsp;</th>';
				//echo '<th  width="5%" class="manage-column" scope="col"  style="text-align: center;">' . $no . '</th>';
				echo '<th class="manage-column" scope="col" width="25%">' . $heading_date . '</th>';
				//echo '<th class="manage-column" scope="col" width="5%"></th>';
				echo '<th class="manage-column" scope="col" width="10%">' . $size . '</th>';
				echo '<th class="manage-column nosort " scope="col" width="15%">' . $backup_file . '</th>';
				echo '<th class="manage-column nosort sorting_disabled" scope="col" width="15%">' . $remove_file . '</th>';
				//echo '<th class="manage-column" scope="col" width="15%"></th>';
				echo '</tr>';
				echo '</tfoot>';

				echo '<tbody>';
				$count = 1;
				foreach ( $options as $key => $option ) {
					$single_id   = $option['backup_id'];
					$backup_time = get_date_from_gmt( $option['date_added'], 'H:i' );
					$backup_date = get_date_from_gmt( $option['date_added'], 'jS, F Y' );

					//get_date_from_gmt(date( 'Y-m-d H:i:s',$this->get_backup_details('next')), 'D, F j, Y H:i');
					//$accu_nonce = wp_create_nonce('accu_auto_backup');
					//$download_link = admin_url('accu_backup_id='.$single_id.'&accu_nonce='.$accu_nonce);
					$download_link = ACCU_AUTO_BACKUP_DIR_URL . $option['filename'];

					echo '<tr ' . ( ( ( $count % 2 ) == 0 ) ? ' class="alternate"' : '' ) . '>';
					echo '<td row_id="' . $single_id . '" class="check-column"><input type="checkbox" class="bkp_chkbx" value="' . $single_id . '"  /></td>';
					//echo '<td style="text-align: center;">' . $count . '</td>';
					echo '<td>' . $backup_date . '<br />' . $backup_time . '</td>';
					//                    echo '<td class="wpdb_log">';
					//                    if (!empty($option['log'])) {
					//                        echo '<button id="popoverid" type="button" class="popoverid btn" data-toggle="popover" title="Log" data-content="' . $option['log'] . '"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></button>';
					//                    }
					//                    echo '</td>';
					echo '<td>' . $this->accu_auto_backup_format_bytes( $option['size'] ) . '</td>';
					echo '<td>';
					echo '<a class="accu-btn accu-btn-dwnld" href="' . $download_link . '" >';
					echo '<span class="glyphicon glyphicon-download-alt"></span>' . __( 'Download', 'accu-auto-backup' ) . '</a></td>';
					echo '<td><a  row_id="' . $single_id . '"  class="accu-btn accu-btn-remove" href="javascript:void();" class="btn btn-default"><span style="color:red" class="glyphicon glyphicon-trash"></span> Remove Backup<a/></td>';
					echo '</tr>';
					$count++;
				}
				echo '</tbody>';
				//$nonce;
				echo ' </table>     
                                </div>
                                  </div>';
			} else {
				echo '<p class="no-backup">' . $no_backup . '</p>';
			}
		}
	}
}
