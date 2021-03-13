<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Interfaces\GenieComponent;
use Lnk7\Genie\Utilities\RegisterApi;
use WP_CLI;

/**
 * Class Deploy
 *
 * @package lnk7\Genie
 */
class Deploy implements GenieComponent
{


    /**
     * Setup
     */
    public static function setup()
    {

        RegisterApi::get('deploy')
            ->run([static::class, 'deploy']);

        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::add_command('deploy', [static::class, 'deploy']);
        }
    }


    /**
     * Run after a deployment.
     */
    public static function deploy()
    {
        do_action('genie_before_deploy');
		static::updateDatabase();
		static::loadReleases();
		Cache::clearCache();
		do_action('genie_after_deploy');
	}


	/**
	 * Update the database
	 */
	protected static function updateDatabase()
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$sqlStatements = apply_filters('genie_update_database', []);
		foreach ($sqlStatements as $sqlStatement) {
			dbDelta($sqlStatement);
		}
	}


	/**
	 * load releases
	 */
	protected static function loadReleases()
	{
		$releaseFolder = apply_filters('genie_release_folder', Genie::getReleasesFolder());

		if (!file_exists($releaseFolder)) {
			return;
		}

		$releases = Options::get('genie_releases', []);

		foreach (glob(trailingslashit($releaseFolder) . '*.php') as $file) {
			if (!in_array($file, $releases)) {
				$releases[] = $file;
				require_once($file);
			}
		}
		Options::set('genie_releases', $releases);

	}

}
