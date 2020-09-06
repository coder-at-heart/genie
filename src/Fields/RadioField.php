<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class RadioField extends Field
{


    /**
     * Choices for this radio set
     *
     * @param array $choices key=> value paid
     *
     * @return $this
     */
    public function choices(array $choices)
    {
        return $this->set('choices', $choices);
    }


    /**
     * Showuld other options be allowed?
     *
     * @param bool $otherChoices
     *
     * @return $this
     */
    public function otherChoices(bool $otherChoices)
    {
        return $this->set('other_choice', $otherChoices);
    }


    /**
     * Save other choices?
     *
     * @param $saveOtherChoice
     *
     * @return $this
     */
    public function saveOtherChoice(bool $saveOtherChoice)
    {
        return $this->set('save_other_choice', $saveOtherChoice);
    }


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('radio');
        $this->layout('vertical');
        $this->returnFormat('array');
    }


    /**
     * Specify the layout of the checkbox inputs. Defaults to 'vertical'. Choices of 'vertical' or 'horizontal'
     *
     * @param $layout
     *
     * @return $this
     */
    public function layout(string $layout)
    {
        return $this->set('layout', $layout);
    }


    /**
     * Return Format
     *
     * @param $returnFormat
     *
     * @return $this
     */
    public function returnFormat($returnFormat)
    {
        return $this->set('return_format', $returnFormat);
    }

}