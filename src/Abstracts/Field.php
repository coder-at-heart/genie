<?php

namespace Lnk7\Genie\Abstracts;

use Lnk7\Genie\Utilities\ConvertString;

Abstract class Field {

    /**
     * Field type - Must be overridden
     *
     * @var string
     */
    protected $type = 'text';

    /**
     * @var bool
     */
    protected $noKey = false;

    /**#
     * meta Query data type
     *
     * Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'.
     *
     * @var string
     */
    protected $metaQuery = 'CHAR';

    /**
     * ACF field settings Array
     *
     * @var array
     */
    protected $settings = [

        /* (string) Unique identifier for the field. Must begin with 'field_' */
        'key'      => '',

        /* (string) Visible when editing the field value */
        'label'    => '',

        /* (string) Used to save and load data. Single word, no spaces. Underscores and dashes allowed */
        'name'     => '',

        /* hack - cant seem to figure out how ACF adds _name to locally imported groups.
            This is needed by the acf_format_value function */
        '_name'    => '',
        '_prepare' => 0,
        '_valid'   => 0,

        'prepend'           => '',
        'append'            => '',

        /* (string) Type of field (text, textarea, image, etc) */
        'type'              => 'text',

        /* (string) Instructions for authors. Shown when submitting data */
        'instructions'      => '',

        /* (int) Whether or not the field value is required. Defaults to 0 */
        'required'          => 0,

        /* (int) read Only. Defaults to 0 */
        'read_only'         => 0,

        /* (mixed) Conditionally hide or show this field based on other field's values.
        Best to use the ACF UI and export to understand the array structure. Defaults to 0 */
        'conditional_logic' => 0,

        /* (array) An array of attributes given to the field element */
        'wrapper'           => [
            'width' => '',
            'class' => '',
            'id'    => '',
        ],

        /* (mixed) A default value used by ACF if no value has yet been saved */
        'default_value'     => '',

        /* Genie Defaults */
        'hidden'            => 0,

        /* Add to search Index */
        'searchable'        => true,

        /* Display Only */
        'displayOnly'       => false,

        /* Can override  WordPress Field */
        'override'          => false,

    ];

    /**
     * Wordpress callbacks for this field
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Wordpress actions for this field.
     *
     * @var array
     */
    protected $actions = [];



    /**
     * Field constructor.
     *
     * @param string $name
     */
    public function __construct( string $name ) {

        $this->set( 'name', $name );
        $this->setDefaults();

    }



    /**
     * Set a value
     *
     * @param $var
     * @param $value
     */
    public function set( $var, $value ) {

        $this->settings[ $var ] = $value;
    }



    /**
     * Set defaults for this field.
     */
    protected function setDefaults() {

        $name = $this->get( 'name' );

        $this->label( (string) ConvertString::From( $name )->toTitleCase() );
        $this->searchable( false );
        $this->required( false );
        $this->set( '_name', $name );
    }



    /**
     * Getter
     *
     * @param $var
     *
     * @return mixed
     */

    public function get( $var ) {

        return $this->settings[ $var ];
    }



    /**
     * Sets a label for this field
     *
     * @param $label
     *
     * @return $this
     */
    public function label( $label ) {

        $this->set( 'label', $label );

        return $this;
    }



    /**
     * Allows overriding wordpress fields
     *
     * @param $field
     *
     * @return $this
     */
    public function override( $field ) {

        $this->set( 'override', $field );

        return $this;
    }




    /**
     * Should the contents of this field be indexed ?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function searchable( bool $value ) {

        $this->set( 'searchable', $value );

        return $this;
    }



    /**
     * Is this field required ?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function required( bool $value ) {

        $this->set( 'required', $value );

        return $this;
    }



    /**
     * Static createor
     *
     * TextField::Called('name')
     *
     * @param $name
     *
     * @return static
     */
    public static function Called( $name ) {

        return new static( $name );
    }



    /**
     * magic function
     *
     * @param $var
     *
     * @return bool|mixed
     */
    public function __get( $var ) {

        return isset( $this->settings[ $var ] ) ? $this->settings[ $var ] : false;
    }



    /**
     * magic function
     *
     * @param $var
     * @param $value
     */
    public function __set( $var, $value ) {

        $this->settings[ $var ] = $value;
    }



    /**
     * Check if the setting has been set
     *
     * @param $var
     *
     * @return bool
     */
    public function __isset( $var ) {

        return isset( $this->settings[ $var ] );
    }



    /**
     * Set the Append
     *
     * @param string $string
     *
     * @return $this
     */
    public function append( string $string ) {

        $this->settings['append'] = $string;

        return $this;
    }



    public function callback( callable $function ) {

        $this->set( 'callback', $function );

        return $this;

    }



    /**
     * Field Conditional Logic as an Array
     *
     * @param $conditionalLogic
     *
     * @return $this
     */
    public function conditionalLogic( $conditionalLogic ) {

        $this->set( 'conditional_logic', $conditionalLogic );

        return $this;
    }



    /**
     * Set the default value for this field.
     *
     * @param $default
     *
     * @return $this
     */
    public function default( $default ) {

        $this->set( 'default_value', $default );

        return $this;
    }



    public function displayOnly( $displayOnly ) {

        $this->set( 'displayOnly', $displayOnly );

        return $this;
    }



    public function generate( $parent_key ) {

        $this->set( 'type', $this->type );
        $key = $this->key;
        if ( ! $key ) {
            $key = $parent_key . '_' . strtolower( $this->get( 'name' ) );
            $this->set( 'key', 'field_' . $key );
        }
        $this->set( 'meta_query', $this->metaQuery );

        if ( $this->isset( 'sub_fields' ) ) {
            $subFields = [];
            $fields    = $this->get( 'sub_fields' );
            foreach ( $fields as $field ) {
                $subFields[] = $field->generate( $key );
            }
            $this->set( 'sub_fields', $subFields );
        }

        // Flexible Content
        if ( $this->isset( 'layouts' ) ) {
            $subFields = [];
            $fields    = $this->get( 'layouts' );
            foreach ( $fields as $field ) {
                $subFields[] = $field->generate( $key );
            }
            $this->set( 'layouts', $subFields );
        }

        // filters
        foreach ( $this->filters as $filter ) {

            $name     = str_replace( [ '{$key}', '{$name}', '{$type}' ], [
                $this->key,
                $this->name,
                $this->type
            ], $filter['name'] );
            $priority = isset( $filter['priority'] ) ?? 10;
            $params   = isset( $filter['params'] ) ?? 1;
            add_filter( $name, $filter['callback'], $priority, $params );

        }

        // actions
        foreach ( $this->actions as $action ) {

            $name     = str_replace( [ '{$key}', '{$name}', '{$type}' ], [
                $this->key,
                $this->name,
                $this->type
            ], $action['name'] );
            $priority = isset( $action['priority'] ) ?? 10;
            $params   = isset( $action['params'] ) ?? 1;
            add_filter( $name, $action['callback'], $priority, $params );

        }

        return $this->settings;
    }



    /**
     * Check if the setting has been set
     *
     * @param $var
     *
     * @return bool
     */
    public function isset( $var ) {

        return isset( $this->settings[ $var ] );
    }



    /**
     * if this field hidden?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function hidden( bool $value ) {

        $this->set( 'hidden', $value );

        return $this;
    }



    /**
     * Sets the HTML id
     *
     * @param $id
     *
     * @return $this
     */
    public function id( $id ) {

        $this->settings['wrapper']['id'] = $id;

        return $this;
    }



    /**
     * Field instructions
     *
     * @param string $instructions
     *
     * @return $this
     */
    public function instructions( string $instructions ) {

        $this->set( 'instructions', $instructions );

        return $this;
    }



    /**
     * Sets the key for this field
     *
     * @param $key
     *
     * @return $this
     */
    public function key( $key ) {

        $this->set( 'key', $key );

        return $this;
    }



    /**
     * Set the Prefix
     *
     * @param string $string
     *
     * @return $this
     */
    public function prepend( string $string ) {

        $this->settings['prepend'] = $string;

        return $this;
    }



    /**
     * Is this field read Only ?
     *
     * @param bool $value
     *
     * @return $this
     */
    public function readOnly( bool $value ) {

        $this->set( 'read_only', $value );

        return $this;
    }



    public function shown( Condition $condition ) {

        $this->set( 'conditions', $condition->generate() );

        return $this;

    }



    /**
     * Set the wrapper Class
     *
     * @param $class
     *
     * @return $this
     */
    public function wrapperClass( $class ) {

        $this->settings['wrapper']['class'] = $class;

        return $this;
    }



    /**
     * Sets the wrtapper width in %
     *
     * @param $width
     *
     * @return $this
     */
    public function wrapperWidth( int $width ) {

        $this->settings['wrapper']['width'] = $width;

        return $this;

    }

}