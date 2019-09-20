<?php

namespace Lnk7\Genie\Abstracts;

/**
 * Class Release
 *
 * Abstract class for database releases
 *
 * @package Genie\Abstracts
 */

Abstract class Release {

	/**
	 * Run this only once?  if set to false this runs on every release.
	 *
	 * @var bool
	 */
	static $runOnce = true;



	/**
	 * function to run when released
	 */
	public static function up() {

	}



	/**
	 * Reverse function if a rollback is requested
	 *
	 * TODO: Implement rollback mechanism
	 */
	public static function down() {

	}

}
