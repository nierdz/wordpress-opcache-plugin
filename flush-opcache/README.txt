=== WP OPcache ===
Contributors: mnttech
Tags: opcache, cache, flush, php, multisite
Requires at least: 4.3
Tested up to: 5.2.4
Stable tag: 3.0.2
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin allows to manage Zend OPcache inside your WordPress admin dashboard.

== Description ==

This extension adds a button in your admin area to flush OPcache easily. It supports both memory and file caching transparently.
If this is a multisite installation, it only shows up in network admin dashboard.
There is a submenu page with all statistics about OPcache. There is an option to automatically flush OPcache when a WordPress or plugin update occurred.
The statistics tabs relies on https://github.com/wp-cloud/opcache-status
If you want to add a translation in your own language, you can use flush-opcache.pot located in languages folder and send me by email.

== Installation ==

As usual...

== Frequently Asked Questions ==

= Does I need to have OPcache activated on my server to use this plugin? =

I think the answer is in the plugin's name ;)

= Do I need to know something before install this plugin? =

Yes, If you're working on a shared server, OPcache is shared across all PHP users so when you flush OPcache, you do it on every websites this server hosts.
Nothing related to this plugin, it's just the way Zend OPcache works... This plugin only triggers `opcache_reset()` function.

== Screenshots ==

1. Here is the button you'll get using Flush OPcache
2. When you just flushed OPcache, here is a notice
3. General settings tabs
4. Statistics tabs

== Changelog ==

= 3.0.2 =
* Fix a bug where flush OPcache button is stuck in admin bar

= 3.0.1 =
* Fully compliant with WordPress coding standards

= 3.0.0 =
* Complete rewrite in OOP for better maintainability
* Fix notice bug https://wordpress.org/support/topic/dismissing-the-opcache-was-successfully-flushed-message/
* Fix bug on prewarm cache https://wordpress.org/support/topic/turning-on-prepcompile-php-option-causes-error/
* Add continuous deployment with travis

= 2.4.3 =
* Fix bug on prewarm cache https://wordpress.org/support/topic/recursivedirectoryiterator-does-not-skip-dotfiles/

= 2.4.2 =
* Add dismiss button to notice when opcache is closed

= 2.4.1 =
* Bugfix release

= 2.4 =
* Add support for opcache.file_cache purging
* Update German translation, courtesy of Kolja Spyra

= 2.3 =
* Update statistics tab with this opcache-status project: https://github.com/wp-cloud/opcache-status
* Add option to hide button in admin bar
* Minor bugfixes
* Update French translation

= 2.2 =
* Bugfix in options
* Compatible with multisite
* Remove powered by option

= 2.1 =
* Change plugin name
* Warning when opcache is loaded but no active
* Add an option to prewarm OPcache

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
