<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Plugins\ACF;
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
        Ajax::Setup();
        BackgroundJob::Setup();
        View::Setup();
        CacheBust::Setup();
        Theme::Setup();

        // Wordpress Objects
        Post::Setup();
        Page::Setup();
        User::Setup();


    }

}