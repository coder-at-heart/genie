<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Utilities\CreateDate;

/**
 * Class Wordpress
 *
 * PHP Session Handler
 *
 * @package Genie
 *
 */
class WordPress {

	static $query_vars;



	/**
	 * Constructor
	 *
	 */
	public static function Setup() {

		add_action( 'parse_request', static::class . '::parse_request' );
		add_filter( 'genie_view_before_render', static::class . '::genie_view_before_render' );
	}



	/**
	 *  End Maintenance Mode
	 *
	 * @return void
	 */
	public static function endMaintenanceMode() {

		update_field( 'maintenance_mode', 0, 'option' );
		update_field( 'maintenance_mode_started', '', 'option' );
	}



	/**
	 * Check if the site is in maintenance mode
	 *
	 * @return bool
	 */
	public static function isInMaintenanceMode() {

		return get_field( 'maintenance_mode', 'option' );
	}



	/**
	 * Wordpress Hook
	 *
	 * We process all variables here and also capture and query variables.
	 *
	 * @param $wp
	 *
	 */
	public static function parse_request( $wp ) {

		static::$query_vars = $wp->query_vars;
	}



	/**
	 * Start Maintenance Mode
	 *
	 */
	public static function startMaintenanceMode() {

		update_field( 'maintenance_mode', 1, 'option' );
		update_field( 'maintenance_mode_started', CreateDate::From( 'now' )->format( 'Y-m-d H:i:s' ), 'option' );

	}



	/**
	 * Hook into the views render function and make the session variables available to twig
	 *
	 * @param $vars
	 *
	 * @return array
	 */
	public static function genie_view_before_render( $vars ) {

		return array_merge( $vars, [ '_query_vars' => static::$query_vars ] );
	}

}