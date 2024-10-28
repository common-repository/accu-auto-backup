<?php
/**
 * The file that defines the corn features.
 *
 * PHP version 5
 *
 * This file concern with all cron/scheduler feature all over the plug-in.
 *
 * LICENSE: GPL-2.0+
 *
 * @category   Module
 * @package    Accu_Auto_Backup
 * @subpackage Accu_Auto_Backup/includes
 * @author     Dhanashree Inc <business@dhanashree.com>
 * @copyright  2018 Dhanashree Inc
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL License
 * @version    SVN: 1.0.0
 * @link       http://www.dhanashree.com/
 * @since      File available since Release 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Accu_Auto_Backup_Cron_Manager' ) ) {

	/**
	 * Cron Class
	 *
	 * PHP version 5
	 *
	 * This class concern with all cron/scheduler feature all over the plug-in.
	 *
	 * LICENSE: GPL-2.0+
	 *
	 * @category   Module
	 * @package    Accu_Auto_Backup
	 * @subpackage Accu_Auto_Backup/includes
	 * @author     Dhanashree Inc <business@dhanashree.com>
	 * @copyright  2018 Dhanashree Inc
	 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL License
	 * @version    Release: 1.0.0
	 * @link       http://www.dhanashree.com/
	 * @since      File available since Release 1.0.0
	 */
	class Accu_Auto_Backup_Cron_Manager {


		/**
		 * The single instance of the class.
		 *
		 * @var Accu_Auto_Backup_Cron_Manager
		 */
		protected static $instance            = null;
		private $_cron_status                 = null;
		private $_accu_tbl                    = null;
		private $_accu_cron_url               = null;
		private $_accu_cron_get_url           = null;
		private $_accu_cron_path              = null;
		private $_accu_cron_exec_path         = null;
		private $_accu_existing_cron_job_list = array();
		private $_accu_cron_command           = null;
		private $_accu_cron_old_command       = null;


		/**
		 * Constructer of Cron Job
		 *
		 * @return null
		 */
		public function __construct() {
			global $wpdb;

			if ( ! isset( $this->_cron_status ) || null === $this->_cron_status ) {
				$this->_cron_status = $this->validate_cron();
			}

			$this->_accu_tbl = $wpdb->prefix . 'accubackup';

			$this->_accu_cron_url = ACCU_DIR_URL . 'accu-auto-backup-cron.php';

			$this->_accu_cron_get_url = esc_url( home_url( 'index.php?accu_auto_backup_cron=yes' ) );

			$this->_accu_cron_path = ACCU_DIR_PATH . 'accu-auto-backup-cron.php';

			$this->_accu_cron_exec_path = ACCU_DIR_PATH . 'log/accu-cron.txt';

			//-- silent cron job path
			$this->_accu_cron_command = '* * * * * GET "' . $this->_accu_cron_get_url . '" > /dev/null 2>&1';

			//--- below cron job path will generate email  on every checking
			$this->_accu_cron_old_command = '* * * * * GET "' . $this->_accu_cron_get_url . '"';

			// check and remove old cron job line
			$this->accu_remove_old_cron_path();
		}

		/**
		 * Main Accu_auto_backup_Cron_manager Instance.
		 *
		 * Ensures only one instance of Accu_auto_backup_Cron_manager is loaded or can be loaded.
		 *
		 * @since  1.0.0
		 * @static
		 *
		 * @see Accu_cron_mgr()
		 *
		 * @return Accu_cron_mgr - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Will append response data to accu_cron.txt file.
		 *
		 * @param type $response Response of called function.
		 *
		 * @return null
		 */
		public function accu_log( $response ) {
			$log_file = ACCU_LOG_FILE;
			if ( $response ) {
				$response_txt = '-----' . current_time( 'Y-m-d h:i:s' ) . "-----\r\n" . $response . "\r\n\r\n";
				file_put_contents( $log_file, $response_txt, FILE_APPEND );
				//-- remoe file if it exceeds from 5 MB
				if ( file_exists( $log_file ) ) {
					$filesize = filesize( $log_file ); // bytes
					$filesize = round( $filesize / 1024, 2 ); // KB with two digits
					if ( $filesize > 5000 ) {
						file_put_contents( ACCU_LOG_FILE, '' );
					}
				}
			}
		}

		/**
		 * Will run cron using front website query string.
		 *
		 * @return null
		 */
		public function run_front_cron() {
			if ( ! is_admin() ) {
				$inputs_value = filter_input( INPUT_GET, 'accu_auto_backup_cron' );
				if ( isset( $inputs_value ) && null !== $inputs_value && '' !== $inputs_value && 'yes' === $inputs_value ) {
					/**
					 *  Check first cron has been enabled or not.
					 */
					$validate_cron_run = $this->validate_cron();

					if ( true === $validate_cron_run ) {
						$response = $this->run_cron();
					} else {
						$accu_errors = $validate_cron_run;
						$this->accu_log( $accu_errors );
					}
				}
				return null;
			}
		}

		/**
		 * Run Cron file.
		 *
		 * @return type
		 */
		public function run_cron() {
			$response        = null;
			$get_current_tmp = current_time( 'timestamp', 1 );
			$get_next_tmp    = $this->get_backup_details( 'next' );
			if ( (float) $get_current_tmp >= (float) $get_next_tmp ) {
				$response = apply_filters( 'accu_backup_event', $response );
				$this->accu_log( $response );
			}
			return $response;
		}

		/**
		 * Validate the Current cron Job.
		 *
		 * @return type
		 */
		public function validate_cron() {
			$check_cron_enable = get_option( 'accu_auto_backup_sc_auto_enabled' );
			$get_cron_method   = get_option( 'accu_auto_backup_backup_method' );

			$output = true;

			if ( ! isset( $check_cron_enable ) || null === $check_cron_enable || 'no' == $check_cron_enable ) {
				$output = __( 'Accu Auto Backup Cron has been disabled.', 'accu-auto-backup' );
			}

			return $output;
		}

		/**
		 * Get Cron Information in General Tab.
		 *
		 * @return html cron html
		 */
		public function get_cron_info() {
			$check_cron_enable = get_option( 'accu_auto_backup_sc_auto_enabled' );

			if ( false !== $check_cron_enable && 'yes' === $check_cron_enable ) {
				$c_time = get_date_from_gmt( date( 'Y-m-d H:i:s' ), 'D, F j, Y H:i' );
				$next_time = get_date_from_gmt( date( 'Y-m-d H:i:s', $this->get_backup_details( 'next' ) ), 'D, F j, Y H:i' );
				$last_time = get_date_from_gmt( date( 'Y-m-d H:i:s', $this->get_backup_details( 'last' ) ), 'D, F j, Y H:i' ); ?>
				<hr/>
				<table class="form-table accu_cron_info_table">
					<tr>
						<td colspan="2">
							<span>
				<?php esc_attr_e( "All the times shown in this section are using WordPress's configured time zone, which you can set in Settings -> General ", 'accu-auto-backup' ); ?></span>
							<br/>
							<span class="accu_info"><?php esc_attr_e( 'Info! Also below schedule will work only when you set the specified cronfile in your cron settings.', 'accu-auto-backup' ); ?></span></td>
					</tr>
					<tr>
						<th>
				<?php _e( 'Next scheduled backups', 'accu-auto-backup' ); ?>:<br>
							<span style="font-weight:normal;"><em><?php _e( 'Now', 'updraftplus' ); ?>: <?php echo $c_time; ?></span></span></em>
						</th>
						<td class="accu-next-backup" >
				<?php echo $next_time; ?>
						</td>
					</tr>

				</table> <hr/>
				<?php
			}
		}

		/**
		 * Calculate next cron job timings.
		 *
		 * @param type $last_timestamp last timestamp of cron.
		 * @param type $interval       interval
		 *
		 * @return type
		 */
		public function calculate_next( $last_timestamp, $interval ) {
			$next_time = 0;
			if ( isset( $last_timestamp ) && isset( $interval ) ) {
				switch ( $interval ) {
					case 'two_min':
						$next_time = strtotime( '+2 minutes', $last_timestamp );
						break;
					case 'daily':
						$next_time = strtotime( '+1 day', $last_timestamp );
						break;
					case 'every_other_day':
						$next_time = strtotime( '+2 day', $last_timestamp );
						break;
					case 'weekly':
						$next_time = strtotime( '+1 week', $last_timestamp );
						break;
					case 'fortnightly':
						$next_time = strtotime( '+2 weeks', $last_timestamp );
						break;
					case 'monthly':
						$next_time = strtotime( '+1 month', $last_timestamp );
						break;
					default:
						break;
				}
			}
			return $next_time;
		}

		/**
		 * Get give Backup Details for next and last timings for General Tab.
		 *
		 * @param type $options option to seek for
		 *
		 * @global type $wpdb
		 *
		 * @return null
		 */
		public function get_backup_details( $options ) {
			$return = '';
			if ( null !== $options ) {
				global $wpdb;
				switch ( $options ) {
					case 'last':
						$get_last = get_option( 'accu_last_backup' );
						if ( false !== $get_last ) {
							$return = (float) get_option( 'accu_last_backup' );
						} else {
							update_option( 'accu_last_backup', current_time( 'timestamp', 1 ) );
							$return = current_time( 'timestamp', 1 );
						}
						break;
					case 'next':
						$get_last_int = get_option( 'accu_auto_backup_sc_auto_interval' );
						if ( false !== $get_last_int ) {
							$return = $this->calculate_next( $this->get_backup_details( 'last' ), get_option( 'accu_auto_backup_sc_auto_interval' ) );
						} else {
							$return = __( 'Please select backup interval from settings.', 'accu-auto-backup' );
						}
						break;
					default:
						break;
				}
			}
			return $return;
		}

		/**
		 * Output HTML for Manual Cron for General Tab.
		 *
		 * @return null
		 */
		public function get_manual_cron_html() {
			$cron_url  = $this->_accu_cron_url;
			$cron_path = $this->_accu_cron_path;
			?>
			<table class="form-table accu_cron_info_table" width="100">
				<tr>
					<td colspan="2"><span><?php _e( 'Please set cron by adding following url to your desire location', 'accu-auto-backup' ); ?></span></td>
				</tr>
				<tr>
					<th width="10%"><?php _e( 'Cron URL:', 'accu-auto-backup' ); ?></th>
					<td width="90%"id="accu_last_backup"><?php echo $this->_accu_cron_command; ?></td>
				</tr>
			</table>
			<?php
		}

		/**
		 * Main Function for add the cron line to linux system crontab.
		 *
		 * @return type
		 */
		public function accu_check_and_add_auto_cron() {
			$get_cron_status = get_option( 'accu_auto_backup_sc_auto_enabled' );
			$get_cron_method = get_option( 'accu_auto_backup_backup_method' );
			$get_os          = PHP_OS;
			$return          = null;

			/* Some possible outputs:
			Linux localhost 2.4.21-0.13mdk #1 Fri Mar 14 15:08:06 EST 2003 i686
			Linux

			FreeBSD localhost 3.2-RELEASE #15: Mon Dec 17 08:46:02 GMT 2001
			FreeBSD

			Windows NT XN1 5.1 build 2600
			WINNT
			 */

			if ( false !== $get_cron_status && 'yes' == $get_cron_status && false !== $get_cron_method && 'auto' === $get_cron_method ) {
				if ( strtoupper( substr( $get_os, 0, 3 ) ) === 'WIN' ) {
					$return = __( 'Sorry! You need to setup cron manually.', 'accu-auto-backup' );
				} elseif ( true === $this->is_func_enabled( 'shell_exec' ) ) {

					//set the cron
					//$this->accuAppendCronjob('* * * * * php ' . $this->_accu_cron_path);

					//checking and removing old file
					$this->accu_remove_old_cron_path();
					$this->accu_append_cronjob( $this->_accu_cron_command );
					//$response = $this->accu_remove_cronjob('* * * * * php ' . $this->accu_cron_path);
				} else {
					$return = __( 'Permission denied! You are not able to run shell commands. please contact your hosting provider to enable.', 'accu-auto-backup' );
				}
			} elseif ( false !== $get_cron_status && 'yes' == $get_cron_status && false !== $get_cron_method && 'manual' == $get_cron_method ) {
				$this->remove_accu_auto_cron_job_line();
			} elseif ( ! isset( $get_cron_status ) || null === $get_cron_status || 'no' == $get_cron_status ) {
				$this->remove_accu_auto_cron_job_line();
			}
			return $return;
		}

		/**
		 * Will Remove the cron line from linux crontab.
		 *
		 * @return null
		 */
		public function remove_accu_auto_cron_job_line() {
			if ( true === $this->is_func_enabled( 'shell_exec' ) ) {
				//$this->accuRemoveCronjob('* * * * * php ' . $this->_accu_cron_path);
				$this->accu_remove_cronjob( $this->_accu_cron_command );
			}
		}

		/**
		 * Check extension or permission for shell_exec or exec command to run for user.
		 *
		 * @param type $func name of function to check.
		 *
		 * @return type
		 */
		public function is_func_enabled( $func ) {
			return is_callable( $func ) && false === stripos( ini_get( 'disable_functions' ), $func );
		}

		/**
		 * Check new/existing line available in crontab of linux system or not.
		 *
		 * @param type $command command to check.
		 *
		 * @return boolean
		 */
		public function accu_cronjob_exists( $command ) {
			$cronjob_exists = false;

			//$current_user = trim(shell_exec('whoami'));
			//exec('crontab -l | grep -i '.$command, $crontab);
			exec( 'crontab -l', $crontab );

			$filename = $this->_accu_cron_exec_path;
			touch(  ACCU_DIR_PATH . 'log/accu-cron.txt' );
			if ( file_exists( $filename ) ) {
				fopen( ACCU_DIR_PATH . 'log/accu-cron.txt', 'w+' );
				file_put_contents( $filename, $crontab );
			}
			if ( isset( $crontab ) && is_array( $crontab ) ) {
				$this->_accu_existing_cron_job_list = $crontab;

				foreach ( $crontab as $key => $single_value ) {
					if ( $single_value == $command ) {
						$cronjob_exists = true;
						break;
					}
				}
			}
			return $cronjob_exists;
		}

		/**
		 * Will remove the old cron path
		 *
		 * @return type
		 */
		public function accu_remove_old_cron_path() {
			if ( isset( $this->_accu_cron_old_command ) && isset( $this->_accu_cron_command ) && true == $this->accu_cronjob_exists( $this->_accu_cron_old_command ) ) {
				$this->accu_remove_cronjob( $this->_accu_cron_old_command );
				$this->accu_append_cronjob( $this->_accu_cron_command );
				$this->accu_log( __( 'Old cron command has been removed.', 'accu-auto-backup' ) );
			}
		}

		/**
		 * Get current user IP to track the reference for log.
		 *
		 * @return type
		 */
		public function accu_get_the_user_ip() {
			if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				//check ip from share internet
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				//to check ip is pass from proxy
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}

		/**
		 * Will Append the cronjob line to linux system crontab.
		 *
		 * @param type $command command to add into crontab.
		 *
		 * @return type
		 */
		public function accu_append_cronjob( $command ) {
			$output = null;
			if ( is_string( $command ) && ! empty( $command ) && $this->accu_cronjob_exists( $command ) === false ) {
				if ( file_exists( $this->_accu_cron_exec_path ) && is_writable( $this->_accu_cron_exec_path ) ) {
					$this->_accu_existing_cron_job_list[] = PHP_EOL . $command . PHP_EOL;

					$new_list = implode( "\n", $this->_accu_existing_cron_job_list );

					file_put_contents( $this->_accu_cron_exec_path, $new_list );

					shell_exec( 'crontab -i ' . $this->_accu_cron_exec_path );

					//$output = shell_exec('crontab -l');
					//file_put_contents($this->accu_cron_exec_path, $output . $command . PHP_EOL);

					$get_current_time = current_time( 'Y-m-d H:i:s' );
					$get_current_ip   = $this->accu_get_the_user_ip();
					$response         = sprintf( /* translators: %1$s time %2$s ip address */ __( 'Accu backup auto cron has been setup on : %1$s from IP Address : %2$s', 'accu-auto-backup' ), $get_current_time, $get_current_ip );
					$this->accu_log( $response );
				} else {
					$this->accu_log( __( 'Accu backup could not able to create cron text file. please check with write permission', 'accu-auto-backup' ) );
				}
			}
			return $output;
		}

		/**
		 * Remove the line or command from crontab of linux system.
		 *
		 * @param type $command command to remove from crontab.
		 *
		 * @return null
		 */
		public function accu_remove_cronjob( $command ) {
			$output = null;
			if ( is_string( $command ) && ! empty( $command ) && $this->accu_cronjob_exists( $command ) === true ) {
				if ( null !== $this->_accu_existing_cron_job_list ) {
					$get_key          = array_search( $command, $this->_accu_existing_cron_job_list );
					$get_current_time = current_time( 'Y-m-d H:i:s' );
					$get_current_ip   = $this->accu_get_the_user_ip();
					unset( $this->_accu_existing_cron_job_list[ $get_key ] );
					$new_list = implode( "\n", $this->_accu_existing_cron_job_list );
					file_put_contents( $this->_accu_cron_exec_path, $new_list );
					$removed_output = null;
					exec( 'crontab -i ' . $this->_accu_cron_exec_path, $removed_output );
					$response = sprintf( /* translators: %1$s command %2$s time %3$s ip address */ __( 'Accu backup auto cron command : %1$s has been removed on : %2$s from IP Address : %3$s', 'accu-auto-backup' ), $command, $get_current_time, $get_current_ip );
					$this->accu_log( $response );
				}
			}
		}
	}

	/**
	 * Main instance of Accu Cron Job
	 *
	 * Returns the main instance of Accu_Cron to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return Accu_Auto_Backup_Cron_Manager instance
	 * */
	function accu_cron_manager() {
		return Accu_Auto_Backup_Cron_Manager::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['accu_cron_mgr'] = accu_cron_manager();
}
