<?php

namespace Lnk7\Genie;

use Lnk7\Genie\BusinessDataObjects\Company;
use Lnk7\Genie\BusinessDataObjects\Country;
use Lnk7\Genie\External\Vera;

/**
 * Class Procedures
 *
 * Procedure are actions that can be initialed through a url. useful for cron
 *
 * https://mt.vitol.com/procedure/clear_cache
 *
 *
 * @package Genie
 */
class Procedures {

	static $paths = [];


	/**
	 * Setup Actions, Filters and Shortcodes
	 */
	public static function Setup() {

		add_action( 'init', static::class . '::init' );

		// Action from the outside world
		add_action( 'wp_ajax_process_procedure', static::class . '::process_procedure' );
		add_action( 'wp_ajax_nopriv_process_procedure', static::class . '::process_procedure' );



		Procedures::register('import', function() {

			Company::import();
			Country::import();
		});

		Procedures::register( 'test_vera', function () {

			$year = date( "Y" );

//			$thisYear = Vera::Holidays()
//				->whereYear( $year )
//				->get();

			$nextYear = Vera::Holidays()
				->whereYear( 2020 )
				->get();

			//	$holidays = array_merge($thisYear,$nextYear);

			Tools::dd( $nextYear );

		} );

		Procedures::register( 'vera_up', function () {

			Tools::dd( Vera::isUp() ? 'Up' : 'Down' );
		} );

	}



	/**
	 * Wordpress Hook
	 *
	 * register our permalink (This will be written to .htaccess when rules are flushed)
	 */
	public static function init() {

		add_rewrite_rule( 'procedure/([^/]*)/?', 'wp-admin/admin-ajax.php?action=process_procedure&path=$1', 'top' );
	}



	/**
	 * Perform the api call.
	 */
	public static function process_procedure() {

		$path = $_REQUEST['path'];
		if ( isset( static::$paths[ $path ] ) ) {
			call_user_func( static::$paths[ $path ] );
		}
		exit;
	}



	/**
	 * Allow other modules to register their paths.
	 *
	 * @param $path
	 * @param $callback
	 */
	public static function register( $path, $callback ) {

		static::$paths[ $path ] = $callback;
	}

}