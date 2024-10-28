<?php

/**
 * Fires During the uninstall of module.
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
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
/* @var wpdb $wpdb */

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( ! is_plugin_active( 'accu-auto-backup-pro/accu-auto-backup.php' ) ) {
	//only uninstall if no BackWPup Version active
	if ( ! class_exists( 'Accu_Auto_Backup' ) ) {

		//delete plugin options
		if ( is_multisite() ) {
			$wpdb->query( 'DELETE FROM ' . $wpdb->sitemeta . " WHERE meta_key LIKE '%accu_%' " );
		} else {
			$wpdb->query( 'DELETE FROM ' . $wpdb->options . " WHERE option_name LIKE '%accu_%' " );
		}
	}
	$upload_dir = wp_upload_dir();
	$plugin_dir = $upload_dir['basedir'] . '/accubackup/';
	if ( true === is_dir( $plugin_dir ) ) {
		array_map( 'unlink', glob( "$plugin_dir/*.*" ) );
		rmdir( untrailingslashit( $plugin_dir ) );
	}
	// drop a custom database table
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}accubackup" );
}
