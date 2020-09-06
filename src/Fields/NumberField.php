<?php

namespace Lnk7\Genie\Fields;

class NumberField extends TextField
{


    /**
     * Minimum value for this field
     *
     * @param $min
     *
     * @return $this
     */
    public function min(int $min)
    {
        return $this->set('min', $min);
    }


    /**
     * Maximum value for this field
     *
     * @param int $max
     *
     * @return $this
     */
    public function max(int $max)
    {
        return $this->set('max', $max);
    }


    /**
     * Increment Step
     *
     * @param int $step
     *
     * @return $this
     */
    public function step(int $step)
    {
        return $this->set('step', $step);
    }


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('number');
        $this->metaQuery('NUMERIC');
    }

}