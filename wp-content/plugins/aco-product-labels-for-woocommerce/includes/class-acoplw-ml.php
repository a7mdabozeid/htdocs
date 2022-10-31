<?php

if (!defined('ABSPATH'))
    exit;

class ACOPLW_ML
{

    /**
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    public $_version;
    private static $_instance   = null;
    public $default_lang        = '';
    public $current_lang        = '';
    private $_active            = false;

    public function __construct()
    {

        if (class_exists('SitePress')) {

            $this->_active = 'wpml';
            $this->default_lang = apply_filters('wpml_default_language', NULL);
            $this->current_lang = apply_filters('wpml_current_language', NULL);

        } else if (defined('POLYLANG_VERSION')) {

            $this->_active = 'polylang';
            $this->default_lang = pll_default_language();
            $this->current_lang = pll_current_language();

        }

    }

    public static function instance($file = '', $version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

    public function is_active()
    {
        return $this->_active !== false;
    }

    public function is_default_lan()
    {
        return ($this->current_lang === $this->default_lang);
    }

    public function default_language()
    {
        return $this->default_lang;
    }

    public function current_language()
    {
        return $this->current_lang;
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

}
