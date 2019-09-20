<?php

namespace Lnk7\Genie\Fields;

class FileField extends TextField {

	protected $type = 'file';

	protected $metaQuery = 'NUMERIC';

	protected function setDefaults() {
		parent::setDefaults();
		$this->searchable(false);
		$this->returnValue( 'array' );
		$this->previewSize( 'thumbnail' );
		$this->library( 'all' );
	}

	/**
	 * Specify the minimum filesize in MB required when uploading. Defaults to 0.
	 * The unit may also be included. eg. '256KB'
	 *
	 * @param string $minSize
	 *
	 * @return $this
	 */
	public function minSize( string $minSize ) {
		$this->set( 'min_size', $minSize );

		return $this;
	}

	/**
	 * Specify the maximum filesize in MB in px allowed when uploading. Defaults to 0.
	 * The unit may also be included. eg. '256KB'
	 *
	 * @param string $maxSize
	 *
	 * @return $this
	 */
	public function maxSize( string $maxSize ) {
		$this->set( 'max_size', $maxSize );

		return $this;
	}

	/**
	 * Comma separated list of file type extensions allowed when uploading.
	 * Defaults to ''
	 *
	 * @param string $mimeTypes
	 *
	 * @return $this
	 */
	public function mimeTypes( string $mimeTypes ) {
		$this->set( 'mime_types', $mimeTypes );

		return $this;
	}

	/**
	 * Specify the type of value returned by get_field(). Defaults to 'array'.
	 * Choices of 'array' (Image Array), 'url' (Image URL) or 'id' (Image ID)
	 *
	 * @param $returnValue string
	 *
	 * @return $this
	 */
	public function returnValue( string $returnValue ) {
		$this->set( 'return_value', $returnValue );

		return $this;
	}

	/**
	 *
	 * Specify the image size shown when editing. Defaults to 'thumbnail'.
	 *
	 * @param $previewSize
	 *
	 * @return $this
	 */
	public function previewSize( $previewSize ) {
		$this->set( 'preview_size', $previewSize );

		return $this;
	}

	/**
	 *  Restrict the image library. Defaults to 'all'. Choices of 'all' (All Images) or 'uploadedTo' (Uploaded to post)
	 *
	 * @param string $library all|uploadedTo
	 *
	 * @return $this
	 */
	public function library( string $library ) {
		$this->set( 'library', $library );

		return $this;
	}

}