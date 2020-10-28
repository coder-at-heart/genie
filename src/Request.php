<?php

namespace Lnk7\Genie;

class Request
{


    /**
     * Input variables
     *
     * @var null
     */
    protected static $data = null;


    protected static $receivedJson = false;


    protected static $jsonValid = true;


    /**
     * Get a value from the input
     *
     * @param string $var
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get(string $var, $default = false)
    {
        if (static::has($var)) {
            return static::$data[$var];
        }
        return $default;
    }


    /**
     * @param $var
     *
     * @return bool
     */
    public static function has($var)
    {
        static::maybeParseInput();
        return isset(static::$data[$var]);
    }


    /**
     * Collect Data from various input mechanisms
     */
    public static function maybeParseInput()
    {
        // Done already?
        if (!is_null(static::$data)) {
            return;
        }

        static::$data = [];

        $body = file_get_contents('php://input');

        if ($body) {
            [static::$receivedJson, static::$jsonValid] = Tools::isValidJson($body);
            static::$data = array_merge(static::$data, json_decode($body, true));
        }

        if (!empty($_GET)) {
            static::$data = array_merge(static::$data, Tools::stripSlashesArray($_GET));
        }

        if (!empty($_POST)) {
            static::$data = array_merge(static::$data, Tools::stripSlashesArray($_POST));
        }
    }


    /**
     * Return data from the request
     *
     * @return null
     */
    public static function getData()
    {
        return static::$data;
    }


    /**
     * Check if we received a json body
     *
     * @return bool
     */
    public static function wasJsonReceived()
    {
        return static::$receivedJson;
    }


    /**
     * Get the request method
     *
     * @return mixed
     */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];

    }


    /**
     * Check if we received valid Json
     *
     * @return bool
     */
    public static function wasJsonReceivedValid()
    {
        return static::$jsonValid;
    }


    /**
     * was the json received invalid ?
     *
     * @return bool
     */
    public static function wasJsonReceivedInvalid()
    {
        return !static::$jsonValid;
    }

}