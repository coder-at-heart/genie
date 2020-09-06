<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Interfaces\GenieComponent;
use Lnk7\Genie\Utilities\HookInto;
use Throwable;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * Class AjaxHandler
 *
 * @package Lnk7\Genie
 */
class AjaxHandler implements GenieComponent
{


    /**
     * An array of paths to use for ajax calls
     *
     * @var array
     */
    protected static $paths = [];


    /**
     * stash of the string to use for the action
     *
     * @var string
     */
    protected static $action;


    public static function canHandle($requestPath) {
        return isset(static::$paths[$requestPath]);
    }


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
                $action = static::getAction();

                HookInto::action('wp_ajax_' . $action)
                    ->orAction('wp_ajax_nopriv_' . $action)
                    ->run(function () {

                        Request::maybeParseInput();

                        $requestPath = Request::get('request');

                        if (!$requestPath) {
                            Response::error([
                                'message' => "No request specified",
                            ]);
                        }

                        if (!static::canHandle($requestPath)) {
                            Response::notFound([
                                'message' => "Request: {$requestPath} not found",
                            ]);
                        }

                        // The Callback exists
                        $callback = static::$paths[$requestPath];
                        $params = Tools::getCallableVariables($callback);

                        if (is_wp_error($params)) {
                            Response::error([
                                'message' => $params->get_error_message(),
                            ]);
                        }

                        // So we have a nice list of params
                        // was a nonce sent?
                        if (!Request::has('nonce')) {
                            Response::error([
                                'message' => 'No nonce specified',
                                'params'  => $params,
                            ]);
                        }

                        if (!wp_verify_nonce(Request::get('nonce'), $requestPath)) {
                            Response::error([
                                'message' => 'invalid nonce',
                            ]);
                        }

                        if (Request::wasJsonReceived() && Request::wasJsonReceivedInvalid()) {
                            Response::error([
                                'message' => "Invalid json received",
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

                        } catch (Throwable $e) {

                            Response::failure([
                                'message' => $e->getMessage(),
                            ]);
                        }
                    });
            });

        /**
         * create the ajax_url function in twig that can prefix the right path, and add the nonce.
         */
        HookInto::filter('genie_view_twig')
            ->run(function (Environment $twig) {
                $function = new TwigFunction('ajax_url', [static::class, 'generateUrl']);
                $twig->addFunction($function);
                return $twig;
            });
    }


    /**
     * get the name to use for the ajax action
     *
     * @return string
     */
    protected static function getAction()
    {
        if (!static::$action) {
            static::$action = apply_filters('genie_ajax_action', 'ajax');
        }
        return static::$action;
    }


    /**
     * Generate a url for an ajax call with the $requestPath
     *
     * @param $requestPath
     *
     * @return string
     */
    public static function generateUrl($requestPath)
    {
        return add_query_arg(
            [
                'nonce'   => wp_create_nonce($requestPath),
                'action'  => static::getAction(),
                'request' => $requestPath,
            ],
            admin_url('admin-ajax.php')
        );
    }


    /**
     * Register an ajax callback function
     *
     * @param string $path
     * @param callable $callback
     */
    public static function register(string $path, callable $callback)
    {
        static::$paths[$path] = $callback;
    }

}