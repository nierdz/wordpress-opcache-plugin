=== Flush OPcache ===
Contributors: mnttech
Tags: opcache, cache, flush, php
Requires at least: 4.3
Tested up to: 4.8
Stable tag: 2.0.1
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin allows to manage Zend OPcache inside your WordPress admin dashboard.

== Description ==

This extension adds a button in your admin area to flush OPcache easily. You also have a submenu page with all statistics about OPcache. There is an option to automatically flush OPcache when a WordPress or plugin update occurred, you can enable or disable it. 
The statistics tabs relies on https://github.com/rlerdorf/opcache-status
If you want to add a translation in your own language, you can use flush-opcache.pot located in lang folder and send me by email.

== Installation ==

As usual...

== Frequently Asked Questions ==

= Does I need to have OPcache activated on my server to use this plugin? =

I think the answer is in the plugin's name ;)

= Do I need to know something before install this plugin? =

Yes, If you're working in a shared server, OPcache is shared across all PHP users so when you flush OPcache, you do it on every websites this server hosts.
Nothing related to this plugin, it's just the way Zend OPcache works... This plugin only triggers `opcache_reset()` function.

== Screenshots ==

1. Here is the button you'll get using Flush OPcache
2. When you just flushed OPcache, here is a notice
3. General settings tabs
4. Statistics tabs

== Changelog ==

= 2.0.1 =
* Minor fix to make translation works on wordpress.org

= 2.0 =
* Add options page
* Add statistics tabs in options pages
* Add option to flush OPcache automatically after an upgrade

= 1.2 =
* Add German translation, courtesy of Kolja Spyra

= 1.1 =
* Add French translation

= 1.0 =
* First version

== Upgrade Notice ==

== Arbitrary section ==
