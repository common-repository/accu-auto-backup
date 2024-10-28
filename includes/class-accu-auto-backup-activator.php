<?php

/**
 * Fires During plug-in activation.
 *
 * PHP version 5
 *
 * LICENSE: GPL-2.0+
 *
 * @category   Module
 * @package    Accu_Auto_Backup
 * @subpackage Accu_Auto_Backup
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
if ( ! class_exists( 'Accu_Auto_Backup_Activator' ) ) {

	/**
	 * Fires During plug-in activation.
	 *
	 * PHP version 5
	 *
	 * LICENSE: GPL-2.0+
	 *
	 * @category   Module
	 * @package    Accu_Auto_Backup
	 * @subpackage Accu_Auto_Backup
	 * @author     Dhanashree Inc <business@dhanashree.com>
	 * @copyright  2018 Dhanashree Inc
	 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL License
	 * @version    Release: 1.0.0
	 * @link       http://www.dhanashree.com/
	 * @since      File available since Release 1.0.0
	 */
	class Accu_Auto_Backup_Activator {


		/**
		 * Short Description. (use period)
		 *
		 * Long Description.
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public static function activate() {
			if ( ! get_option( 'accu_auto_backup_sc_auto_enabled' ) ) {
				add_option( 'accu_auto_backup_sc_auto_enabled', 'no' );
			}

			if ( ! get_option( 'accu_auto_backup_backup_method' ) ) {
				add_option( 'accu_auto_backup_backup_method', 'manual' );
			}

			if ( ! get_option( 'accu_auto_backup_sc_auto_interval' ) ) {
				add_option( 'accu_auto_backup_sc_auto_interval', 'monthly' );
			}

			if ( ! get_option( 'accu_auto_backup_bkp_store_limit' ) ) {
				add_option( 'accu_auto_backup_bkp_store_limit', 10 );
			}

			global $wpdb;

			$table_name = $wpdb->prefix . 'accubackup';

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
                    backup_id mediumint(9) NOT NULL AUTO_INCREMENT,
                    filename tinytext NOT NULL,
                    size varchar(128) NOT NULL,
                    log tinytext DEFAULT '' NOT NULL,
                    date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY  (backup_id)
              ) $charset_collate;";

			include_once ABSPATH . 'wp-admin/includes/upgrade.php';

			dbDelta( $sql );
		}
	}
}
