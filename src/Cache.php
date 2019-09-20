<?php

namespace Lnk7\Genie;

/**
 * Class Cache
 * @package Genie
 */
class Cache {



	public static function Setup() {

		Procedures::register( 'clear_cache', function () {

			Cache::clearCache();
		} );

	}



	/**
	 * Clear Cache
	 */
	public static function clearCache() {

		global $wpdb;

		$prefix = static::getCachePrefix();

		$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '{$prefix}%'" );
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name like '%{$prefix}_api%'  " );

		Log::Info( "Cache Cleared" );
	}



	/**
	 * Cache Key prefix is used for post_meta cache
	 *
	 * @return string
	 */
	public static function getCachePrefix() {

		return 'vcache';
	}
}