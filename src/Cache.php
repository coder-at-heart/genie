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
     *
     * @param int|array|null $id
     */
    public static function clearCache( $id = null ) {

        global $wpdb;

        $where = '';
        if ( is_array( $id ) ) {
            $where = 'and post_id in (' . implode( ',', $id ) . ')';
        } else if ( is_int( $id ) ) {
            $where = 'and post_id = ' . $id;
        }

        $prefix = static::getCachePrefix();

        $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '{$prefix}%'  $where " );
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