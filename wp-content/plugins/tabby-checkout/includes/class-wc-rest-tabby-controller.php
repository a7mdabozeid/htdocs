<?php

add_filter( 'woocommerce_rest_api_get_rest_namespaces', array('WC_REST_Tabby_Controller', 'register'));

class WC_REST_Tabby_Controller {
    const NS = 'tabby/v1';

    const BASE = 'webhook';

    public function webhook($data) {
        
        WC_Tabby_Api::ddlog('info', 'webhook received', null, $data);
        try {
            $txn = json_decode($data->get_body());
            
            if ($txn->order && $txn->order->reference_id && ($order = wc_get_order( $txn->order->reference_id ))) {
                if ($order->needs_payment()) tabby_check_order_paid_real(true, $order, 'webhook');
            } else {
                throw new \Exception("Not valid data posted");
            }

            return ['result' => 'success'];
        } catch (\Exception $e) {
            return ['result' => 'error', 'error' => $e->getMessage()];
        }
        return ['result' => 'error'];
    }

    public function register_routes() {
        register_rest_route(
            self::NS,
            '/' . self::BASE,
            array(
                'methods' => array('POST'),
                'callback' => array( $this, 'webhook' ),
                'permission_callback' => '__return_true',
            )
        );
    }
    public static function register($controllers) {
        $controllers[self::NS][self::BASE] = __CLASS__;
        return $controllers;
    }
    public static function getEndpointUrl() {
        return get_rest_url(null, self::NS . '/' . self::BASE);
    }
}
