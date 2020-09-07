<?php

namespace Lnk7\Genie\Fields;

class TimeField extends DateField
{


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('time_picker');
        $this->metaQuery('TIME');
        $this->displayFormat('g:i a');
        $this->returnFormat('H:i:s');
    }

}