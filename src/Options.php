<?php

namespace Lnk7\Genie;

/**
 * Class Options
 * Mana options in wp_options table
 *
 * @package Lnk7\Genie
 */
class Options
{


    /**
     * the option key
     *
     * @var string
     */
    private static $option = 'genie_options';


    /**
     * options array
     *
     * @var null
     */
    private static $options = null;


    /**
     * get an option
     *
     * @param      $option
     * @param bool $default
     *
     * @return bool|mixed
     */
    public static function get($option, $default = false)
    {
        static::load();
        if (!isset(static::$options[$option])) {
            return $default;
        }

        return static::$options[$option];
    }


    /**
     * load options into memory
     */
    protected static function load()
    {
        if (is_null(static::$options)) {
            static::$options = get_option(static::$option);
        }
    }


    /**
     * set an option
     *
     * @param $option
     * @param $value
     */
    public static function set($option, $value)
    {
        static::load();
        static::$options[$option] = $value;
        static::save();
    }


    /**
     * Save options
     */
    protected static function save()
    {
        update_option(static::$option, static::$options);
    }

}