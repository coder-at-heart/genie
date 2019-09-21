<?php

namespace Lnk7\Genie;

/**
 * Class Theme
 *
 * If you extend this. Remember that any method here is callable from twig {{ theme.method() }}
 *
 * @package Lnk7\Genie
 */
class Theme {

    /**
     * Main Wordpress Hook for the Theme
     */
    public static function Setup() {

        add_filter( 'genie_view_before_render', static::class . '::genie_view_before_render', 10, 1 );

    }


    /**
     * Get the current version of the theme
     *
     * @return mixed
     */
    public static function getVersion() {

        $theme = wp_get_theme();

        return $theme->version;
    }



    /**
     * Add theme variables to the twig template
     *
     * @param $vars
     *
     * @return mixed
     */
    public static function genie_view_before_render( $vars ) {

        global $wp_scripts;

        $vars['theme'] = new static();
        $vars['_site'] = Genie::getSiteVar();

        return $vars;
    }




    /**
     * Super sexy function that allows any wordpress/php function to be called from twig
     *
     * Some of the function echo / print, in which case the return is redundant.
     *
     * @param $function
     * @param $arguments
     *
     * @return mixed
     */
    public function __call( $function, $arguments ) {

        if ( function_exists( $function ) ) {
            return call_user_func_array( $function, $arguments );
        }
    }

}