=== WooCommerce Multilingual & Multicurrency with WPML ===
Contributors: AmirHelzer, strategio, dgwatkins, andrewp-2
Tags: CMS, woocommerce, commerce, ecommerce, e-commerce, products, WPML, multilingual, e-shop, shop
License: GPLv2
Requires at least: 4.7
Tested up to: 6.0
Stable tag: 5.0.2
Requires PHP: 5.6

Make your store multilingual and enable multiple currencies.

== Description ==
Everything you need to start selling internationally is in this plugin. Easily set up and manage products in multiple currencies, no matter the size of your store or the team running it. Or, upgrade by purchasing WPML and translate your entire store to reach new customers all over the world.

See how it works in this short video:

https://www.youtube.com/watch?v=NxkzEywVZSA

= Free Multicurrency Features =

This is the **only free plugin** that includes all of the following [multicurrency features](https://wpml.org/documentation/related-projects/woocommerce-multilingual/multi-currency-support-woocommerce/):

* Set up multiple currencies to display based on a customer’s location
* Add currency switchers to your site
* Format your currencies
* Set your own exchange rates or connect with an automatic exchange rate service
* Set custom prices and shipping rates in your secondary currencies

= Add WPML to Make Your Store Multilingual =

Translate your entire store and unlock even more multicurrency features by pairing WooCommerce Multilingual & Multicurrency with [WPML](https://wpml.org) - the most popular translation plugin for WordPress sites:

* Translate all WooCommerce products (simple, variable, grouped, external)
* Translate all store URLs and endpoints
* Translate product reviews
* Translate product categories and attributes
* Translate content automatically using DeepL, Google Translate, and Microsoft
* Keep the same language throughout the checkout process
* Send emails to clients and admins in their language
* Track inventory without breaking products into languages
* Display currencies based on site language
* Use different payment methods for each currency
* Add functionality using WooCommerce REST API

To get all multilingual features, you will need a [WPML Multilingual CMS or Multilingual Agency account type](https://wpml.org/purchase).

Read more about [translating your WooCommerce store with WPML and WooCommerce Multilingual](https://wpml.org/documentation/related-projects/woocommerce-multilingual/).

= Compatibility With Woocommerce Extensions =

Almost every WooCommerce store uses some extensions. WooCommerce Multilingual is fully compatible with popular extensions, including:

* [WooCommerce Subscriptions](https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-subscriptions-woocommerce-multilingual/)
* [WooCommerce Product Add-ons](https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-product-add-ons-woocommerce-multilingual/)
* [WooCommerce Product Bundles](https://wpml.org/plugin/woocommerce-product-bundles-2/)
* [WooCommerce Bookings](https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-bookings-woocommerce-multilingual/)
* [WooCommerce Composite Products](https://wpml.org/plugin/woocommerce-composite-products-2/)
* [WooCommerce Tab Manager](https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-tab-manager-woocommerce-multilingual/)
* [WooCommerce Table Rate Shipping](https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-table-rate-shipping-woocommerce-multilingual/)

Looking for other extensions that are tested and compatible with WPML? See the complete [list of WordPress plugins that are compatible with WPML](https://wpml.org/documentation/woocommerce-extensions-compatibility/).

== Screenshots ==
1. Currency switcher on the front-end
2. WooCommerce Multicurrency
3. Adding a currency
4. Adding currency switchers
5. Currency switcher options
6. Setting automatic exchange rates
7. Setting custom prices in different currencies
8. Setting custom shipping rates
9. WCML standalone mode

== Frequently Asked Questions ==
= Does this work with other e-commerce plugins? =

No. This plugin is tailored for WooCommerce.

= What do I need to do in my theme? =

Make sure that your theme is not hard-coding any URL. Always use API calls to receive URLs to pages and you'll be fine.

= How do I edit the translations of the cart or checkout page? =

Some themes and plugins provide their own translations via localization files. WordPress loads these translations automatically.

To change any of these translations, you need to scan the theme or plugin providing these files. Go to **WPML &rarr; Theme and Plugins Localization**, select the theme or plugin providing the checkout page, and scan it.

After scanning, you should have the strings available in **WPML &rarr; String Translation**.

Read more about [translating cart and checkout pages](https://wpml.org/documentation/related-projects/woocommerce-multilingual/translating-cart-and-checkout-pages/).

= Can I have different URLs for the store in different languages? =

Yes. You can translate the product permalink base, product category base, product tag base and the product attribute base on the Store URLs section.

= Why do my product category pages return a 404 error? =

In this case, you may need to translate the product category base. You can do that on the Store URLs section.

= Can I set the prices in the secondary currencies? =

By default, the prices in the secondary currencies are determined using the exchange rates that you fill in when you add or edit a currency. On individual products, however, you can override this and set prices manually for the secondary currencies.

= Can I have separate currencies for each language? =

Yes. By default, each currency will be available for all languages, but you can customize this and disable certain currencies on certain languages. You also have the option to display different currencies based on your customers’ locations instead.

= Is this plugin compatible with other WooCommerce extensions? =

WooCommerce Multilingual is compatible with all major WooCommerce extensions. We’re continuously working on checking and maintaining compatibility and collaborate closely with the authors of these extensions.

== Installation ==
= Minimum Requirements =

* WordPress 4.7 or later
* PHP version 5.6 or later
* MySQL version 5.6 or later
* WooCommerce 3.9.0 or later

= Setup =

Install and activate “WooCommerce Multilingual & Multicurrency” on your WordPress site. Then, go to **WooCommerce &rarr; WooCommerce Multilingual & Multicurrency** and enable the multicurrency mode to add more currencies to your store. Read more about [setting up multiple currencies for your online store](https://wpml.org/documentation/related-projects/woocommerce-multilingual/multi-currency-support-woocommerce/).

If you also use the WPML plugin for multilingual functionality, follow the setup wizard to translate the store pages, configure what attributes should be translated, enable the multicurrency mode and more. Read more about [translating your online store](https://wpml.org/documentation/related-projects/woocommerce-multilingual/).

== Changelog ==

= 5.0.2 =

* Removed some leftover development files.
* Fixed the count for parent product categories.
* Restored the filter hook `wcml_client_currency` outside the cached logic.

= 5.0.1 =

Fixes
* Fixed the upgrade routine for the attribute look-up table
* Fixed a fatal error that occurred on sites running a WPML version older than 4.5.2
* Removed an obsolete filter on the â€œwoocommerce_create_page_idâ€ hook
* Fixed an issue with duplication of product terms when using WPMLâ€™s Classic Translation Editor on WordPress 6.0

Compatibility
* WooCommerce Product Bundles: Added translation support for bundle sales

= 5.0.0 =

Features
* WCML can now run as a standalone plugin (without WPML) and offer all the multi-currency features.
* Added support for the new attribute lookup table.
* Added Advanced Translation Editor support for WooCommerce Bookings.
* Added Advanced Translation Editor support for WooCommerce Product Add-ons.
* Added Advanced Translation Editor support for WooCommerce Product Bundles.
* Added Advanced Translation Editor support for WooCommerce Composite Products.
* Added support for â€œWooCommerce Paypal Paymentsâ€ gateway.
* Added support for exchange rate services using the new accounts on API Layer (currencylayer, fixer.io, Exchange Rate API).

Fixes
* Added support for sales of products with custom prices in other currencies.
* Fixed an issue with generating URL slugs when translation is missing from MO file.
* Fixed the WooCommerce REST API when using a URL that contains the language folder.
* Fixed UI distortion when quick editing a product with Yoast SEO plugin enabled.
* Refactored the client currency resolution logic to fix a number of bugs and performance hits.
* Implemented the forcing of translating admin options when sending emails from the dashboard (requires WPML String Translation v3.2.2).
* Fixed the display of the warning message for clashing category slugs.
* Fixed a display glitch in the multi-currency settings when a rate has too many numbers.
* Fixed an issue to show some WCML links on the products list only when the user actually has access to the target pages.
* Fixed an issue with category product count is not updated for translations.
* Merged variations in different languages into one in WC Analytics.
* Fixed a UI glitch (persistent container) once the MaxMind key is set.
* Resolved the recent regressions with the category thumbnails.
* Fixed the missing language column in WC Analytics when multi-currency is disabled.
* Stopped handling the favicon.ico request causing some state inconsistencies.
* Fixed an issue with switcher currencies getting filtered for a second time in the wrong mode.
* Fixed a number of styling issues on the multi-currency settings page.
* Extended the scope of the product reviews translation hook to also load on AJAX requests.
* Fixed the link to WooCommerce â†’ Advanced on WCML â†’ Store URLs.
* Fixed the WooCommerce Attribute Widget Count if the Attribute is set to not translatable in WPML.
* Added a fix to prevent a fatal error with 3rd party gateways when the class definition cannot be found.
* Fixed a possible compatibility issue with WP Rocket when trying to auto-fix the multi-currency settings.

Compatibility
* Name Your Price: Added currency conversion for manually entered prices.
* Name Your Price: Added support for changing the currency of price entered in the cart.
* Stripe Payment Gateway: Fixed an issue with the wrong currency symbol.
* Mix and Match v2: Fixed a compatibility issue with the multi-currency mode.
* WooCommerce Product Add-ons: Fixed a compatibility issue related to global add-on fields assigned to a product category.
* WooCommerce Checkout Add-ons: Fixed the price that was not converted in the default language.

Usability
* Added the ability to pre-fill the currency rate from the exchange rate service when adding a new currency.
* Added the ability to automatically trigger the rates update when a service exchange rate key is added.
* Improved error content when getting exchange rates from a service is failing.
* Added force saving and reloading multi-currency settings when the user adds the first secondary currency.
* Changed the order of exchange rate services, now sorted alphabetically.
* Replaced the currency mode dropdown with radio buttons.

Misc
* Raised the minimal WPML requirement to 4.5.2.
* Updated the OTGS Installer to version 3.


= 4.12.0 =

* Added two more exchange rate services: http://exchangeratesapi.io and http://openexchangerates.org.
* Added an option to update currency exchange rates every hour.
* Added a new option to display product reviews in all languages by default.
* Product reviews can now be translated.
* Product reviews are now wrapped in a div with lang parameter for better SEO.
* Removed loading of jQuery cookie library.
* Began storing the client country when geolocation is in use to allow cache plugins to deliver pages based on location.
* Raised the WPML requirements to 4.4.11.
* Fixed an issue with applying rounding to shipping rates on checkout.
* Fixed an issue with synchronizing translations of WooCommerce Bookings.
* Fixed a possible PHP warning for state inconsistencies in Product Bundles integration.
* Fixed an issue with advanced category rules in Dynamic Pricing.
* Fixed an issue with translated duplicates in the list of store URLs.
* Fixed an issue with translating category IDs in REST response.
* Added language support for the search block widget.
* Fixed a fatal error when updating the product with V1 of the REST API.
* Fixed the results of the Filter by price widget in a secondary currency (with automatic conversion).
* Fixed issues with updating order totals on the order edit screen.
* Fixed a fatal error with single function REST callback.
* Fixed an issue with erratic wrong child product transients.
* Fixed WooCommerce REST namespace detection.
* Fixed bundle items price while creating a new order from backend.
* Fixed compatibility with Name Your Price extension in version 3.0.
* WooCommerce Analytics Dashboard now will not display translated products separately.
* Fixed a conflict when using the wpml_sync_custom_field action hook on a product field.
* Fixed an issue with translating WooCommerce Table Rate Shipping rateÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢s abort messages.
* Fixed an issue with the currency switcher not showing when a new language is added.
* Fixed scenarios for translated products in the WooCommerce Composite Products addon.
* Fixed translating singular labels for attribute taxonomies.
* Fixed the wrong product translation author when using the Classic Translation Editor.
* Fixed the product analytics pagination.
* Variation Swatches And Photos ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œ Fixed a bug when the custom attribute translation is the same as the original.
* Fixed a PHP notice from the currency switcher when an extra language is added.
* Fixed the checkout block redirecting to the wrong language for the confirmation page.
* Fixed the currency conversion when switching subscriptions.
* Fixed the encoding of base permalinks that contain slashes.
* Fixed an issue with Print Invoices/Packing Lists addon using the admin language instead of the order language.
* Improved the attribute taxonomy translation UI when itÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢s not publicly queryable.
* Fixed a link pointing to the wrong admin screen to translate non-WooCommerce taxonomies.
* Fixed an issue with displaying the wrong product category count between languages.
* Added /order-pay/ to reserved endpoints for absolute links converting.
* Fixed the missing and/or duplicated emails with Dokan.

= 4.11.0 =
* Fixed missing product tabs comming from WooCommerce Tab Manager plugin.
* Better compatibility with plugins that manipulate the cart.
* Fixed not working product category order synchronization between languages.
* Fixed shipping country used instead of billing one when checkout.
* Increase minimum Woocommerce version to 3.9.0.
* Fixed error for "Fix translated variations relationships" troubleshooting option.
* Woocommerce Analytics Dashboard now will not display translated products separately.
* Added noindex/nofollow to all reviews page and link redirecting to it (filterable with wcml_noindex_all_reviews_page filter).
* Fixed not translated payment gateway title in new order admin email.
* Fix bought product in different languages.
* Fix when _product_image_gallery meta key contains an extra blank value.
* Set default language for orders while installation.
* Handling warning for rating for newly translated products.
* [WooCommerce Dynamic Pricing] Fix advanced category rules.
* Add Rest API support for more compatibility, like taxonomies, product variations, reports etc.
* Fixed not translated heading paid for Customer Invoice e-mail.
* Fixed all products block in secondary language.
* Override template only if it was not overriden before that.
* Added compatibility class for WOOF - WooCommerce Products Filter plugin.
* Fixed comment synchronization on duplicate content does not copy metadata of WooCommerce.
* Translate Composite Products scenario IDs data .
* Added a notice when multi-currency feature is enabled and an active cache plugin is detected.
* Fixed huge loading time for "Pay for order" Woocommerce page.
* Fixed an undue warning popup when leaving the multi-currency settings page.
* Make the currency switcher to appear on 'my account' page.
* Fixed interface glitches with Stripe payment and multicurrency.
* Fixing the link to edit translation in WooCommerce Translation Editor.

= 4.10.0 =
* Currencies and payment options based on location.
* Fixed notice after WooCommerce Currency was changed.
* Fixed not translated partial refunded email heading and subject.
* Fixed the WC Bookings email string not updated in the settings screen.
* Fixed a PHP notice when one language is not set inside the currency languages settings.
* Fixed a fatal error with MercadoPago addon on WC Settings page.
* Fixed the usage of `wp_safe_redirect` and `wp_redirect` and take into account the returned value before to exit.
* Fixed empty attribute label for translations.
* Fix Redis cache when using Display as Translated mode and creating a variable product.
* Fixed a PHP Notice for some custom fields showing in the classic translation editor.
* Fixed the filter on wc_get_product_terms returning term names instead of slugs.
* Fixed multiple "Low stock" emails are not received by the admin.
* Fixed attribute label translation in German as a secondary language.
* Fixed not ended sale price in secondary currency if same sale dates uses from default.
* Fixed our gateways initialization on `wp_loaded` action.
* Fixed the WC Bookings reminder email that was sent in the wrong language.
* Fixed the WC Bookings email reminders sent multiple times.
* Fixed an issue creating empty "_gravity_form_data" post meta on product translation.
* Fixed no products on secondary language shop page if default language shop page contains special symbols.
* Fixed a performance issue due to comments filtering.

= 4.9.0 =
* Added new hook to Gravity Forms compatibility class.
* Manual shipping prices in secondary currencies.
* Fixed product attribute slug language not changed after changing value.
* Fixed missing numeric attribute values after translation using ATE.
* Fixed mini-cart total calculation when switching a currency.
* Fixed out of stock variable products if "Show only products with custom prices in secondary currencies" option is enabled.
* Fixed WC Tab Manager custom tab translation from ATE was not saved if the description is empty.
* Fixed an error which some additional plugins may cause with WC_Email object.
* Add a filter for WCML_WC_Gateways::get_current_gateway_language().
* Fixed not synchronized WooCommerce Tab Manager global tabs while saving product translation via ATE.
* Fixed not updated tax label after a change on settings page.
* Fixed the value of a custom attribute translation is overwritten on saving the original product.
* Fixed overwritten composite data title and description in translation after original product update.
* Fixed js console error in languages_notice.js file.
* Add language filtering for WooCommerce dashboard stock widgets.
* Fixed creating of several memberships in WooCommerce Membership plugin.

= 4.8.0 =
* Fixed JS SyntaxError on Products listing page.
* Fixed not registered 'Additional Content' emails setting text after first saving.
* Remove extra slash from the end of the translated base slug if a user added it.
* Fix custom fields translation in Translation Editor for Variations post type.
* Fixed customer Completed email has not translated heading and subject with WooCommerce 4.0.
* Fixed duplicated currency code in "Default currency" drop-down on Multi-currency settings page.
* Fixed language selector displayed in wrong place on Permalinks settings page.
* Fix customer order status email language when sent the shop manager use english language and english is not an active language.
* Fixed attributes synchronization may break variations relationships.
* Fixed not saved custom prices if translation is duplicated and Native screen editor selected.
* Fixed multiple same post meta keys translations.
* Add variation single "translatable" custom fields to translation package.
* Fixed error on Subscription renewal via PayPal.
* Fixed not saved The Events Calendar ticket meta if translation done by Translation Service.

= 4.7.0 =
* Replaced some Twig templates with pure PHP templates as the first step towards the removal of Twig dependencies.
* added comp. class to cover price update when products are edited with WOOBE plugin
* Added compatibility class for WooCommerce order status Manager  plugin
* Fixed an issue where the strings for the default payment methods were not properly translated on the Checkout page.
* Fixed an issue with the cache flush during language switching.
* Fixed in the original ticket.
* Fixed an issue where the gateway strings would always register in English instead of the site's default language.
* Fixed languages column width on products table.
* Fixed PHP Notice for WC Variations Swatches And Photos compatibility.
* WooCommerce Bookings compatibility : Fixed notice when trying to cancel booking.
* Fixed an issue where the total price on the Composite product page was not rounded.
* Fixed an issue causing wrong rewrite rules after saving the settings and visiting a page in a language other than the default.
* Fixed an issue with incorrect price converting for the Product add-ons.
* Fixed an issue with the WooCommerce Subscriptions availability in the secondary language after purchasing the subscription in the original language.
* Fixed an issue with the currency reverting to the default one during checkout.
* Fixed removed meta from original product not synchronized to translation.
* Fixed an issue where the BACS gateway instructions were not translated when re-sending the customer notification email from the admin.
* Fixed an issue with missing language information for attribute terms that happened after changing the attribute slug.
* Removed the Twig Composer dependency as it now relies on Twig from the WPML core plugin.
* Fixed an issue where customers would not receive notifications in the correct language.
* Fixed an issue where the Products shortcode was not working in the secondary language.
* Fixed error while sending WooCoomerce Bookings email for bookings which didn't have orders assigned.
* Added compatibility for free version of YIKES Custom Product Tabs.
* Updated compatibility class for WC Checkout Addons
* Fixed the images that were wrongly inserted in the translation job when attachments are not translatable.
* Significantly improved the site performance on when updating the page, post, or a WooCommerce product page in the admin.
* Added the "wp_" prefix to all cookies so that hosting and caching layers can properly handle them.
* Fixed a JavaScript error on the Store URLs tab.
* Fixed an issue where the "Fix translated variations relationships" troubleshooting option was removing translated variations.
* Fixed an issue where product names were not translated in the admin emails.
* Fixed an issue with the price filter widget not showing results in a secondary language.
* Fixed an issue where the shipping classes in secondary languages were not calculated during checkout.
* Display larger images when hovering thumbnails in the WooCommerce Multilingual Products admin page.
* Added the "wcml_new_order_admin_email_language" filter to allow setting the language of emails sent to admins for new or updated orders.
