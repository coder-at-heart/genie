<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class GoogleMapField extends Field {

    protected $type = 'google_map';



    public function centerLatitude( $latitude ) {
        return $this->set( 'center_lat', $latitude );

    }



    public function centerLongitude( $longitude ) {
        return $this->set( 'center_lng', $longitude );

    }



    public function zoom( $zoom ) {
        return $this->set( 'zoom', $zoom );

    }

}
