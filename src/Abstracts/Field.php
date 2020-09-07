<?php

namespace Lnk7\Genie\Abstracts;

use Lnk7\Genie\Traits\HasData;
use Lnk7\Genie\Utilities\ConvertString;
use Lnk7\Genie\Utilities\HookInto;

/**
 * Class Field
 *
 * @package Lnk7\Genie\Abstracts
 * @property string $key
 * @property string $type
 * @property string $name
 * @property string $_name
 * @property string $label
 * @property array $actions
 * @property array $filters
 * @property string $hidden
 * @property string $required
 * @property bool|int|mixed $_prepare
 * @property bool|int|mixed $_valid
 * @property bool|mixed|string $append
 * @property bool|mixed|string $prepend
 * @property bool|mixed|string $instructions
 * @property bool|int|mixed $read_only
 * @property bool|int|mixed $conditional_logic
 * @property bool|mixed|string[] $wrapper
 * @property bool|mixed|string $default_value
 * @property mixed|bool $displayOnly
 * @property mixed|bool $metaQuery
 * @property mixed|bool $override
 */
abstract class Field
{


    use HasData;


    /**
     * Field constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->setDefaults();
    }


    /**
     * Set defaults for all Fields
     */
    protected function setDefaults()
    {
        $this->_name = $this->name;
        $this->_prepare = 0;
        $this->_valid = 0;
        $this->filters = [];
        $this->actions = [];

        $this->type('text');
        $this->key('');
        $this->label((string)ConvertString::from($this->name)->toTitleCase());
        $this->metaQuery('CHAR');
        /* (int) Whether or not the field value is required. Defaults to 0 */
        $this->required(0);

        /* hack - cant seem to figure out how ACF adds _name to locally imported groups.
            This is needed by the acf_format_value function */
        $this->append('');
        $this->prepend('');

        /* (string) Unique identifier for the field. Must begin with 'field_' */
        $this->instructions('');

        /* (int) read Only. Defaults to 0 */
        $this->readOnly(0);

        /* (mixed) Conditionally hide or show this field based on other field's values.
        Best to use the ACF UI and export to understand the array structure. Defaults to 0 */
        $this->conditionalLogic(0);

        /* (array) An array of attributes given to the field element */
        $this->wrapperWidth('');
        $this->wrapperClass('');
        $this->id('');

        /* (mixed) A default value used by ACF if no value has yet been saved */
        $this->default('');

        /* Genie Defaults */
        $this->hidden(false);

        /* Does this field not have any input?  Tab & Message */
        $this->displayOnly(false);

        /* WordPress post field to override on save (e.g post_title) */
        $this->override(false);
    }


    /**
     * Set the field type
     *
     * @param $type
     *
     * @return $this
     */
    protected function type($type)
    {
        return $this->set('type', $type);
    }


    /**
     * Set a value
     *
     * @param $var
     * @param $value
     *
     * @return $this
     */
    public function set($var, $value)
    {
        $this->$var = $value;
        return $this;
    }


    /**
     * Sets the key for this field
     *
     * @param $key
     *
     * @return $this
     */
    public function key($key)
    {
        return $this->set('key', $key);
    }


    /**
     * Sets a label for this field
     *
     * @param $label
     *
     * @return $this
     */
    public function label($label)
    {
        return $this->set('label', $label);
    }


    protected function metaQuery($metaQuery)
    {
        return $this->set('meta_query', $metaQuery);
    }


    /**
     * Is this field required ?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function required(bool $value)
    {
        return $this->set('required', $value);
    }


    /**
     * Set the Append
     *
     * @param string $string
     *
     * @return $this
     */
    public function append(string $string)
    {
        return $this->set('append', $string);
    }


    /**
     * Set the Prefix
     *
     * @param string $string
     *
     * @return $this
     */
    public function prepend(string $string)
    {
        return $this->set('prepend', $string);
    }


    /**
     * Field instructions
     *
     * @param string $instructions
     *
     * @return $this
     */
    public function instructions(string $instructions)
    {
        return $this->set('instructions', $instructions);
    }


    /**
     * Sets a label for this field
     *
     * @param bool $readOnly
     *
     * @return $this
     */
    public function readOnly(bool $readOnly)
    {
        return $this->set('read_only', $readOnly);
    }


    /**
     * Field Conditional Logic as an Array
     *
     * @param $conditionalLogic
     *
     * @return $this
     */
    public function conditionalLogic($conditionalLogic)
    {
        return $this->set('conditional_logic', $conditionalLogic);
    }


    /**
     * Sets the wrapper width in %
     *
     * @param $width
     *
     * @return $this
     */
    public function wrapperWidth( $width)
    {
        $this->data['wrapper']['width'] = $width;

        return $this;
    }


    /**
     * Set the wrapper Class
     *
     * @param $class
     *
     * @return $this
     */
    public function wrapperClass($class)
    {
        $this->data['wrapper']['class'] = $class;

        return $this;
    }


    /**
     * Sets the HTML id
     *
     * @param $id
     *
     * @return $this
     */
    public function id($id)
    {
        $this->data['wrapper']['id'] = $id;

        return $this;
    }


    /**
     * Set the default value for this field.
     *
     * @param $default
     *
     * @return $this
     */
    public function default($default)
    {
        return $this->set('default_value', $default);
    }


    /**
     * if this field hidden?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function hidden(bool $value)
    {
        return $this->set('hidden', $value);
    }


    public function displayOnly($displayOnly)
    {
        return $this->set('displayOnly', $displayOnly);
    }


    /**
     * Allows overriding wordpress fields
     *
     * @param $field
     *
     * @return $this
     */
    public function override($field)
    {
        return $this->set('override', $field);
    }


    /**
     * Static constructor
     *
     * @param $name
     *
     * @return static
     */
    public static function called($name)
    {
        return new static($name);
    }


    /**
     * use {$key},{$name},{$type} in the filter name
     *
     * @param string $action
     * @param callable $callback
     * @param int $priority
     */
    public function addAction(string $action, callable $callback, int $priority = 10)
    {
        $this->actions[] = (object)[
            'hook'     => $action,
            'callback' => $callback,
            'priority' => $priority,

        ];
    }


    /**
     * use {$key},{$name},{$type} in the filter name
     *
     * @param string $filter
     * @param callable $callback
     * @param int $priority
     */
    public function addFilter(string $filter, callable $callback, int $priority = 10)
    {
        $this->filters[] = (object)[
            'hook'     => $filter,
            'callback' => $callback,
            'priority' => $priority,
        ];
    }


    public function action(callable $function)
    {
        return $this->set('callback', $function);
    }


    /**
     * generate the ACF definition for this field
     *
     * @param $parent_key
     *
     * @return array
     */
    public function generate($parent_key)
    {
        $key = $this->key;
        if (!$key) {
            $key = $parent_key . '_' . strtolower($this->name);
            $this->set('key', 'field_' . $key);
        }

        if (isset($this->sub_fields)) {
            $subFields = [];
            foreach ($this->sub_fields as $field) {
                $subFields[] = $field->generate($key);
            }
            $this->set('sub_fields', $subFields);
        }

        // Flexible Content
        if (isset($this->layouts)) {
            $subFields = [];
            foreach ($this->layouts as $field) {
                $subFields[] = $field->generate($key);
            }
            $this->set('layouts', $subFields);
        }

        // filters
        foreach ($this->filters as $filter) {
            HookInto::filter($this->parseHookName($filter->name), $filter->priority)
                ->run($filter->callback);
        }

        // actions
        foreach ($this->actions as $action) {
            HookInto::action($this->parseHookName($action->name), $action->priority)
                ->run($action->callback);
        }

        return $this->data;
    }


    /**
     * Now that we have generated the key, we can return a property hook
     *
     * @param $name
     *
     * @return string|string[]
     */
    protected function parseHookName($name)
    {
        $find = [
            '{$key}',
            '{$name}',
            '{$type}',
        ];
        $replace = [
            $this->key,
            $this->name,
            $this->type,
        ];
        return str_replace($find, $replace, $name);
    }


    /**
     * Field condition
     *
     * @param Condition $condition
     *
     * @return $this
     */
    public function shown(Condition $condition)
    {
        return $this->set('conditions', $condition->generate());
    }

}