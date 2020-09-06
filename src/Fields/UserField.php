<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Abstracts\Field;

class UserField extends Field
{


    /**
     * Limit to Wordpress Role
     *
     * @param string $role
     *
     * @return $this
     */
    public function role(string $role)
    {
        return $this->set('role', $role);
    }


    /**
     * Allow no value to be selected
     *
     * @param $allowNull
     *
     * @return $this
     */
    public function allowNull(bool $allowNull)
    {
        return $this->set('allow_null', $allowNull);
    }


    /**
     * Allow multiple values to be selected
     *
     * @param bool $multiple
     *
     * @return $this
     */
    public function multiple(bool $multiple)
    {
        return $this->set('multiple', $multiple);
    }


    protected function setDefaults()
    {
        parent::setDefaults();
        $this->type('user');
        $this->metaQuery('NUMERIC');
    }

}