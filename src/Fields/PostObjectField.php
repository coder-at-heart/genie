<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class PostObjectField extends Field {

	protected $type = 'post_object';

	protected $metaQuery = 'NUMERIC';

	protected function setDefaults() {
		parent::setDefaults();
		$this->returnFormat( 'id' );
		$this->allowNull( false );
		$this->multiple( false );
	}

	/**
	 * Specify an array of post types to filter the available choices. Defaults to ''
	 *
	 * @param array|string $postObject
	 *
	 * @return $this
	 */
	public function postObject( $postObject ) {
		if ( ! is_array( $postObject ) ) {
			$postObject = [ $postObject ];
		}
		$this->set( 'post_type', $postObject );

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
	 * Specify the type of value returned by get_field(). Defaults to 'object'. Choices of 'object' (Post object) or 'id' (Post ID)
	 *
	 * @param string $returnFormat object|id
	 *
	 * @return $this
	 */
	public function returnFormat( string $returnFormat ) {
		$this->set( 'return_format', $returnFormat );

		return $this;
	}

	/**
	 * Specify if null can be accepted as a value.
	 *
	 * @param bool $allowNull
	 *
	 * @return $this
	 */
	public function allowNull( bool $allowNull ) {
		$this->set( 'allow_null', $allowNull );

		return $this;
	}

	/**
	 * Allow multiple values to be selected
	 *
	 * @param bool $multiple
	 *
	 * @return $this
	 */
	public function multiple( bool $multiple ) {
		$this->set( 'multiple', $multiple );

		return $this;
	}

}