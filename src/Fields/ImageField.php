<?php

namespace Lnk7\Genie\Fields;

class ImageField extends FileField {

    protected $type = 'image';



    /**
     * Specify the minimum width of the image
     *
     * @param int $minWidth
     *
     * @return $this
     */
    public function minWidth( int $minWidth ) {
        return $this->set( 'min_width', $minWidth );

    }



    /**
     * Specify the minimum height of the image
     *
     * @param int $minHeight
     *
     * @return $this
     */
    public function minHeight( int $minHeight ) {
        return $this->set( 'min_height', $minHeight );

    }



    /**
     * Specify the maximum width of the image
     *
     * @param int $maxWidth
     *
     * @return $this
     */
    public function maxWidth( int $maxWidth ) {
        return $this->set( 'max_width', $maxWidth );

    }



    /**
     * Specify the maximum height of the image
     *
     * @param int $maxHeight
     *
     * @return $this
     */
    public function maxHeight( int $maxHeight ) {
        return $this->set( 'max_height', $maxHeight );

    }

}