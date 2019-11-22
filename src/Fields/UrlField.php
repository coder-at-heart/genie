<?php

namespace Lnk7\Genie\Fields;

class UrlField extends TextField {

    protected $type = 'url';



    protected function setDefaults() {

        parent::setDefaults();
        $this->searchable( true );
    }

}
