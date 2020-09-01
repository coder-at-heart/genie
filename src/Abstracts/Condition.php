<?php

namespace Lnk7\Genie\Abstracts;

/**
 * Class Condition
 * Used to generate ACF conditions
 *
 * @package Lnk7\Genie\Abstracts
 */
abstract class Condition
{

    /**
     * field name
     *
     * @var string
     */
    protected $fieldName = 'field';

    /**
     * An Array of conditions
     *
     * @var array
     */
    private $conditions = [];

    /**
     * Parse conditions into groups
     *
     * @var array
     */
    private $group = [];

    /**
     * The current field.
     *
     * @var string|null
     */
    private $field = '';



    /**
     * constructor.
     *
     * @param null $field
     */
    public function __construct($field = null)
    {

        if (!is_null($field)) {
            $this->field = $field;
        }
    }



    /**
     * Static Constructor
     *
     * @param $field
     *
     * @return Condition
     */
    public static function field($field)
    {

        return new static($field);
    }



    /**
     * Start a new group with an And clause
     *
     * @param $field
     *
     * @return $this
     */
    public function and($field)
    {

        $this->field = $field;

        return $this;
    }



    /**
     * Check the field contains
     *
     * @param $value
     *
     * @return $this
     */
    public function contains($value)
    {

        $this->group[] = [
            $this->fieldName => $this->field,
            'operator'       => '==contains',
            'value'          => $value,

        ];

        return $this;
    }



    /**
     * Check the field is empty
     *
     * @return $this
     */
    public function empty()
    {

        $this->group[] = [
            $this->fieldName => $this->field,
            'operator'       => '==empty',

        ];

        return $this;
    }



    /**
     * Check the field equals
     *
     * @param $value
     *
     * @return $this
     */
    public function equals($value)
    {

        $this->group[] = [
            $this->fieldName => $this->field,
            'operator'       => '==',
            'value'          => $value,
        ];

        return $this;
    }



    /**
     * Generate the array condition
     *
     * @return array
     */

    public function generate()
    {

        if (count($this->group) > 0) {
            $this->conditions[] = $this->group;
        }

        return $this->conditions;
    }



    /**
     * check the field matches
     *
     * @param $pattern
     *
     * @return $this
     */
    public function matches($pattern)
    {

        $this->group[] = [
            $this->fieldName => $this->field,
            'operator'       => '==pattern',
            'value'          => $pattern,

        ];

        return $this;
    }



    /**
     * Check the field is not empty
     *
     * @return $this
     */
    public function notEmpty()
    {

        $this->group[] = [
            $this->fieldName => $this->field,
            'operator'       => '!=empty',

        ];

        return $this;
    }



    /**
     * Check the field is not equal to
     *
     * @param $value
     *
     * @return $this
     */
    public function notEquals($value)
    {

        $this->group[] = [
            $this->fieldName => $this->field,
            'operator'       => '!=',
            'value'          => $value,
        ];

        return $this;
    }



    /**
     * Start a new OR group
     *
     * @param $field
     *
     * @return $this
     */
    public function or($field)
    {

        $this->field = $field;
        $this->conditions[] = $this->group;
        $this->group = [];

        return $this;
    }

}
