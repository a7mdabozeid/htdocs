===  CBX User Online & Last Login ===
Contributors: codeboxr, manchumahara
Tags: wordpress user online,useronline,bbpress,buddypress,last login
Requires at least: 3.5
Tested up to: 6.0.0
Stable tag: 1.2.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shows online users based on cookie for guest and session for registered user. It also records the last login of user.

== Description ==

This plugin helps to show online users. Member, guest and bot can be tracked, their counts, most users online etc. This plugin also helps to records any user’s last login time, ip address and device.


### CBX User Online & Last Login by [Codeboxr](https://codeboxr.com/product/cbx-user-online-for-wordpress/)

>📺 [Live Demo](https://codeboxr.net/wordpress/demo-cbx-user-online-for-wordpress/) | 📋 [Documentation](https://codeboxr.com/documentation-for-cbx-user-online-last-login/) | 🌟 [Upgrade to PRO](https://codeboxr.com/product/cbx-user-online-for-wordpress/) |  👨‍💻 [Free Support](https://wordpress.org/support/plugin/cbxuseronline/) | 🤴 [Pro Support](https://codeboxr.com/contact-us) | 📱 [Contact](https://codeboxr.com/contact-us/)


**If you think any necessary feature is missing contact with us, we will add in new release. Best way to check the feature is install the free core version in any dev site and explore**

### 🛄 Core Plugin Features ###

*   Cookie for guest user and login session check for registered user which tracks users perfectly.
*   Most user online count and date
*   Shortcode and widget based display
*   Username, ip, user agent, is from mobile or desktop etc are tracked
*   Simple plugin option  to set refresh time
*   Show logged in member as online list
*   Show site or specific page's online user statistics
*   Dynamically created cookie name for guest visitor
*   [New] Records user's last login time, ip and device from v1.0.6
*   [New] Elementor & WPBakery support from v1.0.9

[youtube https://www.youtube.com/watch?v=nZbt4BtqArI]


## 📺 Live Demo ##
Check [Live Demo](http://codeboxr.net/wordpress/demo-cbx-user-online-for-wordpress/)

##  🧮 Shortcodes ##
Shortcode with lot of params. Shortcode works for any post, page or do_shortcode.
Shortcode Format: `[cbxuseronline]`

## 🀄 Widgets ##

**Classic Widgets**

* Show Memberlist
* Link user to author page
* Show online count
* Show individual count
* Show member count
* Show guest count
* Show bot count
* Show bot count
* Show for current page
* Show most user online
* Show mobile or desktop logged in status

**Elementor Widgets**

* Show Member list
* Link user to author page
* Show online count
* Show individual count
* Show member count
* Show guest count
* Show bot count
* Show bot count
* Show for current page
* Show most user online
* Show mobile or desktop logged in status

**WPBakery Addon**

* Show Member list
* Link user to author page
* Show online count
* Show individual count
* Show member count
* Show guest count
* Show bot count
* Show bot count
* Show for current page
* Show most user online
* Show mobile or desktop logged in status

**👨‍🏫 [See more details and usages guide here](https://codeboxr.com/product/cbx-user-online-for-wordpress)**


### 💎 Pro Plugin Features ###
Note: free version will be always free but we released pro version with some more extra features.

* Dashboard widgets: Online users
* Dashboard widgets: Latest Logged in users (New)
* Dashboard details online user page
* Pro version enables some extra features in shortcode params and widget setting
* Support Elementor & WPBakery page builder for Latest Logged in users.
* Admin details page custom setting
* Shortcode extra params
* Buddpress profile link integration (New in V1.0.4)
* BBpress profile link integration (New in V1.0.4)
* BBpress Online User Statistics (New in V1.0.5)
* User login history feature (New in 1.2.3)


### 👍 Liked Codeboxr? ###

- Join our [Facebook Page](https://www.facebook.com/codeboxr/)
- Learn from our tutorials on [Youtube Channel](https://www.youtube.com/user/codeboxr)
- Or [rate us](https://wordpress.org/support/plugin/cbxuseronline/reviews/#new-post) on WordPress


**🔩 Installation**

How to install the plugin and get it working.


1. Upload `cbxuseronline ` folder  to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Setting-> CBX Useronline to edit settings
4. In any post or page you can write shortcode [cbxuseronline]


== Frequently Asked Questions ==

= what's the cookie name left in browser  ? =

Cookie name is "cbxuseronline-cookie", value is created dynamically



== Screenshots ==


== Changelog ==
= 1.2.8 =
* [Improvement] Cookie doesn't init for cron pages to avoid php warning

= 1.2.7 =
* [Improvement] Cookie init moved to 'init' hook.

= 1.2.6 =
* [Fixed] Plugin's dashboard url fixed from plugin listing page which was messed up in last release

= 1.2.5 =
* [Fixed] Classic widget now saves properly in block based widget
* [Improvement] Helps and Updates are added as sub menu under the main user online dashboard menu setting
* [Pro Addon] New version of pro addon released and need core version 1.2.5 as minimum version required

= 1.2.4 =
* [New] added new static method to check if user online by user id, example: $is_user_online = CBXUseronlineHelper::is_user_online($user_id);
* [Improvement] Documentation updated

= 1.2.3 =
* [New] Add new help & doc page.
* [Improvement] Backend Dashboard UI improved.
* [New] User login history feature in pro addon

= 1.2.2 =
* [Bug Fix] VC and Elementor widget minor improvement

= 1.2.1 =
* [Bug Fix] Doesn't show error or warning for theme or core upgrades.

= 1.2.0 =
* [Bug Fix] Shortcode output now returns, without echos, bug fixed !

= 1.0.10 =
* [Updated] Setting page online user listing now displays user's current browsing url and page title

= 1.0.9 =
* [New Feature] Elementor support added
* [New Feature] WPBakery support added
* [New Feature] Backend setting new tab for current online users
* [New Feature] Backend setting style improvement


= 1.0.8 =
* [Improvement] Updated Setting Api

= 1.0.7 =
* [New Feature] Added new feature to record second last login time, by default off

= 1.0.6 =
* [New Feature] User's last login in admin user listing
* [New Feature] Reset plugin from the plugin setting
* [New Feature] Delete plugin data on uninstall

= 1.0.5 =
* [New Feature] Purge login log record data from setting
* [New Feature] Purge login log when someone log out, solves duplicate show
* [New Feature] Pro addon updated for bbPress to show online user

= 1.0.4 =
* [New Feature] New fields section name as "Integration" for 3rd party plugin integration

