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
     * @param callable $callback
     *
     * @return ReflectionParameter[]|WP_Error
     */
    public static function getCallableVariables(callable $callback)
    {
        try {
            is_callable($callback, false, $name);

            // Closure or normal function
            if ($callback instanceof Closure || strpos($name, "::") === false) {
                $reflection = new ReflectionFunction($callback);
            } else {
                $reflection = new ReflectionMethod($name);
            }
            return $reflection->getParameters();
        } catch (ReflectionException $e) {
            return new WP_Error($e->getMessage());
        }
    }


    /**
     * Remove slashes from an array
     *
     * @param $value
     *
     * @return array|string
     */
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
     * Check if a string is JSON
     *
     * @param $jsonString
     *
     * @return array
     */
    public static function isValidJson($jsonString)
    {
        $data = json_decode($jsonString);
        $isJson = $data && $jsonString != $data;
        $validJson = $isJson && json_last_error() === JSON_ERROR_NONE;
        return [$isJson, $validJson];
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
     * Extract the domain name from a url
     *
     * @param string $url
     *
     * @return string|false
     */
    public static function getDomainName(string $url)
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


    /**
     * Used from a Twig filter to export a json object without single quotes
     *
     * @param $data
     *
     * @return false|string|string[]
     */
    public static function jsonSafe($data)
    {
        return str_replace("'", '&apos;', json_encode($data));
    }

}