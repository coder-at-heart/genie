<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class GoogleMapField extends Field {

	protected $type = 'google_map';


	public function centerLatitude( $latitude ) {
		$this->set( 'center_lat', $latitude );

		return $this;
	}

	public function centerLongitude( $longitude ) {
		$this->set( 'center_lng', $longitude );

		return $this;

	}

	public function zoom( $zoom ) {
		$this->set( 'zoom', $zoom );

		return $this;

	}

}
