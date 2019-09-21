<?php

namespace Lnk7\Genie\Abstracts;

use Lnk7\Genie\View;
use Lnk7\Genie\WordPressObjects\Page;
use Lnk7\Genie\WordPressObjects\User;

abstract class Template {

    /**
     * twig template to use.
     *
     * @var string
     */
    protected static $view = '';

    /**
     * The template path and file name
     *
     * @var string
     */
    protected static $template = '';

    /**
     * variables we need to pass into twig
     *
     * @var array
     */
    protected static $vars = [];



    public static function Setup() {

    }



    public static function Display() {
        static::addVar( 'page', Page::getCurrent() );
        if ( is_user_logged_in() ) {
            static::addVar( '_user', User::getCurrent() );
        }

        // Do any other processing
        static::process();

        echo static::Render();

    }



    /**
     * Add a variable to be sent to twig
     *
     * @param $var
     * @param $value
     *
     * @return $this
     */
    protected static function addVar( $var, $value ) {

        static::$vars[ $var ] = $value;

    }



    /**
     * How do we display this template?
     *
     */
    protected static function process() {

    }



    /**
     * render the template
     *
     * @return string
     */
    protected static function render() {

        return View::make( static::$view, static::$vars );
    }



    protected static function getTemplate() {
        return static::$template;
    }



    /**
     * Add variables to be sent to twig
     *
     * @param $fields
     *
     */
    protected static function addVars( $fields ) {

        static::$vars = array_merge( static::$vars, $fields );

    }

}