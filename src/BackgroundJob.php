<?php

namespace Lnk7\Genie;

/**
 * Class BackgroundJob
 * @package Genie
 *
 * Examples.
 *
 * $calls = class BackgroundJob::start();
 * $calls->add( Ontraport::class . '::utmIT', $utmData );
 * $calls->send();
 *
 * BackgroundJob::start()
 *  ->add( Ontraport::class . '::addTag', [ 'user_id' => $user->ID, 'tag' => $resource->tag_authorised ] )
 *  ->send();
 *
 */
class BackgroundJob {

	/**
	 * The job we're currently processing
	 * Once set this turns off all other triggers.
	 *
	 * @var int
	 */

	static $processingId = false;
	/**
	 * Array of function calls to perform on this background Job.
	 *
	 * @var array
	 */
	var $calls = [];

	/**
	 * Wordpress Hooks !
	 */
	public static function Setup() {

		//  Check if we're processing a background Job
		if ( isset( $_GET['v_bj_id'] ) and $_GET['v_bj_id'] ) {
			$id = absint( $_GET['v_bj_id'] );

			// This clever bit of code ends the connection so we don't hold up the user.
			ob_end_clean();
			ignore_user_abort( true );
			ob_start();
			header( "Connection: close" );
			header( "Content-Length: " . ob_get_length() );
			ob_end_flush();
			flush();

			// stash the id we need to process in the init hook
			static::$processingId = $id;
			add_action( 'init', static::class . '::init', 10000 );

		}

	}

	/**
	 * Hook into Wordpress Init to Run any jobs.
	 *
	 * This will usually only happen from a call from curl.
	 */
	public static function init() {

		// Do we have a job to process
		if ( static::$processingId ) {
			static::ProcessBackGroundJob( static::$processingId );
			exit;
		}
	}

	/**
	 * process this Job !
	 *
	 * @param $id
	 */
	public static function ProcessBackGroundJob( $id ) {

		set_time_limit( 0 );

		Log::Info('Starting Background Job:'.$id);

		$job = get_post( $id );

		$calls = unserialize( base64_decode( $job->post_content ) );
		foreach ( $calls as $args ) {
			$callback = array_shift( $args );
			call_user_func_array( $callback, $args );
		}
		wp_delete_post( $id, true );
		Log::Info('Completed Background Job:'.$id);
	}

	/**
	 * Start a new BackgroundJob Call Stack
	 *
	 * @return BackgroundJob
	 */
	public static function start() {
		$call = new static();

		return $call;
	}

	/**
	 * Add a job to the call Stack
	 *
	 * @return $this
	 */
	function add() {
		$this->calls[] = func_get_args();

		return $this;
	}

	/**
	 * Save the job and send it for processing.
	 */
	function send() {

		$id = wp_insert_post( [
			'post_type'    => 'genie_background_job',
			'post_content' => base64_encode( serialize( $this->calls ) ),
		] );

		$url = home_url() . '/?v_bj_id=' . $id;
		get_headers( $url );
	}


	/**
	 * Check to see if this request is processing a background Job
	 *
	 * @return bool
	 */
	public static function doingBackgroundJob() {
		return static::$processingId ? true : false;
	}


}