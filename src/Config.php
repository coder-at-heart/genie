<?php

namespace Lnk7\Genie;

/**
 * Wrapper to wp-config.php
 * Class Config
 */
class Config
{


    /**
     * Get a config value
     *
     * @param string $value
     * @param bool $default
     *
     * @return bool|mixed
     */
    public static function get(string $value, $default = false)
    {
        if (!defined($value)) {
            return $default;
        }

        return constant($value);
    }

}