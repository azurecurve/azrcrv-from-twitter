=== From Twitter ===

Description:	Automate the retrieval of tweets from Twitter and create posts on your ClassicPress site.
Version:		1.1.0
Tags:			tweets,twitter,automatic 
Author:			azurecurve
Author URI:		https://development.azurecurve.co.uk/
Plugin URI:		https://development.azurecurve.co.uk/classicpress-plugins/to-twitter/
Download link:	https://github.com/azurecurve/azrcrv-from-twitter/releases/download/v1.1.0/azrcrv-from-twitter.zip
Donate link:	https://development.azurecurve.co.uk/support-development/
Requires PHP:	5.6
Requires:		1.0.0
Tested:			4.9.99
Text Domain:	from-twitter
Domain Path:	/languages
License: 		GPLv2 or later
License URI: 	http://www.gnu.org/licenses/gpl-2.0.html

Automate the retrieval of tweets from Twitter and create posts on your ClassicPress site.

== Description ==

# Description

Automate the retrieval of tweets from Twitter and create posts on your ClassicPress site

From Twitter includes the following functionality;
 * Search Twitter and create tweets as posts or as a Tweet custom post type.
 * Specify the title and content in posts for retrieved tweets.
 * Choose whether to save tweet data.
 * Choose cron frequency (hourly, twice daily or daily).
 * Choose how many tweets to return each time the cron runs (max 100 as per Twitter api).
 * Choose whether Tweet images should be downloaded.

Make sure that once all the settings have been configured, you enable the cron job to run on the **Cron Settings** tab.

This plugin is multisite compatible with each site having its own settings.

== Installation ==

# Installation Instructions

* Download the plugin from [GitHub](https://github.com/azurecurve/azrcrv-from-twitter/releases/latest/).
* Upload the entire zip file using the Plugins upload function in your ClassicPress admin panel.
* Activate the plugin.
* Apply for a [Twitter Developer account](https://developer.twitter.com/en/apply-for-access).
* Create your Twitter application [here](https://developer.twitter.com/en/apps).
* Configure settings (including your Consumer API Keys and Access Token and Secret) via the configuration page in the admin control panel.

== Frequently Asked Questions ==

# Frequently Asked Questions

### Can I translate this plugin?
Yes, the .pot fie is in the plugins languages folder and can also be downloaded from the plugin page on https://development.azurecurve.co.uk; if you do translate this plugin, please sent the .po and .mo files to translations@azurecurve.co.uk for inclusion in the next version (full credit will be given).

### Is this plugin compatible with both WordPress and ClassicPress?
This plugin is developed for ClassicPress, but will likely work on WordPress.

== Changelog ==

# Changelog

### [Version 1.1.0](https://github.com/azurecurve/azrcrv-from-twitter/releases/tag/v1.1.0)
 * Add uninstall.
 * Update azurecurve menu and logo.
 * Update translations to escape strings.
 
### [Version 1.0.0](https://github.com/azurecurve/azrcrv-from-twitter/releases/tag/v1.0.0)
 * Initial release.

== Other Notes ==

# About azurecurve

**azurecurve** was one of the first plugin developers to start developing for Classicpress; all plugins are available from [azurecurve Development](https://development.azurecurve.co.uk/) and are integrated with the [Update Manager plugin](https://codepotent.com/classicpress/plugins/update-manager/) by [CodePotent](https://codepotent.com/) for fully integrated, no hassle, updates.

Some of the top plugins available from **azurecurve** are:
* [Add Twitter Cards](https://development.azurecurve.co.uk/classicpress-plugins/add-twitter-cards/)
* [Breadcrumbs](https://development.azurecurve.co.uk/classicpress-plugins/breadcrumbs/)
* [Series Index](https://development.azurecurve.co.uk/classicpress-plugins/series-index/)
* [To Twitter](https://development.azurecurve.co.uk/classicpress-plugins/to-twitter/)
* [Theme Switcher](https://development.azurecurve.co.uk/classicpress-plugins/theme-switcher/)
* [Toggle Show/Hide](https://development.azurecurve.co.uk/classicpress-plugins/toggle-showhide/)