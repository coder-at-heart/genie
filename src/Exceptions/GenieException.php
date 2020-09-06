<?php

namespace Lnk7\Genie\Exceptions;

use Exception;
use Throwable;

class GenieException extends Exception
{


    function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        do_action('genie_exception', $this);
    }

}