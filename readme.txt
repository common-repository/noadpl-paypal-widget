=== NoAd.pl Paypal Widget Plugin ===
Contributors: noadpl
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W7L4HY36DCBPG
Tags: paypal, integration, widget, balance, customizable, goal, balance
Requires at least: 4.4
Tested up to: 4.5.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a customizable widget on your site with informations from your PayPal.com. Make goals and progress bars to keep informing your visitors live.

== Description ==

This plugin is for making a widget with paypal balance (but it needs only a little edit to have all pp api options) 
you can use 4 tags for now [percent] [balance] [currency] [goal] i thint those no need comment :P
[percent] = (balance / goal) * 100% 
I leaved option to put all html in display field, you can put any tags here

I created this plugin under WordPress 4.5 but code is simple so it should work with older versions.

This plugin no need translations becouse you can use html tags free in "display" section use html5 tags or own css classes to display whatever you like


== Installation ==

Just instal on plugins page then go to widget section and search for "PayPal Balance Widget"

1. Upload the plugin files to the `/wp-content/plugins/noad-paypal-widget` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Personalize plugin and put your paypal API data ( https://www.paypal.com/cgi-bin/webscr?cmd=_profile-api-access ) in display put some text with tags [percent] [balance] [currency] [goal]

I personnaly use this code in display field:


PL example: Zebraliśmy już: [balance] [currency] z [goal] [currency] 

ENG example: We have gathered: [balance] [currency] of [goal] [currency] 

and you can use [percent] on some div to make a progress bar.


== Frequently Asked Questions ==

= A question that someone might have =

Q: What are available tags?

A: for now (version 1.0): [percent] [balance] [currency] [goal]

== Screenshots ==

1. Here how it look in widget admin.
2. This is how my personal display code look on site.


== Changelog ==

= 1.0 =
* First release version 1.0
* Added full customizable options to widget admin  section, to make this plugin usable by others.