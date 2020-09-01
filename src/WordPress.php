<?php

namespace Lnk7\Genie;


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



}