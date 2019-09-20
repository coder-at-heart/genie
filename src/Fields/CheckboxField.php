<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class CheckboxField extends Field {

	protected $type = 'checkbox';

	/**
	 * Specify choices for the checkbox
	 *
	 * @param array $choices
	 *
	 * @return $this
	 */
	public function choices( array $choices ) {
		$this->set( 'choices', $choices );

		return $this;
	}

	/**
	 *Text shown along side the checkbox
	 *
	 * @param string $message
	 *
	 * @return $this
	 */
	public function message( string $message ) {
		$this->set( 'message', $message );

		return $this;
	}

	/**
	 * Specify if there should be an "taggle all" option
	 *
	 * @param bool $toggle
	 *
	 * @return $this
	 */
	public function toggle( bool $toggle ) {
		$this->set( 'toggle', $toggle );

		return $this;
	}

	protected function setDefaults() {
		parent::setDefaults();
		$this->layout( 'vertical' );
		$this->returnFormat( 'array' );
	}

	/**
	 * Specify the layout of the checkbox inputs. Defaults to 'vertical'. Choices of 'vertical' or 'horizontal
	 *
	 * @param string $layout vertical|horizontal
	 *
	 * @return $this
	 */
	public function layout( string $layout ) {
		$this->set( 'layout', $layout );

		return $this;
	}

	/**
	 * Specify the return format
	 *
	 * @param string $returnFormat array|value TODO: Check
	 *
	 * @return $this
	 */

	public function returnFormat( string $returnFormat ) {
		$this->set( 'return_format', $returnFormat );

		return $this;
	}

}