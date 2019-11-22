<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class GroupField extends Field {

    protected $type = 'group';



    /**
     * Add Fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function withFields( array $fields ) {

        return $this->set( 'sub_fields', $fields );

    }



    protected function setDefaults() {
        parent::setDefaults();
        $this->layout( 'row' );
    }



    /**
     * layout
     *
     *
     *
     * @param string $layout table|block|row
     *
     * @return $this
     */
    public function layout( string $layout ) {
        return $this->set( 'layout', $layout );

    }

}