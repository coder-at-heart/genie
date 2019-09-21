<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Plugins\ACF;
use Lnk7\Genie\Templates\Example;
use Lnk7\Genie\WordPressObjects\Page;
use Lnk7\Genie\WordPressObjects\Post;
use Lnk7\Genie\WordPressObjects\User;

/**
 * Class Genie
 * @package Lnk7\Genie
 */
class Genie {

    public static function Setup() {

        // We can't do anything without ACF
        if ( ACF::isDisabled() ) {
            return;
        }

        Session::Setup();
        WordPress::Setup();
        AjaxController::Setup();
        BackgroundJob::Setup();
        View::Setup();
        CacheBust::Setup();

        // Wordpress Objects
        Post::Setup();
        Page::Setup();
        User::Setup();

    }



    /**
     * Build the site variable. This is used in Javascript and twig.
     *
     * @return array
     */
    public static function getSiteVar() {

        $siteVar = [
            'urls' => [
                'theme' => get_stylesheet_directory_uri(),
                'ajax'  => admin_url( 'admin-ajax.php' ),
                'home'  => home_url(),
            ],
        ];

        return apply_filters( 'genie_get_site_var', $siteVar );
    }

}