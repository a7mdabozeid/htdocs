<?php
require_once (__DIR__ . '/class-wc-rest-tabby-controller.php');

class WC_Tabby_Webhook {
    public static function register() {
        if (WC_Tabby_Api::needs_setup()) {
            static::ddlog("info", "Tabby is not configured, but webhook 'register' called. Possible first module installation.");
            return;
        }
        // get webhook url
        $url = WC_REST_Tabby_Controller::getEndpointUrl();
        static::ddlog("info", "Checking webhook is registered.", null, ['url' => $url]);
        
        // request all webhooks
        foreach (WC_Tabby_Config::getConfiguredCountries() as $country) {
            // get list of registered hooks
            $hooks = static::getWebhooks($country);
            // bypass not authorized errors
            if (static::isNotAuthorized($hooks)) {
                static::ddlog("info", "Store code not authorized for merchant", null, ['code' => $country]);
                continue;
            }
            static::ddlog("info", "Registered hooks", null, ['hooks' => $hooks]);
            if (!is_array($hooks)) {
                $hooks = [$hooks];
            }
            $registered = false;
            foreach ($hooks as $hook) {
                if (!is_object($hook)) continue;
                if ($hook->url == $url) {
                    if ($hook->is_test !== static::getIsTest()) {
                        static::updateWebhook($hook, $country, $url);
                    }
                    $registered = true;
                }
            }
            if (!$registered) {
                static::registerWebhook($country, $url);
            }
        }
    }
    public static function isNotAuthorized($response) {
        if (is_object($response) && property_exists($response, 'errorType') && $response->errorType == 'not_authorized') return true;
        return false;
    }
    public static function registerWebhook($code, $url) {
        $data = ['url' => $url, 'is_test' => static::getIsTest()];
        static::ddlog("info", "Registering webhook", null, $data);
        return WC_Tabby_Api::request('webhooks', 'POST', $data, $code);
    }
    public static function updateWebhook($hook, $code, $url) {
        $data = ['url' => $url, 'is_test' => static::getIsTest()];
        static::ddlog("info", "Updating webhook", null, $data);
        return WC_Tabby_Api::request('webhooks/' . $hook->id, 'PUT', $data, $code);
    }
    public static function getWebhooks($code) {
        return WC_Tabby_Api::request('webhooks', 'GET', null, $code);
    }
    public static function getIsTest() {
        return (bool)preg_match('#^sk_test#', WC_Tabby_Api::get_api_option('secret_key'));
    }
    public static function unregister() {
    }
    public static function ddlog($status = "error", $message = "Something went wrong", $e = null, $data = null) {
        return WC_Tabby_Api::ddlog($status, $message, $e, $data);
    }
}

