<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class TaxonomyField extends Field {

	protected $type = 'taxonomy';

	protected function setDefaults() {
		parent::setDefaults();
		$this->fieldType( 'select' );
	}

	/**
	 * Specify the taxonomy to select terms from. Defaults to 'category'
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
	 * Specify the appearance of the taxonomy field. Defaults to 'checkbox'.
	 * Choices of 'checkbox' (Checkbox inputs), 'multi_select' (Select field - multiple),
	 * 'radio' (Radio inputs) or 'select' (Select field)
	 *
	 * @param $type
	 *
	 * @return $this
	 */
	public function fieldType( string $type ) {
		$this->set( 'field_type', $type );

		return $this;

	}

	/**
	 * Allow no value to be selected
	 *
	 * @param $allowNull
	 *
	 * @return $this
	 */
	public function allowNull( bool $allowNull ) {
		$this->set( 'allow_null', $allowNull );

		return $this;
	}


	/**
	 * Specify the type of value returned by get_field(). Defaults to 'id'.
	 * Choices of 'object' (Term object) or 'id' (Term ID)
	 *
	 * @param string $returnFormat object|id
	 *
	 * @return $this
	 */

	public function returnFormat( string $returnFormat ) {
		$this->set( 'return_format', $returnFormat );

		return $this;
	}

	/**s
	 * Load terms from the post?
	 *
	 * @param bool $loadTerms
	 *
	 * @return $this
	 */
	public function loadTerms( bool $loadTerms ) {
		$this->set( 'load_terms', $loadTerms );

		return $this;
	}

	/**
	 * Save terms to the post?
	 *
	 * @param bool $saveTerms
	 *
	 * @return $this
	 */
	public function saveTerms( bool $saveTerms ) {
		$this->set( 'save_terms', $saveTerms );

		return $this;
	}

	/**
	 * Specify if terms added should be added to Wordpress
	 *
	 * @param bool $addTerms
	 *
	 * @return $this
	 */
	public function addTerms( bool $addTerms ) {
		$this->set( 'add_term', $addTerms );

		return $this;
	}

}