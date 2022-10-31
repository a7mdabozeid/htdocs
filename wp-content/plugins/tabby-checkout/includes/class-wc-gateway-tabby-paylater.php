<?php
    class WC_Gateway_Tabby_PayLater extends WC_Gateway_Tabby_Checkout_Base {
        const METHOD_CODE = 'tabby_pay_later';
        const TABBY_METHOD_CODE = 'payLater';
        const METHOD_NAME = 'Pay in 14 days';
        const METHOD_DESC = 'No upfront payments. No fees.';

        public function init_form_fields() {
            parent::init_form_fields();

            if (array_key_exists('description_type', $this->form_fields)) {
                unset($this->form_fields['description_type']['options'][0]);
                unset($this->form_fields['description_type']['options'][1]);
            }
        }
    }
