<?php
/**
 * Plugin Name: WP Switch Environment
 * Plugin URI:  https://www.gfirem.com/
 * Description: Switch your WordPress Environment.
 * Author:      GFireM
 * Author URI: https://gfirem.com/
 * Version:     1.0.0
 * Licence:     GPLv2
 * Text Domain: wp-switch-env
 * Domain Path: /languages
 *
 * @package wp_switch_env
 *
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'wp_switch_env' ) ) {

	class wp_switch_env {

		static public $assets_js;
		static public $assets_css;
		static public $views;

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin.
		 */
		public function __construct() {
			self::$assets_css = plugin_dir_url( __FILE__ ) . 'assets/css/';
			self::$assets_js = plugin_dir_url( __FILE__ ) . 'assets/js/';
			self::$views = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
			$this->load_plugin_textdomain();
			require_once 'classes/class-wp-switch-env-manager.php';
			new wp_switch_env_manager();
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wp-switch-env', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}
	}

	add_action( 'plugins_loaded', array( 'wp_switch_env', 'get_instance' ) );
}
