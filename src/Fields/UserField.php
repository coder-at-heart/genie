<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class UserField extends Field {

	protected $type = 'user';

	protected $metaQuery = 'NUMERIC';

	/**
	 * Limit to Wordpress Role
	 *
	 * @param string $role
	 *
	 * @return $this
	 */
	public function role( string $role ) {
		$this->set( 'role', $role );

		return $this;
	}

	/**
	 * Allow no value to be selected
	 *
	 * @param $allowNull
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

}