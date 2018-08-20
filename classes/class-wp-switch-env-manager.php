<?php
/**
 * @package        WordPress
 * @author         GFireM Dev Team
 * @copyright      2017, Themekraft
 * @link           http://www.gfirem.com
 * @license        http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class wp_switch_env_manager {
	private static $plugin_slug = 'wp_switch_env';
	protected static $version = '1.0.0';

	public function __construct() {
		require_once 'class-wp-switch-env-log.php';
		new wp_switch_env_log();
		try {
			if ( ! class_exists( 'Request_Helper' ) ) {
				require_once 'includes/class-request-helper.php';
			}
			require_once 'class-wp-switch-env-admin.php';
			new wp_switch_env_admin();

		} catch ( Exception $ex ) {
			wp_switch_env_log::log( array(
				'action'         => get_class( $this ),
				'object_type'    => self::getSlug(),
				'object_subtype' => 'loading_dependency',
				'object_name'    => $ex->getMessage(),
			) );
		}
	}

	/**
	 * Get plugins version
	 *
	 * @return mixed
	 */
	static function getVersion() {
		return self::$version;
	}

	/**
	 * Get plugins slug
	 *
	 * @return string
	 */
	static function getSlug() {
		return self::$plugin_slug;
	}
}
