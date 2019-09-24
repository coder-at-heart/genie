<?php

namespace Lnk7\Genie\Abstracts;

use JsonSerializable;
use Lnk7\Genie\Cache;
use Lnk7\Genie\Data\WordPress;
use Lnk7\Genie\Registry;
use Lnk7\Genie\Utilities\ConvertString;
use ReflectionClass;
use WP_Error;
use WP_Post;

/**
 * Class WordPressObject
 *
 * Abstract class for all Custom post types. This is the engine of the Vitol Intranet
 *
 * @package Lnk7\Genie\Abstracts
 */
abstract class WordPressObject implements JsonSerializable {

    /**
     * Wordpress Post Type
     *
     * @var string
     */
    static $postType;

    /**
     * Should this be cached ?
     *
     * @var bool
     */
    static $cache = false;

    /**
     * A list of Columns to be displayed in the back-end
     */
    static $columns = [];

    /**
     * Singular version of the post_type
     *
     * @var
     */
    static $singular;

    /**
     * Plural version of the post_type
     *
     * @var
     */
    static $plural;

    /**
     *  Use the gutenberg editor ?
     * @var bool
     */
    static $useGutenberg = false;

    /**
     * Used to store the object data
     *
     * @var mixed|void
     */
    var $data;

    /**
     * id for the currently loaded instance.
     *
     * @var null
     */
    protected $id;



    /**
     * Return a new instance of the Object
     */
    function __construct( $id = null ) {

        if ( ! $id ) {
            return;
        }
        $this->id = $id;

        $this->data = get_post_meta( $id, static::getCacheKey(), true );
        if ( ! $this->data ) {
            $this->data = static::generate( $id );
        }

        // Post processing .. . For anything that should not be cached.
        $this->data = apply_filters( 'genie_' . static::$postType . '_load', $this->data );
    }



    /**
     * Cache key used for this post_type
     *
     * @return string
     */
    protected static function getCacheKey() {

        return Cache::getCachePrefix() . '_object';
    }



    /**
     * Generate this Object and cache it.
     *
     * @param $id
     *
     * @return array|WP_Post|null
     */
    protected static function generate( $id ) {

        $data = get_post( $id );
        if ( ! $data ) {
            return (object) [];
        }

        $fields = get_fields( $data->ID );
        if ( $fields ) {
            foreach ( $fields as $field => $value ) {
                $data->$field = $value;
            }
        }

        // Does this post have a featured image?
        $data->featured_image = get_the_post_thumbnail_url( $data->ID );

        // What about a link to this post?
        $data->permalink = get_the_permalink( $data->ID );

        $data = apply_filters( 'genie_' . static::$postType . '_generate', $data );

        if ( static::$cache ) {
            update_post_meta( $data->ID, static::getCacheKey(), $data );
        }

        return $data;
    }



    /**
     * Setup Wordpress Hooks, filters and register necessary method calls.
     */
    public static function Setup() {
        add_filter( 'wp_insert_post_data', static::class . '::wp_insert_post_data', 10, 2 );
        add_action( 'acf/save_post', static::class . '::acf_after_save_post', 20 );
        add_action( 'init', static::class . '::init', 20 );
        add_filter( 'manage_edit-' . static::$postType . '_columns', static::class . '::columnsHeaders' );
        add_action( 'manage_' . static::$postType . '_posts_custom_column', static::class . '::columnContent', 10, 2 );
        add_filter( 'post_type_link', static::class . '::post_type_link', 10, 2 );
        add_filter( 'genie_view_before_render', static::class . '::genie_view_before_render', 10, 1 );
        add_filter( 'use_block_editor_for_post_type', static::class . '::use_block_editor_for_post_type', 10, 2 );
    }



    /**
     * ACF before the post is saved.
     *
     * We can override some of the wordpress fields here.
     *
     */
    public static function wp_insert_post_data( $data, $postarr ) {

        // Bail early if no data sent.
        if ( empty( $postarr['acf'] ) ) {
            return $data;
        }

        // Not this post type?
        $postType = $data['post_type'];
        if ( $postType != static::$postType ) {
            return $data;
        }

        $fields = static::getFields();

        foreach ( $fields as $field ) {
            if ( $field['override'] ) {
                $value = $postarr['acf'][ $field['key'] ];
                $field = $field['override'];
                if ( is_callable( $field ) ) {
                    list( $field, $value ) = call_user_func( $field, $value );
                }
                if ( in_array( $field, WordPress::$postFields ) ) {
                    $data[ $field ] = $value;
                }
            }
        }

        $data = apply_filters( 'genie_override_' . static::$postType, $data, $postarr );

        return $data;

    }



    /**
     * get the field defintions
     *
     * @return mixed|null
     */
    public static function getFields() {
        return Registry::get( 'fields', static::class );

    }



    /**
     * Generate Object and cache
     *
     * @param $post_id
     */
    public static function acf_after_save_post( $post_id ) {

        global $post;

        if ( ! $post or $post->post_type != static::$postType ) {
            return;
        }

        // Generate Cache
        if ( static::$cache ) {
            static::generate( $post_id );
        }
    }



    /**
     * Parse the schema and build a map of the field name / keys
     * We will use this later when saving data.
     *
     * Problem with setting static::$schema here
     * https://stackoverflow.com/questions/4577187/php-5-3-late-static-binding-doesnt-work-for-properties-when-defined-in-parent
     *
     * @param $schema
     */
    public static function attachSchema( $schema ) {

        $fields  = Registry::get( 'fields' );
        $schemas = Registry::get( 'schemas' );

        if ( ! $fields ) {
            $fields = [];
        }
        if ( ! $schemas ) {
            $schemas = [];
        }

        $class = static::class;

        // modify the schema so we get index arrays
        $level1Fields = [];
        foreach ( $schema['fields'] as $field ) {
            $level1Fields[ $field['name'] ] = $field;
        }

        $fields[ $class ]  = $level1Fields;
        $schemas[ $class ] = $schema;

        Registry::set( 'fields', $fields );
        Registry::set( 'schemas', $schemas );
    }



    /**
     * Handle the display of custom columns
     *
     * @param $column
     * @param $post_id
     *
     */
    public static function columnContent( $column, $post_id ) {

        $object = new static( $post_id );
        echo apply_filters( 'genie_column_' . static::$postType . '_' . $column, $object->$column, $object );

    }



    /**
     * Decide what column headers we should show in the backend
     *
     * @param $columns
     *
     * @return array
     */

    public static function columnsHeaders( $columns ) {

        $columns = [
            "cb"    => "<input type=\"checkbox\" />",
            "title" => __( 'Title' ),
        ];

        return array_merge( $columns, static::$columns );

    }



    /**
     * provide a wrapper for wp_insert_post settings some defaults
     *
     * @param array $array
     *
     * @return int|WP_Error
     */

    public static function create( $array = [] ) {

        $defaults = [
            'post_type'   => static::$postType,
            'post_status' => 'publish',
        ];

        return wp_insert_post( array_merge( $defaults, $array ) );
    }



    /**
     * Get the first result
     *
     * @param array $params
     *
     * @return bool|mixed
     */
    public static function first( $params = [] ) {

        $data = static::get( $params );
        if ( count( $data ) > 0 ) {
            return $data[0];
        }

        return false;

    }



    /**
     * Wrapper around get_posts. Returns an array of Objects
     *
     * @param array $params
     *
     * @return array WP_POST
     */
    public static function get( $params = [] ) {

        $defaultArgs = [
            'numberposts' => - 1,
            'orderby'     => 'date',
            'order'       => 'DESC',
            'post_type'   => static::$postType,
            'post_status' => 'publish',
            'fields'      => 'ids',
        ];

        $data        = [];
        $defaultArgs = apply_filters( 'genie_' . static::$postType . '_get_args', $defaultArgs );
        $args        = array_merge( $defaultArgs, $params );
        $posts       = get_posts( $args );

        foreach ( $posts as $id ) {
            $object = new static( $id );
            $data[] = $object;
        }

        $data = apply_filters( 'genie_' . static::$postType . '_get_data', $data );

        return $data;
    }



    /**
     * Return a new instance of this class
     *
     * @param $id
     *
     * @return WordPressObject
     */
    public static function getById( $id ) {
        return new static( $id );
    }



    /**
     * Find a post by it's slug
     *
     * @param $slug
     *
     * @return bool|mixed
     */
    public static function getBySlug( $slug ) {

        $posts = static::get( [
            'name'        => $slug,
            'post_status' => 'any',
            'fields'      => 'ids',
        ] );

        if ( ! $posts ) {
            return false;
        }

        return new static( $posts[0]->ID );
    }



    /**
     * Get All posts based on a Taxonomy Name
     *
     * @param $name
     * @param $taxonomy
     *
     * @return array
     */
    public static function getByTaxonomyName( $name, $taxonomy ) {

        $term = get_term_by( 'name', $name, $taxonomy );

        return static::get( [
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => [ $term->term_id ],
                ],
            ],
        ] );
    }



    /**
     * Find a post by it's title
     *
     * @param $title
     *
     * @return bool|mixed
     */
    public static function getByTitle( $title ) {

        $posts = static::get( [
            'title'  => $title,
            'fields' => 'ids',
        ] );

        if ( ! $posts ) {
            return false;
        }

        return new static( $posts[0]->ID );
    }



    /**
     * Useful for Templates
     *
     * @return WordPressObject
     */
    public static function getCurrent() {

        return new static( get_the_ID() );
    }



    /**
     * Look through this custom post's schema and return the key for a field.
     * This allows us use the key when creating new data with update_field.
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getKey( string $name ) {

        $schema = Registry::get( 'schemas', static::class );

        return static::findKey( $name, $schema['fields'] );
    }



    /**
     * Recursive function to parse fields map looking for $name
     *
     * @param $name
     * @param $fields
     *
     * @return mixed
     */
    protected static function findKey( $name, $fields ) {

        foreach ( $fields as $field ) {
            if ( $field['name'] === $name ) {
                return $field['key'];
            }
            if ( isset( $field['sub_fields'] ) ) {
                $found = static::findKey( $name, $field['sub_fields'] );
                if($found) {
                    return $found;
                }
            }
        }
        return false;
    }



    /**
     * An alias for static::get()
     *
     * @return array
     */
    public static function getLatest() {

        return static::get();
    }



    /**
     * get the Plural name of the post type
     *
     * @return string
     */
    public static function getPlural() {

        return static::$plural ?? ConvertString::From( static::$postType )->toPlural()->toTitleCase()->return();
    }



    public static function getSingular() {

        return static::$singular ?? ConvertString::From( static::$postType )->toSingular()->toTitleCase()->return();
    }



    /**
     * Wordpress Init Hook
     */
    public static function init() {

        static::createPostType();
    }



    /**
     * Code to instantiate the Custom Post Type. Potentially overridden
     */
    public static function createPostType() {

    }



    /**
     * get the permalink of the content Type
     */
    public static function post_type_link( $url, $post ) {
        return $url;
    }



    /**
     * Use Gutenberg ?
     *
     * @param $current_status
     * @param $post_type
     *
     * @return bool
     */
    public static function use_block_editor_for_post_type( $current_status, $post_type ) {

        // Use your post type key instead of 'product'
        if ( $post_type === static::$postType ) {
            return static::$useGutenberg;
        }

        return $current_status;
    }



    /**
     * Hook into the views render function
     * and make this object available to twig
     *
     */
    public static function genie_view_before_render( $vars ) {

        $reflect = new ReflectionClass( static::class );

//        $vars['_objects'] = array_merge( $vars['_objects'], [ $reflect->getShortName() => new static ] );

        return $vars;
    }



    /**
     * Get the full Schema Definition
     *
     * @return mixed|null
     */
    public static function getSchema() {
        return Registry::get( 'schemas', static::class );

    }



    /**
     * Recursive function to to go through and build up the key Map
     *
     * @param $fields
     *
     * @return array
     */
    protected static function buildNameKeyMap( $fields ) {

        $fieldArray = [];
        foreach ( $fields as $field ) {

            $name = $field['name'];
            $key  = $field['key'];

            $fieldArray[ $name ] = [ 'key' => $key ];
            if ( isset( $field['sub_fields'] ) ) {
                $fieldArray[ $name ]['sub_fields'] = static::buildNameKeyMap( $field['sub_fields'] );
            }
        }

        return $fieldArray;

    }



    /**
     * magic getter
     *
     * @param $var
     *
     * @return mixed
     */
    function __get( $var ) {

        if ( ! is_object( $this->data ) ) {

            return false;
        }

        return $this->data->$var;
    }



    /**
     * magic set
     *
     * @param $var
     * @param $value
     */
    function __set( $var, $value ) {

        $this->data->$var = $value;
    }



    /**
     * Needed from TWIG
     *
     * @param $var
     *
     * @return bool
     */
    function __isset( $var ) {

        return isset( $this->data->$var );
    }



    /**
     * return all data for this post
     *
     * @return mixed|void
     */
    function getData() {

        return $this->data;

    }



    /**
     * What should we serialize when json_encode is called on the object
     *
     * @return mixed|void
     */
    public function jsonSerialize() {

        return $this->data;
    }

}
