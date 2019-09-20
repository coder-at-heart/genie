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
 * @package Genie
 */

class View {

	static $twig;



	public static function Setup() {

		add_action( 'init', static::class . '::init', 1 );
		add_shortcode( 'view', static::class . '::view' );
	}



	public static function init() {

		$debug = WP_DEBUG;
		$cache = ! WP_DEBUG;

		$pathArray = apply_filters( 'genie_view_folders', [
			plugin_dir_path( __FILE__ ) . 'Tables',
		] );

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

		$filter = new TwigFilter( 'slashes', Tools::class . '::addSlashes' );
		$twig->addFilter( $filter );

		$filter = new TwigFilter( 'wpautop', 'wpautop' );
		$twig->addFilter( $filter );

		$filter = new TwigFilter( 'bytes', Tools::class . '::formatBytes' );
		$twig->addFilter( $filter );

		self::$twig = $twig;
	}



	public static function make( $view, $vars = [] ) {

		if ( is_object( $vars ) ) {
			$vars = (array) $vars;
		}

		if ( ! isset( $vars['_objects'] ) ) {
			$vars['_objects'] = [];
		}

		$vars = apply_filters( 'genie_view_before_render', $vars );
		$html = self::$twig->render( $view, $vars );

		return do_shortcode( $html );
	}



	public static function view( $attributes ) {

		$a = (object) shortcode_atts( [
			'view' => '',
		], $attributes );

		if ( ! $a->view ) {
			$a->view = $attributes[0];
		}

		$a->wpautop = ( $a->wpautop == 'yes' ) ? true : false;

		return self::make( $a->view . '.twig', [ 'attributes' => $a ] );
	}



	private static function getCacheFolder() {

		$upload     = wp_upload_dir();
		$upload_dir = $upload['basedir'];

		return $upload_dir . '/twig_cache';
	}

}