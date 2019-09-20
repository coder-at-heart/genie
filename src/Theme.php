<?php

namespace Lnk7\Genie;

use DateTime;
use Lnk7\Genie\Utilities\CreateMenu;
use Lnk7\Genie\Utilities\CreateMenuItem;
use Lnk7\Genie\WordPressObjects\Employee;
use Lnk7\Genie\WordPressObjects\Section;
use WP_Scripts;

/**
 * Class Theme
 * @package Genie
 */

class Theme {

	/**
	 * Main Wordpress Hook for the Theme
	 */
	public static function Setup() {

		add_action( 'wp_enqueue_scripts', static::class . '::wp_enqueue_scripts' );

		add_action( 'init', static::class . '::init' );
		add_action( 'after_setup_theme', static::class . '::after_setup_theme' );
		add_action( 'admin_enqueue_scripts', static::class . '::admin_enqueue_scripts' );
		add_filter( 'tiny_mce_plugins', static::class . '::tiny_mce_plugins' );
		add_filter( 'genie_view_folders', static::class . '::genie_view_folders', 10, 1 );
		add_filter( 'genie_view_before_render', static::class . '::genie_view_before_render', 10, 1 );
		add_filter( 'wp_default_scripts', static::class . '::wp_default_scripts' );
		add_action( 'wp_head', static::class . '::theme_wp_head' );
		add_filter( 'show_admin_bar', '__return_false' );
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
		add_action( 'parse_request', static::class . '::parse_request' );
		add_action( 'admin_init', static::class . '::admin_init' );
		add_action( 'admin_bar_menu', static::class . '::admin_bar_menu' );
		add_filter( 'excerpt_length', static::class . '::excerpt_length', 999 );
		add_filter( 'excerpt_more', static::class . '::excerpt_more' );
		add_shortcode( 'gallery', static::class . '::gallery' );
		add_filter( 'media_view_settings', static::class . '::media_view_settings' );
		add_filter( 'comments_array', static::class . '::comments_array', 0 );
		add_action( 'admin_menu', static::class . '::admin_menu' , 100 );
		add_filter( 'comments_open', '__return_false', 20, 2 );
		add_filter( 'pings_open', '__return_false', 20, 2 );

		add_filter( 'script_loader_src', static::class . '::filter_cache_busting_file_src' );
		add_filter( 'style_loader_src', static::class . '::filter_cache_busting_file_src' );

		add_action( 'enqueue_block_editor_assets', static::class . '::enqueue_block_editor_assets' );

		add_filter( 'query_vars', static::class . '::query_vars' );

		// Add a capability to edit theme options
		$role_object = get_role( 'editor' );
		$role_object->add_cap( 'edit_theme_options' );

		// Add our Image Size
		//TODO: 2000.. WOW ?
		add_image_size( 'banner', 2000 );

		$role = get_role( 'editor' );
		$role->add_cap( 'unfiltered_upload' );

	}



	/**
	 * Remove comments links from admin bar
	 *
	 * @return void
	 */
	public static function admin_bar_menu() {

		if ( is_admin_bar_showing() ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		}
	}



	function admin_menu(){
		// get current login user's role
		$roles = wp_get_current_user()->roles;

		// test role
		if( !in_array('editor',$roles)){
			return;
		}

		//remove menu from site backend.
		remove_menu_page( 'edit-comments.php' ); //Comments
		remove_menu_page( 'themes.php' ); //Appearance
		remove_menu_page( 'plugins.php' ); //Plugins
		remove_menu_page( 'users.php' ); //Users
		remove_menu_page( 'tools.php' ); //Tools
		remove_menu_page( 'options-general.php' ); //Settings
	}



	public static function admin_enqueue_scripts() {

		wp_enqueue_media();

		wp_enqueue_style( 'fontawesome-icons', get_stylesheet_directory_uri() . '/dist/vendors/global/vendors.bundle.css' );

	}



	/**
	 * Redirect any user trying to access comments page
	 *
	 * @return void
	 */
	public static function admin_init() {

		global $pagenow;
		if ( 'edit-comments.php' === $pagenow ) {
			wp_safe_redirect( admin_url() );
			exit;
		}
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	}



	public static function after_setup_theme() {

		add_theme_support( 'menus' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', [ 'search-form' ] );

		register_nav_menus(
			[
				'main_menu'   => 'Main Menu',
				'footer_menu' => 'Footer Menu',
			]
		);

	}



	//TODO? Not sure this is needed



	/**#
	 * TWK function
	 *
	 * @param $size
	 */

	public static function background_image( $size ) {

		$post_id = get_the_ID();

		$thumbnail_id = get_post_thumbnail_id( $post_id );

		if ( $thumbnail_id ):
			$thumb_url_array = wp_get_attachment_image_src( $thumbnail_id, $size, true );
			$image_url       = $thumb_url_array[0];
			$image_alt       = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );

		else:
			$image     = get_field( 'featured_image', 'options' );
			$image_url = $image['sizes'][ $size ];
			$image_alt = $image['alt'];
		endif;

		echo 'style="background-image: url(\'' . $image_url . '\');" aria-label="' . $image_alt . '"';
	}



	/**
	 * Updates the count for comments and trackbacks
	 * Remove Trackbacks From Comments v1.1, by Weblog Tools Collection.
	 * http://weblogtoolscollection.com/archives/2008/03/08/managing-trackbacks-and-pingbacks-in-your-wordpress-theme/
	 *
	 * @param array $comms Array of comments supplied to the comments template.
	 *
	 * @return array $comments
	 *
	 * TODO: Not used I'm sure.
	 */
	public static function comments_array( $comms ) {

		global $comments, $trackbacks;

		$comments = array_filter( $comms, function ( $var ) {

			if ( 'trackback' === $var->comment_type || 'pingback' === $var->comment_type ) {
				return false;
			}

			return true;

		} );

		return $comments;
	}



	public static function enqueue_block_editor_assets() {

		// Load the theme styles within Gutenberg.
		wp_enqueue_style( 'vitol-gutenberg', get_theme_file_uri( '/theme-editor.css' ), false );
	}



	/**
	 * TWK Excerpt
	 *
	 * @param $limit
	 *
	 * @return array|string|string[]|null
	 */
	public static function excerpt( $limit ) {

		$excerpt = explode( ' ', get_the_excerpt(), $limit );

		if ( count( $excerpt ) >= $limit ) {
			array_pop( $excerpt );
			$excerpt = implode( ' ', $excerpt ) . '...';
		} else {
			$excerpt = implode( ' ', $excerpt );
		}

		$excerpt = preg_replace( '`[[^]]*]`', '', $excerpt );

		return $excerpt;
	}



	/**
	 * Changing excerpt length
	 *
	 * @param integer $length Lenght of the excerpt.
	 *
	 * @return integer $length New excerpt length.
	 */
	public static function excerpt_length( $length ) {

		return 22;
	}



	/**
	 * Changing excerpt more
	 *
	 * @param String $more The string shown within the more link.
	 *
	 * @return String
	 */
	public static function excerpt_more( $more ) {

		return '...';
	}



	/**
	 * Append the date and time at the end of enqueued scripts / styles so we can cache bust !
	 *
	 * @param string $src
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function filter_cache_busting_file_src( $src = '' ) {

		global $wp_scripts;

		// If $wp_scripts hasn't been initialized then bail.
		if ( ! $wp_scripts instanceof WP_Scripts ) {
			return $src;
		}

		// Check if script lives on this domain. Can't rewrite external scripts, they won't work.
		$base_url = apply_filters( 'cache_busting_path_base_url', $wp_scripts->base_url, $src );
		if ( ! strstr( $src, $base_url ) ) {
			return $src;
		}

		// Remove the 'ver' query var: ?ver=0.1
		$src   = remove_query_arg( 'ver', $src );
		$regex = '/' . preg_quote( $base_url, '/' ) . '/';
		$path  = preg_replace( $regex, '', $src );
		$file  = null;

		// If the folder starts with wp- then we can figure out where it lives on the filesystem
		if ( strstr( $path, '/wp-' ) ) {
			$file = untrailingslashit( ABSPATH ) . $path;
		}
		if ( ! file_exists( $file ) ) {
			return $src;
		}
		$time_format   = apply_filters( 'cache_busting_path_time_format', 'Y-m-d_G-i' );
		$modified_time = filemtime( $file );
		$dt            = new DateTime( '@' . $modified_time );
		$time          = $dt->format( $time_format );
		$src           = add_query_arg( 'ver', $time, $src );

		return $src;
	}



	/**
	 * Custom WordPress gallery code, remove dt, dl - add to ul li instead.
	 * This means user won't be able to set number of items per row, but can be more customised and responsive.
	 *
	 * @param array  $attr    An associative array of attributes, or an empty string if no attributes are given.
	 * @param string $content The enclosed content (if the shortcode is used in its enclosing form).
	 *
	 * @return shortcode
	 *
	 * TODO: Are we using this????
	 */
	public static function gallery( $attr = [], $content = '' ) {

		$attr['itemtag']    = 'li';
		$attr['icontag']    = '';
		$attr['captiontag'] = 'p';

		// Run the native gallery shortcode callback.
		$html = gallery_shortcode( $attr );

		// Remove all tags except a, img,li, p.
		$html = strip_tags( $html, '<a><img><li><p><style>' );

		// Some trivial replacements.
		$from = [
			"class='gallery-item'",
			"class='gallery-icon landscape'",
			'class="attachment-thumbnail"',
			'a href=',
		];
		$to   = [
			'',
			'',
			'class="tnggallery"',
			'a class="lightbox" href=',
		];
		$html = str_replace( $from, $to, $html );

		// Remove width/height attributes.
		$html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );

		// Wrap the output in ul tags.
		$html = sprintf( '<ul class="gallery">%s</ul>', $html );

		return $html;
	}



	/**
	 * Generate a menu for the themes
	 *
	 * @param string $menu
	 *
	 * @return array|string
	 */

	public static function generateMenu( string $menuName ) {

		$contentTypes = Section::getAllContentTypes();

		$items = wp_get_nav_menu_items( $menuName );

		$menu = CreateMenu::Called( $menuName )
			->addItem( 0, CreateMenuItem::Called( 'top' ) );

		$menuItemID = 100;

		foreach ( $items as $item ) {

			$fields = get_fields( $item->ID );
			foreach ( $fields as $key => $value ) {
				$item->$key = $value;
			}

			$parent = $item->menu_item_parent;

			// let's go through each item and handle it based on it's type
			switch ( $item->object ) {

				case 'section':
					$section = new Section( $item->object_id );

					$menu->addItem(
						$item->ID,
						CreateMenuItem::Called( $section->post_title )
							->icon( $section->icon )
							->sectionStart( $item->section_start )
							->sectionName( $item->section_name )
							->parent( $parent )
					);
					$menu->addChild( $parent, $item->ID );

					foreach ( $contentTypes as $type => $class ) {

						if ( in_array( $type, $section->content_types ) ) {

							if (!$class::showInNavMenu($section)) {
								continue;
							}

							$url = $section->permalink . '/' . $class::getNavLink() . '/';

							$menu->addItem(
								$menuItemID,
								CreateMenuItem::Called( $class::getNavMenu() )
									->url( $url )
									->parent( $item->ID )
							);

							$menu->addChild( $item->ID, $menuItemID );
							$menuItemID ++;

						}

					}

					if($section->links) {
						foreach($section->links as $link) {
							$menu->addItem(
								$menuItemID,
								CreateMenuItem::Called( $link['text'] )
									->newWindow($link['new_window'])
									->url( $link['url'] )
									->parent( $item->ID )
							);

							$menu->addChild( $item->ID, $menuItemID );
							$menuItemID ++;

						}
					}

					break;

				/**
				 * Normal menu Item
				 */
				default:
					$menu->addItem( $item->ID, CreateMenuItem::Called( $item->title )
						->url( $item->url )
						->icon( $item->icon )
						->parent( $parent )
						->sectionStart( $item->section_start )
						->sectionName( $item->section_name )
					);
					$menu->addChild( $parent, $item->ID );
					break;
			}

		}

		$menu->calculateActive();

		return $menu;
	}



	/**
	 * Wrapper for twig
	 * {{ theme.getAllContentTypes() }}
	 *
	 * @return array
	 */
	static function getAllContentTypes() {

		return Section::getAllContentTypes();
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
	 * Wordpress Init Hook
	 *
	 */
	public static function init() {

		// Do some cleaning up !
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'start_post_rel_link' );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link' );

		// Disable Emojis
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

		//Remove wp_autop
		// we can handle this in the theme   {{ theme.wpautop() }}
		remove_filter( 'the_content', 'wpautop' );

		add_filter( 'sharing_services_email', '__return_true' );
	}



	/**
	 * Change default link in gallery settings to Media file instead of Attachment page
	 * Change default size in gallery settings to Medium instead of Thumbnail
	 *
	 * @param array $settings List of media view settings.
	 *
	 * @return array $settings
	 *
	 * TODO:  Why ?
	 */
	public static function media_view_settings( $settings ) {

		$settings['galleryDefaults']['link'] = 'file';
		$settings['galleryDefaults']['size'] = 'medium';

		return $settings;
	}



	/**
	 * Disable the author archive page.
	 *
	 * @param object $query WordPress query object.
	 *
	 * @return object $query
	 *
	 * TODO:  This is not right!
	 */
	public static function parse_request( $query ) {

		if ( isset( $query->query_vars['author'] ) ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit;
		}

		return $query;
	}



	public static function query_vars( $vars ) {

		$vars[] = "search";
		$vars[] = "department_id";
		$vars[] = "office_id";

		return $vars;
	}



	/**
	 * Dump the global site var
	 */
	public static function theme_wp_head() {

		echo '<script type=\'text/javascript\'> var site = ' . json_encode( static::getSiteVar() ) . '</script>';
	}



	public static function tiny_mce_plugins( $plugins ) {

		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, [ 'wpemoji' ] );
		} else {
			return [];
		}
	}



	public static function genie_view_before_render( $vars ) {

		global $wp_scripts;

		$vars['theme']    = new static();
		$vars['_site']    = static::getSiteVar();
		$vars['_scripts'] = $wp_scripts;

		return $vars;
	}



	public static function genie_view_folders( $folders ) {

		$folders[] = get_stylesheet_directory() . '/views';

		return $folders;
	}



	public static function wp_default_scripts( &$scripts ) {

		if ( ! is_admin() ) {
			$scripts->add( 'jquery', false, [ 'jquery-core' ], '1.2.1' );
		}
	}



	/**
	 * Load CSS and Javascript files
	 */
	public static function wp_enqueue_scripts() {

		wp_enqueue_script( 'vendors', get_stylesheet_directory_uri() . '/dist/vendors/global/vendors.bundle.js' );
		wp_enqueue_script( 'bundle', get_stylesheet_directory_uri() . '/dist/js/vitol/scripts.bundle.js' );

		wp_enqueue_script( 'vue', get_stylesheet_directory_uri() . '/dist/js/vue.js', '', '', true );

		// Global Theme Styles(used by all pages)
		wp_enqueue_style( 'vendors-css', get_stylesheet_directory_uri() . '/dist/vendors/global/vendors.bundle.css' );

		wp_enqueue_style( 'bundle-css', get_stylesheet_directory_uri() . '/dist/css/vitol/style.bundle.css' );

		//  Layout Skins(used by all pages)
		wp_enqueue_style( 'skins-css', get_stylesheet_directory_uri() . '/dist/css/vitol/skins/aside/brand.css' );

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



	/**
	 * Build the site variables
	 *
	 * @return array
	 */
	private static function getSiteVar() {

		$help_employee = Settings::get('help_employee');
		$help_email = Settings::get('help_email');

		if ($help_employee) {
			$employee = new Employee($help_employee);
			$help_email = $employee->email;
		}


		return [
			'urls' => [
				'theme' => get_stylesheet_directory_uri(),
				'ajax'  => admin_url( 'admin-ajax.php' ),
				'home'  => home_url(),
			],
			'environment' => genie_ENVIRONMENT,
			'help_email' => $help_email,

		];
	}

}