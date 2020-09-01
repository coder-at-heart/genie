<?php

namespace Lnk7\Genie\Abstracts;

use JsonSerializable;
use Lnk7\Genie\Cache;
use Lnk7\Genie\Data\WordPress;
use Lnk7\Genie\Registry;
use Lnk7\Genie\Utilities\ConvertString;

/**
 * Class CustomPost
 * Abstract class for all Custom post types.
 *
 * @property int ID
 * @property string post_author
 * @property string post_date
 * @property string post_date_gmt
 * @property string post_content
 * @property string post_title
 * @property string post_excerpt
 * @property string post_status
 * @property string comment_status
 * @property string ping_status
 * @property string post_password
 * @property string post_name
 * @property string to_ping
 * @property string pinged
 * @property string post_modified
 * @property string post_modified_gmt
 * @property string post_content_filtered
 * @property int post_parent
 * @property string guid
 * @property int menu_order
 * @property string post_type
 * @property string post_mime_type
 * @property string comment_count
 * @property-read array featured_image
 * @property-read string permalink
 */
abstract class CustomPost implements JsonSerializable {

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
     *
     * @var bool
     */
    static $useGutenberg = false;

    /**
     * Used to store the object data
     *
     * @var array
     */
    var $data = [];



    /**
     * Return a new instance of the Object
     *
     * @param int|null $id
     */
    function __construct( $id = null ) {

        // new object ?
        if ( ! $id ) {
            $this->setDefaults();

            return;
        }

        $this->ID = $id;

        // try and load this object from cache
        if ( static::$cache ) {
            $this->data = get_post_meta( $this->ID, static::getCacheKey(), true );
            if ( ! empty( $this->data ) ) {
                return;
            }
        }

        // No cache or data? Let's rebuild this object
        $this->data = get_post( $this->ID, ARRAY_A );

        // no Post ?
        if ( ! $this->data ) {
            return;
        }

        $fields = get_fields( $this->ID );
        if ( $fields ) {
            foreach ( $fields as $field => $value ) {
                $this->$field = $value;
            }
        }

        // Does this post have a featured image?
        $attachmentID = get_post_thumbnail_id( $this->ID );
        if ( $attachmentID ) {

            $this->featured_image = [];
            $sizes                = get_intermediate_image_sizes();
            foreach ( $sizes as $size ) {
                $src                           = wp_get_attachment_image_src( $attachmentID, $size );
                $this->featured_image[ $size ] = (object) [
                    'url'     => $src[0],
                    'width'   => $src[1],
                    'height'  => $src[2],
                    'resized' => $src[3],
                ];
            }
        }

        // What about a link to this post?
        $this->permalink = get_permalink( $this->ID );

        if ( static::$cache ) {
            $this->beforeCache();
            update_post_meta( $this->ID, static::getCacheKey(), $this->data );
        }

    }



    /**
     */
    function setDefaults() {
        $this->post_status = 'publish';
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
     *  Update properties on this object
     */
    function beforeCache() {

    }



    /**
     * Setup Wordpress Hooks, filters and register necessary method calls.
     */
    public static function Setup() {

        /**
         * After the post is saved... allow some of wordpress fields to be overWritten
         */
        add_filter( 'wp_insert_post_data', function ( $data, $postArray ) {

            // Bail early if no data sent.
            if ( empty( $postArray['acf'] ) ) {
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
                    $value = $postArray['acf'][ $field['key'] ];
                    $field = $field['override'];
                    if ( is_callable( $field ) ) {
                        [ $field, $value ] = call_user_func( $field, $value );
                    }
                    if ( in_array( $field, WordPress::$postFields ) ) {
                        $data[ $field ] = $value;
                    }
                }
            }

            $data = static::override( $data, $postArray );

            return $data;
        }, 10, 2 );

        /**
         * Clear out cache on save
         */
        add_action( 'acf/save_post', function ( $post_id ) {
            global $post;

            if ( ! $post or $post->post_type != static::$postType ) {
                return;
            }

            // Clear Cache so it's generated next time
            if ( static::$cache ) {
                Cache::clearCache( $post_id );
            }

        }, 20 );

        /**
         * Hook for creating the custom Post Type and Schema
         */
        add_action( 'init', function () {
            static::init();
        }, 20 );

        /**
         * Should we use Gutenberg ?
         */
        add_filter( 'use_block_editor_for_post_type', function ( $current_status, $post_type ) {

            // Use your post type key instead of 'product'
            if ( $post_type === static::$postType ) {
                return static::$useGutenberg;
            }

            return $current_status;
        }, 10, 2 );
    }



    /**
     * get the field definitions
     *
     * @return mixed|null
     */
    public static function getFields() {
        return Registry::get( 'fields', static::class );

    }



    /**
     * ACF before the post is saved.
     * We can override some of the wordpress fields here.
     *
     * @param array $data
     * @param array $postArray
     *
     * @return array
     */
    public static function override( array $data, array $postArray ) {

        return $data;

    }



    /**
     * Code to instantiate the Custom Post Type.
     */
    public static function init() {

    }



    /**
     * Parse the schema and build a map of the field name / keys
     * We will use this later when saving data.
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
     * create an Object from an array.
     *
     * @param array $array
     *
     * @return static
     */

    public static function create( $array = [] ) {

        $object = new static();
        $object->fill( $array );
        $object->save();

        return $object;

    }



    /**
     * Fill data properties from an array
     *
     * @param $array
     */
    public function fill( $array ) {
        foreach ( $array as $field => $value ) {
            $this->$field = $value;
        }

    }



    /**
     * @return bool
     */
    function save() {

        $this->beforeSave();

        $this->checkValidity();

        $postFields = [
            'post_title'   => $this->post_title,
            'post_content' => $this->post_content,
            'post_type'    => static::$postType,
            'post_status'  => $this->post_status,
            'post_name'    => $this->post_name,
        ];

        if ( ! $this->ID ) {
            $this->ID = wp_insert_post( $postFields );
        } else {
            wp_update_post( [ 'ID' => $this->ID ] + $postFields );
        }

        $fields = static::getFields();

        foreach ( $fields as $field ) {

            if ( $field['displayOnly'] ) {
                continue;
            }

            if ( ! isset( $this->{$field['name']} ) ) {
                continue;
            };

            update_field( $field['key'], $this->{$field['name']}, $this->ID );

        }

        $this->clearCache();

        return $this->ID;

    }



    /**
     * Before save - Set defaults / fill values
     */
    public function beforeSave() {
    }



    /**
     * Check the validity of this object
     * Throw errors from here and catch from save
     */
    function checkValidity() {
    }



    /**
     * clear the cache for this post
     */
    function clearCache() {
        if ( $this->ID ) {
            Cache::clearCache( $this->ID );
        }
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
     * @return static
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

        return new static( $posts[0] );
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
     * @return bool|static
     */
    public static function getByTitle( $title ) {

        $posts = static::get( [
            'title'  => $title,
            'fields' => 'ids',
        ] );

        if ( ! $posts ) {
            return false;
        }

        return new static( $posts[0] );
    }



    /**
     * Useful for Templates
     *
     * @return static
     */
    public static function getCurrent() {

        return new static( get_the_ID() );
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
     * Get the full Schema Definition
     *
     * @return mixed|null
     */
    public static function getSchema() {
        return Registry::get( 'schemas', static::class );

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
                if ( $found ) {
                    return $found;
                }
            }
        }

        return false;
    }



    /**
     * magic getter
     *
     * @param $var
     *
     * @return mixed
     */
    function __get( $var ) {

        if ( ! is_array( $this->data ) || ! isset( $this->data[ $var ] ) ) {

            return false;
        }

        return $this->data[ $var ];
    }



    /**
     * magic set
     *
     * @param $var
     * @param $value
     */
    function __set( $var, $value ) {

        $this->data[ $var ] = $value;
    }



    /**
     * Needed from TWIG
     *
     * @param $var
     *
     * @return bool
     */
    function __isset( $var ) {
        return isset( $this->data[ $var ] );
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
     * Delete this post
     *
     * @param bool $force
     *
     * @return bool
     */
    function delete( $force = true ) {
        if ( $this->ID ) {
            $this->pretDelete( $this->ID );

            return wp_delete_post( $this->ID, $force );

        }

        return false;
    }



    /**
     * Things to do before delete!
     * Delete other objects / images etc.
     */
    function preDelete() {

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
