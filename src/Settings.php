<?php

namespace Lnk7\Genie;

use DateTimeZone;
use Lnk7\Genie\Fields\ColorSelectorField;
use Lnk7\Genie\Fields\EmailField;
use Lnk7\Genie\Fields\IconField;
use Lnk7\Genie\Fields\MessageField;
use Lnk7\Genie\Fields\NumberField;
use Lnk7\Genie\Fields\PostObjectField;
use Lnk7\Genie\Fields\RepeaterField;
use Lnk7\Genie\Fields\SelectField;
use Lnk7\Genie\Fields\TabField;
use Lnk7\Genie\Fields\TextField;
use Lnk7\Genie\Fields\TrueFalseField;
use Lnk7\Genie\Fields\UrlField;
use Lnk7\Genie\Utilities\CreateDate;
use Lnk7\Genie\Utilities\CreateSchema;
use Lnk7\Genie\Utilities\Where;
use Lnk7\Genie\WordPressObjects\Section;

/**
 * Class Settings
 *
 * Intranet Settings.
 *
 * @package Genie
 */
class Settings {

	private static $dateTimeFormat = 'jS F Y@ g:i a';



	/**
	 * Class Setup
	 */
	public static function Setup() {

		add_action( 'init', static::class . '::init' );
		add_action( 'acf/save_post', static::class . '::acf_save_post', 20 );

	}



	/**
	 * Actions to take when the Global Settings page is saved
	 *
	 */
	public static function acf_save_post() {

		$screen = get_current_screen();
		if ( strpos( $screen->id, 'theme-general-settings' ) == true ) {

			if ( static::get( 'sync_with_vera' ) ) {
				VeraSync::sync_with_vera();
				static::set( 'sync_with_vera', 0 );
			}
			if ( static::get( 'rebuild_index' ) ) {
				Search::rebuild_index();
				static::set( 'rebuild_index', 0 );
			}
		}
	}



	/**
	 * Get an options
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public static function get( $name ) {

		return get_field( $name, 'option' );

	}



	/**
	 * Convert this to an indexed array
	 */
	public static function getEventTypes() {

		$eventTypes  = static::get( 'event_types' );
		$returnArray = [];
		foreach ( $eventTypes as $eventType ) {
			$returnArray[ $eventType['code'] ] = $eventType;
		}

		return $returnArray;

	}



	public static function init() {

		//Options Page
		acf_add_options_page(
			[
				'page_title' => 'Intranet Settings',
				'menu_title' => 'Intranet Settings',
				'menu_slug'  => 'theme-general-settings',
				'capability' => 'update_core',
				'redirect'   => false,
			]
		);

		$contentFields = [
			TabField::Called( 'Content Types' ),
		];

		foreach ( Section::getAllContentTypes() as $contentType => $class ) {
			$contentFields[] = MessageField::Called( $contentType . '._text' )->label( '' )->message( "<h3>{$class::getNavmenu()}</h3>" )
				->wrapperWidth( 80 );
			$contentFields[] = NumberField::Called( $contentType . '_sequence' )
				->wrapperWidth( 20 );
		}

		CreateSchema::Called( 'Global Options' )
			->withFields( [
				TabField::Called( 'General Settings' ),
				SelectField::Called( 'timezone' )
					->label( 'Server Timezone' )
					->choices( array_combine( DateTimeZone::listIdentifiers(), DateTimeZone::listIdentifiers() ) )
					->returnFormat( 'label' )
					->default( 'Europe/London' ),
				PostObjectField::Called( 'help_employee' )->postObject( 'employee' )->wrapperWidth( 50 ),
				EmailField::Called( 'help_email' )->wrapperWidth( 50 )->instructions( 'If no employee is selected, this email will be used' ),
				UrlField::Called('email_server')->required(true),

				TabField::Called( 'Maintenance Mode' ),
				TrueFalseField::Called( 'maintenance_mode' )
					->default( false )
					->message( 'Turning this on will put the site in maintenance mode and display an appropriate message to users' )
					->wrapperWidth( 50 ),
				TextField::Called( 'maintenance_mode_started' )
					->readOnly( true )
					->wrapperWidth( 50 ),

				TabField::Called( 'APIs' ),
				MessageField::Called( 'vera_api' )->instructions( '<h3>Vera API</h3>' ),
				TextField::Called( 'vera_api_key' )
					->required( true ),
				TextField::Called( 'vera_api_url' )
					->required( true ),

				MessageField::Called( 'twitter_api' )->instructions( '<h3>Twitter API</h3>' ),
				TextField::Called( 'twitter_oauth_access_token' )
					->required( true ),
				TextField::Called( 'twitter_oauth_access_token_secret' )
					->required( true ),
				TextField::Called( 'twitter_consumer_key' )
					->required( true ),
				TextField::Called( 'twitter_consumer_secret' )
					->required( true ),

				MessageField::Called( 'google_api' )->instructions( '<h3>Google API</h3>' ),
				TextField::Called( 'google_maps_api' )
					->required( true ),

				TabField::Called( 'Event Types' ),
				RepeaterField::Called( 'event_types' )->withFields( [
					TextField::Called( 'code' )->required( true )->wrapperWidth( 10 ),
					TextField::Called( 'name' )->required( true )->wrapperWidth( 50 ),
				] )->min( 1 ),

				TabField::Called( 'Background Jobs' ),
				MessageField::Called( 'usage_instructions' )->message( 'These jobs usually run run daily, choose which ones to run now, and click save' ),

				TrueFalseField::Called( 'sync_with_vera' )
					->label( 'Sync With vera' )
					->message( 'Initiate the Sync with Vera Routine' )
					->wrapperWidth( 50 ),
				MessageField::Called( 'sync_with_vera_last_run' )
					->message( CreateDate::FromTimestamp( Options::get( 'sync_with_vera_last_run' ) )->adjustToServerTime()->format( static::$dateTimeFormat ) )
					->Label( 'Last Run' )
					->wrapperWidth( 25 ),
				MessageField::Called( 'sync_with_vera_next_run' )
					->message( CreateDate::FromTimestamp( wp_next_scheduled( 'sync_with_vera' ) )->adjustToServerTime()->format( static::$dateTimeFormat ) )
					->Label( 'Next Scheduled' )
					->wrapperWidth( 25 ),

				TrueFalseField::Called( 'rebuild_index' )
					->label( 'Rebuild Index' )
					->wrapperWidth( 50 )
					->message( 'Rebuild the search index' ),
				MessageField::Called( 'sync_index_last_run' )
					->message( CreateDate::FromTimestamp( Options::get( 'sync_index_last_run' ) )->adjustToServerTime()->format( static::$dateTimeFormat ) )
					->Label( 'Last Run' )
					->wrapperWidth( 25 ),

				MessageField::Called( 'sync_index_next_run' )
					->message( CreateDate::FromTimestamp( wp_next_scheduled( 'rebuild_index' ) )->adjustToServerTime()->format( static::$dateTimeFormat ) )
					->Label( 'Next Scheduled' )
					->wrapperWidth( 25 ),

			] )
			->withFields( $contentFields )
			->shown( Where::field( 'options_page' )->equals( 'theme-general-settings' ) )
			->instructionPlacement('field')
			->register();

	}



	public static function set( $name, $var ) {
		update_field( $name, $var, 'option' );
	}
}