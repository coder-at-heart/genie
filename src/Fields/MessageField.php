<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class MessageField extends Field {

    protected $type = 'message';



    /**
     *Text shown
     *
     * @param string $message
     *
     * @return $this
     */
    public function message( string $message ) {
        return $this->set( 'message', $message );

    }



    protected function setDefaults() {
        parent::setDefaults();
        $this->displayOnly( true );
        $this->newLines( 'wpautop' );
        $this->escapeHTML( false );
    }



    /**
     * Decides how to render new lines. Detauls to 'wpautop'. Choices of 'wpautop' (Automatically add paragraphs), 'br' (Automatically add <br>) or '' (No Formatting)
     *
     * @param $newLines  string wpautop|br|ni;;
     *
     * @return $this
     */
    public function newLines( string $newLines ) {
        return $this->set( 'new_lines', $newLines );

    }



    /**
     * Should HTML be escaped ?
     *
     * @param bool $escape
     *
     * @return $this
     */
    public function escapeHTML( bool $escape ) {
        return $this->set( 'esc_html', $escape );

    }

}