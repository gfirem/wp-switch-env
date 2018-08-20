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

class wp_switch_env_log {

    function __construct() {
        add_filter( 'aal_init_roles', array( $this, 'aal_init_roles' ) );
    }

    public function aal_init_roles( $roles ) {
        $roles_existing          = $roles['manage_options'];
        $roles['manage_options'] = array_merge( $roles_existing, array( wp_switch_env_manager::getSlug() ) );

        return $roles;
    }

    public static function log( $args ) {
        if ( function_exists( "aal_insert_log" ) ) {
            aal_insert_log( $args );
        }
    }

}