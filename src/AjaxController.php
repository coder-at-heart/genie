<?php

namespace Lnk7\Genie;

/**
 * Class AjaxController
 * @package Genie
 */

class AjaxController {

	/**
	 * An array of handlers
	 * @var array
	 */
	public static $handlers = [];

	/**
	 * the action name used for ajax calls
	 * @var string
	 */
	private static $ajaxActionName = 'genie_ajax';



	/**
	 * Setup
	 */
	public static function Setup() {

		add_action( 'wp_ajax_' . static::getAjaxActionName(), static::class . '::handleAjaxCall' );
		add_action( 'wp_ajax_nopriv_' . static::getAjaxActionName(), static::class . '::handleAjaxCall' );
		static::register( 'test', static::class . '::test' );
	}



	/**
	 * Get the Action Name
	 *
	 * @return string
	 */
	public static function getAjaxActionName() {

		return static::$ajaxActionName;
	}



	/**
	 * Handle the ajax call received
	 */
	public static function handleAjaxCall() {

		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			static::fail( _x( 'Only POST Requests Accepted', 'Ajax fail message when no request is not made via POST', 'vitol-plugin' ) );
		}

		$inputJSON = file_get_contents( 'php://input' );

		if ( ! $inputJSON ) {
			static::fail( _x( 'No Input Detected', 'Ajax Fail message when no input detected ', 'vitol-plugin' ) );
		}

		$data = json_decode( $inputJSON );

		if ( ! isset( $data->handler ) ) {
			static::fail( _x( 'No Hander Set', 'Ajax Fail message when no action is set on input', 'vitol-plugin' ) );
		}
		$handler = sanitize_text_field( $data->handler );

		if ( ! isset( static::$handlers[ $handler ] ) ) {
			static::fail( _x( 'No handler registered for this action', 'Ajax Fail message when an action has been requested which has no handler ', 'vitol-plugin' ) );
		}

		$call = static::$handlers[ $handler ];

		if ( ! is_callable( $call ) ) {
			static::fail( _x( 'Handler Not Callable', 'Ajax Fail message when the handler is not callable', 'vitol-plugin' ) );
		}

		$results = call_user_func( $call, $data );

		static::end( $results );
		exit;

	}



	/**
	 * register an ajax call
	 *
	 * @param $name
	 * @param $callback
	 */
	public static function register( $name, $callback ) {

		static::$handlers[ $name ] = $callback;
	}



	/**
	 * Used in testing
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public static function test( $data ) {

		return [
			'success'      => true,
			'dataReceived' => $data,
		];
	}



	/**
	 * End the ajax call
	 *
	 * @param $array
	 */
	private static function end( $array ) {

		if ( ! is_array( $array ) ) {
			static::fail( _x( 'Handler did not return an Array', 'Ajax Fail message when the handler did not return an array', 'vitol-plugin' ) );
		}

		print json_encode( $array );
		exit;
	}



	/**
	 * send a failed message
	 *
	 * @param $message
	 */
	private static function fail( $message ) {

		static::end( [
			'success' => false,
			'message' => $message,
		] );
	}

}