<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Woo_Refund_And_Exchange_Lite_Api_Process' ) ) {

	/**
	 * The plugin API class.
	 *
	 * This is used to define the functions and data manipulation for custom endpoints.
	 *
	 * @since      1.0.0
	 * @package    woo-refund-and-exchange-lite
	 * @subpackage woo-refund-and-exchange-lite/includes
	 * @author     Wp Swings <wpswings.com>
	 */
	class Woo_Refund_And_Exchange_Lite_Api_Process {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

		}

		/**
		 * Define the function to process data for custom endpoint.
		 *
		 * @since    1.0.0
		 * @param   object $wrael_request  data of requesting headers and other information.
		 * @return  Array $wps_rma_rest_response    returns processed data and status of operations.
		 */
		public function wps_rma_refund_request_process( $wrael_request ) {
			$data                  = $wrael_request->get_params();
			$order_id              = isset( $data['order_id'] ) ? absint( $data['order_id'] ) : 0;
			$products              = isset( $data['products'] ) ? $data['products'] : '';
			$reason                = isset( $data['reason'] ) ? $data['reason'] : '';
			$refund_method         = isset( $data['refund_method'] ) ? $data['refund_method'] : '';
			$wps_rma_rest_response = array();
			$order_obj             = wc_get_order( $order_id );
			$wps_rma_check_tax     = get_option( $order_id . 'check_tax', false );
			if ( ! empty( $order_id ) && ! empty( $order_obj ) && ! empty( $reason ) ) {
				$check_refund = wps_rma_show_buttons( 'refund', $order_obj );
				if ( 'yes' === $check_refund ) {
					if ( wps_rma_pro_active() && ! empty( $products ) ) {
						$products1     = array();
						$refund_items  = array();
						$refund_amount = 0;
						$flag          = true;
						$qty_flag      = true;
						$item_flag     = true;
						$invalid_item  = true;
						$invalid_qty   = true;
						$item_detail   = array();
						foreach ( $order_obj->get_items() as $item_id => $item ) {
							$product = $item->get_product();
							if ( 'variation' === $product->get_type() ) {
								$variation_id                 = $item->get_variation_id();
								$item_detail[ $variation_id ] = $item->get_quantity();
							} else {
								$product_id                 = $item->get_product_id();
								$item_detail[ $product_id ] = $item->get_quantity();
							}
						}
						$json_validate = wps_json_validate( $products );
						if ( $json_validate ) {
							foreach ( $order_obj->get_items() as $item_id => $item ) {
								foreach ( json_decode( $products ) as $key => $value ) {
									if ( ( isset( $value->product_id ) && array_key_exists( $value->product_id, $item_detail ) ) || ( isset( $value->variation_id ) && array_key_exists( $value->variation_id, $item_detail ) ) && isset( $value->qty ) ) {
										$product = $item->get_product();
										if ( 'variation' === $product->get_type() ) {
											$variation_id = $item->get_variation_id();
										} else {
											$product_id = $item->get_product_id();
										}
										if ( ( isset( $value->product_id ) && $product_id == $value->product_id ) || ( isset( $value->variation_id ) && $variation_id == $value->variation_id ) ) {
											$item_refund_already = get_post_meta( $order_id, 'wps_rma_request_made', true );
											if ( ! empty( $item_refund_already ) && isset( $item_refund_already[ $item_id ] ) && 'completed' === $item_refund_already[ $item_id ] ) {
												$flag = false;
											} elseif ( $value->qty > $item->get_quantity() ) {
												$qty_flag = false;
											} else {
												$item_arr               = array();
												$item_arr['product_id'] = $item->get_product_id();
												$product = $item->get_product();
												if ( 'variation' === $product->get_type() ) {
													$variation_id = $item->get_variation_id();
												} else {
													$variation_id = 0;
												}
												$item_arr['item_id']      = $item_id;
												$item_arr['variation_id'] = $variation_id;
												$item_arr['qty']          = $value->qty;
												$wps_rma_check_tax        = get_option( $order_id . 'check_tax', false );
												$tax_price                = $item->get_total_tax() / $item->get_quantity();
												$item_price               = $item->get_total() / $item->get_quantity();
												if ( empty( $wps_rma_check_tax ) ) {
													$item_arr['price'] = $item_price;
													$refund_amount    += $item_price;
												} elseif ( 'wps_rma_inlcude_tax' === $wps_rma_check_tax ) {
													$item_arr['price'] = $item_price + $tax_price;
													$refund_amount    += $item_price + $tax_price;
												} elseif ( 'wps_rma_exclude_tax' === $wps_rma_check_tax ) {
													$item_arr['price'] = $item_price - $tax_price;
													$refund_amount    += $item_price - $tax_price;
												}
												$refund_items[] = $item_arr;
											}
										}
									} else {
										$item_flag = true;
										if ( isset( $value->product_id ) || isset( $value->variation_id ) ) {
											$item_flag = false;
										}
										if ( $item_flag ) {
											$invalid_item = false;
										} elseif ( ! isset( $value->qty ) ) {
											$invalid_qty = false;
										} else {
											$item_flag = false;
										}
									}
								}
							}
						}
						if ( ! $flag ) {
							$wps_rma_rest_response['message'] = 'error';
							$wps_rma_rest_response['status']  = 404;
							$wps_rma_rest_response['data']    = esc_html__( 'Return Request Already has been made and accepted for the items you have given', 'woo-refund-and-exchange-lite' );
						} elseif ( ! $qty_flag ) {
							$wps_rma_rest_response['message'] = 'error';
							$wps_rma_rest_response['status']  = 404;
							$wps_rma_rest_response['data']    = esc_html__( 'Quantity given for items is greater than the orders items quantity', 'woo-refund-and-exchange-lite' );
						} elseif ( ! $item_flag ) {
							$wps_rma_rest_response['message'] = 'error';
							$wps_rma_rest_response['status']  = 404;
							$wps_rma_rest_response['data']    = esc_html__( 'These item id does not belong to the order', 'woo-refund-and-exchange-lite' );
						} elseif ( ! $invalid_item ) {
							$wps_rma_rest_response['message'] = 'error';
							$wps_rma_rest_response['status']  = 404;
							$wps_rma_rest_response['data']    = esc_html__( 'Please give the item ids which needs to be refunded', 'woo-refund-and-exchange-lite' );
						} elseif ( ! $invalid_qty ) {
							$wps_rma_rest_response['message'] = 'error';
							$wps_rma_rest_response['status']  = 404;
							$wps_rma_rest_response['data']    = esc_html__( 'Please give the item qty which needs to be refunded', 'woo-refund-and-exchange-lite' );
						} elseif ( ! $json_validate ) {
							$wps_rma_rest_response['message'] = 'error';
							$wps_rma_rest_response['status']  = 404;
							$wps_rma_rest_response['data']    = esc_html__( 'Products are given by you doesn\'t a valid json format', 'woo-refund-and-exchange-lite' );
						} elseif ( empty( $products ) ) {
							$wps_rma_rest_response['status'] = 404;
							$wps_rma_rest_response['data']   = esc_html__( 'Please Provide the data for the products', 'woo-refund-and-exchange-lite' );
						} elseif ( empty( $products ) ) {
							$wps_rma_rest_response['status'] = 404;
							$wps_rma_rest_response['data']   = esc_html__( 'Please Provide the data for the products', 'woo-refund-and-exchange-lite' );
						} else {
							$products1['products']      = $refund_items;
							$products1['order_id']      = $order_id;
							$products1['subject']       = $reason;
							$products1['refund_method'] = $refund_method;
							$products1['amount']        = $refund_amount;
							$wps_rma_resultsdata        = wps_rma_save_return_request_callback( $order_id, $refund_method, $products1 );
							if ( ! empty( $wps_rma_resultsdata ) ) {
								$wps_rma_rest_response['message'] = 'success';
								$wps_rma_rest_response['status']  = 200;
								$wps_rma_rest_response['data']    = esc_html__( 'Refund Request Send Successfully', 'woo-refund-and-exchange-lite' );
							} else {
								$wps_rma_rest_response['message'] = 'error';
								$wps_rma_rest_response['status']  = 404;
								$wps_rma_rest_response['data']    = esc_html__( 'Some problem occur while refund requesting', 'woo-refund-and-exchange-lite' );
							}
						}
					} else {
						$products1     = array();
						$refund_items  = array();
						$refund_amound = 0;
						if ( ! empty( $order_obj ) ) {
							foreach ( $order_obj->get_items() as $item_id => $item ) {
								$item_arr               = array();
								$item_arr['product_id'] = $item->get_product_id();
								if ( $item->is_type( 'variable' ) ) {
									$variation_id = $item->get_variation_id();
								} else {
									$variation_id = 0;
								}
								$item_arr['item_id']      = $item_id;
								$item_arr['variation_id'] = $variation_id;
								$item_arr['qty']          = $item->get_quantity();
								$item_tax                 = $item->get_total_tax() / $item->get_quantity();
								$item_price               = $item->get_total() / $item->get_quantity();
								if ( empty( $wps_rma_check_tax ) ) {
									$item_arr['price'] = $item_price;
									$refund_amound    += $item_price;
								} elseif ( 'wps_rma_inlcude_tax' === $wps_rma_check_tax ) {
									$item_arr['price'] = $item_price + $item_tax;
									$refund_amound    += $item_price + $item_tax;
								} elseif ( 'wps_rma_exclude_tax' === $wps_rma_check_tax ) {
									$item_arr['price'] = $item_price - $item_tax;
									$refund_amound    += $item_price - $item_tax;
								}
								$refund_items[] = $item_arr;
							}
							$products1['products']      = $refund_items;
							$products1['order_id']      = $order_id;
							$products1['subject']       = $reason;
							$products1['refund_method'] = 'manual_method';
							$products1['amount']        = $refund_amound;
						}
						$wps_rma_resultsdata = wps_rma_save_return_request_callback( $order_id, 'manual_method', $products1 );
						$flag_refund_made    = false;
						$products            = get_post_meta( $order_id, 'wps_rma_return_product', true );
						if ( isset( $products ) && ! empty( $products ) ) {
							foreach ( $products as $date => $product ) {
								if ( 'complete' === $product['status'] ) {
									$flag_refund_made = true;
								}
							}
						}
						if ( $flag_refund_made ) {
							$wps_rma_rest_response['message'] = 'error';
							$wps_rma_rest_response['status']  = 404;
							$wps_rma_rest_response['data']    = esc_html__( 'Return Request Already has been made and accepted', 'woo-refund-and-exchange-lite' );
						} elseif ( ! empty( $wps_rma_resultsdata ) ) {
							$wps_rma_rest_response['message'] = 'success';
							$wps_rma_rest_response['status']  = 200;
							$wps_rma_rest_response['data']    = esc_html__( 'Return Request Send Successfully', 'woo-refund-and-exchange-lite' );
						} else {
							$wps_rma_rest_response['message'] = 'error';
							$wps_rma_rest_response['status']  = 404;
							$wps_rma_rest_response['data']    = esc_html__( 'Some problem occur while refund requesting', 'woo-refund-and-exchange-lite' );
						}
					}
				} else {
					$wps_rma_rest_response['status'] = 404;
					$wps_rma_rest_response['data']   = $check_refund;
				}
			} elseif ( empty( $order_id ) ) {
				$wps_rma_rest_response['status'] = 404;
				$wps_rma_rest_response['data']   = esc_html__( 'Please Provide the order id to perform the process', 'woo-refund-and-exchange-lite' );
			} elseif ( empty( $order_obj ) ) {
				$wps_rma_rest_response['status'] = 404;
				$wps_rma_rest_response['data']   = esc_html__( 'Please Provide the correct order id to perform the process', 'woo-refund-and-exchange-lite' );
			} elseif ( empty( $reason ) ) {
				$wps_rma_rest_response['status'] = 404;
				$wps_rma_rest_response['data']   = esc_html__( 'Please Provide the reason for refund', 'woo-refund-and-exchange-lite' );
			}
			return $wps_rma_rest_response;
		}

		/**
		 * Define the function to process data for custom endpoint.
		 *
		 * @since    1.0.0
		 * @param   object $wrael_request  data of requesting headers and other information.
		 * @return  Array $wps_rma_rest_response    returns processed data and status of operations.
		 */
		public function wps_rma_refund_request_accept_process( $wrael_request ) {
			$wps_rma_rest_response = array();
			$data                  = $wrael_request->get_params();
			$order_id              = isset( $data['order_id'] ) ? absint( $data['order_id'] ) : 0;
			$flag                  = false;
			$order_obj             = wc_get_order( $order_id );
			$flag_completed        = false;
			if ( ! empty( $order_id ) && ! empty( $order_obj ) ) {
				$products = get_post_meta( $order_id, 'wps_rma_return_product', true );
				if ( isset( $products ) && ! empty( $products ) ) {
					foreach ( $products as $date => $product ) {
						if ( 'pending' === $product['status'] ) {
							$flag = true;
						} elseif ( 'complete' === $product['status'] ) {
							$flag_completed = true;
						}
					}
				}
				if ( $flag ) {
					$wps_rma_resultsdata = wps_rma_return_req_approve_callback( $order_id, $products );
					if ( ! empty( $wps_rma_resultsdata ) ) {
						$wps_rma_rest_response['status'] = 200;
						$wps_rma_rest_response['data']   = esc_html__( 'Return Request Accepted Successfully', 'woo-refund-and-exchange-lite' );
					} else {
						$wps_rma_rest_response['status'] = 404;
						$wps_rma_rest_response['data']   = esc_html__( 'Some problem occur while refund request accepting', 'woo-refund-and-exchange-lite' );
					}
				} elseif ( $flag_completed ) {
					$wps_rma_rest_response['status'] = 404;
					$wps_rma_rest_response['data']   = esc_html__( 'You have approved the refund request already', 'woo-refund-and-exchange-lite' );
				} else {
					$wps_rma_rest_response['status'] = 404;
					$wps_rma_rest_response['data']   = esc_html__( 'You can only accept the refund request when the request has been made earlier', 'woo-refund-and-exchange-lite' );
				}
			} elseif ( empty( $order_obj ) ) {
				$wps_rma_rest_response['status'] = 404;
				$wps_rma_rest_response['data']   = esc_html__( 'Please Provide the correct order id to perform the process', 'woo-refund-and-exchange-lite' );
			} else {
				$wps_rma_rest_response['status'] = 404;
				$wps_rma_rest_response['data']   = esc_html__( 'Please Provide the order id to perform the process', 'woo-refund-and-exchange-lite' );
			}
			return $wps_rma_rest_response;
		}

		/**
		 * Define the function to process data for custom endpoint.
		 *
		 * @since    1.0.0
		 * @param   object $wrael_request  data of requesting headers and other information.
		 * @return  Array $wps_rma_rest_response    returns processed data and status of operations.
		 */
		public function wps_rma_refund_request_cancel_process( $wrael_request ) {
			$wps_rma_rest_response = array();
			$data                  = $wrael_request->get_params();
			$order_id              = isset( $data['order_id'] ) ? absint( $data['order_id'] ) : 0;
			$flag                  = false;
			$flag_cancel           = false;
			$order_obj             = wc_get_order( $order_id );
			if ( ! empty( $order_id ) && ! empty( $order_obj ) ) {
				$products = get_post_meta( $order_id, 'wps_rma_return_product', true );
				if ( isset( $products ) && ! empty( $products ) ) {
					foreach ( $products as $date => $product ) {
						if ( 'pending' === $product['status'] ) {
							$flag = true;
						} elseif ( 'cancel' === $product['status'] ) {
							$flag_cancel = true;
						}
					}
				}
				if ( $flag ) {
					$wps_rma_resultsdata = wps_rma_return_req_cancel_callback( $order_id, $products );
					if ( ! empty( $wps_rma_resultsdata ) ) {
						$wps_rma_rest_response['status'] = 200;
						$wps_rma_rest_response['data']   = esc_html__( 'Return Request Cancel Successfully', 'woo-refund-and-exchange-lite' );
					} else {
						$wps_rma_rest_response['status'] = 404;
						$wps_rma_rest_response['data']   = esc_html__( 'Some problem occur while refund request cancelling', 'woo-refund-and-exchange-lite' );
					}
				} elseif ( $flag_cancel ) {
					$wps_rma_rest_response['status'] = 404;
					$wps_rma_rest_response['data']   = esc_html__( 'You have cancelled the refund request already', 'woo-refund-and-exchange-lite' );
				} else {
					$wps_rma_rest_response['status'] = 404;
					$wps_rma_rest_response['data']   = esc_html__( 'You can only perform the refund request cancel when the request request has been made earlier', 'woo-refund-and-exchange-lite' );
				}
			} elseif ( empty( $order_obj ) ) {
				$wps_rma_rest_response['status'] = 404;
				$wps_rma_rest_response['data']   = esc_html__( 'Please Provide the correct order id to perform the process', 'woo-refund-and-exchange-lite' );
			} else {
				$wps_rma_rest_response['status'] = 404;
				$wps_rma_rest_response['data']   = esc_html__( 'Please Provide the order id to perform the process', 'woo-refund-and-exchange-lite' );
			}
			return $wps_rma_rest_response;
		}
	}
}
