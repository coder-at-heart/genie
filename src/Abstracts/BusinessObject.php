<?php

namespace Lnk7\Genie\Abstracts;

/**
 * Used to store and handle a data object.
 *
 * Class BusinessObject
 * @package Lnk7\Genie\Abstracts
 */
Abstract class BusinessObject {

	/**
	 * Properties.
	 *
	 * @var object
	 */
	var $props;



	/**
	 * Helper function
	 *
	 * @param $data
	 *
	 * @return BusinessObject
	 */
	public static function create( array $data = [] ) {

		return new static( $data );
	}



	/**
	 * Contact constructor.
	 *
	 * @param $data
	 */
	public function __construct( array $data = [] ) {

		$this->props = (object) [];

		foreach ( $data as $key => $value ) {
			$this->props->$key = $value;
		}

		return $this;
	}



	/**
	 * Magic getter
	 *
	 * @param $var
	 *
	 * @return bool
	 */

	public function __get( $var ) {

		if ( ! isset( $this->props->$var ) ) {
			return false;
		}

		return $this->props->$var;

	}



	/**
	 * Magic Setter
	 *
	 * @param $var
	 * @param $value
	 */
	public function __set( $var, $value ) {

		$this->props->$var = $value;

	}



	/**
	 * Check isset
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {

		return isset( $this->props->$name );

	}

}