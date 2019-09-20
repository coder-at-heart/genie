<?php

namespace Lnk7\Genie\Fields;

class RepeaterField extends GroupField {

	protected $type = 'repeater';



	/**
	 * Sets a label for the add Button
	 *
	 * @param $label
	 *
	 * @return $this
	 */
	public function buttonLabel( $label ) {

		$this->set( 'button_label', $label );

		return $this;
	}



	public function collapsed( $collapsed ) {

		$this->set( 'collapsed', $collapsed );

		return $this;
	}



	/**
	 * Specify the maximum posts allowed to be selected. Defaults to 0
	 *
	 * @param int $number
	 *
	 * @return $this
	 */
	public function max( int $number ) {

		$this->set( 'max', $number );

		return $this;
	}



	/**
	 * Specify the minimum posts required to be selected. Defaults to 0
	 *
	 * @param int $number
	 *
	 * @return $this
	 */
	public function min( int $number ) {

		$this->set( 'min', $number );

		return $this;
	}



	protected function setDefaults() {

		parent::setDefaults();
		$this->layout( 'table' );
	}

}