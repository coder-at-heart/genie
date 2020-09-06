<?php

namespace Lnk7\Genie\Fields;

class PasswordField extends TextField
{


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('password');
    }


}
