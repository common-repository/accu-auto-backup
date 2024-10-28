<?php

/**
 * The file that defines the core plugin class
 *
 * PHP version 5
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
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
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    Accu_Auto_Backup
 * @subpackage Accu_Auto_Backup/includes
 * @author     DINC <business@dhanashree.com>
 * @since      1.0.0
 */
if ( ! class_exists( 'Accu_Auto_Backup' ) ) {

	/**
	 * The core plugin class.
	 *
	 * This is used to define internationalization, admin-specific hooks, and
	 * public-facing site hooks.
	 *
	 * Also maintains the unique identifier of this plugin as well as the current
	 * version of the plugin.
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
	class Accu_Auto_Backup {


		/**
		 * The loader that's responsible for maintaining and registering all hooks that power
		 * the plugin.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @var    Accu_auto_backup_Loader    $loader    Maintains and registers all hooks for the plugin.
		 */
		protected $loader;

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @var    string    $plugin_name    The string used to uniquely identify this plugin.
		 */
		protected $plugin_name;

		/**
		 * The current version of the plugin.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @var    string    $version    The current version of the plugin.
		 */
		protected $version;

		/**
		 * Define the core functionality of the plugin.
		 *
		 * Set the plugin name and the plugin version that can be used throughout the plugin.
		 * Load the dependencies, define the locale, and set the hooks for the admin area and
		 * the public-facing side of the site.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
				$this->version = PLUGIN_NAME_VERSION;
			} else {
				$this->version = '1.0.0';
			}
			$this->define_constants();
			$this->plugin_name = 'accu_auto_backup';

			$this->load_dependencies();
			$this->set_locale();
			$this->define_admin_hooks();
		}

		/**
		 * Restrict the clone
		 *
		 * @return null
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0' );
		}

		/**
		 * Define constants for plug-ins.
		 *
		 * @return null
		 */
		public function define_constants() {
			if ( ! defined( 'ACCU_AUTO_BACKUP_VERSION' ) ) {
				define( 'ACCU_AUTO_BACKUP_VERSION', '1.0.0' );
			}

			if ( ! defined( 'ACCU_DIR_URL' ) ) {
				define( 'ACCU_DIR_URL', plugin_dir_url( dirname( __FILE__ ) ) );
			}

			if ( ! defined( 'ACCU_DIR_PATH' ) ) {
				define( 'ACCU_DIR_PATH', plugin_dir_path( dirname( __FILE__ ) ) );
			}

			if ( ! defined( 'ACCU_LOG_FILE' ) ) {
				define( 'ACCU_LOG_FILE', plugin_dir_path( dirname( __FILE__ ) ) . '/log/accu-log.txt' );
			}

			$upload_dir = wp_upload_dir();

			if ( ! defined( 'ACCU_AUTO_BACKUP_DIR' ) ) {
				define( 'ACCU_AUTO_BACKUP_DIR', $upload_dir['basedir'] . '/accubackup/' );
			}

			if ( ! defined( 'ACCU_AUTO_BACKUP_DIR_URL' ) ) {
				define( 'ACCU_AUTO_BACKUP_DIR_URL', $upload_dir['baseurl'] . '/accubackup/' );
			}
		}

		/**
		 * Load the required dependencies for this plugin.
		 *
		 * Include the following files that make up the plugin:
		 *
		 * - Accu_auto_backup_Loader. Orchestrates the hooks of the plugin.
		 * - Accu_auto_backup_i18n. Defines internationalization functionality.
		 * - Accu_auto_backup_Admin. Defines all hooks for the admin area.
		 * - Accu_auto_backup_Public. Defines all hooks for the public side of the site.
		 *
		 * Create an instance of the loader which will be used to register the hooks
		 * with WordPress.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return null
		 */
		private function load_dependencies() {

			/**
			 * The class responsible for orchestrating the actions and filters of the
			 * core plugin.
			 */
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-accu-auto-backup-loader.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-accu-auto-backup-i18n.php';

			/**
			 * The class responsible for defining internationalization functionality
			 * of the plugin.
			 */
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-accu-auto-backup-cron-manager.php';

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-accu-auto-backup-admin.php';

			/**
			 * The class responsible for manage all backup related functions
			 * */
			include_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-accu-auto-backup-db-functions.php';

			/**
			 * The class responsible for manage all listing page
			 * */
			//require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-accu-auto-backup-listing.php';

			$this->loader = new Accu_Auto_Backup_Loader();
		}

		/**
		 * Define the locale for this plugin for internationalization.
		 *
		 * Uses the Accu_auto_backup_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return null
		 */
		private function set_locale() {
			$plugin_i18n = new Accu_Auto_Backup_I18n();

			$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 * of the plugin.
		 *
		 * @since  1.0.0
		 * @access private
		 * @return null
		 */
		private function define_admin_hooks() {
			$plugin_admin = new Accu_Auto_Backup_Admin( $this->get_plugin_name(), $this->get_version() );

			//added for new version - 17-04-2018
			$this->loader->add_action( 'upgrader_process_complete', $plugin_admin, 'accu_auto_backup_upgrade_completed', 10, 2 );

			$this->loader->add_action( 'admin_notices', $plugin_admin, 'accu_auto_backup_display_update_notice', 10, 2 );

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting' );

			$this->loader->add_action( 'admin_init', $plugin_admin, 'accu_settings' );
			//$this->loader->addAction('admin_init', $plugin_admin, 'accuAutoBackupAdminInit');

			$plugin_db_functions = new Accu_Auto_Backup_Db_Functions();

			$this->loader->add_action( 'admin_notices', $plugin_db_functions, 'accu_backup_admin_notice', 10 );

			$this->loader->add_filter( 'accu_backup_event', $plugin_db_functions, 'accu_auto_backup_bkp_process' );
			$this->loader->add_action( 'accu_backup_listing', $plugin_db_functions, 'list_backups' );

			$this->loader->add_action( 'wp_ajax_accu_dt_get_list', $plugin_db_functions, 'get_accu_auto_backup_backps_list' );
			$this->loader->add_action( 'wp_ajax_remove_single_backup', $plugin_db_functions, 'remove_single_backup_handler' );
			$this->loader->add_action( 'wp_ajax_delete_selected', $plugin_db_functions, 'delete_selected_handler' );
			$this->loader->add_action( 'wp_ajax_download_selected', $plugin_db_functions, 'download_selected_handler' );

			$this->loader->add_action( 'display_cron_info', accu_cron_manager(), 'get_cron_info' );
			$this->loader->add_action( 'action_remove_accu_auto_cron_job_line', accu_cron_manager(), 'remove_accu_auto_cron_job_Line', 10 );

			//include-cron
			$this->loader->add_action( 'wp_loaded', accu_cron_manager(), 'run_front_cron', 99 );
		}


		/**
		 * Run the loader to execute all of the hooks with WordPress.
		 *
		 * @since  1.0.0
		 * @return instance
		 */
		public function run() {
			$this->loader->run();
		}

		/**
		 * The name of the plugin used to uniquely identify it within the context of
		 * WordPress and to define internationalization functionality.
		 *
		 * @since  1.0.0
		 * @return string    The name of the plugin.
		 */
		public function get_plugin_name() {
			return $this->plugin_name;
		}

		/**
		 * The reference to the class that orchestrates the hooks with the plugin.
		 *
		 * @since  1.0.0
		 * @return Accu_auto_backup_Loader    Orchestrates the hooks of the plugin.
		 */
		public function get_loader() {
			return $this->loader;
		}

		/**
		 * Retrieve the version number of the plugin.
		 *
		 * @since  1.0.0
		 * @return string    The version number of the plugin.
		 */
		public function get_version() {
			return $this->version;
		}
	}
}
