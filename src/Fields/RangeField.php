<?php

namespace Lnk7\Genie\Fields;

class RangeField extends NumberField
{

    protected $type = 'range';

    protected $metaQuery = 'NUMERIC';

}