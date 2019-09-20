<?php

namespace Lnk7\Genie\Fields;

class TextAreaField extends TextField {

	protected $type = 'textarea';

	protected function setDefaults() {
		parent::setDefaults();
		$this->newLines( '' );
		$this->searchable( true );
	}

	/**
	 * The number of rows for this input
	 *
	 * @param $rows
	 *
	 * @return $this
	 */
	public function rows( int $rows ) {
		$this->set( 'rows', $rows );

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

}
