<?php

namespace FRFreeVendor\WPDesk\Library\Marketing\Boxes;

/**
 * Marketing boxes assets.
 */
class Assets
{
    const SCRIPTS_VERSION = '1.1';
    /**
     * Enqueue assets.
     */
    public static function enqueue_assets()
    {
        \add_action('admin_enqueue_scripts', function () {
            \wp_enqueue_style('support-boxes', \plugin_dir_url(\dirname(__FILE__, 2)) . 'assets/support-boxes.css', '', self::SCRIPTS_VERSION);
            \wp_enqueue_script('support-boxes', \plugin_dir_url(\dirname(__FILE__, 2)) . 'assets/support-boxes.js', ['jquery'], self::SCRIPTS_VERSION, \true);
            /**
             * @see http://flexboxgrid.com/
             */
            \wp_enqueue_style('flexbox-grid', \plugin_dir_url(\dirname(__FILE__, 2)) . 'assets/flexboxgrid.min.css', '', self::SCRIPTS_VERSION, \true);
        }, 299);
    }
    /**
     * Enqueue owl library assets.
     */
    public static function enqueue_owl_assets()
    {
        \add_action('admin_enqueue_scripts', function () {
            \wp_enqueue_style('owl-slider', \plugin_dir_url(\dirname(__FILE__, 2)) . 'assets/owl-slider/owl.carousel.min.css', '', self::SCRIPTS_VERSION);
            \wp_enqueue_style('owl-slider-theme', \plugin_dir_url(\dirname(__FILE__, 2)) . 'assets/owl-slider/owl.theme.default.min.css', '', self::SCRIPTS_VERSION);
            \wp_enqueue_script('owl-slider', \plugin_dir_url(\dirname(__FILE__, 2)) . 'assets/owl-slider/owl.carousel.min.js', ['jquery'], self::SCRIPTS_VERSION, \true);
        }, 200);
    }
}
