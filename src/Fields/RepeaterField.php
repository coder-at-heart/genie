<?php

namespace Lnk7\Genie\Fields;

class RepeaterField extends GroupField {

    protected $type = 'repeater';



    /**
     * Sets a label for the add Button
     *
     * @param $label
     *
     * @return $this
     */
    public function buttonLabel( $label ) {

        return $this->set( 'button_label', $label );

    }



    public function collapsed( $collapsed ) {

        return $this->set( 'collapsed', $collapsed );

    }



    /**
     * Specify the maximum posts allowed to be selected. Defaults to 0
     *
     * @param int $number
     *
     * @return $this
     */
    public function max( int $number ) {

        return $this->set( 'max', $number );

    }



    /**
     * Specify the minimum posts required to be selected. Defaults to 0
     *
     * @param int $number
     *
     * @return $this
     */
    public function min( int $number ) {

        return $this->set( 'min', $number );

    }



    protected function setDefaults() {

        parent::setDefaults();
        $this->layout( 'table' );
    }

}