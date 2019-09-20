<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class PageLinkField extends Field {

	protected $type = 'page_link';

	protected $metaQuery = 'NUMERIC';

	protected function setDefaults() {
		parent::setDefaults();
		$this->postObject( 'page' );
	}

	/**
	 * Specify an array of post types to filter the available choices. Defaults to ''
	 *
	 * @param array $postObject
	 *
	 * @return $this
	 */
	public function postObject( array $postObject ) {
		$this->set( 'post_type', $postObject );

		return $this;
	}

	/**
	 * Specify an array of taxonomies to filter the available choices. Defaults to ''
	 *
	 * @param string $taxonomy
	 *
	 * @return $this
	 */
	public function taxonomy( string $taxonomy ) {
		$this->set( 'taxonomy', $taxonomy );

		return $this;
	}


	/**
	 * Specify if null can be accepted as a value.
	 *
	 * @param bool $allowNull
	 *
	 * @return $this
	 */
	public function allowNull( bool $allowNull ) {
		$this->set( 'allow_null', $allowNull );

		return $this;
	}

	/**
	 * Allow multiple values to be selected
	 *
	 * @param bool $multiple
	 *
	 * @return $this
	 */
	public function multiple( bool $multiple ) {
		$this->set( 'multiple', $multiple );

		return $this;
	}


	/**
	 * Allow Archives
	 *
	 * @param bool $allowArchives
	 *
	 * @return $this
	 */
	public function allowArchives( bool $allowArchives ) {
		$this->set( 'allow_archives', $allowArchives );

		return $this;
	}

}