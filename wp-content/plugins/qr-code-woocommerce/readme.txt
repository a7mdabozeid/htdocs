=== QR Code Woocommerce ===
Contributors: gangesh
Tags: qr-code, qrcode, woocommerce
Requires at least: 4.8
Tested up to: 5.9
Requires PHP: 5.6
Stable tag: 2.0.5
License: GPL
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin creates printable QR Codes for Simple and Variable product types also for Coupon code as well.

== Description ==

<a href="https://wooqr.com">Demo<a/>

Latest Update - 16 Aug 2021
* Added QR design option with color, text and image overlay options.

Update - 22 Jul 2021
* Complete overhaul of code. Using Woocommerce REST API and JS based QR code library

Feature - 8th Feb 2021
* Products -> Bulk QR Code Generator

Simple yet powerful plugin that facilitate Woocommerce shops to be accessible by mobile devices. With this plugin QR code are generated and can be printed for Simple and variable product types. Plugin also supports Coupon QR code.
With print PDF option shop owners can reach users by offline means, like physical stores or marketing banners.

Useful for:
1. Allow easy access over mobile devices.
2. Physical Store Owners.
3. Affiliates.
4. Marketing on multiple domains.
5. Offline promotions.


Shortcode usage:

For product
[wooqr id="product_id" title="1" price="1"]

For Coupon
[wooqr id="coupon_id" title="1" description="1"]

product_id = Actual product id.
title = If title is set to 1, Product title will show below QR code.
price = If price is set to 1, Product Price will show.
description = Enable or disable coupon description

From v 1.0
* product_id is not required if shortcode used on Single product page.

Currently plugin supports following features

1.Generate or Delete Simple Product Qr code
2.Generate or Delete Variable Product Qr Code
3.Download Simple or Variable Product Pdf
4.Download Coupon Qr or Coupon pdf

== Screenshots ==
1. Simple Product
2. Variable Product
3. Coupons
4. Shortcode Output
5. Bulk QR Code
6. Design QR code

== Installation ==
To install this plugin:

1. Download the plugin
2. Upload the plugin to the wp-content/plugins directory,
3. Go to “plugins” in your WordPress admin, then click activate.


== Changelog ==

= 2.0.5 =
* Feature: Added Google font integration for QR code label design

= 2.0.4 =
* Bug fix: Added default value for QR design

= 2.0.3 =

* Feature: QR design section added
* Modify: Add separate menu option for QR design

= 2.0.2 =

* Fix: Added fix for Variable price range display

= 2.0.1 =

* Fix: Added check for ID in shortcode parameter


= 2.0 =

* Complete overhaul of code. Using Woocommerce REST API and JS based QR code library


= 1.1 =

* Separated Bulk QR code file for better management
* Updated variation query for Bulk QR code generation
* Fixed product_type woocommerce notice.


= 1.0 =

* Shortcode can work without product ID if used on Single Product page.


= 0.9 =

* Generate Bulk QR code from single screen


= 0.8 =

* Added fix to wrap long title text below QR code

= 0.7 =

* Added fix to add Variable product attribute with product title
* Minor UI fixes

= 0.6 =

* Minor UI fixes

= 0.5 =

* Product title added on QR Code on print option
* QR image size increased for Print option
* Minor UI fixes


= 0.4 =
* Shortcode fix for Gutenburg editor
* Added frontend style for shortcode


= 0.3 =
* Banner updated

= 0.2 =
Readme Update with User case and Shortcode instructions

= 0.1 =
* Initial Release