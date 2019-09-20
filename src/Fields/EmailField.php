<?php

namespace Lnk7\Genie\Fields;

class EmailField extends TextField {

	protected $type = 'email';


	protected function setDefaults() {

		parent::setDefaults();
		$this->searchable( true );
	}

}
