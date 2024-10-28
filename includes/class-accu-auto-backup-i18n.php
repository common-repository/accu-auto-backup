<?php

/**
 * Define the internationalization functionality
 *
 * PHP version 5
 *
 * This file is used to setting the language files for translation.
 *
 * LICENSE: GPL-2.0+
 *
 * @category   Module
 * @package    Accu_Auto_Backup
 * @subpackage Accu_Auto_Backup/admin/partials
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
if ( ! class_exists( 'Accu_Auto_Backup_I18n' ) ) {

	/**
	 * Define the internationalization functionality
	 *
	 * PHP version 5
	 *
	 *  Loads and defines the internationalization files for this plugin
	 * so that it is ready for translation.
	 *
	 * LICENSE: GPL-2.0+
	 *
	 * @category   Module
	 * @package    Accu_Auto_Backup
	 * @subpackage Accu_Auto_Backup/admin/partials
	 * @author     Dhanashree Inc <business@dhanashree.com>
	 * @copyright  2018 Dhanashree Inc
	 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL License
	 * @version    Release: 1.0.0
	 * @link       http://www.dhanashree.com/
	 * @since      File available since Release 1.0.0
	 */
	class Accu_Auto_Backup_I18n {


		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain(
				'accu-auto-backup',
				false,
				ACCU_DIR_PATH . '/languages/'
			);
		}
	}
}
