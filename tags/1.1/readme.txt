=== Flush OPcache ===
Contributors: mnttech
Tags: opcache, cache, flush, php
Requires at least: 4.3
Tested up to: 4.8
Stable tag: 1.1
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin only does one thing : Add a simple button in admin dashboard to flush PHP OPcache.

== Description ==

If you need to add a simple button in your admin area to flush PHP OPcache, this is the plugin you need. For everything else, you're in the wrong place :)

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

== Changelog ==

= 1.1 =
* Add French translation

= 1.0 =
* First version

== Upgrade Notice ==

== Arbitrary section ==
