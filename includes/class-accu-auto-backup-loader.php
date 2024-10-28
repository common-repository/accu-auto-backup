<?php

/**
 * Register all actions and filters for the plugin
 *
 * PHP version 5
 *
 * Register all actions and filters for the plugin
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
if ( ! class_exists( 'Accu_Auto_Backup_Loader' ) ) {

	/**
	 * Register all actions and filters for the plugin.
	 *
	 * Maintain a list of all hooks that are registered throughout
	 * the plugin, and register them with the WordPress API. Call the
	 * run function to execute the list of actions and filters.
	 *
	 * @category   Class
	 * @package    Accu_Auto_Backup
	 * @subpackage Accu_Auto_Backup/includes
	 * @author     DINC <business@dhanashree.com>
	 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL License
	 * @version    Release: 1.0.0
	 * @link       http://www.dhanashree.com/
	 */
	class Accu_Auto_Backup_Loader {


		/**
		 * The array of actions registered with WordPress.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @var    array    $actions    Actions registered with WP
		 *                              to fire when plugin loads.
		 */
		protected $actions;

		/**
		 * The array of filters registered with WordPress.
		 *
		 * @since  1.0.0
		 * @access protected
		 * @var    array    $filters    The filters registered with WordPress
		 * to fire when the plugin loads.
		 */
		protected $filters;

		/**
		 * Initialize the collections used to maintain the actions and filters.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->actions = array();
			$this->filters = array();
		}

		/**
		 * Add a new action to the collection to be registered with WordPress.
		 *
		 * @param string $hk       The name of the WordPress
		 * @param object $cmpnt    A reference to the instance of the object
		 *                         on which the action is defined.
		 * @param string $cb       The name of the function
		 *                         definition on the
		 *                         $component.
		 * @param int    $priority Optional. The priority at which the function
		 *                         should be fired. Default is 10.
		 * @param int    $args     Optional. The number of arguments
		 *                         that should be passed to the
		 *                         $callback. Default is 1.
		 *
		 * @since  1.0.0
		 * @return null
		 */
		public function add_action( $hk, $cmpnt, $cb, $priority = 10, $args = 1 ) {
			$this->actions = $this->_add( $this->actions, $hk, $cmpnt, $cb, $priority, $args );
		}

		/**
		 * Add a new filter to the collection to be registered with WordPress.
		 *
		 * @param string $hk       The name of the WordPress
		 *                         filter that is being
		 *                         registered.
		 * @param object $cmp      A reference to the instance of
		 *                         the object on which the filter
		 *                         is defined.
		 * @param string $cb       The name of the function
		 *                         definition on the
		 *                         $component.
		 * @param int    $priority Optional. The priority at which the
		 *                         function should be fired. Default is
		 *                         10.
		 * @param int    $args     Optional. The number of arguments
		 *                         that should be passed to the
		 *                         $callback.  Default is 1
		 *
		 * @since  1.0.0
		 * @return filter list
		 */
		public function add_filter( $hk, $cmp, $cb, $priority = 10, $args = 1 ) {
			$this->filters = $this->_add( $this->filters, $hk, $cmp, $cb, $priority, $args );
		}

		/**
		 * A utility function that is used to
		 * register the actions and hooks into a single
		 * collection.
		 *
		 * @param array  $hooks         Collection of hooks that is being registered.
		 * @param string $hook          The name of the WordPress
		 *                              filter that is being registered.
		 * @param object $component     A reference to the instance of
		 *                              the object on which the filter is defined.
		 * @param string $callback      The name of the function
		 *                              definition on the $component.
		 * @param int    $priority      The priority at which
		 *                              the function should be fired.
		 * @param int    $accepted_args The number of arguments that
		 *                              should be passed to the $callback.
		 *
		 * @access private
		 * @since  1.0.0

		 * @return array                 The collection of actions
		 *                               and filters registered with WordPress.
		 */
		private function _add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
			$hooks[] = array(
				'hook'          => $hook,
				'component'     => $component,
				'callback'      => $callback,
				'priority'      => $priority,
				'accepted_args' => $accepted_args,
			);

			return $hooks;
		}

		/**
		 * Register the filters and actions with WordPress.
		 *
		 * @since  1.0.0
		 * @return null;
		 */
		public function run() {
			foreach ( $this->filters as $hook ) {
				add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
			}

			foreach ( $this->actions as $hook ) {
				add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
			}
		}
	}
}
