<?php

namespace Lnk7\Genie\Fields;

class LayoutField extends GroupField
{


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


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('layout');
        $this->layout('block');
    }

}