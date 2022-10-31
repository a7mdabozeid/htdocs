<?php
namespace ACFWP\Models\Objects;

use ACFWP\Helpers\Helper_Functions;
use ACFWP\Helpers\Plugin_Constants;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * @deprecated 2.6.3
 */
class Loyalty_Programs_Settings extends \WC_Settings_Page
{

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * ACFW_Settings constructor.
     *
     * @since 2.2
     * @access public
     *
     * @param Plugin_Constants $constants        Plugin constants object.
     * @param Helper_Functions $helper_functions Helper functions object.
     */
    public function __construct(Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
    }

    /**
     * Output the settings.
     *
     * @since 2.2
     * @access public
     */
    public function output()
    {
        wc_deprecrated_function('Loyalty_Programs_Settings::' . __FUNCTION__, '2.6.3');
    }

    /**
     * Save settings.
     *
     * @since 2.2
     * @access public
     */
    public function save()
    {
        wc_deprecrated_function('Loyalty_Programs_Settings::' . __FUNCTION__, '2.6.3');
    }
}
