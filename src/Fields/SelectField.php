<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class SelectField extends Field {

	protected $type = 'select';

	protected function setDefaults() {
		parent::setDefaults();
		$this->allowNull( true );
		$this->ui( true );
		$this->returnFormat( 'array' );
	}

	/**
	 * Choices for this select dropdown
	 *
	 * @param array $choices key=> value paid
	 *
	 * @return $this
	 */
	public function choices( array $choices ) {
		$this->set( 'choices', $choices );

		return $this;
	}

	/**
	 * Should the values be loaded by Ajax?
	 *
	 * @param bool $ajax
	 *
	 * @return $this
	 */
	public function ajax(bool $ajax ) {
		$this->set( 'ajax', $ajax );

		return $this;
	}

	/**
	 * Allow no value to be selected
	 *
	 * @param $allowNull
	 *
	 * @return $this
	 */
	public function allowNull( $allowNull ) {
		$this->set( 'allow_null', $allowNull );

		return $this;
	}

	/**
	 * use an improved UI ?
	 *
	 * @param $ui
	 *
	 * @return $this
	 */
	public function ui(bool $ui ) {
		$this->set( 'ui', $ui );

		return $this;
	}



	/**
	 * select multiple values?
	 *
	 * @param bool $multiple
	 *
	 * @return $this
	 */
	public function multiple(bool $multiple) {
		$this->set( 'multiple', $multiple );

		return $this;

	}

	/**
	 * Return Format
	 *
	 * @param $returnValue
	 *
	 * @return $this
	 */
	public function returnFormat( string $returnValue ) {
		$this->set( 'return_format', $returnValue );

		return $this;
	}

}