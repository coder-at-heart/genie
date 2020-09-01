<?php

namespace Lnk7\Genie\Plugins;

class ACF
{

    /**
     * Check if ACF is disabled
     *
     * @return bool
     */
    public static function isDisabled()
    {

        return !static::isEnabled();
    }



    /**
     * Check if ACF is enabled
     *
     * @return bool
     */
    public static function isEnabled()
    {

        return function_exists('get_field');
    }

}