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

class wp_switch_env_admin {
	private $settings;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_admin_settings' ) );
		$this->options       = get_option( 'wp_switch_options' );
		$this->settings = array(
			'free' => array(
				'name'          => 'Free',
				'remove_option' => array( 'test' ),
				'activate'      => array( 'wc4bp-changes/wc4bp-changes.php', 'wc4bp-booking-premium/wc4bp-booking.php' ),
				'deactivate'    => array( 'gutenberg/gutenberg.php', 'wc4bp-groups/wc4bp-groups.php' ),
			),
			'pro'  => array(
				'name'          => 'Professional',
				'remove_option' => array( 'test' ),
				'activate'      => array( 'gutenberg/gutenberg.php', 'wc4bp-groups/wc4bp-groups.php' ),
				'deactivate'    => array( 'wc4bp-changes/wc4bp-changes.php', 'wc4bp-booking-premium/wc4bp-booking.php' ),
			),
		);
	}

	public function admin_menu() {
		add_menu_page( __( 'Switch Env', 'wp-switch-env' ), __( 'Switch Env', 'wp-switch-env' ), 'manage_options', wp_switch_env_manager::getSlug(), array( $this, 'screen' ), 'dashicons-controls-repeat' );
	}

	public function screen() {
		try {
			include_once( wp_switch_env::$views . 'admin-screen.php' );
		} catch ( Exception $exception ) {
			wp_switch_env_log::log( array(
				'action'         => get_class( $this ),
				'object_type'    => wp_switch_env_manager::getSlug(),
				'object_subtype' => 'loading_dependency',
				'object_name'    => $exception->getMessage(),
			) );
		}
	}

	public function register_admin_settings() {
		try {
			add_settings_section( 'section_general', '', '', 'wp_switch_options' );
			add_settings_field( 'settings', __( '<b>JSON Setting</b>', 'wp-switch-env' ), array( $this, 'json_setting_callback' ), 'wp_switch_options', 'section_general' );
			add_settings_field( 'current_env', __( '<b>Current Environment</b>', 'wp-switch-env' ), array( $this, 'current_environment_callback' ), 'wp_switch_options', 'section_general' );
			add_settings_section( 'section_details', '', '', 'wp_switch_options' );
			add_settings_field( 'env_details', __( '<b>Environment Details</b>', 'wp-switch-env' ), array( $this, 'environment_details' ), 'wp_switch_options', 'section_details' );
			register_setting( 'wp_switch_options', 'wp_switch_options', array( $this, 'change_environment' ) );
		} catch ( Exception $exception ) {
			wp_switch_env_log::log( array(
				'action'         => get_class( $this ),
				'object_type'    => wp_switch_env_manager::getSlug(),
				'object_subtype' => 'loading_dependency',
				'object_name'    => $exception->getMessage(),
			) );
		}
	}

	public static function load_plugins_dependency() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	public function change_environment( $options ) {
		if ( isset( $options['current_env'] ) && intval( $options['current_env'] ) > 0 ) {
			foreach ( $this->settings as $key => $data ) {
				if ( intval( $key ) === intval( $options['current_env'] ) ) {
					self::load_plugins_dependency();
					if ( isset( $data['activate'] ) ) {
						foreach ( $data['activate'] as $plugin_name ) {
							$result = activate_plugin( $plugin_name );
							if ( ! empty( $result ) && is_wp_error( $result ) ) {
								/** @var $result WP_Error */
								add_settings_error( 'current_env', $result->get_error_code(), $result->get_error_message() );

								return false;
							}
						}
					}
					if ( isset( $data['deactivate'] ) ) {
						foreach ( $data['deactivate'] as $plugin_name ) {
							deactivate_plugins( $plugin_name );
						}

					}
				}
			}
		}

		return $options;
	}

	public function environment_details() {
		$options       = $this->options;
		$settings = array();
		if ( ! empty( $options['settings'] ) ) {
			$settings = json_decode( $options['settings'], true);
		}
		if(!empty($settings)) {
			foreach ( $settings as $key => $data ) {
				echo '<h3>' . $data['name'] . '</h3>';
				echo '<strong>' . __( 'Activate', 'wp-switch-env' ) . '</strong>';
				echo '<ol>';
				foreach ( $data['activate'] as $plugin_name ) {
					echo '<li>' . $plugin_name . '</li>';
				}
				echo '</ol>';
				echo '<strong>' . __( 'Deactivate', 'wp-switch-env' ) . '</strong>';
				echo '<ol>';
				foreach ( $data['deactivate'] as $plugin_name ) {
					echo '<li>' . $plugin_name . '</li>';
				}
				echo '</ol>';
				echo '<strong>' . __( 'Remove Option(s)', 'wp-switch-env' ) . '</strong>';
				echo '<ol>';
				foreach ( $data['remove_option'] as $remove_option ) {
					echo '<li>' . $remove_option . '</li>';
				}
				echo '</ol>';
				echo '<hr/>';
			}
		} else {
			_e('<p>Need a setting json!</p>', 'wp-switch-env');
		}
	}

	public function json_setting_callback() {
		try {
			$options = $this->options;
			include_once( wp_switch_env::$views . 'json-settings.php' );
		} catch ( Exception $exception ) {
			wp_switch_env_log::log( array(
				'action'         => get_class( $this ),
				'object_type'    => wp_switch_env_manager::getSlug(),
				'object_subtype' => 'loading_dependency',
				'object_name'    => $exception->getMessage(),
			) );
		}
	}

	/**
	 * Current environment screen
	 */
	public function current_environment_callback() {
		try {
			$options       = $this->options;
			$current_value = ! empty( $options['current_env'] ) ? $options['current_env'] : '';
			$settings = array();
			if ( ! empty( $options['settings'] ) ) {
				$settings = json_decode( $options['settings'], true);
			}

			include_once( wp_switch_env::$views . 'switch-env.php' );
			submit_button();
		} catch ( Exception $exception ) {
			wp_switch_env_log::log( array(
				'action'         => get_class( $this ),
				'object_type'    => wp_switch_env_manager::getSlug(),
				'object_subtype' => 'loading_dependency',
				'object_name'    => $exception->getMessage(),
			) );
		}

	}
}