<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class FlexibleContentField extends Field {

    protected $type = 'flexible_content';



    /**
     * Sets a label for the add Button
     *
     * @param $label
     *
     * @return $this
     */
    public function buttonLabel( $label ) {

        $this->set( 'button_label', $label );

        return $this;
    }



    /**
     * Specify the maximum posts allowed to be selected. Defaults to 0
     *
     * @param int $number
     *
     * @return $this
     */
    public function max( int $number ) {

        $this->set( 'max', $number );

        return $this;
    }



    /**
     * Specify the minimum posts required to be selected. Defaults to 0
     *
     * @param int $number
     *
     * @return $this
     */
    public function min( int $number ) {

        $this->set( 'min', $number );

        return $this;
    }



    /**
     * Collapse all Sections
     *
     * @param bool $collapse
     *
     * @return $this
     */
    public function collapseAll( $collapse ) {

        $this->set( 'collapse_all_flexible', $collapse );

        return $this;
    }



    /**
     * Add Fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function withLayouts( array $fields ) {

        $this->set( 'layouts', $fields );

        return $this;

    }

}