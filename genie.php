<?php

/**
 * Plugin Name:       Genie
 * Plugin URI:        https://lnk7.com/plugins/genie/
 * Description:       Lnk7's Genie: The Advanced Programmer Toolkit
 * Version:           1.4.13
 * Requires at least: 5.5
 * Author:            Sunil Jaiswal
 * Author URI:        https://lnk7.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lnk7-genie
 * Domain Path:       /languages
 */

namespace GeniePluginExample;

use GeniePluginExample\PostTypes\Page;
use GeniePluginExample\PostTypes\Post;
use GeniePluginExample\PostTypes\Testimonial;
use Lnk7\Genie\Genie;

include_once('vendor/autoload.php');

Genie::createPlugin()
    ->withComponents([
        Plugin::class,
        Post::class,
        Page::class,
        Testimonial::class,
        Shortcodes::class,
    ])
    ->useViewsFrom( 'example/src/twig')
    ->releasesFolder( 'example/src/php/Releases')
    ->start();
