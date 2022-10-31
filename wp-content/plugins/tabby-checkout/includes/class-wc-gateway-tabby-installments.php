<?php
    class WC_Gateway_Tabby_Installments extends WC_Gateway_Tabby_Checkout_Base {
        const METHOD_CODE = 'tabby_installments';
        const TABBY_METHOD_CODE = 'installments';
        const METHOD_NAME = '4 interest-free payments';
        const METHOD_DESC = 'No fees. Pay with any card.';

        public function init_form_fields() {
            parent::init_form_fields();

            $this->form_fields['card_theme'] = [
                'title' => __( 'Promo Card Theme', 'tabby-checkout' ),
                'type' => 'text',
                'default' => __( 'default', 'tabby-checkout' ),
            ];
        }
    }
