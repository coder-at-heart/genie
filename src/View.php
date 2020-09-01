<?php

namespace Lnk7\Genie;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

/**
 * Class View
 *
 * Wrapper around twig
 *
 * @package Lnk7\Genie
 */
class View {

    /**
     * Twig Object
     *
     * @var
     */
    static $twig;

    /**
     * Vars used ina  template
     */
    var $vars = [];

    /**
     * The twig template. This could be a filename or a string.
     *
     * @var
     */
    var $template;

    var $processShortcodes = true;

    private $templateType = 'file';



    /**
     * View constructor.
     *
     * @param $template
     */
    function __construct( $template ) {
        $this->template = $template;
    }



    public static function Setup() {

        add_action( 'init', function () {

            $debug = WP_DEBUG;
            $cache = ! WP_DEBUG;

            $pathArray = apply_filters( 'genie_view_folders', [] );

            $fileLoader = new FilesystemLoader( $pathArray );
            $loader     = new ChainLoader( [ $fileLoader ] );

            $configArray = [
                'autoescape'  => false,
                'auto_reload' => true,
            ];

            if ( $debug ) {
                $configArray['debug'] = true;
            }
            if ( $cache ) {
                $configArray['cache'] = static::getCacheFolder();
            }

            $twig = new Environment( $loader, $configArray );

            if ( $debug ) {
                $twig->addExtension( new DebugExtension() );
            }
            $filter = new TwigFilter( 'json', Tools::class . '::jsonSafe' );
            $twig->addFilter( $filter );

            $filter = new TwigFilter( 'wpautop', 'wpautop' );
            $twig->addFilter( $filter );

            self::$twig = apply_filters( 'genie_twig_init', $twig );;
        }, 1 );

        add_shortcode( 'genie_view', function ( $attributes, $content ) {
            $a = (object) shortcode_atts( [
                'view' => '',
            ], $attributes );

            if ( ! $a->view ) {
                $a->view = $attributes[0] ?? $content;
            }

            return static::with( $a->view )
                         ->addVars( $attributes )
                         ->render();

        } );
    }



    /**
     * Get cache folder for Twig
     *
     * @return string
     */
    private static function getCacheFolder() {

        $upload     = wp_upload_dir();
        $upload_dir = $upload['basedir'];

        return $upload_dir . '/twig_cache';
    }



    public function render() {

        $vars = apply_filters( 'genie_view_before_render', $this->vars );

        if ( $this->templateType === 'string' ) {
            $template = View::$twig->createTemplate( $this->template );
            $html     = $template->render( $vars );
        } else {
            $html = View::$twig->render( $this->template, $vars );
        }

        if ( $this->processShortcodes ) {
            $html = do_shortcode( $html );
        }

        return $html;

    }



    /**
     *
     * Add variables to the twig template
     *
     * @param $fields
     *
     * @return $this
     */
    public function addVars( $fields ) {

        $this->vars = array_merge( $this->vars, $fields );

        return $this;

    }



    /**
     * which template to use?
     *
     * This could be a file or a string
     *
     * @param $template
     *
     * @return static
     */
    public static function with( $template ) {

        $type               = substr( strtolower( $template ), - 5 ) === '.twig' ? 'file' : 'string';
        $view               = new static( $template );
        $view->templateType = $type;

        return $view;
    }



    /**
     * Enabled shortcode on this template
     *
     * @return $this
     */
    function enableShortcodes() {
        $this->processShortcodes = true;

        return $this;
    }



    /**
     * do not process shortcodes
     *
     * @return $this
     */

    function disableShortcodes() {
        $this->processShortcodes = false;

        return $this;
    }



    /**
     * Add a variable to be sent to twig
     *
     * @param $var
     * @param $value
     *
     * @return $this
     */
    public function addVar( $var, $value ) {

        $this->vars[ $var ] = $value;

        return $this;

    }



    /**
     * Output the view rather than return it.
     */
    public function display() {
        echo $this->render();
    }

}