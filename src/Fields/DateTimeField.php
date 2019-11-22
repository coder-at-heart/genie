<?php

namespace Lnk7\Genie\Fields;

class DateTimeField extends DateField {

    protected $type = 'date_time_picker';

    protected $metaQuery = 'DATETIME';



    protected function setDefaults() {
        parent::setDefaults();
        $this->displayFormat( 'd/m/Y g:i a' );
        $this->returnFormat( 'Y-m-d H:i:s' );
    }

}