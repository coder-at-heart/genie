<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class CheckboxField extends Field {

    protected $type = 'checkbox';



    /**
     * Specify choices for the checkbox
     *
     * @param array $choices
     *
     * @return $this
     */
    public function choices( array $choices ) {
        return $this->set( 'choices', $choices );

    }



    /**
     *Text shown along side the checkbox
     *
     * @param string $message
     *
     * @return $this
     */
    public function message( string $message ) {
        return $this->set( 'message', $message );

    }



    /**
     * Specify if there should be an "taggle all" option
     *
     * @param bool $toggle
     *
     * @return $this
     */
    public function toggle( bool $toggle ) {
        return $this->set( 'toggle', $toggle );

    }



    protected function setDefaults() {
        parent::setDefaults();
        $this->layout( 'vertical' );
        $this->returnFormat( 'array' );
    }



    /**
     * Specify the layout of the checkbox inputs. Defaults to 'vertical'. Choices of 'vertical' or 'horizontal
     *
     * @param string $layout vertical|horizontal
     *
     * @return $this
     */
    public function layout( string $layout ) {
        return $this->set( 'layout', $layout );

    }



    /**
     * Specify the return format
     *
     * @param string $returnFormat array|value TODO: Check
     *
     * @return $this
     */

    public function returnFormat( string $returnFormat ) {
        return $this->set( 'return_format', $returnFormat );

    }

}