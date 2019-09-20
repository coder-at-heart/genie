<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class RelationshipField extends Field {

	protected $type = 'relationship';

	protected function setDefaults() {
		parent::setDefaults();
		$this->filters(['search']);
		$this->elements(['featured_image']);
	}

	/**
	 * Specify an array of post types to filter the available choices. Defaults to ''
	 *
	 * @param array $postObject
	 *
	 * @return $this
	 */
	public function postObject( array $postObject ) {
		$this->set( 'post_type', $postObject );
		$this->returnFormat('id');

		return $this;
	}

	/**
	 * Specify an array of taxonomies to filter the available choices. Defaults to ''
	 *
	 * @param string $taxonomy
	 *
	 * @return $this
	 */
	public function taxonomy( string $taxonomy ) {
		$this->set( 'taxonomy', $taxonomy );

		return $this;
	}


	/**
	 *  Specify the available filters used to search for posts. Choices of 'search' (Search input), 'post_type' (Post type select) and 'taxonomy' (Taxonomy select)
	 *
	 * @param array $filters
	 *
	 * @return $this
	 */
	public function filters( array $filters ) {
		$this->set( 'filters', $filters );

		return $this;
	}

	/**
	 *  pecify the visual elements for each post. Choices of 'featured_image' (Featured image icon)
	 *
	 * @param array $elements
	 *
	 * @return $this
	 */
	public function elements( array $elements ) {
		$this->set( 'elements', $elements );

		return $this;
	}

	/**
	 * Specify the minimum posts required to be selected. Defaults to 0
	 *
	 * @param array $number
	 *
	 * @return $this
	 */
	public function min( array $number ) {
		$this->set( 'min', $number);

		return $this;
	}

	/**
	 * Specify the maximum posts allowed to be selected. Defaults to 0
	 *
	 * @param array $number
	 *
	 * @return $this
	 */
	public function max( array $number ) {
		$this->set( 'max', $number);

		return $this;
	}


	/**
	 * Specify the type of value returned by get_field(). Defaults to 'object'.
	 * Choices of 'object' (Post object) or 'id' (Post ID)
	 *
	 * @param string $returnFormat object|id
	 *
	 * @return $this
	 */

	public function returnFormat( string $returnFormat ) {
		$this->set( 'return_format', $returnFormat );

		return $this;
	}



}