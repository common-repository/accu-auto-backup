<?php
/**
 * The admin-specific functionality of the plug-in.
 *
 * PHP version 5
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
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
if ( ! class_exists( 'Accu_Auto_Backup_Admin' ) ) {

	/**
	 * The admin-specific functionality of the plug-in.
	 *
	 * PHP version 5
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
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
	class Accu_Auto_Backup_Admin {


		/**
		 * The ID of this plugin.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string    $plugin_name    The ID of this plugin.
		 */
		private $_plugin_name;

		/**
		 * The options name to be used in this plugin
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string      $option_name    Option name of this plugin
		 */
		private $_option_name = 'accu_auto_backup';

		/**
		 * The version of this plugin.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string    $version    The current version of this plugin.
		 */
		private $_version;

		/**
		 * List the possible intervals of auto backups
		 *
		 * @since 1.0.0
		 * $access protected
		 * @var   array
		 */
		protected $intervals;

		/**
		 * No of backup stored
		 *
		 * @since 1.0.0
		 * $access protected
		 * @var   array All=0,1,2,3,4,5,6,7,8,9,10
		 */
		protected $bkp_store_limit;
		protected $current_sys_os;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @param string $plugin_name The name of this plugin.
		 * @param string $version     The version of this plugin.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $plugin_name, $version ) {
			$this->_plugin_name = $plugin_name;
			$this->_version     = $version;
			$this->intervals    = array(
				//'two_min'         => __( 'Two Minutes', 'accu-auto-backup' ),
				'daily'           => __( 'Daily', 'accu-auto-backup' ),
				'every_other_day' => __( 'Every other day', 'accu-auto-backup' ),
				'weekly'          => __( 'Weekly', 'accu-auto-backup' ),
				'fortnightly'     => __( 'Fortnightly', 'accu-auto-backup' ),
				'monthly'         => __( 'Monthly', 'accu-auto-backup' ),
			);

			$this->bkp_store_limit = [
				1,
				2,
				3,
				4,
				5,
				6,
				7,
				8,
				9,
				10,
			];

			$this->current_sys_os = PHP_OS;
		}

		/**
		 * Display HTML for Further Information on General Setting Tab Page.
		 *
		 * @return html
		 */
		public function accu_get_plugin_info() {
			$further_info = __( 'Further Informations', 'accu-auto-backup' );
			$html         = '<div class="accu_plugin_info updated is-dismissible well">
                        <h2> ' . $further_info . ' </h2>
                         <ul class="accu_ul">
                            <li>' . __( 'Accu auto backup allow you to take backup as per schedule like ( Daily,Weekly,Monthly.. ).', 'accu-auto-backup' ) . '</li>
                            <li>' . __( 'All taken backup is stored in zip format, you can download them from Database Backups.', 'accu-auto-backup' ) . '</li>
                         </ul>
                    </div>';
			echo $html;

		}

		/**
		 * This function runs when WordPress completes its upgrade process
		 * It iterates through each plugin updated to see if ours is included
		 * added for new version - 17-04-2018
		 * @param $upgrader_object Array
		 * @param $options Array
		 */
		public function accu_auto_backup_upgrade_completed( $upgrader_object, $options ) {
			// The path to our plugin's main file
			$our_plugin = basename( plugin_dir_path( dirname( __FILE__, 1 ) ) );
			// If an update has taken place and the updated type is plugins and the plugins element exists
			if ( 'update' == $options['action'] && 'plugin' == $options['type'] && isset( $options['plugins'] ) ) {
				// Iterate through the plugins being updated and check if ours is there
				foreach ( $options['plugins'] as $plugin ) {
					if ( $plugin == $our_plugin ) {
						// Set a transient to record that our plugin has just been updated
						set_transient( 'accu_auto_backup_updated', 1 );
						accuCronManager()->accuCheckAndAddAutoCron();
					}
				}
			}
		}

		/**
		 * Show a notice to anyone who has just updated this plugin
		 * This notice shouldn't display to anyone who has just installed the plugin for the first time
		 */
		function accu_auto_backup_display_update_notice() {
			// Check the transient to see if we've just updated the plugin
			if ( get_transient( 'accu_auto_backup_updated' ) ) {
				echo '<div class="notice notice-success">' . __( 'Thanks for updating', 'accu-auto-backup' ) . '</div>';
				delete_transient( 'accu_auto_backup_updated' );
			}
		}

		/**
		 * Get Backup List
		 *
		 * @return list
		 */
		public function get_accu_backup_backups() {
			$get_list = get_option( 'accu_auto_backup_backups' );
			return $get_list;
		}

		/**
		 *  For common form submissions and admin init process
		 *
		 * @return null
		 */
		public function accu_auto_backup_admin_init() {
			// Start Fixed Vulnerability  for data save in options
			if ( isset( $_GET['page'] ) && 'accu_auto_backup' == $_GET['page'] ) {
				if ( ! empty( $_POST ) && ! ( isset( $_POST['page'] ) && 'accu_auto_backup' == $_POST['page'] ) ) {
					$nonce = $_REQUEST['_wpnonce'];
				}
			}
		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since  1.0.0
		 * @return enqueue style
		 */
		public function enqueue_styles() {

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Accu_auto_backup_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Accu_auto_backup_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */
			if ( isset( $_GET['page'] ) ) {
				if ( 'accu_auto_backup' == $_GET['page'] ) {
					wp_enqueue_style( 
                                                'accu_auto_backup_dataTablescss',
                                                plugin_dir_url( __FILE__ ) . '/css/jquery.dataTables.min.css' );

					wp_enqueue_style(
                                                'accu_auto_backup_dataTablescss' );

					wp_enqueue_style( 
                                                $this->_plugin_name, plugin_dir_url( __FILE__ ) . 'css/accu-auto-backup-admin.css',
                                                array(),
                                                $this->_version,
                                                'all' );
                                        
     
        
        
				}
			}
		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function enqueue_scripts() {

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Accu_auto_backup_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Accu_auto_backup_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */
			if ( isset( $_GET['page'] ) ) {
				if ( 'accu_auto_backup' == $_GET['page'] ) {
					wp_enqueue_script( 'jquery' );

					$accu_nonce = wp_create_nonce( 'accu_auto_backup' );

					wp_enqueue_script( 'accu_auto_backup_dataTables', plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.js', array( 'jquery' ) );

					wp_enqueue_script( 'accu_auto_backup_dataTables' );

					wp_enqueue_script( $this->_plugin_name, plugin_dir_url( __FILE__ ) . 'js/accu-auto-backup-admin.js', array( 'jquery' ), $this->_version, false );

					wp_localize_script(
						$this->_plugin_name,
						'accu_ajax_obj',
						array(
							'ajaxurl'           => admin_url( 'admin-ajax.php' ),
							'confirm_msg'       => __( 'Are you sure to delete?', 'accu-auto-backup' ),
							'select_msg'        => __( 'Please select any backup to be remove.', 'accu-auto-backup' ),
							'record_delete_msg' => __( 'Record(s) deleted successfully.', 'accu-auto-backup' ),
							'wait_msg'          => __( 'Please wait while we processing.....', 'accu-auto-backup' ),
							'nonce'             => $accu_nonce,
						)
					);
				}
			}
		}

		/**
		 * Add an options page under the Settings sub-menu
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function add_options_page() {
			$this->plugin_screen_hook_suffix = add_submenu_page(
				'tools.php',
				__( 'Accu Backup Settings', 'accu-auto-backup' ),
				__( 'Accu Auto Backup', 'accu-auto-backup' ),
				'manage_options',
				$this->_plugin_name,
				array( $this, 'display_options_page' )
			);
		}

		/**
		 * Render the options page for plugin
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function display_options_page() {
			include_once 'partials/accu-auto-backup-admin-display.php';
		}

		/**
		 * Register all related settings of this plugin
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function register_setting() {
			add_settings_section(
				$this->_option_name . '_general',
				__( 'General', 'accu-auto-backup' ),
				array( $this, 'accu_auto_backup_general_cb' ),
				$this->_plugin_name
			);

			add_settings_field(
				$this->_option_name . '_sc_auto_enabled',
				__( 'Schedule Backup Enabled', 'accu-auto-backup' ),
				array( $this, 'accu_auto_backup_sc_auto_enabled_cb' ),
				$this->_plugin_name,
				$this->_option_name . '_general',
				array( 'label_for' => $this->_option_name . '_sc_auto_enabled' )
			);

			add_settings_field(
				$this->_option_name . '_backup_method',
				__( 'Backup Method', 'accu-auto-backup' ),
				array( $this, 'accu_auto_backup_backup_method_cb' ),
				$this->_plugin_name,
				$this->_option_name . '_general',
				array( 'label_for' => $this->_option_name . '_backup_method' )
			);

			add_settings_field(
				$this->_option_name . '_sc_auto_interval',
				__( 'Schedule Backup Interval', 'accu-auto-backup' ),
				array( $this, 'accu_auto_backup_sc_auto_interval_cb' ),
				$this->_plugin_name,
				$this->_option_name . '_general',
				array( 'label_for' => $this->_option_name . '_sc_auto_interval' )
			);

			add_settings_field(
				$this->_option_name . '_bkp_store_limit',
				__( 'No of Backup Copies in Vault', 'accu-auto-backup' ),
				array( $this, 'accu_auto_backup_bkp_store_limit_cb' ),
				$this->_plugin_name,
				$this->_option_name . '_general',
				array( 'label_for' => $this->_option_name . '_bkp_store_limit' )
			);

			register_setting( $this->_plugin_name, $this->_option_name . '_sc_auto_enabled', array( $this, 'sanitize_auto_enabled' ) );

			register_setting( $this->_plugin_name, $this->_option_name . '_backup_method', array( $this, 'accu_auto_backup_backup_method_verify' ) );

			register_setting( $this->_plugin_name, $this->_option_name . '_sc_auto_interval' );

			register_setting( $this->_plugin_name, $this->_option_name . '_bkp_store_limit', 'intval' );
		}

		/**
		 * Render the text for the general section
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function accu_auto_backup_general_cb() {
			//echo '<p>' . __('Please change the settings accordingly.', 'accu-auto-backup') . '</p>';
		}

		/**
		 * Render the radio input field for position option
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function accu_auto_backup_backup_method_cb() {
			$_backup_method = get_option( $this->_option_name . '_backup_method' );
			$isautodisable  = '';
			if ( strtoupper( substr( $this->current_sys_os, 0, 3 ) ) === 'WIN' ) {
				$_backup_method = 'manual';
				$isautodisable  = 'disabled';
			} ?>
			<fieldset>
				<label>
					<input type="radio" <?php echo $isautodisable; ?> name="<?php echo $this->_option_name . '_backup_method'; ?>" id="<?php echo $this->_option_name . '_backup_method'; ?>" value="auto" <?php checked( $_backup_method, 'auto' ); ?>>
			<?php _e( 'Auto ( using cron job )', 'accu-auto-backup' ); ?>
				</label>
			<?php
			if ( '' !== $isautodisable ) {
				echo "<span class='winos'>" . __( 'We are unable to manage auto scheduler for windows based hosting servers due to some command restrictions.', 'accu_auto_bacup' ) . '</span>';
			}
			?>
				<br>
				<label>
					<input type="radio" name="<?php echo $this->_option_name . '_backup_method'; ?>" value="manual" <?php checked( $_backup_method, 'manual' ); ?>>
			<?php _e( 'Manual', 'accu-auto-backup' ); ?>
				</label>
					<?php
					if ( 'manual' === $_backup_method ) {
						accu_cron_manager()->get_manual_cron_html();
					}
					?>
			</fieldset>
				<?php
		}

		/**
		 * Render the radio input field for position option
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function accu_auto_backup_sc_auto_enabled_cb() {
			$sc_status = get_option( $this->_option_name . '_sc_auto_enabled' );
			?>
			<fieldset>
			<label>
				<input type="radio" name="<?php echo $this->_option_name . '_sc_auto_enabled'; ?>" id="<?php echo $this->_option_name . '_sc_auto_enabled'; ?>" value="yes" <?php checked( $sc_status, 'yes' ); ?>>
			<?php _e( 'Yes', 'accu-auto-backup' ); ?>
				</label>
				<br>
				<label>
				<input type="radio" name="<?php echo $this->_option_name . '_sc_auto_enabled'; ?>" value="no" <?php checked( $sc_status, 'no' ); ?>>
			<?php _e( 'No', 'accu-auto-backup' ); ?>
				</label>
			</fieldset>
			<?php
		}

		/**
		 * Render the radio input field for position option
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function accu_auto_backup_sc_auto_interval_cb() {
			$sc_auto_interval_status = get_option( $this->_option_name . '_sc_auto_interval' );
			$intervals               = $this->intervals;
			?>
			<fieldset>
				<select  name="<?php echo $this->_option_name . '_sc_auto_interval'; ?>" id="<?php echo $this->_option_name . '_sc_auto_interval'; ?>" >
			<?php
			//printf(__('<option value=""> -- %s -- </option>','accu_auto_backup'),__( 'Select', 'accu_auto_backup' ));
			foreach ( $intervals as $key => $single_inverval ) {
				$selected = ( $sc_auto_interval_status == $key ) ? 'selected="selected"' : '';
				echo "<option value='$key' $selected>$single_inverval</option>";
			}
			?>
				</select>
			</fieldset>
			<?php
		}

		/**
		 * Render the radio input field for position option
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function accu_auto_backup_bkp_store_limit_cb() {
			$bkp_store_limit_status = get_option( $this->_option_name . '_bkp_store_limit' );
			$intervals              = $this->bkp_store_limit;
			?>
			<fieldset>
				<select  name="<?php echo $this->_option_name . '_bkp_store_limit'; ?>" id="<?php echo $this->_option_name . '_bkp_store_limit'; ?>" >
			<?php
			//printf(__('<option value=""> -- %s -- </option>','accu_auto_backup'),__( 'Select', 'accu_auto_backup' ));
			foreach ( $intervals as $key => $single_inverval ) {
				$selected = ( (int) $single_inverval == (int) $bkp_store_limit_status ) ? 'selected="selected"' : '';
				echo "<option value='$single_inverval' $selected>$single_inverval</option>";
			}
			?>
				</select>
				<span class="rm_wrng"><?php _e( 'Caution ! It will remove your older back-ups on next backup schedule if current no of backups are more you select.', 'accu-auto-backup' ); ?></span>
			</fieldset>
			<?php
		}

		/**
		 * Sanitize the text position value before being saved to database
		 *
		 * @param string $sc_auto_enable_status $_POST value
		 *
		 * @since 1.0.0
		 *
		 * @return string           Sanitized value
		 */
		public function sanitize_auto_enabled( $sc_auto_enable_status ) {
			if ( in_array( $sc_auto_enable_status, array( 'yes', 'no' ), true ) ) {
				return $sc_auto_enable_status;
			}
		}

		/**
		 * Will check and set cron for auto mode.
		 *
		 * @param type $_backup_method pass the method of cron.
		 *
		 * @return string
		 */
		public function accu_auto_backup_backup_method_verify( $_backup_method ) {
			if ( in_array( $_backup_method, array( 'auto', 'manual' ), true ) ) {
				if ( $_backup_method ) {
				}
				return $_backup_method;
			} else {
				return 'manual';
			}
		}

		/**
		 * Define the cron line in system setting on setting form submission.
		 *
		 * @global type $options
		 * @return null
		 */
		public function accu_settings() {
			if ( isset( $_REQUEST['page'] ) && 'accu_auto_backup' == $_REQUEST['page'] && isset( $_REQUEST['settings-updated'] ) && 'true' === $_REQUEST['settings-updated'] && is_admin() && is_user_logged_in() ) {
				global $options;
				$checking = accu_cron_manager()->accu_check_and_add_auto_cron();
				$message  = null;
				$type     = 'error';
				$message  = $checking;
				if ( null !== $message ) {
					add_settings_error(
						'accu_cron_setting_error',
						esc_attr( 'settings_updated' ),
						$message,
						$type
					);
				}
			}
		}

		/**
		 *  Defines the terms and condition for module
		 *
		 * @return html
		 */
		public function terms_and_conditions() {
			$html  = '';
			$html .= '<p>' . __(
				'We as author/developer don’t give guarantee and don’t promise that the Accu Auto Backup plugin will work well on  all technical environment though we tried our best and taken care for its best availability and its workability. The Accu Auto Backup plug-in from Dhanashree Inc provided "as is" without any warranties, obvious or implied.  We will not be responsible for any direct, indirect or any other damage or loss by usage of Accu Auto Backup plug-in. User understands and confirm own responsibility up on agreement of usage of Accu Auto Backup plug-in.',
				'accu_auto_backup'
			) . '</p>';
			return $html;
		}
	}
}
