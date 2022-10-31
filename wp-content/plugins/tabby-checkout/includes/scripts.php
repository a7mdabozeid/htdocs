<?php
add_action('wp_head', 'tabby_add_header_scripts');
function tabby_add_header_scripts() {
	if (is_checkout()) {
		echo '<script  type="text/javascript"  src="https://'.TABBY_CHECKOUT_DOMAIN.'/integration.js"></script>' . "\n";
		echo '<script  type="text/javascript"  src="https://www.datadoghq-browser-agent.com/datadog-logs-eu.js"></script>' . "\n";
        echo '<script  type="text/javascript">window.DD_LOGS && DD_LOGS.init({clientToken: "pubfaebfd894cede96e2919047c2318e21b", datacenter: "eu", forwardErrorsToLogs: true, sampleRate: 100, service: "woo"});const ddLog = (eventName, data = {}) => {if (!window.DD_LOGS) return false; data.host = document.location.hostname; DD_LOGS.logger.info(eventName, data); };</script>';
		echo '<script  type="text/javascript"  src="' . plugins_url('tabby-checkout/js/tabby.js') . '?v='.MODULE_TABBY_CHECKOUT_VERSION.'"></script>' . "\n";
		echo '<style>#place_order:disabled {opacity:0.5; text-decoration: none !important;} .payment_method_tabby_pay_later img, .payment_method_tabby_installments img {max-width:20%;}</style>' . "\n";
        if ($config = get_option('woocommerce_tabby_installments_settings')) {
            if (!array_key_exists('description_type', $config) || $config['description_type'] < 2) {
		        echo '<script  type="text/javascript"  src="https://'.TABBY_CHECKOUT_DOMAIN.'/tabby-card.js"></script>' . "\n";
            }
        };
        if ($config = get_option('woocommerce_tabby_credit_card_installments_settings')) {
            if (!array_key_exists('description_type', $config) || $config['description_type'] < 2) {
		        echo '<script  type="text/javascript"  src="https://'.TABBY_CHECKOUT_DOMAIN.'/tabby-payment-method-snippet-cci.js"></script>' . "\n";
            }
        };
    
	};
	if ((is_product() || is_cart()) && is_tabby_promo_enabled()) {
		echo '<script  type="text/javascript"  src="https://'.TABBY_CHECKOUT_DOMAIN.'/tabby-promo.js" defer></script>' . "\n";
    };
}
function is_tabby_promo_enabled() {
    require_once dirname(__FILE__) . '/class-wc-gateway-tabby-checkout-base.php';
    return 
        (is_tabby_promo_enabled_for_product() || is_tabby_promo_enabled_for_cart()) &&
        WC_Tabby_Config::isAvailableForCurrency() &&
        (TabbyPromo::getMaxPrice() == 0 || TabbyPromo::getPrice() <= TabbyPromo::getMaxPrice()) &&
        (TabbyPromo::getMinTotal() == 0 || TabbyPromo::getPrice() >= TabbyPromo::getMinTotal());
}
function is_tabby_promo_enabled_for_product() {
    return (get_option('tabby_promo') !== 'no') && is_product();
}
function is_tabby_promo_enabled_for_cart() {
    return (get_option('tabby_promo_cart') !== 'no') && is_cart();
}

add_action('woocommerce_proceed_to_checkout'    , 'tabby_product_promotion', 15 );
add_action('woocommerce_before_add_to_cart_form', 'tabby_product_promotion'     );
function tabby_product_promotion() {
    if (is_tabby_promo_enabled()) {
?>
<script type="text/javascript" defer>
function initTabbyPromotions() {
    var tabbyConf = <?php echo json_encode(TabbyPromo::getConfig()); ?>;
    var price = jQuery('#tabbyPromo').attr('data-tabby-price');
    var currency = jQuery('#tabbyPromo').attr('data-tabby-currency');
    if (price) {
        tabbyConf.price = price;
        tabbyConf.currency = currency;
    }
    if (tabbyConf.localeSource == 'html') tabbyConf.lang = document.documentElement.lang;
    var tabbyPromo = new TabbyPromo(tabbyConf);
}
if (typeof TabbyPromo == 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        initTabbyPromotions();
    });
} else {
    initTabbyPromotions();
}
jQuery(document.body).on('updated_wc_div', initTabbyPromotions);
// addon for product variations
document.addEventListener('DOMContentLoaded', () => {
    if (jQuery('.variations_form').length) {
        jQuery('.variations_form').on('show_variation', function (target, variation, purchasable) {
            jQuery('#tabbyPromo').attr('data-tabby-price', variation.display_price);
            initTabbyPromotions();
        })
    }
});
</script>   
   <div id="tabbyPromo" style="margin-bottom: 20px" data-tabby-price="<?php echo TabbyPromo::getPrice(); ?>" data-tabby-currency="<?php echo TabbyPromo::getCurrency(); ?>"></div>
<?php
    }
}
class TabbyPromo {
    public static function getConfig() {
        return [
            "selector"  => "#tabbyPromo",
            "merchantCode" => self::getMerchantCode(),
            "publicKey" => self::getPublicKey(),
            "lang"      => self::getLocale(),
            "localeSource"=> get_option('tabby_checkout_locale_html') == 'yes' ? 'html' : '',
            "currency"  => self::getCurrency(),
            "price"     => self::getPrice(),
            "email"     => self::getEmail(),
            "phone"     => self::getPhone(),
            "source"    => self::getSource(),
            "theme"     => self::getTheme(),
            "productType"=> self::getProductType(),
        ];
    }
    public static function getProductType() {
        require_once dirname(__FILE__) . '/class-wc-gateway-tabby-credit-card-installments.php';
        $gateway = new WC_Gateway_Tabby_Credit_Card_Installments();
        $gateway->init_settings();
        return $gateway->enabled == 'yes' ? 'creditCardInstallments' : 'installments';
    }
    public static function getTheme() {
        return get_option('tabby_checkout_promo_theme', '');
    }
    public static function getSource() {
        return is_product() ? 'product' : 'cart';
    }
    public static function getMerchantCode() {
        return get_option('tabby_store_code', null) ?: null;
    }
    public static function getPublicKey() {
        return get_option('tabby_checkout_public_key');
    }
    public static function getLocale() {
        return get_locale();
    }
    public static function getCurrency() {
        return WC_Tabby_Config::getTabbyCurrency();
    }
    public static function getPrice() {
        if (is_product()) {
            $product = wc_get_product();
            $price = number_format((float)wc_get_price_including_tax( $product ), 2, '.', '');
        } else {
            $price = number_format(self::get_order_total(), 2, '.', '');
        }
        return $price;
    }
    public static function get_order_total() {

        $total    = 0;
        $order_id = absint( get_query_var( 'order-pay' ) );

        // Gets order total from "pay for order" page.
        if ( 0 < $order_id ) {
            $order = wc_get_order( $order_id );
            $total = (float) $order->get_total();

            // Gets order total from cart/checkout.
        } elseif ( 0 < WC()->cart->total ) {
            $total = (float) WC()->cart->total;
        }

        return $total;
    }
    public static function getMaxPrice() {
        return get_option('tabby_checkout_promo_price', 0);
    }
    public static function getMinTotal() {
        return get_option('tabby_checkout_promo_min_total', 0);
    }
    public static function getEmail() {
        $current_user = wp_get_current_user();
        return $current_user && $current_user->user_email ? $current_user->user_email : null;
    }
    public static function getPhone() {
        $current_user_id = get_current_user_id();
        $phones = [];
        $delimiter = '|';
        if ($current_user_id) {
            $metas = get_user_meta($current_user_id);
            foreach ($metas as $name => $meta) {
                if (preg_match("/phone/", $name)) {
                    $phones[] = implode($delimiter, $meta);
                }
            }
        }
        return $current_user_id ? implode($delimiter, $phones) : null;
    }
}
