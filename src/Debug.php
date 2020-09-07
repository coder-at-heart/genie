<?php

namespace Lnk7\Genie;

/**
 * Class Debug
 *
 * @package Lnk7\Genie
 */
class Debug
{


    /**
     * Dump a variable to the console
     *
     * @param mixed $var
     */
    public static function console($var)
    {
        if (is_array($var) or is_object($var)) {
            $var = json_encode($var);
        } else {
            $var = "$var";
        }
        echo "<script>console.log($var)</script>";
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


}