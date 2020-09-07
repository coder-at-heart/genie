<?php

namespace Lnk7\Genie\Fields;

class UrlField extends TextField
{


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('url');
    }

}
