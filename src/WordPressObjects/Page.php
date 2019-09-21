<?php

namespace Lnk7\Genie\WordPressObjects;

use Lnk7\Genie\Abstracts\WordPressObject;

/**
 * Class Page
 * @package Lnk7\Genie\WordPressObjects
 */
class Page extends WordPressObject {

    /**
     * Post Type Slug
     * @var string
     */
    static $postType = 'page';


    /**
     * Get first Page that uses a template
     *
     * @param $template
     *
     * @return bool|Page
     */
    public static function findByTemplate( $template ) {

        $posts = get_posts( [
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'   => '_wp_page_template',
                    'value' => $template,
                ],
            ],
        ] );

        if ( ! $posts ) {
            return false;
        }

        return new static( $posts[0]->ID );

    }
}