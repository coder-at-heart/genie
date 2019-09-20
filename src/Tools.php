<?php

namespace Lnk7\Genie;

use Lnk7\Genie\WordPressObjects\Page;

/**
 * Class Tools
 * @package Genie
 */
class Tools {

	/**
	 * Add slashes to a string
	 *
	 * @param        $string
	 * @param string $chars
	 *
	 * @return string
	 */
	public static function addSlashes( $string, $chars = '"' ) {

		return addcslashes( $string, $chars );

	}



	/**
	 * Dump a variable to the console
	 *
	 * @param $var
	 */
	public static function console( $var ) {

		if ( is_array( $var ) or is_object( $var ) ) {
			$var = print_r( $var, true );
		}
		print "<script>console.log(" . json_encode( $var ) . ")</script>";
	}



	/**
	 * Dump a variable
	 *
	 * @param $var
	 */
	public static function d( $var ) {

		if ( is_array( $var ) or is_object( $var ) ) {
			$var = print_r( $var, true );
		}
		print "<pre>$var</pre>";
	}



	/**
	 * Dump and die
	 *
	 * @param $var
	 */
	public static function dd( $var ) {

		self::d( $var );
		exit;
	}



	public static function formatBytes( $size, $precision = 0 ) {

		$base     = log( $size, 1024 );
		$suffixes = [ '', 'K', 'M', 'G', 'T' ];

		return round( pow( 1024, $base - floor( $base ) ), $precision ) . $suffixes[ floor( $base ) ];
	}



	/**
	 * Extract the domain name from a url
	 *
	 * @param $url
	 *
	 * @return bool
	 */
	public static function getDomainName( $url ) {

		$pieces = parse_url( $url );
		$domain = isset( $pieces['host'] ) ? $pieces['host'] : $pieces['path'];
		if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs ) ) {
			return $regs['domain'];
		}

		return false;
	}



	/**
	 * Get an IP Address
	 * @return mixed
	 */
	public static function getIpAddress() {

		return $_SERVER['REMOTE_ADDR'];
	}





	/**
	 * Pick up headers
	 */
	public static function getRequestHeaders() {

		$headers = [];
		foreach ( $_SERVER as $key => $value ) {
			if ( substr( $key, 0, 5 ) <> 'HTTP_' ) {
				continue;
			}
			$header             = str_replace( ' ', '-', ucwords( str_replace( '_', ' ', strtolower( substr( $key, 5 ) ) ) ) );
			$headers[ $header ] = $value;
		}

		return $headers;
	}



	/**
	 * Check if the current user is a Site administrator
	 *
	 * @return boolean
	 */
	public static function isSiteAdmin() {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		return in_array( 'administrator', wp_get_current_user()->roles );
	}



	public static function jsonSafe( $data ) {

		return str_replace( "'", '&apos;', json_encode( $data ) );

	}



	/**
	 * Check is url exists
	 *
	 * @param $url
	 *
	 * @return bool
	 */
	function urlExists( $url ) {

		$transient = 'genie_urlExists_' . md5( $url );

		if ( false === ( $exists = get_transient( $transient ) ) ) {
			$headers = @get_headers( $url );
			if ( ! $headers || strpos( $headers[0], '404' ) ) {
				$exists = 'no';
			} else {
				$exists = 'yes';
			}
			set_transient( $transient, $exists, 12 * HOUR_IN_SECONDS );
		}

		return $exists == 'yes';
	}

}