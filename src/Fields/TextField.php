<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class TextField extends Field
{


    /**
     * Sets the placeholder for the field.
     *
     * @param $placeholder
     *
     * @return $this
     */
    public function placeholder($placeholder)
    {
        return $this->set('placeholder', $placeholder);
    }


    /**
     * Set prepend text
     *
     * @param $prepend
     *
     * @return $this
     */
    public function prepend($prepend)
    {
        return $this->set('prepend', $prepend);
    }


    /**
     * Set append Text
     *
     * @param $append
     *
     * @return $this
     */
    public function append($append)
    {
        return $this->set('append', $append);
    }


    /**
     * Is this field Read Only ?
     *
     * @param bool $readOnly
     *
     * @return $this
     */

    public function readOnly(bool $readOnly)
    {
        return $this->set('readonly', $readOnly);
    }


    /**
     * Should this field be disabled?
     *
     * @param bool $disabled
     *
     * @return $this
     */
    public function disabled(bool $disabled)
    {
        return $this->set('disabled', $disabled);
    }


    /**
     * The maximum length accpted
     *
     * @param int $maxLength
     *
     * @return $this
     */
    public function maxLength(int $maxLength)
    {
        return $this->set('maxlength', $maxLength);
    }


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('text');
    }

}
