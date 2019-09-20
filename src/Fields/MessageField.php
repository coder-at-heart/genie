<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class MessageField extends Field {

	protected $type = 'message';

	protected function setDefaults() {
		parent::setDefaults();
		$this->displayOnly(true);
		$this->newLines( 'wpautop' );
		$this->escapeHTML( false );
	}

	/**
	 *Text shown
	 *
	 * @param string $message
	 *
	 * @return $this
	 */
	public function message( string $message ) {
		$this->set( 'message', $message );

		return $this;
	}

	/**
	 * Decides how to render new lines. Detauls to 'wpautop'. Choices of 'wpautop' (Automatically add paragraphs), 'br' (Automatically add <br>) or '' (No Formatting)
	 *
	 * @param $newLines  string wpautop|br|ni;;
	 *
	 * @return $this
	 */
	public function newLines( string $newLines ) {
		$this->set( 'new_lines', $newLines );

		return $this;

	}

	/**
	 * Should HTML be escaped ?
	 *
	 * @param bool $escape
	 *
	 * @return $this
	 */
	public function escapeHTML( bool $escape ) {
		$this->set( 'esc_html', $escape );

		return $this;

	}

}