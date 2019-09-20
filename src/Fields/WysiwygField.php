<?php

namespace Lnk7\Genie\Fields;

class WysiwygField extends TextField {

	protected $type = 'wysiwyg';



	/**
	 * Hide the medias upload button?
	 *
	 * @param bool $mediaUpload
	 *
	 * @return $this
	 */
	public function mediaUpload( bool $mediaUpload ) {

		$this->set( 'media_upload', $mediaUpload );

		return $this;
	}



	/**
	 * Specify which tabs are available. Defaults to 'all'. Choices of 'all' (Visual & Text), 'visual' (Visual Only) or text (Text Only)
	 *
	 * @param $tabs
	 *
	 * @return $this
	 */
	public function tabs( string $tabs ) {

		$this->set( 'tabs', $tabs );

		return $this;
	}



	/**
	 * Specify the editor's toolbar. Defaults to 'full'.
	 * Choices of 'full' (Full), 'basic' (Basic) or a custom toolbar (https://www.advancedcustomfields.com/resources/customize-the-wysiwyg-toolbars/)
	 *
	 * @param string $toolbar
	 *
	 * @return $this
	 */
	public function toolbar( string $toolbar ) {

		$this->set( 'toolbar', $toolbar );

		return $this;
	}



	protected function setDefaults() {

		parent::setDefaults();
		$this->tabs( 'all' );
		$this->toolbar( 'basic' );
		$this->mediaUpload( false );
		$this->searchable( true );

	}

}