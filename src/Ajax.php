<?php

namespace Lnk7\Genie;

use lnk7\Genie\Library\Request;
use lnk7\Genie\Library\Response;

use ReflectionMethod;

class Ajax {

    //Registered Ajax paths
    static $paths = [];



    /**
     * Setup Actions, Filters and Shortcodes
     */
    public static function Setup() {
        add_action( 'init', static::class . '::init' );

        // Action from the outside world
        add_action( 'wp_ajax_api_call_v2', static::class . '::api_call' );
        add_action( 'wp_ajax_nopriv_api_call_v2', static::class . '::api_call' );

    }


    /**
     * Wordpress Hook
     *
     * register our permalink (This will be written to .htaccess when rules are flushed
     */
    public static function init() {
        add_rewrite_rule( 'api/(.*)$', 'wp-admin/admin-ajax.php?action=api_call_v2&request=$1', 'top' );
    }


    /**
     * Allow other modules to register their paths.
     *
     * @param $path
     * @param $callback
     */
    public static function Register( $path, $callback ) {
        static::$paths[ $path ] = $callback;
    }



    /**
     * Perform the api call.
     *
     */
    public static function api_call() {

        $requestPath = $_REQUEST['request'];

        if ( ! isset( static::$paths[ $requestPath ] ) ) {
            Response::NotFound( "{$requestPath}, not found" );
        }
        $callback = static::$paths[ $requestPath ];

        $callbackParams = [];

        $reflection = new ReflectionMethod( $callback );
        $params     = $reflection->getParameters();

        foreach ( $params as $param ) {
            $name  = $param->getName();
            $value = Request::get( $name );
            if ( ! $param->isOptional() and !isset($value) ) {
                Response::Failure( "required parameter {$name} is missing"  );
            }
            $callbackParams[ $name ] = $value;
        }
        call_user_func_array( $callback, $callbackParams );

    }

}