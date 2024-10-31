=== Plugin Vulnerabilities ===
Contributors: whitefirdesign, pluginvulnerabilities
Tags: exploit, plugin security, plugin vulnerability, plugins, security, vuln, vulnerability, vulnerabilities
Requires at least: 4.0
Tested up to: 4.9
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Alerts you when exploited vulnerabilities are in your installed plugins and provides access to our more comprehensive Plugin Vulnerabilities service.

== Description ==

This plugin checks the plugins you have installed against a list of vulnerabilities in plugins that we have seen hackers trying to exploit. If the installed version of a plugin is vulnerable an alert is added to the Installed Plugins page and an email alert is sent, otherwise details of the vulnerabilities are included on the Plugin Vulnerabilities page.

This data can also be helpful when cleaning up a hacked website, as you want to determine how the website was hacked when doing that and this data may provide part of information needed to do that.

Since the vulnerability data for the plugin is included in the plugin, you will need to keep the plugin up to date to insure you have the latest data. You can use our [Automatic Plugin Updates plugin](https://wordpress.org/plugins/automatic-plugin-updates/) to automatically update this plugin and your other installed plugins.

You can use our [Plugin Security Checker plugin](https://wordpress.org/plugins/plugin-security-checker/) to check if a plugin might contain additional security issues.

= Sign Up For Our Plugin Vulnerabilities Service =

You can get alerted for known vulnerabilities in all the plugins you use, not just ones that we are already seeing evidence that hackers are targeting, when you [sign up](https://www.pluginvulnerabilities.com/product/subscription/) for our Plugin Vulnerabilities service. As the data for that comes from checking with our service's API, you don't need to update the plugin to get alerted to new issues and you can have checks done as often as hourly.

Currently our service warns about vulnerabilities in the most recent version of plugins with over one million active installs that are still available in the Plugin Directory.

Through the service you also have access to a number of [other important features](https://www.pluginvulnerabilities.com/why-plugin-vulnerabilities/) including the ability to [suggest/vote for which plugins we will do security reviews of](https://www.pluginvulnerabilities.com/wordpress-plugin-security-reviews/) and help when dealing with a situation where you are using a plugin where the vulnerability has yet to be fixed (we can usually provide a temporary fix for the issue).

You can currently sign up for half off when you use the coupon code "HalfOff" when [signing up](https://www.pluginvulnerabilities.com/product/subscription/). We include a free lifetime subscription to the service when [we do a WordPress Hack Cleanup](https://www.whitefirdesign.com/services/hacked-wordpress-website-cleanup.html).

== Frequently Asked Questions ==

= Where Does The Data Come From? =
As part of data collection for our [Plugin Vulnerabilities service](https://www.pluginvulnerabilities.com) we monitor a number of channels including our own websites, third party data on hacking attempts, and the WordPress support forums, for indications that hackers are targeting plugins. In many cases all we know based on that is that a plugin is being targeted, so we then will use our knowledge of previous vulnerabilities that hackers have targeted to find what a hacker may be targeting in the plugin.

= Adding/Correcting Our Data =
If you want to let us know of a missing exploited vulnerability or if we need to correct something in the listing for an included vulnerability, please contact us [here](https://www.pluginvulnerabilities.com/contact/). For missing vulnerabilities please include a link to the details of the vulnerability.

= Getting More Complete Data =
If you want to be warned about all vulnerabilities, not just that those that are already targeted by hackers you can sign up for our [Plugin Vulnerabilities service](https://www.pluginvulnerabilities.com).

= Can the Plugin Cause False Positives with Other Security Scanners =
This plugin determines if plugins are vulnerable based on the version of the plugin in use instead of trying to identify vulnerable code, so it will not cause false positives in other tools unless they are poorly made (which is true far too often).

== Screenshots ==

1. Alert Shown on Installed Plugins Page For Vulnerability In Version of Plugin In Use

2. Full Listing of Vulnerabilities With Frequent Exploitation Attempts That Have Existed in Installed Plugins

3. Email Alert

== Changelog ==

= 2.0.69 - 9/5/2018 =
* Added data on vulnerabilities in UnGallery and WP Support Plus Responsive Ticket System.

= 2.0.68 - 8/8/2018 =
* Added data on vulnerability in Ultimate Member.

= 2.0.67 - 6/8/2018 =
* Added data on vulnerability in Site Editor.

= 2.0.66 - 6/4/2018 =
* Added data on vulnerabilities in Category and Page Icons, HD Webplayer Plugin for WordPress, KingComposer, WordPress Font Uploader, and WP Js External Link Info.

= 2.0.65 - 5/10/2018 =
* Added data on vulnerabilities in Google Drive for WordPress (wp-google-drive).

= 2.0.64 - 3/5/2018 =
* Added data on vulnerabilities in IP Logger, Newsletters, and Open Flash Chart Core.

= 2.0.63 - 2/22/2018 =
* Added data on vulnerability in Category Order and Taxonomy Terms Order.

= 2.0.62 - 2/21/2018 =
* Added data on vulnerability in Smart Google Code Inserter.

= 2.0.61 - 2/13/2018 =
* Added data on vulnerability in Email Subscribers.

= 2.0.60 - 2/8/2018 =
* Added data on vulnerability in user files.

= 2.0.59 - 1/9/2018 =
* Added data on vulnerability in LearnDash LMS.

= 2.0.58 - 1/2/2018 =
* Added data on vulnerabilities in Duplicate Page And Post, No Follow All External Links, and WP No External Links.

= 2.0.57 - 12/22/2017 =
* Added data on vulnerability in Pretty Links.

= 2.0.56 - 12/20/2017 =
* Changed vulnerability in Table Maker.
* Added data on vulnerabilities in Gallery by BestWebSoft and Sharexy.

= 2.0.55 - 12/19/2017 =
* Added data on vulnerability in Table Maker.

= 2.0.54 - 12/15/2017 =
* Added data on vulnerabilities in Membership Simplified, SendinBlue Subscribe Form And WP SMTP, and Work The Flow File Upload.

= 2.0.53 - 11/27/2017 =
* Added data on vulnerability in PHP Event Calendar.

= 2.0.52 - 11/9/2017 =
* Added data on vulnerabilities in Formidable Forms and Shortcodes Ultimate.

= 2.0.51 - 10/16/2017 =
* Added data on vulnerability in Facebook Like Box.

= 2.0.50 - 10/9/2017 =
* Added data on vulnerabilities in Brandfolder and mb.miniAudioPlayer.

= 2.0.49 - 10/4/2017 =
* Added data on vulnerabilities in Appointments and Flickr Gallery.

= 2.0.48 - 9/11/2017 =
* Added data on vulnerabilities in Display Widgets.

= 2.0.47 - 8/16/2017 =
* Added data on vulnerabilities in Asgaros Forum and Social Sticky Animated.

= 2.0.46 - 6/8/2017 =
* Added data on vulnerabilities in 1 Flash Gallery, Flutter, Image Symlinks, MiwoFTP, N-Media Repository Manager, and WP-CRM.
* Added alert for vulnerability in current version on More Details pages when adding new plugins.

= 2.0.45 - 4/20/2017 =
* Added data on vulnerability in WooCommerce Catalog Enquiry.

= 2.0.44 - 4/11/2017 =
* Added data on vulnerabilities in Analytic and Lightbox Wp.

= 2.0.43 - 3/16/2017 =
* Added data on vulnerabilities in How to Create an App for Android iPhone Easytouch, Webapp builder, Wordpress Mobile app Builder, and Wp2android.

= 2.0.42 - 3/6/2017 =
* Added data on vulnerabilities in CMS Commander Client and Zen Mobile App Native.

= 2.0.41 - 3/3/2017 =
* Added vulnerabilities

= 2.0.40 - 2/13/2017 =
* Added vulnerabilities

= 2.0.39 - 2/6/2017 =
* Added vulnerabilities

= 2.0.38 - 1/30/2017 =
* Added vulnerabilities

= 2.0.37 - 1/27/2017 =
* Added vulnerabilities

= 2.0.36 - 1/26/2017 =
* Added vulnerabilities

= 2.0.35 - 1/25/2017 =
* Added vulnerabilities

= 2.0.34 - 1/9/2017 =
* Added vulnerabilities

= 2.0.33 - 12/15/2016 =
* Added vulnerabilities

= 2.0.32 - 12/12/2016 =
* Added vulnerability

= 2.0.31 - 11/15/2016 =
* Added vulnerability

= 2.0.30 - 11/8/2016 =
* Added vulnerability

= 2.0.29 - 10/28/2016 =
* Added vulnerabilities

= 2.0.28 - 10/24/2016 =
* Added vulnerabilities

= 2.0.27 - 10/20/2016 =
* Added vulnerabilities

= 2.0.26 - 10/14/2016 =
* Added vulnerability

= 2.0.25 - 10/6/2016 =
* Added vulnerabilities

= 2.0.24 - 10/3/2016 =
* Added vulnerabilities

= 2.0.23 - 9/23/2016 =
* Added vulnerabilities

= 2.0.22 - 9/19/2016 =
* Added vulnerabilities
* Added ability to see our estimate of the likelihood of a vulnerability being exploited, when using the companion service

= 2.0.21 - 8/29/2016 =
* Added vulnerabilities
* Added ability to see listing of false vulnerability reports to plugin's page when using the companion service

= 2.0.20 - 8/15/2016 =
* Added email alerts for vulnerabilities in plugins with exploit attempts (if you already have the plugin installed you will need to deactivate and then reactivate the plugin to turn these on)
* Improved admin page UI
* Added vulnerabilities

= 2.0.19 - 8/1/2016 =
* Added vulnerabilities

= 2.0.18 - 7/18/2016 =
* Added vulnerability

= 2.0.17 - 7/15/2016 =
* Added additional vulnerabilities

= 2.0.16 =
* Added additional vulnerabilities

= 2.0.15 =
* Added additional vulnerabilities

= 2.0.14 =
* Added additional vulnerabilities

= 2.0.13 =
* Added additional vulnerabilities

= 2.0.12 =
* Added additional vulnerabilities

= 2.0.11 =
* Added additional vulnerabilities

= 2.0.10 =
* Added additional vulnerabilities

= 2.0.9 =
* Added additional vulnerabilities

= 2.0.8 =
* Added additional vulnerabilities

= 2.0.7 =
* Added additional vulnerabilities
* Added vulnerability listings on plugin detail pages

= 2.0.6 =
* Added additional vulnerabilities

= 2.0.5 =
* Added developer advisories

= 2.0.4 =
* Added additional vulnerabilities

= 2.0.3 =
* Added additional vulnerabilities
* Stopped unnecessary cron runs

= 2.0.2 =
* Added additional vulnerabilities
* Stopped unnecessary cron runs
* Fixed issue causing some alerts to not be show on Installed Plugins page
* Update for API response change

= 2.0.1 =
* Added additional vulnerabilities

= 2.0 =
* Reduced included vulnerabilities to ones that have frequently exploit attempts
* Added capability to access Plugin Vulnerabilities service

= 1.0.34 =
* Added 8 vulnerabilities

= 1.0.33 =
* Added 6 vulnerabilities

= 1.0.32 =
* Added 7 vulnerabilities

= 1.0.31 =
* Added 11 vulnerabilities

= 1.0.30 =
* Added 12 vulnerabilities

= 1.0.29 =
* Added 7 vulnerabilities

= 1.0.28 =
* Added 7 vulnerabilities

= 1.0.27 =
* Added 8 vulnerabilities

= 1.0.26 =
* Added 7 vulnerabilities

= 1.0.25 =
* Added 16 vulnerabilities

= 1.0.24 =
* Added 8 vulnerabilities

= 1.0.23 =
* Added 8 vulnerabilities

= 1.0.22 =
* Added 9 vulnerabilities

= 1.0.21 =
* Added 8 vulnerabilities

= 1.0.20 =
* Added 20 vulnerabilities

= 1.0.19 =
* Added 8 vulnerabilities

= 1.0.18 =
* Added 9 vulnerabilities

= 1.0.17 =
* Added optional email alerts
* Added 9 vulnerabilities

= 1.0.16 =
* Added 9 vulnerabilities

= 1.0.15 =
* Added 11 vulnerabilities

= 1.0.14 =
* Added 6 vulnerabilities

= 1.0.13 =
* Added 5 vulnerabilities

= 1.0.12 =
* Added 11 vulnerabilities

= 1.0.11 =
* Added 4 vulnerabilities

= 1.0.10 =
* Added 7 vulnerabilities

= 1.0.9 =
* Added 4 vulnerabilities

= 1.0.8 =
* Added 6 vulnerabilities

= 1.0.7 =
* Added 9 vulnerabilities

= 1.0.6 =
* Added 17 vulnerabilities

= 1.0.5 =
* Added 16 vulnerabilities

= 1.0.4 =
* Added 14 vulnerabilities

= 1.0.3 =
* Added 30 vulnerabilities

= 1.0.2 =
* Added 8 vulnerabilities

= 1.0.1 =
* Added 6 vulnerabilities

= 1.0 =
* Initial release