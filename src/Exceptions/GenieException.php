<?php

namespace Lnk7\Genie\Exceptions;

use Exception;
use Throwable;

class GenieException extends Exception
{


    protected $data = null;


    protected $backtrace = [];


    function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        do_action('genie_exception', $this);
    }


    /**
     * Static constructor
     *
     * @param $message
     * @param null $data
     *
     * @return static
     */
    public static function withMessage($message, $data = null)
    {
        $error = new static($message);
        $error->data = $data;
        return $error;

    }


    /**
     * throw this error
     *
     * @throws GenieException
     */
    public function throw()
    {
        $this->backtrace = debug_backtrace();

        throw $this;

    }


    /**
     * does this error message have data?
     *
     * @return bool
     */
    public function hasData()
    {
        return !is_null($this->data);
    }


    /**
     * Add data to this exception
     *
     * @param $data
     *
     * @return $this
     */
    public function withData($data)
    {
        $this->data = $data;

        return $this;
    }


    /**
     * get the data for this exception
     *
     * @return mixed
     */
    function getData()
    {
        return $this->data;
    }


    /**
     * get the backtrace
     *
     * @return mixed
     */
    function getBacktrace()
    {
        return $this->backtrace;
    }

}