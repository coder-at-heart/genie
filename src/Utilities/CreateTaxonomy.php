<?php

namespace Lnk7\Genie\Utilities;

/**
 * Class CreateTaxonomy
 *
 * Wrapper around the register_taxonomy function
 *
 * @package Genie\Utilities
 */
class CreateTaxonomy {

	/**
	 * see https://codex.wordpress.org/Function_Reference/register_taxonomy
	 *
	 * @var array
	 */
	protected $definition = [
		'label'                 => '',
		'labels'                => [
			'name'                       => '',
			'singular_name'              => '',
			'menu_name'                  => '',
			'all_items'                  => '',
			'edit_item'                  => '',
			'view_item'                  => '',
			'update_item'                => '',
			'add_new_item'               => '',
			'new_item_name'              => '',
			'parent_item'                => '',
			'parent_item_colon'          => '',
			'search_items'               => '',
			'popular_items'              => '',
			'separate_items_with_commas' => '',
			'add_or_remove_items'        => '',
			'choose_from_most_used'      => '',
			'not_found'                  => '',
			'back_to_items'              => '',
		],
		'public'                => false,
		'publicly_queryable'    => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => false,
		'show_in_rest'          => false,
		'show_tagcloud'         => false,
		'meta_box_cb'           => null,
		'show_admin_column'     => true,
		'description'           => '',
		'hierarchical'          => true,
		'update_count_callback' => '',
		'query_var'             => '',
		'rewrite'               => true,
		'sort'                  => '',

	];

	/**
	 * Taxonomy slug
	 *
	 * @var string
	 */
	protected $taxonomy;



	/**
	 * static constructor
	 *
	 * @param $name
	 *
	 * @return CreateTaxonomy
	 */
	public static function Called( $name ) {

		return new static( $name );
	}



	/**
	 * CreateCustomPostType constructor.
	 *
	 * @param string $name Name
	 */
	public function __construct( $name ) {

		$string = ConvertString::From( $name );

		$this->taxonomy = $string->toSingular()->toSnakeCase();

		$singular = (string) $string->toTitleCase()->toSingular();
		$plural   = (string) $string->toPlural();

		$this->setLabels( [

			'name'                       => $plural,
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			'all_items'                  => 'All ' . $plural,
			'edit_item'                  => 'Edit ' . $singular,
			'view_item'                  => 'View ' . $singular,
			'update_item'                => 'Update ' . $singular,
			'add_new_item'               => 'Add New ' . $singular,
			'new_item_name'              => 'New ' . $singular . ' Name',
			'parent_item'                => 'Parent ' . $singular,
			'parent_item_colon'          => 'Parent ' . $singular . ':',
			'search_items'               => 'Search ' . $plural,
			'popular_items'              => 'Popular ' . $plural,
			'separate_items_with_commas' => 'Separate ' . $plural . ' with commas',
			'add_or_remove_items'        => 'Add or remove ' . $plural,
			'choose_from_most_used'      => 'Choose from the most used ' . $plural,
			'not_found'                  => 'No ' . $plural . ' found',
			'back_to_items'              => '← Back to ' . $plural,
		] );

		$this->set( 'label', $plural );
		$this->set( 'description', $name );
	}



	/**
	 * Sets this taxonomy as being hidden
	 *
	 * @return $this
	 */
	function hidden() {

		$this->set( 'show_ui', false );
		$this->set( 'show_in_nav_menus', false );

		return $this;
	}



	/**
	 * Register the Taxonomy
	 */
	function register() {

		register_taxonomy( $this->taxonomy, null, $this->definition );

	}



	function set( $attribute, $value ) {

		$this->definition[ $attribute ] = $value;

		return $this;
	}



	function setLabel( $label, $name ) {

		$this->definition['labels'][ $label ] = $name;

		return $this;
	}



	function setLabels( array $labels ) {

		foreach ( $labels as $label => $value ) {
			$this->setLabel( $label, $value );
		}

		return $this;
	}

}
