=== Plugin Name ===
Contributors: mennolui, slimndap
Tags: digital, signage, narrowcasting, displays, screens, signs, onsite, foyer, lobby, kiosk, venue, theater, cinema
Requires at least: 4.1
Tested up to: 4.7
Stable tag: 1.1.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl.html
Donate link: https://www.paypal.me/mennoluitjes

A free Digital Signage plugin for WordPress. Create and show off slideshows on your networked displays.

== Description ==

Create slideshows and show them off on any networked display. Hardware not included :-)

= Features =
* Set up slides, channels (slideshows) and displays.
* Choose slide duration and transition effect.
* Set or change the channel on a display.
* Schedule a temporary channel on a display.

= Features for theaters, music venues, festivals =
Foyer comes with build in support for the [Theater for WordPress plugin](https://wordpress.org/plugins/theatre/). With Theater & Foyer you can easily publish your events on your website, and showcase them on your onsite displays.

= Slide formats =
* Image: Displays an image, covering the entire slide.
* PDF: Creates a slide for each page in an uploaded PDF, displaying that page contained within the slide.
* Production: Displays the image, title and event details of a selected production (requires Theater for WordPress).

More features and slide formats are coming soon.

= Feature suggestions wanted! =
Since this plugin is brand new, I'm curious..

* how do you plan to use it?
* in what environment (theater foyer, school canteen, hotel lobby, private office, shop window, ..)?
* what features are you looking for and are currently missing?

[Send me an email](mailto:menno@mennoluitjes.nl) to let me know!

== Installation ==

Install and activate the plugin via the WordPress Plugins admin screen.

1. Go to _Plugins_ → _Add new_.
1. Search for 'Foyer'.
1. Click _Install now_.
1. Don't forget to _Activate Plugin_.

There are currently no settings. Just go ahead and add slides, channels and displays.

== Frequently Asked Questions ==

= How do I set up my slideshow on my digital sign? =
1. In WordPress go to _Slides_ and add some.
1. Go to _Channels_, add one, and add some of you slides.
1. Go to _Displays_, add one, and subscribe it to your channel.
1. Preview the display, note the URL (something like http://your.site/foyer/display-number-one), and load this page in the web browser of your digital sign.

Your digital sign will now display the channel it is subscribed to. If you change the channel for this display in WordPress, the digital sign will change with it. You can even schedule channels on displays.

Set up a display for each digital sign for maximum remote flexibility.

= What hardware should I use for my digital sign? =
Generally speaking you need a computer with a web browser and internet connection, and a display linked to that computer. A Smart TV with built in web browser might work, but maybe not as reliable.

I recommend using a (mini-)computer with the Chrome browser in kiosk mode, and a Full HD (1920 x 1080) display.

When setting up multiple digital signs with their own content, each display needs its own (mini-)computer.

= Landscape or portrait? =
You choose! Install your digital sign the way you prefer. Foyer will follow. Slide templates are designed to work in both landscape and portrait mode. Only the background image will be cropped differently, of course.

= Can I change the looks of slides? =
Yes, this is possible if you know how to write CSS. Just include some CSS in the theme of your website that targets the slide HTML. If you don't have access to the theme you can use a custom CSS plugin to add some CSS.

= Can I change the template of a slide format? Can I add my own slide formats? =
Yes, this is possible if you know how to write WordPress templates. Documentation for developers is coming soon.

== Screenshots ==

Coming soon.

== Changelog ==

= 1.0.0 =
First public release!

= 1.0.1 =
Improved code security: Sanitized and validated all user input, and escaped and sanitized the output of the plugin.

= 1.1.0 =
Added a PDF slide format. Creates a slide for each page in an uploaded PDF.

== Upgrade Notice ==
