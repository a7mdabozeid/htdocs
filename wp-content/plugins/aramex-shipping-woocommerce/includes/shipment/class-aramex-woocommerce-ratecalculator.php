<?php
/*
Plugin Name:  Aramex Shipping WooCommerce
Plugin URI:   https://aramex.com
Description:  Aramex Shipping WooCommerce plugin
Version:      1.0.0
Author:       aramex.com
Author URI:   https://www.aramex.com/solutions-services/developers-solutions-center
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  aramex
Domain Path:  /languages
*/

include_once __DIR__ . '../../core/class-aramex-helper.php';

/**
 * Controller for Rate Calculator functionality
 */
class Aramex_Ratecalculator_Method extends Aramex_Helper
{

    /**
     * Starting method
     *
     * @return mixed|string|void
     */
    public function run()
    {
        check_admin_referer('aramex-shipment-check' . wp_get_current_user()->user_email);
        $info = $this->getInfo(wp_create_nonce('aramex-shipment-check' . wp_get_current_user()->user_email));
        $postArray = $this->formatPost($_POST);
        $post = $postArray['data'];
        $account = $info['clientInfo']['AccountNumber'];
        $response = array();
        
        try {
            $country_code = $info['clientInfo']['country'];
            $count_object = new WC_Countries();
            $countries = $count_object->get_countries();
            foreach ($countries as $key => $value) {
                if ($key == $country_code) {
                    $countryName = $value;
                }
            }
            $countryName = ($countryName) ? $countryName : "";
            $params = array(
                'ClientInfo' => $info['clientInfo'],
                'Transaction' => array(
                    'Reference1' => $post['reference']
                ),
                'OriginAddress' => array(
                    'StateOrProvinceCode' => html_entity_decode($post['origin_state']),
                    'City' => html_entity_decode($post['origin_city']),
                    'PostCode' => str_replace(" ","",$post['origin_zipcode']),
                    'CountryCode' => $post['origin_country']
                ),
                'DestinationAddress' => array(
                    'StateOrProvinceCode' => html_entity_decode($post['destination_state']),
                    'City' => html_entity_decode($post['destination_city']),
                    'PostCode' => str_replace(" ","", $post['destination_zipcode']),
                    'CountryCode' => $post['destination_country'],
                ),
                'ShipmentDetails' => array(
                    'PaymentType' => $post['payment_type'],
                    'ProductGroup' => $post['product_group'],
                    'ProductType' => $post['service_type'],
                    'ActualWeight' => array('Value' => $post['text_weight'], 'Unit' => $post['weight_unit']),
                    'ChargeableWeight' => array('Value' => $post['text_weight'], 'Unit' => $post['weight_unit']),
                    'NumberOfPieces' => $post['total_count'],
                    'InsuranceAmount' =>  array(
                        'Value' => $post['insurance_amount'],
                        'CurrencyCode' =>$post['currency_code'],
                    )
                ),
                'PreferredCurrencyCode' => $post['currency_code'],
               
            );
            //SOAP object
            $soapClient = new SoapClient($info['baseUrl'] . 'aramex-rates-calculator-wsdl.wsdl',
                array("trace" => true, 'cache_wsdl' => WSDL_CACHE_NONE));
            try {
                $results = $soapClient->CalculateRate($params);
                if ($results->HasErrors) {
                    if (count((array)$results->Notifications->Notification) > 1) {
                        $error = "";
                        if(is_array($results->Notifications->Notification)){
                            foreach ($results->Notifications->Notification as $notify_error) {
                                $error .= 'Aramex: ' . $notify_error->Code . ' - ' . $notify_error->Message . "<br>";
                            }
                        }else{
                            $error .= 'Aramex: ' . $results->Notifications->Notification->Code . ' - ' . $results->Notifications->Notification->Message . "<br>";
                        }
                        $response['error'] = $error;
                    } else {
                        $response['error'] = 'Aramex: ' . $results->Notifications->Notification->Code . ' - ' . $results->Notifications->Notification->Message;
                    }
                    $response['type'] = 'error';
                } else {
                    $response['type'] = 'success';
                    $amount = "<p class='amount'>" . $results->TotalAmount->Value . " " . $results->TotalAmount->CurrencyCode . "</p>";
                    $text = __('Local taxes - if any - are not included. Rate is based on account number ',
                            'aramex') . $account . __(" in ", 'aramex') . $countryName;
                    $response['html'] = $amount . $text;
                }
            } catch (Exception $e) {
                $response['type'] = 'error';
                $response['error'] = $e->getMessage();
            }
        } catch (Exception $e) {
            $response['type'] = 'error';
            $response['error'] = $e->getMessage();
        }
        print json_encode($response);
        die();
    }
}
