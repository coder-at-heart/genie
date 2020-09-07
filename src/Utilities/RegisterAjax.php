<?php

namespace Lnk7\Genie\Utilities;


use Lnk7\Genie\AjaxHandler;

class RegisterAjax

{


    /**
     * the url of the ajax call
     *
     * @var array
     */
    protected $url;


    protected $nonce = '';


    /**
     * constructor.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }


    /**
     * static constructor
     *
     * @param $url
     *
     * @return static
     */
    public static function url($url)
    {
        return new static($url);
    }


    /**
     * Set the callback and register the actions and filters
     *
     * @param callable $callback
     */
    public function run(callable $callback)
    {
        AjaxHandler::register($this->url, $callback);
    }


}