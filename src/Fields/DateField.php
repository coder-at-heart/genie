<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class DateField extends Field
{

    protected $type = 'date_picker';

    protected $metaQuery = 'DATE';



    /**
     * Set the 1st day of the week
     *
     * @param int $day
     *
     * @return $this
     */
    public function firstDay(int $day)
    {
        return $this->set('first_day', $day);

    }



    protected function setDefaults()
    {
        parent::setDefaults();
        $this->displayFormat('d/m/Y');
        $this->returnFormat('Y-m-d');
    }



    /**
     * Sets the display Format (PHP Date format)
     *
     * @param string $format
     *
     * @return $this
     */
    public function displayFormat(string $format)
    {
        return $this->set('display_format', $format);

    }



    /**
     * Specify the return format (PHP date format)
     *
     * @param string $format
     *
     * @return $this
     */

    public function returnFormat(string $format)
    {
        return $this->set('return_format', $format);

    }

}