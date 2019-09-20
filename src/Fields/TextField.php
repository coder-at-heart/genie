<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class TextField extends Field {

	protected $type = 'text';



	protected function setDefaults() {

		parent::setDefaults();
		$this->searchable( true );
	}



	/**
	 * Sets the placeholder for the field.
	 *
	 * @param $placeholder
	 *
	 * @return $this
	 */
	public function placeholder( $placeholder ) {
		$this->set( 'placeholder', $placeholder );

		return $this;

	}

	/**
	 * Set prepend text
	 *
	 * @param $prepend
	 *
	 * @return $this
	 */
	public function prepend( $prepend ) {
		$this->set( 'prepend', $prepend );

		return $this;

	}

	/**
	 * Set append Text
	 *
	 * @param $append
	 *
	 * @return $this
	 */
	public function append( $append ) {
		$this->set( 'append', $append );

		return $this;

	}

	/**
	 * Is this field Read Only ?
	 *
	 * @param bool $readOnly
	 *
	 * @return $this
	 */

	public function readOnly( bool $readOnly ) {
		$this->set( 'readonly', $readOnly );

		return $this;

	}

	/**
	 * Should this field be disabled?
	 *
	 * @param bool $disabled
	 *
	 * @return $this
	 */
	public function disabled( bool $disabled ) {
		$this->set( 'disabled', $disabled );

		return $this;

	}

	/**
	 * The maximum length accpted
	 *
	 * @param int $maxLength
	 *
	 * @return $this
	 */
	public function maxLength( int $maxLength ) {
		$this->set( 'maxlength', $maxLength );

		return $this;

	}

}
