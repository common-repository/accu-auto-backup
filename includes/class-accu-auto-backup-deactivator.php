<?php

/**
 * Fired during plugin deactivation.
 *
 * PHP version 5
 *
 * This class defines all code necessary to run during the plugin's deactivation.
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
if ( ! class_exists( 'Accu_Auto_Backup_Deactivator' ) ) {

	/**
	 * Fired during plugin deactivation.
	 *
	 * PHP version 5
	 *
	 * This class defines all code necessary to run during the plugin's deactivation.
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
	class Accu_Auto_Backup_Deactivator {


		/**
		 * Deactivate function.
		 *
		 * Long Description.
		 *
		 * @return null
		 * @since  1.0.0
		 */
		public static function deactivate() {
			update_option( 'accu_auto_backup_sc_auto_enabled', 'no' );

			if ( has_action( 'action_remove_accu_auto_cron_job_line' ) ) {
				// action exists so execute it
				do_action( 'action_remove_accu_auto_cron_job_line' );
			}
		}
	}
}
