<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Library\Request;
use Lnk7\Genie\Library\Response;
use Lnk7\Genie\Utilities\HookInto;
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
    protected static $paths = [];



    /**
     * Setup Actions, Filters and Shortcodes
     */
    public static function Setup()
    {

        HookInto::action('init')
            ->run(function () {
                $path = apply_filters('genie_ajax_path', 'ajax');
                $action = apply_filters('genie_ajax_action', 'ajax');
                add_rewrite_rule($path . '/(.*)$', 'wp-admin/admin-ajax.php?action=' . $action . '&request=$1', 'top');

                HookInto::action('wp_ajax_' . $action)
                    ->orAction('wp_ajax_nopriv_' . $action)
                    ->run([static::class, 'ajax']);
            });

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



    /**
     * Perform the ajax call.
     */
    protected static function ajax()
    {

        $requestPath = $_REQUEST['request'];

        if (!isset(static::$paths[$requestPath])) {
            Response::NotFound([
                'message' => "{$requestPath}, not found",
            ]);
        }

        // Callback exists
        $callback = static::$paths[$requestPath];
        $params = Tools::getCallableVariables($callback);

        if (is_wp_error($params)) {
            Response::Error([
                'message' => $params->get_error_message(),
            ]);
        }

        // So we have a nice list of params
        $callbackParams = [];

        try {
            foreach ($params as $param) {
                $name = $param->getName();
                $value = Request::get($name);
                if (!$param->isOptional() and !isset($value)) {
                    Response::Failure(['message' => "required parameter {$name} is missing"]);
                }
                $callbackParams[$name] = $value;
            }

            Response::Success(call_user_func_array($callback, $callbackParams));

        } catch (Throwable $e) {
            Response::Failure([
                'message' => $e->getMessage(),
            ]);
        }
    }

}