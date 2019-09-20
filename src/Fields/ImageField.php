<?php

namespace Lnk7\Genie\Fields;

class ImageField extends FileField {

	protected $type = 'image';

	/**
	 * Specify the minimum width of the image
	 *
	 * @param int $minWidth
	 *
	 * @return $this
	 */
	public function minWidth( int $minWidth ) {
		$this->set( 'min_width', $minWidth );

		return $this;
	}

	/**
	 * Specify the minimum height of the image
	 *
	 * @param int $minHeight
	 *
	 * @return $this
	 */
	public function minHeight( int $minHeight ) {
		$this->set( 'min_height', $minHeight );

		return $this;
	}

	/**
	 * Specify the maximum width of the image
	 *
	 * @param int $maxWidth
	 *
	 * @return $this
	 */
	public function maxWidth( int $maxWidth ) {
		$this->set( 'max_width', $maxWidth );

		return $this;
	}

	/**
	 * Specify the maximum height of the image
	 *
	 * @param int $maxHeight
	 *
	 * @return $this
	 */
	public function maxHeight( int $maxHeight ) {
		$this->set( 'max_height', $maxHeight );

		return $this;
	}

}