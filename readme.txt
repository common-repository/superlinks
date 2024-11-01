=== SuperLinks v0.1 ===
Contributors: geldmacher
Tags: links, blogroll
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 0.1

SuperLinks is a sidebar widget that gives you greater control of displaying your links and blogroll.

== Description ==

SuperLinks replaces the standard WordPress 2.5 Links widget with a more customizable one. With
SuperLinks you can add as many Links widgets as you want, and set the display options for each 
widget. For instance, you can set which Links category is displayed on each block.

SuperLinks was inspired by the LinkBlock plugin, which provided a very similar service. However,
LinkBlock development stopped before WordPress 2.5, so SuperLinks was created to fill the void.

Development of SuperLinks was greatly aided by reading the source of some existing plugins. They
are:
* Lorna Timbah's "Top Commentators Widget" (http://webgrrrl.net/archives/my-top-commentators-widget-quick-dirty.htm)
* The original LinkBlock widget (LinkBlock: http://www.optera.net/projects/wordpress/linkblock/)
* The stock WordPress widgets found in widgets.php
Thanks to the developers of those plugins for the help their code provided!

== Installation ==

This section describes how to install the plugin and get it working.

Follow these simple steps:
1. Remove all existing Links sidebar widgets (see FAQ for why)
1. Upload `superlinks.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to your widgets administration and add and cofigure SuperLinks widgets

== Frequently Asked Questions ==

= What happened to the standard Links widget? =

When SuperLinks registers itself, it unregisters the Links widget that comes installed with
WordPress by default. Don't worry though -- anything you could have done with the standard
Links widget can be done with SuperLinks.
