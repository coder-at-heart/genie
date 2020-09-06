<?php

namespace Lnk7\Genie\Fields;

class EmailField extends TextField
{


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('email');
    }

}
