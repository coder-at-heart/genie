<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class ColorPickerField extends Field
{


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('color_picker');
    }
}
