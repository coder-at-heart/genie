<?php

namespace Lnk7\Genie\Abstracts;

use Illuminate\Support\Collection;
use JsonSerializable;
use Lnk7\Genie\Cache;
use Lnk7\Genie\Exception\GenieNotFoundException;
use Lnk7\Genie\Registry;
use Lnk7\Genie\Utilities\ConvertString;
use Lnk7\Genie\WordPress;

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
abstract class CustomPost implements JsonSerializable
{

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
    protected $data = [];


    /**
     * used as a story of loaded data
     *
     * @var array
     */
    protected $originalData = [];



    /**
     * Return a new instance of the Object
     *
     * @param int|null $id
     *
     * @throws GenieNotFoundException
     */
    function __construct($id = null)
    {

        // new object ?
        if (!$id) {
            $this->setDefaults();

            return;
        }

        $this->ID = $id;

        // try and load this object from cache
        if (static::$cache) {
            $this->data = get_post_meta($this->ID, static::getCacheKey(), true);
        }

        // No data?  let's build it.
        if (empty($this->data)) {

            $postData = get_post($this->ID, ARRAY_A);

            // No Data ?
            if (!$postData) {
                throw new GenieNotFoundException("Could not find a " . static::$plural . " with an ID of " . $id);
            }

            $this->fill($postData);

            $acfFields = get_fields($this->ID);
            if ($acfFields) {
                foreach ($acfFields as $field => $value) {
                    $this->data[$field] = $value;
                }
            }

            if (static::$cache) {
                $this->beforeCache();
                update_post_meta($this->ID, static::getCacheKey(), $this->data);
            }
        }

        $this->originalData = $this->data;

    }



    /**
     * Set defaults for this object
     */
    public function setDefaults()
    {
        $this->post_status = 'publish';
        $this->post_type = static::$postType;
    }



    /**
     * Cache key used for this post_type
     *
     * @return string
     */
    protected static function getCacheKey()
    {
        return Cache::getCachePrefix() . '_object';
    }



    /**
     * Fill data properties from an array
     *
     * @param $array
     */
    public function fill($array)
    {
        foreach ($array as $field => $value) {
            $this->$field = $value;
        }
    }



    /**
     *  Update properties on this object
     */
    public function beforeCache()
    {

    }



    /**
     * Setup Wordpress Hooks, filters and register necessary method calls.
     */
    public static function Setup()
    {

        /**
         * After the post is saved... allow some of wordpress fields to be overWritten
         */
        add_filter('wp_insert_post_data', function ($data, $postArray) {

            // Make sure we have acf data
            if (empty($postArray['acf'])) {
                return $data;
            }

            // Not this post type?
            $postType = $data['post_type'];
            if ($postType != static::$postType) {
                return $data;
            }

            $fields = static::getFields();

            foreach ($fields as $field) {
                if ($field['override']) {
                    $value = $postArray['acf'][$field['key']];
                    $field = $field['override'];
                    if (is_callable($field)) {
                        [$field, $value] = call_user_func($field, $value);
                    }
                    if (in_array($field, WordPress::$postFields)) {
                        $data[$field] = $value;
                    }
                }
            }

            $data = static::override($data, $postArray);

            return $data;
        }, 10, 2);

        /**
         * Clear out cache on save
         */
        add_action('acf/save_post', function ($post_id) {
            global $post;

            if (!$post or $post->post_type != static::$postType) {
                return;
            }

            // Clear Cache so it's generated next time
            if (static::$cache) {
                Cache::clearCache($post_id);
            }

        }, 20);

        /**
         * Hook for creating the custom Post Type and Schema
         */
        add_action('init', function () {
            static::init();
        }, 20);

        /**
         * Should we use Gutenberg ?
         */
        add_filter('use_block_editor_for_post_type', function ($currentStatus, $postType) {

            // Use your post type key instead of 'product'
            if ($postType === static::$postType) {
                return static::$useGutenberg;
            }

            return $currentStatus;
        }, 10, 2);
    }



    /**
     * Get the field definitions from the registry
     *
     * @return mixed|null
     */
    public static function getFields()
    {
        return Registry::get('fields', static::class);

    }



    /**
     * Capture and use ACF before the post is saved.
     * We can override some of the wordpress fields here.
     *
     * @param array $data
     * @param array $postArray
     *
     * @return array
     */
    public static function override(array $data, array $postArray)
    {

        return $data;

    }



    /**
     * Code to instantiate the Custom Post Type.
     * Create Posts and schemas here
     */
    public static function init()
    {

    }



    /**
     * Parse the schema and build a map of the field name / keys
     * We will use this later when saving data.
     * Problem with setting static::$schema here so we use the registry instead.
     * https://stackoverflow.com/questions/4577187/php-5-3-late-static-binding-doesnt-work-for-properties-when-defined-in-parent
     * This is called from CreateSchema
     *
     * @param $schema
     */
    public static function attachSchema($schema)
    {

        $fields = Registry::get('fields');
        $schemas = Registry::get('schemas');

        if (!$fields) {
            $fields = [];
        }
        if (!$schemas) {
            $schemas = [];
        }

        $class = static::class;

        // modify the schema so we get index arrays
        $level1Fields = [];
        foreach ($schema['fields'] as $field) {
            $level1Fields[$field['name']] = $field;
        }

        $fields[$class] = $level1Fields;
        $schemas[$class] = $schema;

        Registry::set('fields', $fields);
        Registry::set('schemas', $schemas);
    }



    /**
     * Create an Object from an array of key value pairs..
     *
     * @param array $array
     *
     * @return static
     */

    public static function create($array = [])
    {

        $object = new static();
        $object->setDefaults();
        $object->fill($array);
        $object->save();

        return $object;

    }



    /**
     * Save the custom post type
     *
     * @return int
     */
    public function save()
    {

        $this->beforeSave();

        $this->checkValidity();

        if (!$this->isDirty()) {
            return $this->ID;
        }

        $postFields = [];
        foreach (WordPress::$postFields as $field) {
            if ($this->$field) {
                $postFields[$field] = $this->$field;
            }
        }

        wp_insert_post($postFields);

        $fields = static::getFields();

        foreach ($fields as $field) {

            if ($field['displayOnly']) {
                continue;
            }

            $name = $field['name'];
            $key = $field['key'];

            if (!array_key_exists($name, $this->data)) {
                continue;
            }

            // Only update if we need to.
            if (!array_key_exists($name, $this->originalData) || $this->originalData[$name] !== $this->data[$name]) {
                update_field($key, $this->data[$name], $this->ID);
            }

        }

        $this->clearCache();

        return $this->ID;

    }



    /**
     * Before save - Set defaults / fill values
     */
    public function beforeSave()
    {
    }



    /**
     * Check the validity of this object
     * Throw errors from here and catch from save
     */
    public function checkValidity()
    {
    }



    /**
     * Check if the post needs saving
     *
     * @return bool
     */
    public function isDirty()
    {
        return $this->data !== $this->originalData;
    }



    /**
     * clear the cache for this post
     */
    public function clearCache()
    {
        if ($this->ID) {
            Cache::clearCache($this->ID);
        }
    }



    /**
     * Return a new instance of this class
     *
     * @param $id
     *
     * @return static
     * @throws GenieNotFoundException
     */
    public static function getById($id)
    {
        return new static($id);
    }



    /**
     * Find a post by it's slug
     *
     * @param $slug
     *
     * @return bool|mixed
     * @throws GenieNotFoundException
     */
    public static function getBySlug($slug)
    {

        $objects = static::get([
            'name'        => $slug,
            'post_status' => 'any',
            'fields'      => 'ids',
        ]);

        if ($objects->isEmpty()) {
            return false;
        }

        return $objects->first();
    }



    /**
     * Wrapper around get_posts. Returns an array of Objects
     *
     * @param array $params
     *
     * @return Collection
     * @throws GenieNotFoundException
     */
    public static function get(array $params = [])
    {

        $defaultArgs = [
            'numberposts' => -1,
            'orderby'     => 'date',
            'order'       => 'DESC',
            'post_type'   => static::$postType,
            'post_status' => 'publish',
            'fields'      => 'ids',
        ];

        $defaultArgs = apply_filters('genie_' . static::$postType . '_get_args', $defaultArgs);
        $posts = get_posts(array_merge($defaultArgs, $params));
        $collection = new Collection();
        foreach ($posts as $id) {
            $collection->add(new static($id));
        }

        return $collection;
    }



    /**
     * Get All posts based on a Taxonomy Name
     *
     * @param $name
     * @param $taxonomy
     *
     * @return Collection
     * @throws GenieNotFoundException
     */
    public static function getByTaxonomyName($name, $taxonomy)
    {

        $term = get_term_by('name', $name, $taxonomy);

        return static::get([
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => [$term->term_id],
                ],
            ],
        ]);
    }



    /**
     * Find a post by it's title
     *
     * @param $title
     *
     * @return bool|static
     * @throws GenieNotFoundException
     */
    public static function getByTitle($title)
    {

        $objects = static::get([
            'title'  => $title,
            'fields' => 'ids',
        ]);

        if ($objects->isEmpty()) {
            return false;
        }

        return $objects->first();
    }



    /**
     * Useful for Templates
     *
     * @return static
     * @throws GenieNotFoundException
     */
    public static function getCurrent()
    {
        return new static(get_the_ID());
    }



    /**
     * get the Plural name of the post type
     *
     * @return string
     */
    public static function getPlural()
    {
        return static::$plural ?? ConvertString::From(static::$postType)->toPlural()->toTitleCase()->return();
    }



    public static function getSingular()
    {
        return static::$singular ?? ConvertString::From(static::$postType)->toSingular()->toTitleCase()->return();
    }



    /**
     * Get the full Schema Definition
     *
     * @return mixed|null
     */
    public static function getSchema()
    {
        return Registry::get('schemas', static::class);
    }



    /**
     * Look through this custom post's schema and return the key for a field.
     * This allows us use the key when creating new data with update_field.
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getKey(string $name)
    {

        $schema = Registry::get('schemas', static::class);

        return static::findKey($name, $schema['fields']);
    }



    /**
     * Recursive function to parse fields map looking for $name
     *
     * @param $name
     * @param $fields
     *
     * @return mixed
     */
    protected static function findKey($name, $fields)
    {

        foreach ($fields as $field) {
            if ($field['name'] === $name) {
                return $field['key'];
            }
            if (isset($field['sub_fields'])) {
                $found = static::findKey($name, $field['sub_fields']);
                if ($found) {
                    return $found;
                }
            }
        }

        return false;
    }



    public function featuredImage()
    {
        // Does this post have a featured image?
        $attachmentID = get_post_thumbnail_id($this->ID);

        if (!$attachmentID) {
            return false;
        }
        $images = [];

        $sizes = get_intermediate_image_sizes();
        foreach ($sizes as $size) {
            $src = wp_get_attachment_image_src($attachmentID, $size);
            $images[$size] = (object)[
                'url'     => $src[0],
                'width'   => $src[1],
                'height'  => $src[2],
                'resized' => $src[3],
            ];
        }

        return $images;

    }



    public function permalink()
    {
        return get_permalink($this->ID);
    }



    /**
     * magic getter
     *
     * @param $var
     *
     * @return mixed
     */
    public function __get($var)
    {

        if (array_key_exists($var, $this->data)) {
            return $this->data[$var];
        }

        return false;

    }



    /**
     * magic set
     *
     * @param $var
     * @param $value
     */
    public function __set($var, $value)
    {
        $this->data[$var] = $value;
    }



    /**
     * Needed from twig templates
     *
     * @param $var
     *
     * @return bool
     */
    public function __isset($var)
    {
        return isset($this->data[$var]);
    }



    /**
     * Return all data for this post
     *
     * @return mixed|void
     */
    public function getData()
    {
        return $this->data;
    }



    /**
     * Delete this post
     *
     * @param bool $force
     *
     * @return bool
     */
    public function delete($force = true)
    {
        if ($this->ID) {
            $this->preDelete();
            return wp_delete_post($this->ID, $force);
        }
        return false;
    }



    /**
     * Things to do before delete!
     * Delete other objects / images etc.
     */
    public function preDelete()
    {

    }



    /**
     * What should we serialize when json_encode is called on the object
     *
     * @return mixed|void
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

}