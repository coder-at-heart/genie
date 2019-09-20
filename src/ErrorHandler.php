<?php

namespace Lnk7\Genie;

/**
 * Class Log
 *
 * Logging class that dumps to the wordpress error log
 *
 * @package Genie
 */
class ErrorHandler {

	/**
	 * see https://www.php.net/manual/en/errorfunc.constants.php
	 * @var array
	 */

	static private $errorMap = [
		E_ERROR           => [
			'name'     => 'E_ERROR',
			'logLevel' => 'Critical',
		],
		E_WARNING         => [
			'name'     => 'E_WARNING',
			'logLevel' => 'Error',
		],
		E_PARSE           => [
			'name'     => 'E_PARSE',
			'logLevel' => 'Critical',
		],
		E_NOTICE          => [
			'name'     => 'E_NOTICE',
			'logLevel' => 'Info',
		],
		E_CORE_ERROR      => [
			'name'     => 'E_CORE_ERROR',
			'logLevel' => 'Critical',
		],
		E_COMPILE_ERROR   => [
			'name'     => 'E_COMPILE_ERROR',
			'logLevel' => 'Critical',
		],
		E_COMPILE_WARNING => [
			'name'     => 'E_COMPILE_WARNING',
			'logLevel' => 'Error',
		],
		E_USER_ERROR      => [
			'name'     => 'E_USER_ERROR',
			'logLevel' => 'Critical',
		],
		E_USER_WARNING    => [
			'name'     => 'E_USER_WARNING',
			'logLevel' => 'Error',
		],
		E_USER_NOTICE => [
			'name'     => 'E_USER_NOTICE',
			'logLevel' => 'Info',
		],
		E_STRICT            => [
			'name'     => 'E_STRICT',
			'logLevel' => 'Info',
		],
		E_RECOVERABLE_ERROR => [
			'name'     => 'E_RECOVERABLE_ERROR',
			'logLevel' => 'Error',
		],
		E_DEPRECATED        => [
			'name'     => 'E_DEPRECATED',
			'logLevel' => 'Info',
		],
		E_USER_DEPRECATED   => [
			'name'     => 'E_USER_DEPRECATED',
			'logLevel' => 'Info',
		],
		E_ALL               => [
			'name'     => 'E_ALL',
			'logLevel' => 'Info',
		],

	];

	public static function Setup() {
		set_error_handler( static::class . '::error_handler', E_ALL );

	}



	/**
	 * error handler
	 *
	 * @param $errNo
	 * @param $errStr
	 * @param $errFile
	 * @param $errLine
	 */
	public static function error_handler( $errNo, $errStr, $errFile, $errLine ) {

		$errorType = static::$errorMap[ $errNo ];

		$message = $errorType['name'] . ": " . $errStr . " in " . $errFile . ':' . $errLine;

		$log = new Log( $errorType['logLevel'] );
		$log->maybeAddStackTrace()->message( $message )->save();

	}

}