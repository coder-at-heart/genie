<?php

namespace Lnk7\Genie;

/**
 * Class Log
 *
 * Logging class that dumps to the wordpress error log
 *
 * @package Genie
 */
class Log {

	/**
	 * Message Properties.
	 *
	 * @var array
	 */
	private $props = [
		'type'               => '',
		'logLevel'           => null,
		'message'            => null,
		'timestamp'          => null,
		'applicationID'      => null,
		'sessionID'          => null,
		'requestID'          => null,
		'ipAddress'          => null,
		'computerName'       => null,
		'userName'           => null,
		'processMemoryUsage' => null,
		'availableMemory'    => null,
		'version'            => null,
		'applicationSize'    => null,
		'technology'         => null,
	];



	/**
	 * Log a critical message Shortcut
	 *
	 * @param mixed ...$variables
	 *
	 * @return Log
	 */
	public static function Critical( ...$variables ) {

		$log = new static( 'Critical' );
		$log->message( $variables )->addStackTrace()->save();

		return $log;
	}



	/**
	 * Debug message shortcut
	 *
	 * @param mixed ...$variables
	 *
	 * @return Log
	 */
	public static function Debug( ...$variables ) {

		$log = new static( 'Debug' );
		$log->message( $variables )->save();

		return $log;
	}



	/**
	 * Error message shortcut
	 *
	 * @param mixed ...$variables
	 *
	 * @return Log
	 */
	public static function Error( ...$variables ) {

		$log = new static( 'Error' );
		$log->message( $variables )->addStackTrace()->save();

		return $log;
	}



	/**
	 * Information message Shortcut
	 *
	 * @param mixed ...$variables
	 *
	 * @return Log
	 */

	public static function Info( ...$variables ) {

		$log = new static( 'Info' );
		$log->message( $variables);
		$log->save();

		return $log;
	}



	/**
	 * Convert a variable to a string so we can log it.
	 *
	 * @param $var
	 *
	 * @return mixed
	 */
	public static function Stringify( $var ) {

		if ( is_object( $var ) or is_array( $var ) ) {
			return print_r( $var, true );
		}

		return addslashes( $var );

	}



	/**
	 * get the log file
	 *
	 * @param string $suffix
	 *
	 * @return string
	 */
	public static function getLogFile( $suffix = '' ) {

		$path = Config::get( 'genie_LOG_PATH', WP_CONTENT_DIR );

		return $path . "/vitol{$suffix}.log";
	}



	/**
	 * Log constructor.
	 *
	 * @param string $logLevel
	 */
	function __construct( $logLevel = 'Debug' ) {

		$current_user = wp_get_current_user();
		$email        = is_object( $current_user ) ? $current_user->user_email : '';

		// Set some defaults.
		$this->logLevel( $logLevel )
			->timestamp( time() )
			->applicationID( 'my.vitol.com' )
			->sessionID( Session::getSessionID() )
			->ipAddress( $_SERVER['REMOTE_ADDR'] )
			->userName( $email )
			->version( Theme::getVersion() )
			->computerName( $_SERVER['HTTP_HOST'] )
			->processMemoryUsage( Tools::formatBytes( memory_get_usage(), 0 ) )
			->availableMemory( ini_get( 'memory_limit' ) )
			->technology( 'PHP' )
			->type( $this->getCalledMethod() );

	}



	function addStackTrace() {

		$this->props['stackTrace'] = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );

		return $this;
	}



	function applicationID( $applicationID ) {

		$this->props['applicationID'] = $applicationID;

		return $this;
	}



	function applicationSize( $widthHeightArray ) {

		$this->props['applicationSize'] = $widthHeightArray;

		return $this;
	}



	function availableMemory( $memory ) {

		$this->props['availableMemory'] = $memory;

		return $this;
	}



	function computerName( $computerName ) {

		$this->props['computerName'] = $computerName;

		return $this;
	}



	function dump() {

		Tools::dd( $this->toJSON( JSON_PRETTY_PRINT ) );
	}



	function ipAddress( $ipAddress ) {

		$this->props['ipAddress'] = $ipAddress;

		return $this;
	}



	function logLevel( $type ) {

		$this->props['logLevel'] = $type;

		return $this;
	}



	public function maybeAddStackTrace() {

		if ( in_array( $this->props['logLevel'], [ 'Error', 'Critical' ] ) or genie_ENVIRONMENT ==='development' ) {
			$this->addStackTrace();
		}

		return $this;
	}



	function message( ...$variables ) {

		$messages = [];

		foreach ( $variables as $variable ) {
			$messages[] = static::Stringify( $variable );
		}
		$this->props['message'] = implode( ', ', $messages );

		return $this;
	}



	function processMemoryUsage( $memory ) {

		$this->props['processMemoryUsage'] = $memory;

		return $this;
	}



	function requestID( $requestID ) {

		$this->props['requestID'] = $requestID;

		return $this;
	}



	function save() {

		$logFile = static::getLogFile();
		$json    = $this->toJSON();
		file_put_contents( $logFile, $json . "\n", FILE_APPEND );

		// the short log format is useful for local development
		if ( Config::get( 'genie_LOG_SHORT', false ) ) {
			$logFile = static::getLogFile( '-short' );
			file_put_contents( $logFile, $this->props['logLevel'] . ': ' . $this->props['message'] . "\n", FILE_APPEND );
			if ( isset( $this->props['stackTrace'] ) ) {
				file_put_contents( $logFile, print_r( $this->props['stackTrace'], true ) . "\n", FILE_APPEND );
			}
		}

	}



	function sessionID( $sessionID ) {

		$this->props['sessionID'] = $sessionID;

		return $this;
	}



	function technology( $technology ) {

		$this->props['technology'] = $technology;

		return $this;
	}



	function timestamp( $timestamp ) {

		$this->props['timestamp'] = $timestamp;

		return $this;
	}



	function toJSON( $options = null ) {

		return json_encode( $this->props, $options | JSON_PARTIAL_OUTPUT_ON_ERROR );
	}



	function type( $type ) {

		$this->props['type'] = $type;

		return $this;
	}



	function userName( $userName ) {

		$this->props['userName'] = $userName;

		return $this;
	}



	function version( $version ) {

		$this->props['version'] = $version;

		return $this;
	}



	private function getCalledMethod() {

		$steps    = debug_backtrace();
		$class    = '';
		$function = '';
		foreach ( $steps as $step ) {
			if ( $step['class'] !== static::class ) {
				$class    = $step['class'];
				$function = $step['function'];
				break;
			}
		}

		return "$class::$function";
	}

}