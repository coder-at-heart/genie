<?php

namespace Lnk7\Genie;

/**
 * Class Registry
 *
 * Simple Object Cache
 *
 * @package Genie
 */

class Registry {

	/**
	 * array of values
	 *
	 * @var
	 */
	private static $registry = [];



	/**
	 * get a value from the registry
	 *
	 *
	 * @param      $group
	 * @param null $index
	 *
	 * @return mixed|null
	 */
	public static function get( $group, $index = null ) {

		if ( is_null( $index ) ) {

			if (!isset(static::$registry[ $group ])) {
				static::$registry[ $group ] = [];
			}
			return static::$registry[ $group ];
		}

		if (isset(static::$registry[ $group ][ $index ])) {
			return static::$registry[ $group ][ $index ];
		}

		return null;

	}



	/**
	 * Add a value to a registry array
	 *
	 * @param $group
	 * @param $index
	 * @param $value
	 */
	public static function push( $group, $index, $value ) {

		if ( ! isset( static::$registry[ $group ] ) ) {
			static::$registry[ $group ] = [];
		}
		static::$registry[ $group ][ $index ] = $value;
	}



	/**
	 * Set a value in teh registry
	 *
	 * @param $group
	 * @param $value
	 */
	public static function set( $group, $value ) {

		static::$registry[ $group ] = $value;
	}

}