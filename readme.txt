=== Blocksolid Gateway ===
Contributors: peripatus
Tags: intranet, gutenberg, block editor, blocks, gated, gated content, members area, gateway
Stable tag: 1.0.7
Requires at least: 5.5
Tested up to: 6.5
Requires PHP: 5.6
Donate link: https://www.peripatus.uk/payments/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gated content based upon a Members Only flag for pages, posts and categories with Gutenberg support.

== Description ==

Blockolid Gateway is a simple way to gate posts, pages, categories and their decendants with optional support for gutenberg and creation of a custom user role of "Member".

== Installation ==

* Install the plugin using the WordPress 'Add New plugin' functionality and activate.

* Once activated check the settings to setup.

* Categories, posts and classic pages gain a "Members Only" checkbox.  Gutenberg pages gain a "Gateway" settings panel containing a "Members Only" checkbox.

* Any Category set to "Members Only" will block access to all posts within it and within any sub-categories.

* Any page set to "Members Only" will also block access to any child pages that it might have.

* Use your own theme's styles to style the login box

== Screenshots ==

1. /assets/screenshots/screenshot-1.png

A gated page

== Changelog ==

= 1.0.7 =

*React code updated, bumped up to WordPress 6.5 - 18 March 2024*

= 1.0.6 =

*Stopped triggering on main events list page if event with newest published date was members only - 31 August 2023*

= 1.0.5 =

*Removed bug that stopped any content being displayed if "Show an impression of page under the login box" was not ticked irrespective of login status - 22 August 2023*

= 1.0.4 =

*Added a Members Only box to The Event Calendar event pages - 07 August 2023*

= 1.0.3 =

*Removed issues with custom post types - 06 January 2023*

= 1.0.2 =

*Added settings link to Plugins page - 13 December 2022*

= 1.0.1 =

*Second release - 07 December 2022*

= 1.0.0 =

*Initial release - 30 November 2022*

== Upgrade Notice ==

= 1.0.0 =

This is the first public release to be published