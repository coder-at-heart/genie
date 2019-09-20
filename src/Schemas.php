<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Fields\EmailField;
use Lnk7\Genie\Fields\FileField;
use Lnk7\Genie\Fields\FlexibleContentField;
use Lnk7\Genie\Fields\IconField;
use Lnk7\Genie\Fields\LayoutField;
use Lnk7\Genie\Fields\MessageField;
use Lnk7\Genie\Fields\NumberField;
use Lnk7\Genie\Fields\PostObjectField;
use Lnk7\Genie\Fields\RadioField;
use Lnk7\Genie\Fields\RepeaterField;
use Lnk7\Genie\Fields\SelectField;
use Lnk7\Genie\Fields\TabField;
use Lnk7\Genie\Fields\TextAreaField;
use Lnk7\Genie\Fields\TextField;
use Lnk7\Genie\Fields\TrueFalseField;
use Lnk7\Genie\Fields\WysiwygField;
use Lnk7\Genie\Utilities\CreateSchema;
use Lnk7\Genie\Utilities\When;
use Lnk7\Genie\Utilities\Where;
use Lnk7\Genie\WordPressObjects\Section;

/**
 * Class Schemas
 *
 * Additional ACF schemas
 *
 * @package Genie
 */
class Schemas {

	public static function Setup() {

		add_action( 'init', static::class . '::init' );
	}



	public static function ContactsTab() {

		return [
			TabField::Called( 'Contact Box' ),
			TrueFalseField::Called( 'contact_box' )
				->message( 'Show a contacts at the bottom?' ),
			WysiwygField::Called( 'contact_box_text' )
				->shown( When::field( 'contact_box' )->equals( 1 ) )
				->wrapperWidth( 50 ),
			RepeaterField::Called( 'contact_box_contacts' )
				->shown( When::field( 'contact_box' )->equals( 1 ) )
				->wrapperWidth( 50 )
				->min( 1 )
				->buttonLabel( 'Add Contact' )
				->withFields( [
					PostObjectField::Called( 'employee_id' )
						->label( 'Contact' )
						->postObject( 'employee' ),

				] ),
		];
	}



	public static function ExcerptTab() {

		return [
			TabField::Called( 'excerpt_tab' )->label( 'Excerpt' ),

			TextAreaField::Called( 'excerpt' )
				->instructions( 'If you don not provide one, an excerpt will be generated automatically' )
				->rows( 3 ),
		];
	}



	public static function formTab() {

		return [


		];

	}



	public static function init() {

		self::createMenuSchema();
		self::createContentControlSchema();
		self::createPageWithFAQs();
		self::homePage();
		self::heroHeader();
	}



	private static function createContentControlSchema() {

		$contentTypes = Registry::get( 'ContentTypes' );

		$where = new Where();
		foreach ( $contentTypes as $contentType => $data ) {
			$where->or( 'post_type' )->equals( $contentType );
		}

		$sections     = Section::get( [ 'order' => 'ASC', 'orderby' => 'post_title' ] );
		$sectionArray = [];

		foreach ( $sections as $section ) {
			$sectionArray[ $section->ID ] = $section->post_title;
		}

		// Content Control
		CreateSchema::Called( 'Content Control' )
			->withFields( [
				TabField::Called( 'Section' ),
				RadioField::Called( 'section_id' )
					->label( 'Section' )
					->choices( $sectionArray )
					->returnFormat( 'value' ),

				TabField::Called( 'Offices' ),
				SelectField::Called( 'office_type' )
					->label( 'Choose the offices that have access' )
					->choices( [
						'all'    => 'All',
						'only'   => 'Only for these offices...',
						'except' => 'All offices except...',
					] )
					->returnFormat( 'value' )
					->default( 'all' ),
				PostObjectField::Called( 'office_differences' )
					->label( 'Offices' )
					->postObject( [ 'office' ] )
					->multiple( true )
					->shown(
						When::field( 'office_type' )->notEquals( 'all' )
					),
				TabField::Called( 'Dept.' ),
				TabField::Called( 'Dept.' ),
				SelectField::Called( 'department_type' )
					->choices( [
						'all'    => 'All',
						'only'   => 'Only for these departments...',
						'except' => 'All Departments except...',
					] )
					->label( 'Choose the Departments that have access' )
					->returnFormat( 'value' )
					->default( 'all' ),

				PostObjectField::Called( 'department_differences' )
					->label( 'Departments' )
					->postObject( [ 'department' ] )
					->multiple( true )
					->shown(
						When::field( 'department_type' )->notEquals( 'all' )
					),

			] )
			->shown( $where )
			->position( 'side' )
			->menuOrder( 10 )
			->register();
	}



	private static function createMenuSchema() {

		//	menu Items
		CreateSchema::Called( 'Menu' )
			->withFields( [
				IconField::Called( 'icon' ),
				TrueFalseField::Called( 'section_start' )->message( 'Start a new Section before this menu Item?' ),
				TextField::Called( 'section_name' )->shown( When::field( 'section_start' )->equals( 1 ) ),

			] )
			->shown( Where::field( 'nav_menu_item' )->equals( 'all' ) )
			->register();
	}



	private static function createPageWithFAQs() {

		// Home Page
		CreateSchema::Called( 'Page with FAQs Template' )
			->withFields( [
				RepeaterField::Called( 'faqs' )
					->withFields( [
						TextField::Called( 'question' )->required( true ),
						WysiwygField::Called( 'answer' )
							->mediaUpload( false )->toolbar( 'full' ),
					] )
					->layout( 'block' ),
			] )
			->shown( Where::field( 'post_template' )->equals( 'templates/page-with-faqs.php' ) )
			->hideOnScreen( [ 'the_content' ] )
			->register();

	}



	private static function heroHeader() {

		// Home Page
		CreateSchema::Called( 'Hero Header' )
			->withFields( [
				TrueFalseField::Called( 'hero_show' )->label( 'Hero Header On?' )
					->default( true ),
				TextField::Called( 'hero_subtitle' )
					->shown( When::field( 'hero_show' )->equals( 1 ) ),
				TrueFalseField::Called( 'hero_use_featured_image' )->label( 'Use featured Image?' )
					->default( true )->shown( When::field( 'hero_show' )->equals( 1 ) ),
			] )
			->shown( Where::field( 'post_type' )->equals( 'page' )
				->or( 'post_type' )->equals( 'update' )
				->or( 'post_type' )->equals( 'insurance-type' )
			)
			->position( 'side' )
			->register();

	}



	private static function homePage() {

		// Home Page
		CreateSchema::Called( 'Home Page' )
			->withFields( [
				TabField::Called( 'Settings' ),
				NumberField::Called( 'updates' )
					->instructions( 'The number of Updates to show' )
					->min( 1 )
					->max( 5 )
					->wrapperWidth( 50 ),
				NumberField::Called( 'new_joiners' )
					->instructions( 'The number of new Joiners to show' )
					->min( 1 )
					->max( 10 )
					->wrapperWidth( 50 ),
			] )
			->shown( Where::field( 'post_template' )->equals( 'templates/home.php' ) )
			->hideOnScreen( [ 'the_content' ] )
			->register();

	}
}