<?php

namespace Lnk7\Genie\Utilities;

use Lnk7\Genie\Abstracts\Condition;
use Lnk7\Genie\Abstracts\Field;
use Lnk7\Genie\Debug;
use Lnk7\Genie\Tools;

/**
 * Class CreateSchema
 * Powerful wrapper around ACF.
 *  - Avoids having to create schema in the backend
 *  - Dynamically create schema
 *
 * @package Lnk7\Genie
 */
class CreateSchema
{


    private $key;


    private $title;


    private $menu_order = 0;


    private $location;


    private $position = 'normal';  //acf_after_title|normal|side


    private $style = 'default';   //default|seamless


    private $label_placement = 'top'; // top|left


    private $instruction_placement = 'label'; // label|field


    private $hide_on_screen = [];


    private $fields = [];


    private $attachTo = null;


    /**
     * CreateSchema constructor.
     *
     * @param $title
     */
    public function __construct($title)
    {
        $this->key = 'group_' . sanitize_title($title);
        $this->title = $title;
    }


    /**
     * Static constructor
     *
     * @param $name
     *
     * @return CreateSchema
     */

    public static function called($name)
    {
        return new static($name);
    }


    /**
     * Attach this schema to a post type - effectively defining Fields
     *
     * @param $class
     *
     * @return $this
     */
    public function attachTo($class)
    {
        $this->attachTo = $class;

        return $this;
    }


    /**
     * Helper function
     */
    function dump()
    {
        Debug::dd($this->generateSchemaArray());
    }


    /**
     * Create the schema for ACF, and attach if necessary.
     *
     * @return array
     */
    protected function generateSchemaArray()
    {
        $fields = [];
        foreach ($this->fields as $field) {
            $fields[] = $field->generate(sanitize_title($this->title));
        }

        foreach ($fields as &$field) {
            $field = $this->convertNameToKey($field, $fields);
        }

        $schema =
            [
                'key'                   => $this->key,
                'title'                 => $this->title,
                'menu_order'            => $this->menu_order,
                'fields'                => $fields,
                'location'              => $this->location,
                'position'              => $this->position,
                'style'                 => $this->style,
                'label_placement'       => $this->label_placement,
                'instruction_placement' => $this->instruction_placement,
                'hide_on_screen'        => $this->hide_on_screen,
            ];

        if ($this->attachTo) {
            call_user_func($this->attachTo . '::attachSchema', $schema);
        }

        return $schema;
    }


    /**
     * Go through the field definitions and convert a name
     * to a acf key where needed.
     * We come at the 1st level.  We need to be careful of sub_fields
     * having the same name
     *
     * @param $field
     * @param $fields
     *
     * @return mixed
     */
    protected function convertNameToKey($field, $fields)
    {
        if (isset($field['conditions'])) {
            foreach ($field['conditions'] as &$condition) {
                foreach ($condition as &$statement) {
                    if (isset($statement['field'])) {
                        $name = $statement['field'];
                        // only do this if it's not a key
                        if (substr($name, 0, 6) != 'field_') {
                            $statement['field'] = $this->findNameInFieldsAndReturnKey($name, $fields);
                        }
                    }
                }
            }
        }
        if (isset($field['sub_fields'])) {
            foreach ($field['sub_fields'] as &$subfield) {
                $subfield = $this->convertNameToKey($subfield, $fields);
            }
        }

        return $field;
    }


    /**
     * Recursive function to parse sub_fields looking for $name
     *
     * @param $name
     * @param $fields
     *
     * @return mixed
     */
    protected function findNameInFieldsAndReturnKey($name, $fields)
    {
        foreach ($fields as $field) {
            if ($field['name'] === $name) {
                return $field['key'];
            }
            if (isset($field['sub_fields'])) {
                $found = $this->findNameInFieldsAndReturnKey($name, $field['sub_fields']);
                if ($found) {
                    return $found;
                }
            }
        }

        return false;
    }


    /**
     * An Array of Wordpress elements to hide on Screen
     * 'permalink',
     * 'the_content',
     * 'excerpt',
     * 'discussion',
     * 'comments',
     * 'revisions',
     * 'slug',
     * 'author',
     * 'format',
     * 'page_attributes',
     * 'featured_image',
     * 'categories',
     * 'tags',
     * 'send-trackbacks',
     *
     * @param array $hide_on_screen
     *
     * @return $this
     */
    public function hideOnScreen(array $hide_on_screen)
    {
        $this->hide_on_screen = $hide_on_screen;

        return $this;
    }


    /**
     * Instruction Placement
     *
     * @param string $instruction_placement
     *
     * @return $this
     */
    public function instructionPlacement(string $instruction_placement)
    {
        $this->instruction_placement = $instruction_placement;

        return $this;
    }


    /**
     * label placement
     *
     * @param string $label_placement
     *
     * @return $this
     */
    public function labelPlacement(string $label_placement)
    {
        $this->label_placement = $label_placement;

        return $this;
    }


    /**
     * Menu Order
     *
     * @param int $menuOrder
     *
     * @return $this
     */
    public function menuOrder(int $menuOrder)
    {
        $this->menu_order = $menuOrder;

        return $this;
    }


    /**
     * position
     *
     * @param string $position
     *
     * @return $this
     */
    public function position(string $position)
    {
        $this->position = $position;

        return $this;
    }


    /**
     * Generate and register the schema with ACF
     *
     * @param int $sequence
     */
    function register($sequence = 20)
    {
        HookInto::action('init', $sequence)
            ->run(function () {
                $schema = $this->return();
                acf_add_local_field_group($schema);
            });
    }


    /**
     * Generate the Schema Array
     *
     * @return array
     */
    function return()
    {
        return $this->generateSchemaArray();
    }


    /**
     * Accepts a condition where to show this schema
     *
     * @param Condition $condition
     *
     * @return $this
     */
    public function shown(Condition $condition)
    {
        $this->location = $condition->generate();

        return $this;
    }


    /**
     * Sets the field styles
     *
     * @param string $style
     *
     * @return $this
     */
    public function style(string $style)
    {
        $this->style = $style;

        return $this;
    }


    /**
     * Add a single field
     *
     * @param array $fields
     *
     * @return $this
     */
    public function withField(Field $field)
    {
        $this->fields[] = $field;

        return $this;
    }


    /**
     * Field definitions. Required.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function withFields(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }

}
