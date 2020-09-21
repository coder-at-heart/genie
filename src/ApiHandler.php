<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Interfaces\GenieComponent;
use Lnk7\Genie\Utilities\HookInto;
use Throwable;

/**
 * Class ApiHandler
 *
 * @package Lnk7\Genie
 */
class ApiHandler implements GenieComponent
{


    /**
     * An array of paths to use for ajax calls
     *
     * @var array
     */
    protected static $routes = [];


    /**
     * Setup Actions, Filters and Shortcodes
     */
    public static function setup()
    {

        /**
         * Handle the ajax call.
         */
        HookInto::action('init')
            ->run(function () {
                $path = apply_filters('genie_api_path', 'api');
                $action = apply_filters('genie_api_action', 'genie_api');
                add_rewrite_rule($path . '/(.*)$', 'wp-admin/admin-ajax.php?action=' . $action . '&route=$1', 'top');

                HookInto::action('wp_ajax_' . $action)
                    ->orAction('wp_ajax_nopriv_' . $action)
                    ->run(function () {

                        Request::maybeParseInput();

                        $route = Request::get('route');

                        if (!$route) {
                            Response::error([
                                'message' => "No request specified",
                            ]);
                        }

                        if (!static::canHandle($route)) {
                            Response::notFound([
                                'message' => "Request: {$route} not found",
                            ]);
                        }

                        // The Callback exists
                        $callback = static::$routes[$route]->callback;
                        $method = static::$routes[$route]->method;

                        if ($method !== Request::method()) {
                            Response::error([
                                'message' => "This route does not support " . Request::method() . " requests",
                            ]);
                        }

                        $params = Tools::getCallableParameters($callback);

                        if (is_wp_error($params)) {
                            Response::error([
                                'message' => $params->get_error_message(),
                            ]);
                        }

                        if (Request::wasJsonReceived() && Request::wasJsonReceivedInvalid()) {
                            Response::error([
                                'message'             => "Invalid json received",
                                'json_last_error'     => json_last_error(),
                                'json_last_error_msg' => json_last_error_msg(),

                            ]);
                        }

                        try {

                            $callbackParams = [];

                            foreach ($params as $param) {
                                $name = $param->getName();
                                $value = Request::get($name);
                                if (!$param->isOptional() and !isset($value)) {
                                    Response::failure(['message' => "required parameter {$name} is missing"]);
                                }
                                $callbackParams[$name] = $value;
                            }
                            Response::success(call_user_func_array($callback, $callbackParams));

                        } catch (Throwable $error) {

                            $response = [
                                'message' => $error->getMessage(),
                            ];

                            if (method_exists($error, 'getData')) {
                                $response['data'] = $error->getData();
                            }
                            if (WP_DEBUG && method_exists($error, 'getTrace')) {
                                $response['trace'] = $error->getTrace();
                            }

                            Response::failure($response);
                        }
                    });
            });

    }


    /**
     * Check that a path is registered
     *
     * @param $route
     *
     * @return bool
     */
    public static function canHandle($route)
    {
        return array_key_exists($route, static::$routes);
    }


    /**
     * Register an ajax callback function
     *
     * @param string $path
     * @param string $method
     * @param callable $callback
     */
    public static function register(string $path, string $method, callable $callback)
    {
        static::$routes[$path] = (object)[
            'method'   => strtoupper(trim($method)),
            'callback' => $callback,
        ];
    }

}