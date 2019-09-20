<?php

namespace Lnk7\Genie;

use DateTime;
use Lnk7\Genie\WordPressObjects\Employee;

/**
 * Class Search
 * @package Genie
 */

class Search {

	/**
	 * Setup Routine
	 */
	public static function Setup() {

		add_action( 'init', static::class . '::init' );
		add_action( 'rebuild_index', static::class . '::rebuild_index' );

		AjaxController::register( 'search', static::class . '::search' );

		Procedures::register( 'rebuild_index', function () {

			static::rebuildInBackground();
		} );

	}



	/**
	 * Returns the Table Name
	 *
	 * @return string
	 */
	public static function getTableName() {

		global $wpdb;

		return $wpdb->prefix . "genie_search";

	}



	/**
	 * Wordpress Init hook
	 */
	public static function init() {

		if ( ! wp_next_scheduled( 'rebuild_index' ) ) {

			$date = new DateTime( '3:00 am' );
			$time = $date->getTimestamp();
			wp_schedule_event( $time, 'daily', 'rebuild_index' );
		}

	}


	/**
	 * Rebuild the entire Index
	 */
	public static function rebuildInBackground() {

		global $wpdb;

		Log::Info( 'search::rebuildInBackground Started' );

		// Make sure we're in the background
		if ( ! BackgroundJob::doingBackgroundJob() ) {
			Log::Error( 'Unable to run - Process not in background' );

			return;
		}

		$table_name = static::getTableName();
		$wpdb->query( "delete from $table_name" );

		$postTypes = Registry::get( 'PostTypes' );

		foreach ( $postTypes as $type => $class ) {
			if ( ! $class::$indexable ) {
				unset( $postTypes[ $type ] );
			}
		}

		$posts = get_posts( [
			'numberposts' => - 1,
			'post_type'   => array_keys( $postTypes ),
		] );

		foreach ( $posts as $post ) {

			$class  = $postTypes[ $post->post_type ];
			$object = new $class( $post->ID );
			$object->updateSearchIndex();
			unset( $object );
		}

		Options::set( 'sync_index_last_run', time() );

		Log::Info( 'search::rebuildInBackground Completed Successfully' );

	}



	/**
	 * Submit a rebuild jon into the background
	 */
	public static function rebuild_index() {

		BackgroundJob::start()->add(
			static::class . '::rebuildInBackground'
		)->send();
	}



	/**
	 * Perform a search
	 */
	public static function search( $data ) {

		global $wpdb;

		$term    = esc_sql( trim( $data->settings->term ) );
		$context = esc_sql( trim( $data->settings->context ) );

		$classes = Registry::get( 'PostTypes' );

		if ( strpos( $term, '"' ) === false ) {

			$terms = explode( ' ', $term );
			$terms = array_map( function ( $term ) {

				return '+*' . $term . '*';
			}, $terms );
			$term  = implode( ' ', $terms );

		}

		$table_name = static::getTableName();

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT S.id FROM $table_name S, $wpdb->posts P WHERE S.id = P.ID and content_type = 'employee' AND MATCH content against (%s IN BOOLEAN MODE) >0 order by P.post_title asc ",
			$term
		) );

		$totalEmployees  = count( $results );
		$results         = array_splice( $results, 0, 6 );
		$employeeResults = [];

		foreach ( $results as $result ) {
			$employeeResults[] = new Employee( $result->id );
		}

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT id, content_type, context, MATCH (content) AGAINST (%s IN BOOLEAN MODE) as score FROM $table_name WHERE content_type != 'employee' AND MATCH content against (%s IN BOOLEAN MODE) >0 order by score desc",
			$term, $term
		) );

		$searchResultsInContext = [];
		$searchResults          = [];

		$usedContentTypes = [];

		foreach ( $results as $result ) {

			$class = $classes[ $result->content_type ];

			$usedContentTypes[ $result->content_type ] = [
				'singular' => $class::getSingular(),
				'plural'   => $class::getPlural(),
			];

			$object          = new $class( $result->id );
			$object->context = $result->context;

			if ( $object->authorised ) {

				if ( $context == $result->context ) {
					$searchResultsInContext[] = $object;
				} else {
					$searchResults[] = $object;
				}
			}

		}

		$combinedResults = array_merge( $searchResultsInContext, $searchResults );

		return [
			'usedContentTypes' => $usedContentTypes,
			'totalEmployees'   => $totalEmployees,
			'employeeResults'  => $employeeResults,
			'totalResults'     => count( $combinedResults ),
			'searchResults'    => $combinedResults,
		];

	}



	/**
	 * Update the index of the document
	 *
	 * @param $id    Wordpress $id from the post Table
	 * @param $content_type
	 * @param $content
	 * @param $context
	 */
	public static function updateIndex( $id, $content_type, $content, $context ) {

		global $wpdb;

		if ( is_array( $content ) ) {
			$content = implode( ". ", $content );
		}

		$result = $wpdb->replace( static::getTableName(), compact( 'id', 'content_type', 'content', 'context' ) );

		if ( $result === false ) {
			Log::Error( 'Error updating index', $id, $content_type, $content, $context );
		}

	}

}