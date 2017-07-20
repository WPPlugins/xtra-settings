=== XTRA Settings ===
Contributors: fures
Donate link: http://www.fures.hu/xtra-settings/donate.php
Tags: WordPress settings, hidden settings, tips and tricks, tweaks, WordPress options
Requires at least: 3.7
Tested up to: 4.8
Stable tag: 1.4.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

XTRA adds 40+ hidden settings, tweaks and options to tailor your wordpress website in a clean format and super-light weight.

== Description ==
XTRA adds 40+ hidden settings, tweaks and options to tailor your wordpress website in a clean format and super-light weight. The plugin uses WP actions, filters, the .htaccess file and the wp-config.php file for setting options. This plugin also includes a manual Database backup, cleanup and optimization tool to make your wordpress website lighter and faster. Also included are Related Posts, Share Buttons, WP Auto-Update, Maintenance Mode and Debug Mode with easy switches. Image compression organized in bulk ajax batches for fast speed and convenience.

= Features =
* Security - Server hardening, WP Security settings, disable xml-rpc and all feeds
* Speed - Compression, Cache, Memory and Minifier
* SEO - settings and JavaScript Defer and Footer
* Social - settings and Share Buttons
* WP Settings - Auto-Update, Maintenance and Debug modes
* Post settings - Revisions, Related Posts, Content changes
* Database - Backup, Cleanup and Optimize
* Cron Jobs - check and delete
* Plugins info
* Images - Bulk Compression and Maximum Size

== Installation ==
1. Install the plugin through the WordPress plugins screen directly or upload the plugin files to the `/wp-content/plugins/xtra-settings` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress Admin.

== Screenshots ==
1. **XTRA Settings Security screen**
2. **XTRA Settings Database Cleanup and Optimize screen**
3. **XTRA Settings Speed screen**
4. **XTRA Settings WP Settings screen**
5. **XTRA Settings Posts and Content screen**
6. **XTRA Settings WP Modes screen**

== Frequently Asked Questions ==
* Please make a backup of your .htaccess and wp-config.php files.
* Plugin deactivation restores all WP settings

== Changelog ==

= 1.4.6 =
* Added Ajax Bulk Image Compression methods with image backup and restore
* Added Auto-Resize large image upload to maximum width and height
* Tweaked Protect From Malicious URL Requests: including HTTP_USER_AGENT libwww, Wget, EmailSiphon, EmailWolf
* Tweaked php output buffer handling to increase code speed
* Tweaked JS and CSS position in the footer and defer parsing
* Tweaked regexp for add self-link, share buttons and related posts positioning

= 1.4.5 =
* Added Highlight Post Color by Status
* Added Defer parsing of all JavaScript in the SEO tab
* Added Move all JavaScript to the footer in the SEO tab
* Added Light View and Vertical Tabs in Xtra Plugin Settings
* Tweaked UI with sticky selected tabs
* Removed block URL longer than 255 from malicious requests in Security tab

= 1.4.4 =
* Fixed Share Buttons and Related Posts after article position

= 1.4.3 =
* Include section dashicons to make recognition easier
* Some tweaks on Share Buttons and Related Posts positioning
* Tweaked the gui to clear design and improve readability

= 1.4.2 =
* Added Related Posts feature with options in Posts tab
* Fixed Cron job removal
* Tweaked meta keywords on non-tagged pages
* Tweaked show share buttons with [xtra_share_buttons] shortcode
* Tweaked the HTML Minifier code working with inline img codes (e.g. Ploylang flags)

= 1.4.1 =
* Added Check Auto-Update Now trigger button
* Added Plugins Auto-Update exclude list
* Added Remove double title meta tag in SEO
* Fixed readme bug in Upgrade Notice
* Fixed SEO bug adding head meta and OG tags
* Tweaked Add self-link to all uploaded images
* Tweaked the HTML Minifier code to be more reliable

= 1.4.0 =
* Tweak Open All Links in New Tab and Image Self Link regexes
* Tweak plugin deactivation method
* Tweak getting post image as thumbnail or 1st attachment or 1st in-post image
* Removed the tab called All
* Added color codes for tabs
* Added HTML Minifier

= 1.3.9 =
* Added X-Header Apache security settings to protect 3 attack types
* Finetuned Apache server settings for Compression and Caching
* Separate Apache server settings in case you have a different web server

= 1.3.8 =
* Fixed Social share buttons not just on posts, also on pages

= 1.3.7 =
* Added Social tab
* Added 8 Social share buttons in the Social tab
* Added Facebook JS SDK block option
* Added Redirect Attachments to their Parent Post
* Repositioned 2 redirect settings into the SEO tab

= 1.3.6 =
* Added Cron Jobs management
* Added SEO tab
* Added Facebook OG tags and Twitter cards in HTML head using post title, excerpt and thumbnail image
* Remove Plugins Information - did not add extra value
* Optimized tabs: put all WP settings into one tab

= 1.3.4 =
* Tabbed user interface
* Added Maintenance mode and Plugins Information

= 1.2.1 =
* Redesigned user interface
* Added Database Backup, Cleanup and Optimize features

= 1.1.0 =
* Added WordPress Debug mode settings
* Added WordPress Auto-Update settings

= 1.0.0 =
* Initial version

== Upgrade Notice ==

= 1.4.6 =
For the best user experience, always upgrade to the latest version.