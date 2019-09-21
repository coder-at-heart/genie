<?php

namespace Lnk7\Genie;

/**
 * Class Cache
 *
 * @package Lnk7\Genie
 */
class Cache {

    /**
     * Clear Cache
     */
    public static function clearCache() {

        global $wpdb;

        $prefix = static::getCachePrefix();

        $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '{$prefix}%'" );
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name like '%{$prefix}_api%'  " );
    }


    /**
     * Cache Key prefix is used for post_meta cache
     *
     * @return string
     */
    public static function getCachePrefix() {

        return apply_filters( 'genie_get_cache_prefix', 'gcache' );
    }
}