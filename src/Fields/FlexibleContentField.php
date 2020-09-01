<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class FlexibleContentField extends Field
{

    protected $type = 'flexible_content';



    /**
     * Sets a label for the add Button
     *
     * @param $label
     *
     * @return $this
     */
    public function buttonLabel($label)
    {

        return $this->set('button_label', $label);

    }



    /**
     * Specify the maximum posts allowed to be selected. Defaults to 0
     *
     * @param int $number
     *
     * @return $this
     */
    public function max(int $number)
    {

        return $this->set('max', $number);

    }



    /**
     * Specify the minimum posts required to be selected. Defaults to 0
     *
     * @param int $number
     *
     * @return $this
     */
    public function min(int $number)
    {

        return $this->set('min', $number);

    }



    /**
     * Add Fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function withLayouts(array $fields)
    {

        return $this->set('layouts', $fields);

    }

}