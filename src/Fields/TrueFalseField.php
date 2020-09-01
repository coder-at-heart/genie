<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;
use Lnk7\Genie\Fields\Traits\message;

class TrueFalseField extends Field
{

    protected $type = 'true_false';

    protected $metaQuery = 'NUMERIC';



    /**
     *Text shown along side the field
     *
     * @param string $message
     *
     * @return $this
     */
    public function message(string $message)
    {
        return $this->set('message', $message);

    }



    protected function setDefaults()
    {
        parent::setDefaults();
        $this->ui(true);
        $this->onText('Yes');
        $this->offText('No');
    }



    /**
     * Show an use an UI switch?
     *
     * @param $ui
     *
     * @return $this
     */
    public function ui(bool $ui)
    {
        return $this->set('ui', $ui);

    }



    /**
     * Text to show on the switch in the on position
     *
     * @param string $text
     *
     * @return $this
     */
    public function onText(string $text)
    {
        return $this->set('ui_on_text', $text);

    }



    /**
     * text to show on the switch in the off position
     *
     * @param string $text
     *
     * @return $this
     */
    public function offText(string $text)
    {
        return $this->set('ui_off_text', $text);

    }

}