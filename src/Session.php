<?php

namespace Lnk7\Genie;

/**
 * Class Session
 *
 * PHP Session Handler
 *
 * @package Lnk7\Genie
 *
 */
class Session {

    /**
     * Constructor
     *
     */
    public static function Setup() {

        // Run once everything has been setup
        add_action( 'after_setup_theme', function () {
            $sessionName = apply_filters( 'genie_session_name', 'genie_session' );

            session_name( $sessionName );

            if ( ! session_id() ) {
                session_start();
            }
            $maxTime = ini_get( "session.gc_maxlifetime" );

            // Force our cookie expiry date
            setcookie( session_name(), session_id(), time() + $maxTime, '/' );

            // Last request was more than $maxTime seconds ago?
            if ( isset( $_SESSION['sessionLastActivity'] ) && ( time() - $_SESSION['sessionLastActivity'] > $maxTime ) ) {
                static::destroy();
            }

            // Update last activity time stamp
            $_SESSION['sessionLastActivity'] = time();

            if ( ! isset( $_SESSION['sessionCreated'] ) ) {
                $_SESSION['sessionCreated'] = time();

            } else if ( time() - $_SESSION['sessionCreated'] > $maxTime ) {

                // The Session started more than $maxTime seconds ago,
                // change session ID for the current session and invalidate old session ID
                session_regenerate_id( true );

                // update creation time
                $_SESSION['sessionCreated'] = time();
            }

        } );

        /**
         * Wordpress Hook
         *
         * We process all variables here and also capture and query variables.
         *
         */
        add_action( 'parse_request', function ( $wp ) {
            self::processVariables();
            self::set( 'query_vars', $wp->query_vars );
        } );

        /**
         *
         * Var shortcode
         *
         * [var] shortcode
         * [var email default='']
         *
         */
        add_shortcode( 'var', function ( $atts ) {

            $a = (object) shortcode_atts( [
                'var'     => $atts[0],
                'default' => '',
            ], $atts );

            return self::find( $a->var, $a->default );
        } );

        // Plug the session into all views
        add_filter( 'genie_view_before_render', function ( $vars ) {
            return array_merge( $vars, [ '_session' => $_SESSION ] );
        }, 10, 1 );
    }



    /**
     * Destroys the session
     */
    public static function destroy() {
        // unset $_SESSION variable for the run-time
        session_unset();
        // destroy session data in storage
        session_destroy();
    }



    /**
     * Get the variables that needs to be saved, and then add them to the session.
     *
     */
    public static function processVariables() {

        $fields = apply_filters( 'genie_session_parse_request', [] );

        foreach ( $fields as $field ) {
            if ( isset( $_REQUEST[ $field ] ) ) {
                if ( function_exists( 'filter_var' ) ) {
                    $val = filter_var( $_REQUEST[ $field ], FILTER_SANITIZE_STRING );
                } else {
                    $val = $_REQUEST[ $field ];
                }
                $_SESSION[ $field ] = stripslashes( $val );
            }
        }
    }



    /**
     * Save a value to the Session
     *
     * @param $var
     * @param $value
     */
    public static function set( $var, $value ) {
        $_SESSION[ $var ] = $value;
    }



    /**
     * look for a value in the session. can be accessed by dot notation (like twig)
     *
     * $object->property['index']
     *
     * Session::get(object.property.index);
     *
     * @param $var
     * @param bool $default
     *
     * @return mixed
     */
    private static function find( $var, $default = false ) {
        $lookAt = $_SESSION;
        $keys   = explode( '.', $var );
        foreach ( $keys as $key ) {
            if ( is_object( $lookAt ) and property_exists( $lookAt, $key ) ) {
                $lookAt = $lookAt->$key;
                continue;
            }
            if ( is_array( $lookAt ) and isset( $lookAt[ $key ] ) ) {
                $lookAt = $lookAt[ $key ];
                continue;
            }
            $lookAt = $default;
        }

        return $lookAt;
    }



    /**
     * Check if the session has a value
     *
     * @param $field
     *
     * @return bool
     */
    public static function has( $field ) {
        return self::find( $field ) ? true : false;
    }



    /**
     * Get a value from the session
     */
    public static function get( $var, $default = false ) {
        return self::find( $var, $default );
    }



    /**
     * Remove a value for the session
     *
     * @param $var
     */
    public static function remove( $var ) {
        unset( $_SESSION[ $var ] );
    }



    /**
     * Get the session ID
     *
     * @return string
     */
    public static function getSessionID() {
        return session_id();
    }

}