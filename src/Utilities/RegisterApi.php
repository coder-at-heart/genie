<?php

namespace Lnk7\Genie\Utilities;

use Lnk7\Genie\ApiHandler;

class RegisterApi

{


    /**
     * the url of the api call
     *
     * @var array
     */
    protected $url;


    /**
     * The accepted Method
     *
     * @var string
     */
    protected $method = 'GET';


    /**
     * constructor.
     *
     * @param string $url
     * @param string $method
     */
    public function __construct(string $url, $method = 'GET')
    {
        $this->url = $url;
        $this->method = $method;
    }


    /**
     * static constructor
     *
     * @param $url
     *
     * @return static
     */
    public static function get($url)
    {
        return new static($url, 'GET');
    }


    /**
     * static constructor
     *
     * @param $url
     *
     * @return static
     */
    public static function post($url)
    {
        return new static($url, 'POST');
    }


    /**
     * static constructor
     *
     * @param $url
     *
     * @return static
     */
    public static function put($url)
    {
        return new static($url, 'PUT');
    }


    /**
     * static constructor
     *
     * @param $url
     *
     * @return static
     */
    public static function delete($url)
    {
        return new static($url, 'DELETE');
    }


    /**
     * static constructor
     *
     * @param $url
     *
     * @return static
     */
    public static function patch($url)
    {
        return new static($url, 'PATCH');
    }


    /**
     * Set the callback and register the actions and filters
     *
     * @param callable $callback
     */
    public function run(callable $callback)
    {
        ApiHandler::register($this->url, $this->method, $callback);
    }

}