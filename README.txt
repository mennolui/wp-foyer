=== Foyer - Digital Signage for WordPress ===
Contributors: mennolui, slimndap
Tags: digital signage, signage, narrowcasting, slideshow, theater
Requires at least: 4.1
Tested up to: 5.7
Stable tag: trunk
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl.html
Donate link: https://www.paypal.me/mennoluitjes

A free Digital Signage plugin for WordPress. Create and show off slideshows on your networked displays.

== Description ==

Create slideshows and show them off on any networked display. Hardware not included :-)

**Check out the demo sign & website:**
[https://demo.foyer.tv](https://demo.foyer.tv)
[https://foyer.tv](https://foyer.tv)

= Features =
* **Set up slides, channels (slideshows) and displays**.
* Choose from various Slide formats and Slide backgrounds.
* Choose slide duration and transition effect.
* Change the channel on a display when needed, or..
* Schedule a temporary channel on a display.

= Slide formats =
* **Default**: Displays a background only.
* **Text**: Displays some text.
* **Post**: Displays title, date and content of a post, and optionally the featured image.
* **Recent posts**: Displays a slide for each recent post.
* **Event**: Displays title and details of an event, with its featured image as default background (requires Theater for WordPress).
* **Upcoming events**: Displays a slide for each upcoming event (requires Theater for WordPress).
* **External web page**: Displays a web page to your liking.
* **PDF**: Creates a slide for each page in an uploaded PDF, displaying that page contained within the slide.

= Slide backgrounds =
* **Image**: Displays an image, covering the entire slide background.
* **Video**: Displays an uploaded or external video, or a specified fragment, as slide background.
* **YouTube**: Displays a YouTube video, or a specified fragment, as slide background.

More features, slide formats and slide backgrounds are coming soon. Most notably advanced scheduling of channels and slides.

= Features for theaters, music venues, festivals =
Foyer comes with built-in support for [Theater for WordPress](https://wordpress.org/plugins/theatre/). With Theater & Foyer you can easily publish your events on your website *and* your onsite displays.

== Enjoying Foyer? ==

* Leave a review on [WordPress.org](https://wordpress.org/support/plugin/foyer/reviews/?filter=5/#new-post) :-)
* Leave a review on [Capterra](https://www.capterra.nl/reviews/173756/foyer---digital-signage-for-wordpress).
* Subscribe to the [Foyer Newsletter](https://eepurl.com/gkiymb).
* Visit the [Foyer website & blog](https://foyer.tv/).

= Donations =
Donations are very welcome and help me dedicate more time to developing this plugin.

* [Donate through PayPal](https://www.paypal.me/mennoluitjes).
* Bitcoin: 1LWZ4RRjpA34GqS5dVAw1fbrFweW97WZVG
* ETH (or tokens): 0xfd8ab9b18960ffc72ad2ef110c50afd2985cca7d

= Translate Foyer into your native language =
Swedish, Hindi, Italian, French, Indonesian, Arabic, ... Have you ever translated a WordPress plugin, or want to give it a try? [Find your language here](https://translate.wordpress.org/projects/wp-plugins/foyer) and translate some Foyer strings. Improvements of existing Foyer translations are welcome too.

= Missing feature? =
Let me know what features you are missing! Create a request in the [support forum](https://wordpress.org/support/plugin/foyer).

== Screenshots ==

1. Manage your Digital Signage from the WordPress admin.

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
1. Preview the display, note the URL (something like https://your.site/foyer/name-of-your-display), and load this URL in the web browser of your digital sign. Make sure this is a browser where you are *not* logged-in to WordPress.

Your digital sign will now display the channel it is subscribed to. If you change the channel for this display in WordPress, the digital sign will change with it. You can even schedule channels on displays.

Set up a display for each digital sign for maximum remote flexibility.

= What hardware should I use for my digital sign? =
Generally speaking you need a computer with a web browser and internet connection, and a display linked to that computer. A Smart TV with built in web browser might work, but maybe not as reliable.

I recommend using a (mini-)computer with the Chrome browser in kiosk mode, and a Full HD (1920 x 1080) display.

When setting up multiple digital signs with their own content, each display needs its own (mini-)computer.

= Can I use a Raspberry Pi mini-computer for my digital sign? =
Sure! Be aware that transitions and video playback on the Pi will be very choppy though, if they work at all. Use the 'No transition' setting for channels, don't add videos, and your Raspberry Pi digital sign will be fine.

I can recommend installing the paid version of [Raspberry Digital Signage](https://www.binaryemotions.com/digital-signage/raspberry-digital-signage/) as operating system on the SD card of a Raspberry Pi 3 or higher. Just power up your Pi, enter the URL of your Foyer display when asked, and you'll have an instant digital sign each time you power up.

= Can I use an Android mini-computer or tablet for my digital sign? =
Absolutely! Transitions and video playback should be smooth, of course depending on the hardware used.

I can recommend installing the free [Fully Kiosk Browser & App Lockdown](https://play.google.com/store/apps/details?id=de.ozerov.fully) app on your Android device. Use this app instead of the default Chrome browser. Just enter the URL of your Foyer display when asked, and you'll have an instant full screen digital sign each time you power up the device. This app also makes sure nobody can interact with the screen - handy for tablets - and avoids display sleep. You might want to use hardware that supports auto power-on after power outage.

The default Chrome browser will also work, but without the benefits mentioned above. Open your Foyer display URL in Chrome and tap 'Add to Home screen' from the Chrome menu. A shortcut will be added to your Home screen. When you launch this shortcut your display will be shown full screen.

= Landscape or portrait? =
You choose! Install your digital sign the way you prefer. Foyer will follow. Slide templates are designed to work in both landscape and portrait mode. Only the background image will be cropped differently, of course.

= Can I change the looks of slides? =
Yes, this is possible if you know how to write CSS. Just include some CSS in the theme of your website that targets the slide HTML. If you don't have access to the theme you can add some CSS using the WordPress Customizer.

= Can I change the template of a slide format? =
Yes, this is possible if you know how to write WordPress templates. Create a foyer/slides/ directory in your theme. Next locate the template of the slide format you want to change in the public/templates/slides/ directory in the Foyer plugin directory. Copy the template file to your foyer/slides/ directory, without changing the filename. You should find the template in your theme now overrules the template included with the plugin. Note that this plugin is still in its early stages of development. You might have to copy the latest version of the template file and reapply your changes when major changes to the plugin are released.

= Can I add my own slide formats? =
Yes, this is possible if you know how to write WordPress templates, and how to register PHP functions to WordPress plugin hooks. Have a look at how the plugin adds slide formats itself, in includes/class-foyer.php. More documentation for developers is coming soon.

= The top/bottom or left/right part of my image is missing, why? =
All images are displayed in a way that they fully cover the display. So if your display has a landscape orientation, and you upload an image with portrait orientation, the image will still cover the entire width of the display, which is great. But of course the top and bottom part of that image will not be visible. The greater the difference between the display orientation and the uploaded image orientation, the greater the top/bottom part or left/right part that will be invisible.

The plugin always displays the center-middle part of each image. So if the important part of your image is in the top part of the image, that might not be visible. You might have to crop that image before uploading and adding to a slide.

If you want to have full control you can always create and upload images that have the exact same orientation and proportions as your display. For most displays this is 1920x1080 pixels (landscape) or 1080x1920 (portrait). That way the images will be 100% visible.

= My changes are not directly visible on my displays, what's happening? =
Changes to displays, channels and slides are never instantly visible on your digital signs. Each digital sign tries to contact your website every 5 minutes to load any changes. If you changed the channel for a display, the new channel will be shown right after the slide that is currently being displayed. For any other changes, like adding slides or updated content, the new slides will be shown right after a full cycle of the slides that are currently being displayed.

= My favorite shortcode (slider, builder, script, embed, etc..) doesn't work on Foyer slides, can you help? =
Using shortcodes (etc..) is possible, but it is not supported and unfortunately I can't help you if they don't work.

Some shortcodes will work, some won't, and some work initially but will stop working after 5 minutes (when your display loads new slide content). This is because most shortcodes are simply coded to work on one static web page, not in the context of a Foyer slideshow.

That being said please go ahead and test your favorite shortcode on a Foyer slide. Preview your display and if the shortcode still works as expected after 10 minutes you will be fine. If not maybe a different shortcode will work. An alternative approach that does work with shortcodes is building a page that includes the shortcode, then display that web page within your Foyer slideshow using the External web page slide format.

= Does Foyer work together with caching plugins like W3 Total Cache or WP Super Cache? =
Yes, but your cache settings might need some tweaking. Your display loads new content every 5 minutes. With page cache or browser cache enabled your display will not show the changes you made within 5 minutes but instead this might take hours. Make sure all cache layers are disabled for Foyer displays.

Settings for W3 Total Cache: Add `/foyer/*` on a new line under Performance > Page Cache > Never cache the following pages.

== Changelog ==

= 1.7 =
Release Date: November 15, 2018

Introduces the Upcoming Events and Recent Posts slide formats. Displays a slide for each of your upcoming events / recent posts, limited to a certain category if you wish. Upcoming Events requires the Theater for WordPress plugin.

Bug fixes:

* Fixed an issue where video and YouTube backgrounds did not play on displays that did not yet include a video or YouTube background (1.7.2). Thanks [amosar](https://wordpress.org/support/users/amosar/) for troubleshooting!
* Fixed an issue where the end date was not set when selecting a start date for a temporary channel on the edit display screen, and a JavaScript error was thrown in the browser console (1.7.5).

Hey developers!:

* Added support for add-on plugins. You can now code Foyer add-on plugins, including template files that take precedence over Foyer template files (1.7.2).
* Fixed an issue where developers could not use HTML in Theater production titles on the Production and Upcoming productions slide formats (1.7.3).
* Added a 'foyer/public/enqueue_scripts/before' action hook that is triggered before the Foyer scripts are enqueued, so add-on plugins can bind events before Foyer does (1.7.4).
* Added an event 'slides:removing-old-slide-group' that is triggered just before a slide group is removed (1.7.4).
* Added the slide group class as parameter to the 'slides:removing-old-slide-group' and 'slides:loaded-new-slide-group' events, so these slide groups can be selectively targeted (1.7.4).
* Added a filter that allows displaying of slide previews on the channel admin screen to be disabled (1.7.4).

= 1.6 =
Release Date: October 3, 2018

Introduces the highly anticipated self-hosted Video slide background. Displays an uploaded or externally hosted video. Works best with MP4 files. The slide background displaying a YouTube video is now called... 'YouTube'.

Enhancements:

* Limited the WordPress media library to display only usable media files when adding media to a slide (1.6.0). PDF files only for the PDF slide format, video files only for the new Video slide background, image files only for the Image slide background.

= 1.5 =
Release Date: March 2, 2018

Introduces the Post slide format and the Manual text slide format. Displays title, date and content of a post, and optionally the featured image. The Manual text slide format displays your text: pre-title, title, subtitle, content. Also, slides that generate multiple slides (like the PDF slide format) are now called.. Magic slide stacks! The channel admin is improved by displaying title and properties of each slide.

Enhancements:

* Added a new option to enable sound for a video background (1.5.1).
* Video backgrounds no longer play when previewed while editing a Channel (1.5.1).
* Displayed the slide background, next to the slide format, on the slides admin page (1.5.1).
* Tweaked some translatable strings to make translation easier (1.5.1).
* Renamed the 'Manual text' slide format to 'Text', keeping it simple (1.5.1).
* Added a hint about minimal image sizes to the image slide background admin (1.5.2).
* Removed Dutch translation files (1.5.2). Translations are now fully handled by https://translate.wordpress.org/projects/wp-plugins/foyer. Translations welcome!
* Added a Web App manifest that enables displays to launch full screen on Android (1.5.4). Just tap 'Add to Home screen' from the Chrome menu and launch this shortcut.
* Encouraged iOS to play YouTube background videos (1.5.5). Works! However not when in "Low Power Mode", and not for videos with sound enabled.
* YouTube videos now cover the entire slide background (1.5.5). Hello vertical videos!
* Added support for passing on template args to slide backgrounds, so developers (and I) can code more complex slide stacks (1.5.7).

Bug fixes:

* Fixed an issue where YouTube videos stopped playing after 5 minutes when page caching was enabled (1.5.1). Thanks [Heinz](https://wordpress.org/support/users/wp_hela/) for troubleshooting!
* Fixed an issue where images on slides were over cropped, even when adding exact Full HD sizes (1.5.2).
* Fixed a 404 Not Found issue when accessing a display on its pretty permalink in fresh Foyer installs (1.5.3).
* Fixed an issue where slideshows would not continue to the next slide when the video of the current slide is not playing, eg. in case of prolonged network failure (1.5.5).
* Fixed an issue where background images were not covering the entire slide, in Edge (1.5.5).
* Fixed an issue where the rewrite rules are not flushed after plugin update, but a PHP Warning is thrown instead (1.5.6).

= 1.4 =
Release Date: February 14, 2018

Introduces a brand new way to build slides: choose a format, then a background. Now you can build event slides with video backgrounds. Or, coming up in a future release :-), WordPress Post slides on a background color.

= 1.3 =
Release Date: November 25, 2017

Introduces the External web page slide format. Displays a web page to your liking. This could be anything! A dashboard, a social media wall, a live feed, teletext!, .. anything that has its own URL.

Enhancements:

* Made the PDF slide format processing work for WordPress < 4.7 (1.3.1).
* Added notifications to the PDF slide format admin screen, displayed when PDF processing is not supported (no Imagick/Ghostscript installed), and when PDF file previews won’t work (WordPress < 4.7) (1.3.1).
* Removed the PDF slide format admin screen notifications added in 1.3.1, below the Upload PDF File button, as they proved to be unreliable. Instead added an admin notification, displayed only when PDF processing actually fails after saving a PDF slide (1.3.2).
* Displays now only use channels that are published, and channels now only use slides that are published (so no draft or private or trashed slides) (1.3.2).
* The Channel columns in the Display admin table now contain 'None' if no channel is set (1.3.2).
* Major internal changes that no one should notice: Refactored all non-object classes to use static methods, and switched from using a central Foyer_Loader class to registering actions and filters directly from Foyer, Foyer_Admin and Foyer_Public classes (1.3.2).
* Added a foyer-reset-display detection to JS, in anticipation of the 1.4.0 release that will need to be able to trigger it (1.3.3).

Bug fixes:

* Fixed an issue where the uploaded image on an event slide was never displayed (1.3.1).
* Fixed an issue introduced in 1.2.6 where the scheduled channel date time pickers no longer worked (1.3.1).
* Fixed an issue introduced in 1.2.6 where the media library lightbox texts were no longer set (1.3.1).
* Fixed an issue where the 'External web page' slide format displayed a border around the web page, depending on the theme and browser used (1.3.2).
* Fixed an issue where the Landscape / Portrait buttons were not styled correctly, depending on the theme used (1.3.2).
* Fixed a long unnoticed JS error that occurred while attempting loading new display data when no slide group was empty yet (1.3.3).

= 1.2 =
Release Date: April 12, 2017

Introduces the Video slide format. Displays a specified fragment of a YouTube video.

Enhancements:

* Added a ‘No transition’ option to channels, eg. for displaying on Raspberry Pi mini-computers (1.2.4).
* Added longer slide durations, up to 120 seconds (1.2.4).
* Added a foyer/public/enqueue_styles and a foyer/public/enqueue_scripts action, for theme developers (1.2.5).
* Made it possible to enqueue Foyer scripts outside of the Foyer plugin (1.2.6).

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
* Fixed an issue where some HTML code was visible on Production slides (1.2.6).
* Changed the name of the Production slide format to Event, same terminology as in Theater for WordPress (1.2.6).

= 1.1 =
Release Date: March 28, 2017

Added a PDF slide format. Creates a slide for each page in an uploaded PDF.

Bug fixes:

* When adding slides to a channel, the list of possible slides was limited to 5 items (1.1.0).
* Fatal error on install/upgrade on older PHP versions (< 5.5): Can't use function return value in write context (1.1.1).
* All slide of a channel were removed after re-ordering the slides (1.1.2).
* Javascript error occurred when a slide’s freshly selected image didn't have a generated preview image (eg. PDFs on hosting not capable of converting PDFs) (1.1.2).
* Adding an image to a slide was only possible when the image was already in the media library (1.1.3).

= 1.0 =
Release Date: March 20, 2017

First public release!

Bug fixes:

* Improved code security: Sanitized and validated all user input, and escaped and sanitized the output of the plugin (1.0.1).


== Upgrade Notice ==
= 1.7.5 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.7.4 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.7.3 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.7.2 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.7.1 =
Introduces the Recent Posts slide format in addition to the Upcoming Events slide format. Check the changelog for full details.

= 1.7.0 =
Introduces the Upcoming Events slide format. Check the changelog for full details.

= 1.6.0 =
Introduces the highly anticipated self-hosted Video slide background. Check the changelog for full details.

= 1.5.7 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.5.6 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.5.5 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.5.4 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.5.3 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.5.2 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.5.1 =
Bug fixes and minor enhancements. Check the changelog for full details.

= 1.5.0 =
Introduces the Post slide format, the Manual text slide format and Magic slide stacks. Check the changelog for all details.

= 1.4.0 =
Introduces a brand new way to build slides: choose a format, then a background. Check the changelog for all details.

= 1.3.3 =
Fixes some issues and brings some minor enhancements. Check the changelog for all details.

= 1.3.2 =
Fixes some issues and brings some minor enhancements. Check the changelog for all details.

= 1.3.1 =
Fixes an issue introduced in 1.2.6 where the scheduled channel date time pickers no longer worked. Fixes an issue introduced in 1.2.6 where the media library lightbox texts were no longer set. Fixes an issue where the uploaded image on an event slide was never displayed. Made the PDF slide format processing work for WordPress < 4.7. Added notifications to the PDF slide format admin screen, displayed when PDF processing is not supported (no Imagick/Ghostscript installed), and when PDF file previews won’t work (WordPress < 4.7).

= 1.3 =
Introduces the External web page slide format. Displays a web page to your liking. This could be anything! A dashboard, a social media wall, a live feed, teletext!, .. anything that has its own URL.

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
Fixes two bugs. Fixes an issue where all slides of a channel were removed after re-ordering the slides. Fixes a Javascript error that occurred when a slide’s freshly selected image didn't have a generated preview image (eg. PDFs on hosting not capable of converting PDFs).

= 1.1.1 =
Fixes a fatal error on install/upgrade on older PHP versions (< 5.5).

= 1.1 =
Added a PDF slide format. Creates a slide for each page in an uploaded PDF.

= 1.0.1 =
Improved code security: Sanitized and validated all user input, and escaped and sanitized the output of the plugin.

