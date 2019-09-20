<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class TabField extends Field {

	protected $type = 'tab';

	protected function setDefaults() {
		parent::setDefaults();
		$this->displayOnly(true);
		$this->placement( 'top' );
	}

	/**
	 * Tab Placement
	 *
	 * @param string $placement
	 *
	 * @return $this
	 */
	public function placement( string $placement ) {
		$this->set( 'placement', $placement );

		return $this;
	}

	/**
	 * Is this tab an Endpoint?
	 *
	 * @param bool $endpoint
	 *
	 * @return $this
	 */
	public function endpoint( bool $endpoint ) {
		$this->set( 'endpoint', $endpoint );

		return $this;

	}

}