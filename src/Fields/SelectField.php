<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class SelectField extends Field {

    protected $type = 'select';



    /**
     * Choices for this select dropdown
     *
     * @param array $choices key=> value paid
     *
     * @return $this
     */
    public function choices( array $choices ) {
        return $this->set( 'choices', $choices );

    }



    /**
     * Should the values be loaded by Ajax?
     *
     * @param bool $ajax
     *
     * @return $this
     */
    public function ajax( bool $ajax ) {
        return $this->set( 'ajax', $ajax );

    }



    /**
     * select multiple values?
     *
     * @param bool $multiple
     *
     * @return $this
     */
    public function multiple( bool $multiple ) {
        return $this->set( 'multiple', $multiple );

    }



    protected function setDefaults() {
        parent::setDefaults();
        $this->allowNull( true );
        $this->ui( true );
        $this->returnFormat( 'array' );
    }



    /**
     * Allow no value to be selected
     *
     * @param $allowNull
     *
     * @return $this
     */
    public function allowNull( $allowNull ) {
        return $this->set( 'allow_null', $allowNull );

    }



    /**
     * use an improved UI ?
     *
     * @param $ui
     *
     * @return $this
     */
    public function ui( bool $ui ) {
        return $this->set( 'ui', $ui );

    }



    /**
     * Return Format
     *
     * @param $returnValue
     *
     * @return $this
     */
    public function returnFormat( string $returnValue ) {
        return $this->set( 'return_format', $returnValue );
    }

}