<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class GroupField extends Field {

	protected $type = 'group';

	protected function setDefaults() {
		parent::setDefaults();
		$this->layout( 'row' );
	}

	/**
	 * layout
	 *
	 *
	 *
	 * @param string $layout  table|block|row
	 *
	 * @return $this
	 */
	public function layout( string $layout ) {
		$this->set( 'layout', $layout );

		return $this;
	}

	/**
	 * Add Fields
	 *
	 * @param array $fields
	 *
	 * @return $this
	 */
	public function withFields( array $fields ) {

		$this->set( 'sub_fields', $fields );

		return $this;

	}

}