<?php

namespace Lnk7\Genie;

use Closure;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use WP_Error;

/**
 * Class Tools
 *
 * @package Lnk7\Genie
 */
class Tools
{

    /**
     * Add slashes to a string
     *
     * @param        $string
     * @param string $chars
     *
     * @return string
     */
    public static function addSlashes($string, $chars = '"')
    {

        return addcslashes($string, $chars);

    }



    /**
     * @param $callback
     *
     * @return ReflectionParameter[]|WP_Error
     */
    public static function getCallableVariables($callback)
    {
        try {

            if ($callback instanceof Closure) {
                $reflection = new ReflectionFunction($callback);
            } else {
                if (is_array($callback)) {
                    $method = $callback[0] . '::' . $callback[1];
                } else {
                    $method = $callback;
                }

                $reflection = new ReflectionMethod($method);
            }
            return $reflection->getParameters();
        } catch (ReflectionException $e) {
            return new WP_Error($e->getMessage());
        }

    }



    public static function stripSlashesArray($value)
    {
        $value = is_array($value) ?
            array_map('stripslashes_deep', $value) :
            stripslashes($value);

        return $value;
    }



    /**
     * Check if a String is JSON and return the object or the original string.
     *
     * @param $string
     *
     * @return bool|object|array
     */
    public static function maybeConvertFromJson($string)
    {

        $json = json_decode($string);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $json;
        }

        return $string;
    }



    /**
     * Check if a String is JSON and return it, or convert it to Json
     *
     * @param $string
     *
     * @return bool|object|array
     */
    public static function maybeConvertToJson($string)
    {
        if (is_string($string)) {
            $json = json_decode($string);
            if (json_last_error() == JSON_ERROR_NONE) {
                return $string;
            }

        }

        return json_encode($string);
    }



    /**
     * Dump a variable to the console
     *
     * @param $var
     */
    public static function console($var)
    {

        if (is_array($var) or is_object($var)) {
            $var = print_r($var, true);
        }
        print "<script>console.log(" . json_encode($var) . ")</script>";
    }



    /**
     * Dump and die
     *
     * @param $var
     */
    public static function dd($var)
    {

        self::d($var);
        exit;
    }



    /**
     * Dump a variable
     *
     * @param $var
     */
    public static function d($var)
    {

        if (is_array($var) or is_object($var)) {
            $var = print_r($var, true);
        }
        print "<pre>$var</pre>";
    }



    /**
     * Extract the domain name from a url
     *
     * @param $url
     *
     * @return bool
     */
    public static function getDomainName($url)
    {

        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }

        return false;
    }



    /**
     * Get an IP Address
     *
     * @return mixed
     */
    public static function getIpAddress()
    {

        return $_SERVER['REMOTE_ADDR'];
    }



    /**
     * Pick up headers
     */
    public static function getRequestHeaders()
    {

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }

        return $headers;
    }



    public static function jsonSafe($data)
    {

        return str_replace("'", '&apos;', json_encode($data));

    }

}