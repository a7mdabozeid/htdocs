/* global jQuery */
import "./assets/styles/index.scss";
import { orders_with_coupons_init } from "./orders_with_coupons";

jQuery( document ).ready( ($) => {

    orders_with_coupons_init( $ );
} );