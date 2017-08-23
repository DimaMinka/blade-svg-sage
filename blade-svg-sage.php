<?php
/*
Plugin Name:        Blade SVG for Sage
Plugin URI:         http://github.com/log1x/blade-svg-sage
Description:        WordPress plugin to add Blade SVG to Sage 9
Version:            1.0.5
Author:             Log1x
Author URI:         http://github.com/log1x/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
GitHub Plugin URI:  log1x/blade-svg-sage
GitHub Branch:      master
*/

namespace BladeSvg;

use BladeSvg\SvgFactory;

/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ensure dependencies are loaded
 */
if (!file_exists($composer = __DIR__.'/vendor/autoload.php') && !file_exists($composer = __DIR__.'/dist/autoload.php')) {
    return new \WP_Error('<h1>Error</h1><p>Please run `composer install` to use Blade SVG.</p>', 'Autoloader not found.');
}
require_once $composer;

/**
 * Initialize the plugin
 */
function init()
{
    if (function_exists('\App\sage')) {
        \App\sage()->singleton(SvgFactory::class, function () {
            $config = [
                'spritesheet_path' => apply_filters('bladesvg_spritesheet_path', get_dist_path('images/svg/icons')),
                'svg_path'         => apply_filters('bladesvg_image_path', get_dist_path('images/svg/icons')),
                'inline'           => apply_filters('bladesvg_inline', true),
                'class'            => apply_filters('bladesvg_class', 'svg'),
                'sprite_prefix'    => apply_filters('bladesvg_sprite_prefix', '')
            ];

            return new SvgFactory($config);
        });
    }
}

/**
 * Create @svg() Blade directive
 */
function directive()
{
    if (function_exists('\App\sage')) {
        \App\sage('blade')->compiler()->directive('icon', function ($expression) {
            return "<?php echo e(svg_image($expression)); ?>";
        });

        \App\sage('blade')->compiler()->directive('svg', function ($expression) {
            return "<?php echo e(svg_image($expression)); ?>";
        });
    }
}

add_action('after_setup_theme', __NAMESPACE__ . '\init', 30);
add_action('after_setup_theme', __NAMESPACE__ . '\directive', 30);
