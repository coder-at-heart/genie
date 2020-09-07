<?php

namespace Lnk7\Genie\Fields;

class DateTimeField extends DateField
{


    protected function setDefaults()
    {
        parent::setDefaults();

        $this->type('date_time_picker');
        $this->metaQuery('DATETIME');
        $this->displayFormat('d/m/Y g:i a');
        $this->returnFormat('Y-m-d H:i:s');
    }

}