<?php

class WC_Tabby_Config {
    const ALLOWED_CURRENCIES = ['AED','SAR','BHD','KWD'];
    const ALLOWED_COUNTRIES  = [ 'AE', 'SA', 'BH', 'KW'];
    
    public static function isAvailableForCountry($country_code) {
        // enbaled only for SA and AE
        if (($allowed = static::getConfiguredCountries()) === false) {
            $allowed = static::ALLOWED_COUNTRIES;
        };
        return in_array($country_code, $allowed);
    }
    public static function getConfiguredCountries() {
        return get_option('tabby_countries', false);
    }
    public static function isAvailableForCurrency($currency_code = null) {
        if (is_null($currency_code)) {
            $currency_code = static::getTabbyCurrency();
        }
        return in_array($currency_code, static::ALLOWED_CURRENCIES);
    }
    public static function getTabbyCurrency() {
        return apply_filters("tabby_checkout_tabby_currency", get_woocommerce_currency());
    }
}
