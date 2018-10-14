=== Plugin Name ===

Contributors: modernwebservices
Donate link: https://modernwebservices.com.au/donate
Tags: spambot recaptcha
Requires at least: 4.0
Tested up to: 4.9
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Prevent spam-bots from harvesting your email address or other sensitive information form your website by requiring users to click to reveal the information.

== Description ==

The plugin leverages Google's invisible reCaptcha technology. These keys must be input via the plugin's admin integrations page.

The main admin page enables admins to manage a list of key/value pairs, where the key is the name of the pair, and the value is the protected value that should only be revealed once a user has proved they are human.

When editing post/page content, a TinyMCE integration enables the post author to click a button to generate a shortcode.
The author will be prompted to select the pair they wish to render, along with a initial value to display.

When the page is rendered, the initial value is rendered into the page. Users can click the initial value to reveal the protected value.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `mws-click-to-reveal.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the plugin's integration page and enter your Google reCaptcha keys.

== Screenshots ==

1. Key/Protected-Value management page
2. TinyMCE editor shortcode integration

== Changelog ==

= 1.0.3
* Update tags, WP tested version

= 1.0.2
* Add support for shortcodes in sidebars

= 1.0.1 =
* Add support for telephone numbers

= 1.0.0 =
* Initial release.
