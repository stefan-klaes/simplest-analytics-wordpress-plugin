=== Simplest Analytics ===
Contributors: codenlassen
Donate link: https://www.paypal.com/donate/?hosted_button_id=DM53FXEJ53CUJ 
Tags: analytics, statistic, tracking, cookieless
Requires at least: 4.0
Tested up to: 6.1
Requires PHP: 7.4
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple webanalytics stored in the own database without setting cookies.

== Description ==

Simplest Analytics tracks visits and unique users session based serverside without setting 3rd party cookies. It is also possible to track events and url paramaters. After 
installing and activation the plugin it's working without further configuration. If you want to set up custom tracking events or url parameters you can use the settings tab in 
the WordPress backend.

= Why use Simplest Analytics? =

It is working. No function overload. Track visits, unique users and custom events like clicks.

= What are the main features of the plugin? =

* Track page visits and unique users
* Track traffic on sites
* Track where your traffic comes from
* Track where your woocommerce sales come from
* Track custom events when a user clicks an element
* Track the use of url parameters like your-site.com/?campaign=whatever

== Custom function you can use to track further events ==

You can use this function to track further events in php hooks like form submissions, apply coupons, add to cart, etc:

$data = [];
$data['track_type'] = "event"; // event or pageview
$data['event_action'] = "yourevent"; // name of the event
simple_analytics_track_data($data); // function that saves the event

= How are the charts in the admin dashboard generated? =

The charts are based on google-charts.js which only load in the admin backend. The data is stored in the database on the same server where your WordPress installation 
is located.

== Installation ==

1. Upload the zip to the '/wp-content/plugins/' directory and unzip
1. Activate the plugin through the 'Plugins' menu in WordPress

OR go to 'Plugins' > 'Add new', and search for 'simplest analytics' to install through the WordPress dashboard.


== Frequently Asked Questions ==

= Does this plugin sets cookies? =
No, it's serverside tracking.

= Can I track unique visitors? =
Yes, vitits and unique visitors.

= Can I track clicks? =
Yes, very easy to set up.

= I need support! =
Please visit https://www.coden-lassen.de/referenzen/plugin-simplest-analytics

= Do you want to contrubute with code? =
Please send a pull request https://github.com/stefan-klaes/simplest-analytics-wordpress-plugin


== Screenshots ==

1. analytics dashboard
2. website referrer and events
3. woocommerce sales
4. woocommerce referrer and events
5. url parameter setting
6. event setting

== Changelog ==

= 1.1.2 - 2023-02-07 =
* updated readme
* contrubite via github

= 1.1.1 - 2023-01-23 =
* new logo, banner and screenshots
* better german translation

= 1.1.0 - 2023-01-23 =
* more woocommerce sale tracking features
* updated readme

= 1.0.1 - 2023-01-18 =
* updated style of the line charts
* moved php functions inside of a class

= 1.0.0 - 2023-01-10 =
* First Release

== Upgrade Notice ==

= 1.1.1 =
New woocommerce tracking features