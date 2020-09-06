<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class GoogleMapField extends Field
{


    public function centerLatitude($latitude)
    {
        return $this->set('center_lat', $latitude);
    }


    public function centerLongitude($longitude)
    {
        return $this->set('center_lng', $longitude);
    }


    public function zoom($zoom)
    {
        return $this->set('zoom', $zoom);
    }


    /**
     * Set defaults for this field
     */
    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('google_map');
    }

}
