<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Utilities\CreateDate;

/**
 * Class Wordpress
 *
 * WordPress Helper Functions
 *
 * @package Lnk7\Genie
 *
 */
class WordPress {

    /**
     * Query Variables
     *
     * @var
     */
    static $query_vars;



    /**
     * Constructor
     *
     */
    public static function Setup() {

        // We process all variables here and also capture and query variables.
        add_action( 'parse_request', function ( $wp ) {
            static::$query_vars = $wp->query_vars;
        } );

        // Hook into the views render function and make the session variables available to twig
        add_filter( 'genie_view_before_render', function ( $vars ) {
            return array_merge( $vars, [ '_query_vars' => static::$query_vars ] );
        } );
    }



    /**
     *  End Maintenance Mode
     *
     * @return void
     */
    public static function endMaintenanceMode() {

        update_field( 'maintenance_mode', 0, 'option' );
        update_field( 'maintenance_mode_started', '', 'option' );
    }



    /**
     * Check if the site is in maintenance mode
     *
     * @return bool
     */
    public static function isInMaintenanceMode() {

        return get_field( 'maintenance_mode', 'option' );
    }



    /**
     * Start Maintenance Mode
     *
     */
    public static function startMaintenanceMode() {

        update_field( 'maintenance_mode', 1, 'option' );
        update_field( 'maintenance_mode_started', CreateDate::From( 'now' )->format( 'Y-m-d H:i:s' ), 'option' );

    }

}