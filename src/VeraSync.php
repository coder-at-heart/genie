<?php

namespace Lnk7\Genie;

use DateTime;
use Lnk7\Genie\External\Vera;
use Lnk7\Genie\WordPressObjects\Department;
use Lnk7\Genie\WordPressObjects\Employee;
use Lnk7\Genie\WordPressObjects\Event;
use Lnk7\Genie\WordPressObjects\Office;

/**
 * Class VeraSync
 * @package Genie
 */
class VeraSync {

	/**
	 * Class Setup
	 */
	public static function Setup() {

		add_action( 'sync_with_vera', static::class . '::sync_with_vera' );
		add_action( 'init', static::class . '::init' );

		Procedures::register( 'vera_sync', function () {

			static::sync_with_vera();

			header( 'Content-Type: application/json' );

			print json_encode( [
				'success' => 'true',
				'message' => 'Vera Sync Initiated',
			] );

		} );

	}



	/**
	 * Wordpress init Hook
	 */
	public static function init() {

		if ( ! wp_next_scheduled( 'sync_with_vera' ) ) {
			$date = new DateTime( '1:00 am' );
			$time = $date->getTimestamp();
			wp_schedule_event( $time, 'twicedaily', 'sync_with_vera' );
		}

	}



	public static function syncInBackground() {

		Log::Info( static::class, 'syncInBackground Started' );

		// Make sure we're in the background
		if ( ! BackgroundJob::doingBackgroundJob() ) {
			Log::Error( 'Unable to run - Process not in background' );

			return;
		}

		$run = true;

		$lastTime = (integer) Options::get( 'sync_with_vera_last_run' );
		if ( time() - $lastTime < 300 ) {
			$run = false;
			Log::Error( static::class, '< 5 minutes since last sync... aborting' );
		}

		If ( ! Vera::isUp() ) {
			$run = false;
			Log::Error( static::class, 'Vera Down... aborting' );
		}

		if ( $run ) {
			set_time_limit( 0 );
			Office::syncWithVera();
			Department::syncWithVera();
			Employee::syncWithVera();
			Event::syncWithVera();
			Search::rebuildInBackground();
			Options::set( 'sync_with_vera_last_run', time() );
		}

		Log::Info( static::class, 'syncInBackground Completed' );
	}

	/**
	 * Submit a sync job
	 */
	public static function sync_with_vera() {

		BackgroundJob::start()
			->add( static::class . '::syncInBackground' )
			->send();
	}

}