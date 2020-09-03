<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Library\Request;
use Lnk7\Genie\Library\Response;
use ReflectionException;
use ReflectionMethod;
use Throwable;

/**
 * Class Ajax
 *
 * @package Lnk7\Genie
 */
class Ajax
{

    /**
     * An array of paths to use for ajax calls
     *
     * @var array
     */
    static $paths = [];



    /**
     * Setup Actions, Filters and Shortcodes
     */
    public static function Setup()
    {
        add_action('init', function () {
            $path = apply_filters('genie_ajax_path', 'ajax');
            $action = apply_filters('genie_ajax_action', 'ajax');
            add_rewrite_rule($path . '/(.*)$', 'wp-admin/admin-ajax.php?action=' . $action . '&request=$1', 'top');

            // Action from the outside world
            add_action('wp_ajax_' . $action, function () {
                static::ajax();
            });
            add_action('wp_ajax_nopriv_' . $action, function () {
                static::ajax();
            });

        });

    }



    /**
     * Perform the ajax call.
     *
     * @throws ReflectionException
     */
    protected static function ajax()
    {

        $requestPath = $_REQUEST['request'];

        if (!isset(static::$paths[$requestPath])) {
            Response::NotFound(['message' => "{$requestPath}, not found"]);
        }
        $callback = static::$paths[$requestPath];

        $callbackParams = [];

        $reflection = new ReflectionMethod($callback);
        $params = $reflection->getParameters();

        try {
            foreach ($params as $param) {
                $name = $param->getName();
                $value = Request::get($name);
                if (!$param->isOptional() and !isset($value)) {
                    Response::Failure(['message' => "required parameter {$name} is missing"]);
                }
                $callbackParams[$name] = $value;
            }

            $result = call_user_func_array($callback, $callbackParams);
            Response::Success([
                'response' => $result,
            ]);
        } catch (Throwable $e) {
            Response::Failure([
                'message' => $e->getMessage(),
            ]);
        }
    }



    /**
     * Allow other modules to register their paths.
     *
     * @param $path
     * @param $callback
     */
    public static function Register($path, $callback)
    {
        static::$paths[$path] = $callback;
    }

}