<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class RadioField extends Field {

	protected $type = 'radio';

	protected function setDefaults() {
		parent::setDefaults();
		$this->layout( 'vertical' );
		$this->returnFormat( 'array' );
	}

	/**
	 * Choices for this radio set
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
	 * Showuld other options be allowed?
	 *
	 * @param bool $otherChoices
	 *
	 * @return $this
	 */
	public function otherChoices( bool $otherChoices ) {
		$this->set( 'other_choice', $otherChoices );

		return $this;
	}

	/**
	 * Save other choices?
	 *
	 * @param $saveOtherChoice
	 *
	 * @return $this
	 */
	public function saveOtherChoice( bool $saveOtherChoice ) {
		$this->set( 'save_other_choice', $saveOtherChoice );

		return $this;
	}

	/**
	 * Specify the layout of the checkbox inputs. Defaults to 'vertical'. Choices of 'vertical' or 'horizontal'
	 *
	 * @param $layout
	 *
	 * @return $this
	 */
	public function layout( string $layout ) {
		$this->set( 'layout', $layout );

		return $this;
	}

	/**
	 * Return Format
	 *
	 * @param $returnFormat
	 *
	 * @return $this
	 */
	public function returnFormat( $returnFormat ) {
		$this->set( 'return_format', $returnFormat );

		return $this;
	}

}