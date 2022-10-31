=== OTP Login Woocommerce & Gravity Forms===
Contributors: xootix, xootixsupport
Donate link: https://www.paypal.me/xootix
Tags: woocommerce, sms, login, phone, register, gravity form
Requires PHP: 5.2.4
Requires at least: 3.0.1
Tested up to: 6.0
Stable tag: 2.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

 Allow users to login/signup with One time password(OTP) sent on their phone

== Description ==
[Live Demo](http://demo.xootix.com/mobile-login-for-woocommerce/)
Allow users to login/signup with one time password(OTP) sent on your mobile device.

### Features And Options:

* Adds a phone number field to the signup form
* No need to remember email/password, login with OTP
* Fully customizable
* Woocommerce & Gravity Forms Compatible
* Fully Ajaxed.


= Third Party Services =

This plugin uses third party services to send SMSs

### SMS Operators available:

* [Firebase ( Free messages )](https://firebase.google.com/)
* [Twilio](https://www.twilio.com/)
* [Amazon](https://aws.amazon.com/sns/)

For detecting country code, plugin uses API endpoints for looking up user IP address. This is optional and can be disabled from the settings
Options available
[ipify](http://api.ipify.org), [ipecho](http://ipecho.net/plain), [ident](http://ident.me), [whatismyipaddress](http://bot.whatismyipaddress.com)


### Pro Features:

* Popup & inline form design ( Try here)
https://wordpress.org/plugins/easy-login-woocommerce/
* Woocommerce checkout form integration

== Documentation ==
**[Documentation](https://docs.xootix.com/mobile-login-for-woocommerce/)**

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Click on Login/Signup Popup on the dashboard.

== Frequently Asked Questions ==

= How to setup? =
1. Go to plugin settings.
2. You need a third party service to send SMS, select the desired one.
3. Please read the documentation to get API keys.


= How to translate? =
It is recommended to use plugin "Loco Translate". Below is the manual way.
1. Download PoEdit.
2. Open the mobile-login-woocommerce.pot file in PoEdit. (/plugins/mobile-login-woocommerce/languages/
   mobile-login-woocommerce.pot)
3. Create new translation & translate the text.
4. Save the translated file with name "mobile-login-woocommerce-Language_code". For eg: German(mobile-login-woocommerce-de_DE)
   , French(mobile-login-woocommerce-fr_FR). -- [Language code list](https://make.wordpress.org/polyglots/teams/)
5. Save Location: Your wordpress directory/wp-content/languages/


= How to override templates? =
Plugin template files are under mobile-login-woocommerce/templates folder.
Copy the template file to your theme->templates folder and make the desired changes there.





== Screenshots ==
1. Banner

== Upgrade Notice ==

= 1.0 =

== Changelog ==

= 2.1 =
* Fix - Security issues

= 2.0 =
* New - Inline input for OTP Verification
* New - Country code for login form
* New - Improved country code select field
* New - Phone number column on users page
* Tweak - Design improvement

= 1.4 =
* Fix - Geolocation not working
* Fix - Conflict with dokan
* Fix - Other minor fixes

= 1.3 =
Fix - Phone code not showing

= 1.2 =
Fix - Disable OTP registration

= 1.1 =
* Add - Twilio Service
* Add - (Option) auto signup on verification
* Add - (Option) - display OTP form first 
* Tweak - Slight changes in design

= 1.0.0 =
* Initial Public Release.