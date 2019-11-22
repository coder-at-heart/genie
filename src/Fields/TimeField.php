<?php

namespace Lnk7\Genie\Fields;

class TimeField extends DateField {

    protected $type = 'time_picker';

    protected $metaQuery = 'TIME';



    protected function setDefaults() {
        parent::setDefaults();
        $this->displayFormat( 'g:i a' );
        $this->returnFormat( 'H:i:s' );
    }

}