=== Foyer - Digital Signage for WordPress ===
Contributors: mennolui, slimndap
Tags: digital, signage, narrowcasting, displays, screens, signs, onsite, foyer, lobby, kiosk, venue, theater, cinema
Requires at least: 4.1
Tested up to: 4.9
Stable tag: trunk
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl.html
Donate link: https://www.paypal.me/mennoluitjes

A free Digital Signage plugin for WordPress. Create and show off slideshows on your networked displays.

== Description ==

Create slideshows and show them off on any networked display. Hardware not included :-)

**Check out this demo sign:**
[http://demo.foyer.tv](http://demo.foyer.tv)

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
* Video: Displays a specified fragment of a YouTube video.
* Event: Displays the image, title and details of a selected event (requires Theater for WordPress).

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
1. Go to _Channels_, add one, and add some of your slides.
1. Go to _Displays_, add one, and subscribe it to your channel.
1. Preview the display, note the URL (something like http://your.site/foyer/name-of-your-display), and load this page in the web browser of your digital sign.

Your digital sign will now display the channel it is subscribed to. If you change the channel for this display in WordPress, the digital sign will change with it. You can even schedule channels on displays.

Set up a display for each digital sign for maximum remote flexibility.

= What hardware should I use for my digital sign? =
Generally speaking you need a computer with a web browser and internet connection, and a display linked to that computer. A Smart TV with built in web browser might work, but maybe not as reliable.

I recommend using a (mini-)computer with the Chrome browser in kiosk mode, and a Full HD (1920 x 1080) display.

When setting up multiple digital signs with their own content, each display needs its own (mini-)computer.

= Can I use a Raspberry Pi mini-computer for my digital sign? =
Absolutely! Be aware that transitions and video playback on the Pi will be very choppy though, if they work at all. Use the 'No transition' setting for channels, don't add videos, and your Raspberry Pi digital sign will be fine.

I can recommend installing the paid version of [Raspberry Digital Signage](http://www.binaryemotions.com/digital-signage/raspberry-digital-signage/) as operating system on the SD card of a Raspberry Pi 3 Model B. Just power up your Pi, enter the URL of your Foyer display when asked, and you'll have an instant digital sign each time you power up.

= Landscape or portrait? =
You choose! Install your digital sign the way you prefer. Foyer will follow. Slide templates are designed to work in both landscape and portrait mode. Only the background image will be cropped differently, of course.

= Can I change the looks of slides? =
Yes, this is possible if you know how to write CSS. Just include some CSS in the theme of your website that targets the slide HTML. If you don't have access to the theme you can use a custom CSS plugin to add some CSS.

= Can I change the template of a slide format? Can I add my own slide formats? =
Yes, this is possible if you know how to write WordPress templates. Documentation for developers is coming soon.

= My changes are not directly visible on my displays, what's happening? =
Changes to displays, channels and slides are never instantly visible on your digital signs. Each digital sign tries to contact your website every 5 minutes to see if you made any changes. If so and you changed the channel for a display, the new channel will be shown right after the slide that is currently being displayed. For any other changes, like adding slides, the new slides will be shown right after a full cycle of the slides that are currently being displayed.


== Screenshots ==

Coming soon.

== Changelog ==

= 1.2 =
Release Date: April 12, 2017

Introduces the Video slide format. Displays a specified fragment of a YouTube video.

Enhancements:

* Added a ‘No transition’ option to channels, eg. for displaying on Raspberry Pi mini-computers (1.2.4).
* Added longer slide durations, up to 120 seconds (1.2.4).

Bug fixes:

* The video start time was off during the very first loop through video slides (1.2.1).
* Fresh channel content was loaded every 30 seconds when viewing a display, changed this to every 5 minutes as intended (1.2.1).
* Removed all JS console logging that was used during development (1.2.1).
* The ‘Not a valid YouTube video URL’ notification was visible when starting a new video slide (1.2.1).
* The video preview in the admin would not work when editing an existing video slide (1.2.2).
* Improved handling of changed start and end fields in the video slide admin when no valid video URL is entered (1.2.2).
* Improved the video preview in the video slide admin by pausing the preview when the video URL field is changed and not valid (1.2.2).
* Some WordPress JavaScript admin functionality was prevented from working correctly, eg. the Media modal / image selector lightbox (1.2.3).
* The list of available channels was limited to only 5 when editing a display (1.2.3).
* PHP logged an Undefined index PHP Notice (1.2.3).
* The first slide of a channel could not be removed (1.2.4).
* Added a foyer/public/enqueue_styles and a foyer/public/enqueue_scripts action, for theme developers (1.2.5).
* Made it possible to enqueue Foyer scripts outside of the Foyer plugin (1.2.6).
* Fixed an issue where some HTML code was visible on Production slides (1.2.6).
* Changed the name of the Production slide format to Event, same terminology as in Theater for WordPress (1.2.6).

= 1.1 =
Release Date: March 28, 2017

Added a PDF slide format. Creates a slide for each page in an uploaded PDF.

Bug fixes:

* When adding slides to a channel, the list of possible slides was limited to 5 items (1.1.0).
* Fatal error on install/upgrade on older PHP versions (< 5.5): Can't use function return value in write context (1.1.1).
* All slide of a channel were removed after re-ordering the slides (1.1.2).
* Javascript error occured when a slide’s freshly selected image didn't have a generated preview image (eg. PDFs on hosting not capable of converting PDFs) (1.1.2).
* Adding an image to a slide was only possible when the image was already in the media library (1.1.3).

= 1.0 =
Release Date: March 20, 2017

First public release!

Bug fixes:

* Improved code security: Sanitized and validated all user input, and escaped and sanitized the output of the plugin (1.0.1).


== Upgrade Notice ==

= 1.2.6 =
Fixes an issue where some HTML code was visible on Production slides. Changed the name of the Production slide format to Event, same terminology as in Theater for WordPress. Made it possible to enqueue Foyer scripts outside of the Foyer plugin.

= 1.2.5 =
Added a foyer/public/enqueue_styles and a foyer/public/enqueue_scripts action, for theme developers.

= 1.2.4 =
Fixes an issue where the first slide of a channel could not be removed. Added a ‘No transition’ option to channels. Added longer slide durations, up to 120 seconds.

= 1.2.3 =
Fixes an issue that prevented some WordPress JavaScript admin functionality from working correctly, eg. the Media modal / image selector lightbox. Fixes an issue where the list of available channels was limited to only 5 when editing a display. Fixes an Undefined index PHP Notice.

= 1.2.2 =
Fixes an issue with the video slide admin screen and introduces some minor improvements of the video slide admin screen.

= 1.2.1 =
Fixes some issues with the video slide and its admin screen.

= 1.2 =
Introduces the Video slide format. Displays a specified fragment of a YouTube video.

= 1.1.3 =
Fixes an issue where adding an image to a slide was only possible when the image was already in the media library.

= 1.1.2 =
Fixes two bugs. Fixes an issue where all slides of a channel were removed after re-ordering the slides. Fixes a Javascript error that occured when a slide’s freshly selected image didn't have a generated preview image (eg. PDFs on hosting not capable of converting PDFs).

= 1.1.1 =
Fixes a fatal error on install/upgrade on older PHP versions (< 5.5).

= 1.1 =
Added a PDF slide format. Creates a slide for each page in an uploaded PDF.

= 1.0.1 =
Improved code security: Sanitized and validated all user input, and escaped and sanitized the output of the plugin.

