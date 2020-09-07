<?php

namespace Lnk7\Genie\Utilities;

/**
 * Class CreateCustomPostType
 * A handy wrapper around register_post_type
 *
 * @package Lnk7\Genie\Utilities
 */
class CreateCustomPostType
{


    /**
     * see https://codex.wordpress.org/Function_Reference/register_post_type
     *
     * @var array
     */
    protected $definition = [
        'label'               => '',
        'labels'              => [
            "add_new"               => "Add New",
            "not_found"             => "Not found",
            "not_found_in_trash"    => "Not found in Trash",
            "featured_image"        => "Featured Image",
            "set_featured_image"    => "Set featured image",
            "remove_featured_image" => "Remove featured image",
            "use_featured_image"    => "Use as featured image",
        ],
        'description'         => '',
        'public'              => false,
        'menu_icon'           => '',
        'supports'            => ['title', 'thumbnail', 'editor'],
        'taxonomies'          => [],
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 20,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => false,
        'can_export'          => true,
        'has_archive'         => true,
        'hierarchical'        => false,
        'rewrite'             => true,
        'exclude_from_search' => true,
        'show_in_rest'        => true,
        'publicly_queryable'  => false,
        'query_var'           => '',
        'capability_type'     => 'post',

    ];


    /**
     * Post Type
     *
     * @var string
     */
    protected $postType;


    /**
     * CreateCustomPostType constructor.
     *
     * @param string $name Post Type Name
     */
    public function __construct(string $name)
    {
        $string = ConvertString::from($name);

        $this->postType = $string->toSingular()->toSlug();

        $singular = (string)$string->toTitleCase()->toSingular();
        $plural = (string)$string->toPlural();

        $this->setLabels([
            "name"                  => $plural,
            "singular_name"         => $singular,
            "menu_name"             => $plural,
            "name_admin_bar"        => $singular,
            "archives"              => "$plural Archives",
            "attributes"            => "$singular Attributes",
            "parent_item_colon"     => "Parent $singular:",
            "all_items"             => "All $plural",
            "add_new_item"          => "Add New $singular",
            "new_item"              => "New $singular",
            "edit_item"             => "Edit $singular",
            "update_item"           => "Update $singular",
            "view_item"             => "View $singular",
            "view_items"            => "View $plural",
            "search_items"          => "Search $singular",
            "insert_into_item"      => "Insert into $singular",
            "uploaded_to_this_item" => "Uploaded to this $singular",
            "items_list"            => "$plural list",
            "items_list_navigation" => "$plural list navigation",
            "filter_items_list"     => "Filter $plural list",
        ]);

        $this->set('label', $plural);
        $this->set('description', $name);
    }


    function setLabels(array $labels)
    {
        foreach ($labels as $label => $value) {
            $this->setLabel($label, $value);
        }

        return $this;
    }


    function setLabel($label, $name)
    {
        $this->definition['labels'][$label] = $name;

        return $this;
    }


    function set($attribute, $value)
    {
        $this->definition[$attribute] = $value;

        return $this;
    }


    /**
     * Constructor wrapper
     *
     * @param $name
     *
     * @return CreateCustomPostType
     */
    public static function called($name)
    {
        return new static($name);
    }


    function addSupportFor($for)
    {
        // Turn it into an array if it's not one
        $for = is_array($for) ? $for : [$for];

        $supports = array_merge($for, $this->definition['supports']);
        $this->set('supports', $supports);

        return $this;
    }


    function addTaxonomy($taxonomy)
    {
        $this->definition['taxonomies'][] = $taxonomy;
        return $this;
    }


    /**
     * Set up a custom post type to work only in the backend.
     *
     * @return $this
     */
    function backendOnly()
    {
        $this->set('rewrite', false);
        $this->set('query_var', false);
        $this->set('publicly_queryable', false);
        $this->set('public', false);

        return $this;
    }


    /**
     * Set up a custom post type to work in the front-end
     *
     * @return $this
     */
    function frontend()
    {
        $this->set('rewrite', true);
        $this->set('query_var', true);
        $this->set('publicly_queryable', true);
        $this->set('public', true);

        return $this;
    }


    function adminOnly()
    {
        $this->set('capabilities', [
            'edit_post'          => 'update_core',
            'read_post'          => 'update_core',
            'delete_post'        => 'update_core',
            'edit_posts'         => 'update_core',
            'edit_others_posts'  => 'update_core',
            'delete_posts'       => 'update_core',
            'publish_posts'      => 'update_core',
            'read_private_posts' => 'update_core',
        ]);
        return $this;
    }


    /**
     * Setup a custom post type so it's not seen anywhere.
     *
     * @return $this
     */
    function hidden()
    {
        $this->set('show_ui', false);
        $this->set('show_in_nav_menus', false);

        return $this;
    }


    function icon(string $icon)
    {
        $this->set('menu_icon', $icon);

        return $this;
    }


    /**
     * Register this custom post type
     *
     * @param int $sequence
     */
    function register($sequence = 20)
    {
        HookInto::action('init', $sequence)
            ->run(function () {
                register_post_type($this->postType, $this->definition);
            });
    }


    function removeSupportFor($for)
    {
        // Turn it into an array if it's not one
        $for = is_array($for) ? $for : [$for];

        foreach ($for as $support) {
            if (($key = array_search($support, $this->definition['supports'])) !== false) {
                unset($this->definition['supports'][$key]);
            }
        }

        return $this;
    }

}