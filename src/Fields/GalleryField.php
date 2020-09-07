<?php

namespace Lnk7\Genie\Fields;

class GalleryField extends ImageField
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


    /**
     * Set defaults for this field
     */
    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('gallery');
        $this->insert('append');
    }


    /**
     * Where to insert the image
     *
     * @param string $insert append|prepend
     *
     * @return $this
     */
    public function insert(string $insert)
    {
        return $this->set('insert', $insert);
    }

}