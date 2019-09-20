<?php

namespace Lnk7\Genie\Fields;

class NumberField extends TextField {

	protected $type = 'number';

	protected $metaQuery = 'NUMERIC';

	/**
	 * Minimum value for this field
	 *
	 * @param $min
	 *
	 * @return $this
	 */
	public function min( int $min ) {
		$this->set( 'min', $min );

		return $this;

	}

	/**
	 * Maximum value for this field
	 *
	 * @param int $max
	 *
	 * @return $this
	 */
	public function max( int $max ) {
		$this->set( 'max', $max );

		return $this;

	}

	/**
	 * Increment Step
	 *
	 * @param int $step
	 *
	 * @return $this
	 */
	public function step( int $step ) {
		$this->set( 'step', $step );

		return $this;

	}

}