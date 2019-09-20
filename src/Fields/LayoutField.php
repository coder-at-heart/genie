<?php

namespace Lnk7\Genie\Fields;

class LayoutField extends GroupField {

	protected $type = 'layout';



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
		$this->layout( 'block' );
	}

}