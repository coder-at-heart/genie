<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;
use Lnk7\Genie\Fields\Traits\message;

class TrueFalseField extends Field {

	protected $type = 'true_false';

	protected $metaQuery = 'NUMERIC';

	protected function setDefaults() {
		parent::setDefaults();
		$this->ui( true );
		$this->onText('Yes');
		$this->offText('No');
	}

	/**
	 *Text shown along side the field
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
	 * Show an use an UI switch?
	 *
	 * @param $ui
	 *
	 * @return $this
	 */
	public function ui( bool $ui ) {
		$this->set( 'ui', $ui );

		return $this;
	}

	/**
	 * Text to show on the switch in the on position
	 *
	 * @param string $text
	 *
	 * @return $this
	 */
	public function onText( string $text ) {
		$this->set( 'ui_on_text', $text );

		return $this;
	}

	/**
	 * text to show on the switch in the off position
	 *
	 * @param string $text
	 *
	 * @return $this
	 */
	public function offText( string $text ) {
		$this->set( 'ui_off_text', $text );

		return $this;
	}


}