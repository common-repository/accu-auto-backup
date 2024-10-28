<?php

/**
 * The plugin bootstrap file
 *
 * PHP version 7
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @category  Module
 * @package   Accu_Auto_Backup
 * @author    Dhanashree Inc <business@dhanashree.com>
 * @copyright 2022 Dhanashree Inc
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt GPL License
 * @version   SVN: 1.0.4
 * @link      http://www.dhanashree.com/
 * @since     File available since Release 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Accu Auto Backup
 * Plugin URI:        http://www.dhanashree.com/web-development-services/module-development/wordpress-accu-auto-backup
 * Description:       Accu auto backup plugin takes WordPress database backup automatically with defined schedule.
 * Version:           1.0.4
 * Author:            Dhanashree Inc
 * Author URI:        http://www.dhanashree.com/
 * Author Email:      business@dhanashree.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       accu-auto-backup
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-accu-auto-backup-activator.php
 *
 * @return null
 */
function activateaccuautobackup() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-accu-auto-backup-activator.php';
	Accu_Auto_Backup_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-accu-auto-backup-deactivator.php
 *
 * @return null
 */
function deactivateaccuautobackup() {
	include_once plugin_dir_path( __FILE__ ) . 'includes/class-accu-auto-backup-deactivator.php';
	Accu_Auto_Backup_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activateaccuautobackup' );
register_deactivation_hook( __FILE__, 'deactivateaccuautobackup' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( ! is_plugin_active( 'accu-auto-backup-pro/accu-auto-backup.php' ) ) {
	require plugin_dir_path( __FILE__ ) . 'includes/class-accu-auto-backup.php';
	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since  1.0.0
	 * @return instance of Class
	*/
	function runaccuautobackup() {
		$plugin = new Accu_Auto_Backup();
		$plugin->run();
	}
	runaccuautobackup();
}
