<?php
    require_once __DIR__ . '/class-wc-gateway-tabby-checkout-base.php';

    class WC_Gateway_Tabby_Credit_Card_Installments extends WC_Gateway_Tabby_Checkout_Base {
        const METHOD_CODE = 'tabby_credit_card_installments';
        const TABBY_METHOD_CODE = 'creditCardInstallments';
        const METHOD_NAME = 'Interest-free credit card payments';
        const METHOD_DESC = 'No fees. Pay with any credit card.';

        public function init_form_fields() {
            parent::init_form_fields();

            if (array_key_exists('description_type', $this->form_fields)) {
                unset($this->form_fields['description_type']['options'][1]);
            }
        }

    public function is_available() {
        $is_available = parent::is_available();

        if (!WC()->customer) {
            $is_available = true;
        } else {
            if (!($country = WC()->customer->get_shipping_country())) {
                $country = WC()->customer->get_billing_country();
            }

            if ($country && $country != 'AE') {
                $is_available = false;
            }
        }

        return $is_available;
    }

    }
