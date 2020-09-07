<?php

namespace Lnk7\Genie\Fields;

class RangeField extends NumberField
{


    protected $type = 'range';


    protected $metaQuery = 'NUMERIC';


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('range');
        $this->metaQuery('NUMERIC');
    }
}

