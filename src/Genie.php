<?php

namespace Lnk7\Genie;

use Lnk7\Genie\Plugins\ACF;

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

        /**
         * Main Wordpress Hook for the Theme
         */

        add_filter( 'genie_view_before_render', function ( $vars ) {

            $siteVar = [
                'urls' => [
                    'theme' => get_stylesheet_directory_uri(),
                    'ajax'  => admin_url( 'admin-ajax.php' ),
                    'home'  => home_url(),
                ],
            ];

            $vars['_site'] = apply_filters( 'genie_get_site_var', $siteVar );

            return $vars;
        }, 10, 1 );

        do_action('genie_setup');

    }

}