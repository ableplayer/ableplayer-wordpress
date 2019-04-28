=== AblePlayer ===
Contributors: terrillthompson
Plugin URI: https://github.com/ableplayer-wordpress
Author URI: http://terrillthompson.com
Tags: html5,media,audio,video,accessibility
Requires at least: 2.6
Tested up to: 4.5
Stable tag: 0.1
License: MIT
License URI: https://github.com/ableplayer-wordpress/LICENSE


HTML5 accessible media player

== Description ==

This plug-in uses Able Player, an open-source fully-accessible cross-browser HTML5 media player, to embed audio or video within your WordPress page.

== Installation ==

1. Upload the unzipped folder `ableplayer` to the `/wp-content/plugins/` directory.
1. Activate the Able Player plugin through the 'Plugins' menu in WordPress
1. Follow the Instructions for Use

== Instructions for Use ==

There are currently two ways to add an Able Player instance to a WordPress site:

1. Enter or paste any valid HTML5 Able Player code into your web page. Full documentation, including a list and explanation of all supported HTML5 attributes, is available on the [Able Player](http://ableplayer.github.io/ableplayer) project page on GitHub.

2. Enter an [able-player] shortcode, in combination with one or more [able-source] shortcodes, plus one or more [able-track] shortcodes.

= The [able-player] shortcode =

The [able-player] shortcode is a container, so must be terminated by the [/able-player] shortcode. In between these opening and closing shortcodes, you must have at least one [able-source] shortcode.

The [able-player] shortcode supports the following attributes:

* type (required) - either "video" or "audio"
* id - a unique id for the player (if omitted, one will be assigned)
* autoplay - "true" or "false" (default is "false")
* loop' - "true" or "false" (default is "false")
* playsinline - "true" or "false" (default is "true")
* hidecontrols - "true" or "false" (default is "false")
* poster - the URL of a poster image, displayed before the user presses Play
* width - a value in pixels (by default, the player will be sized to fit its container)
* height - a value in pixels (by default, the height of the player will be in proportion to the width)
* heading - The HTML heading level (1-6) of the visually hidden "Media Player" heading that precedes the player (for the benefit of screen reader users). If omitted, a heading level will be intelligently assigned based on context.
* speed - "animals" or "arrows" (default is "animals")
* start - start time at which to start playing the media, in seconds
* volume - "0" to "10" (default is "7" to avoid overpowering screen reader audio)
* seekinterval - number of seconds to forward/rewind.
* nowplaying - "true" or "false" to include a "Selected Track" section within the media player (default is "false")

= The [able-source] shortcode =

The [able-source] shortcode is self-contained (i.e., it does not need a closing shortcode).

The [able-source] shortcode supports the following attributes:

* src - URL of the media file
* type - the mime type of the media file (e.g., "audio/mpeg", "video/mp4")
* desc-src - URL of the media file for the described version
* sign-src - URL of the media file for synchronized sign language
* youtube-id - 11-character YouTube ID
* youtube-desc-id - 11-character YouTube ID of the described version
* youtube-nocookie => "true" or "false" (use "true" to embed YouTube untracked, for added privacy)
* vimeo-id - Vimeo ID
* vimeo-desc-id - Vimeo ID for described version

= The [able-track] shortcode =

The [able-track] shortcode is self-contained (i.e., it does not need a closing shortcode).

The [able-track] shortcode supports the following attributes:

* kind - "captions", "subtitles", "chapters", "descriptions", or "metadata"
* src - URL to the track file (must be a valid WebVTT file)
* srclang - language code of the track language (e.g., "en" for English, "es" for Español)
* label - A label to display in the captions/subtitles menu (e.g., "English")


= Example 1 =

This example uses HTML to add a video player to the page, with one source (an MP4 file) and four tracks (for captions, descriptions, and chapters in English; and subtitles in Spanish).

<video id="able-player-1" data-able-player preload="auto" poster="path_to_image.jpg">
  <source type="video/mp4" src="path_to_video.mp4">
  <track kind="captions" src="path_to_captions.vtt" srclang="en" label="English">
  <track kind="subtitles" src="path_to_subtitles.vtt" srclang="es" label="Español">
  <track kind="descriptions" src="path_to_descriptions.vtt" srclang="en">
  <track kind="chapters" src="path_to_chapters.vtt" srclang="en">
</video>

= Example 2 =

This example is the same as Example 1, but uses shortcodes.

[able-player type="video" id="able-player-1" poster="path_to_image.jpg">
  [able-source type="video/mp4" src="path_to_video.mp4"]
  [able-track kind="captions" src="path_to_captions.vtt" srclang="en" label="English"]
  [able-track kind="subtitles" src="path_to_subtitles.vtt" srclang="es" label="Español"]
  [able-track kind="descriptions" src="path_to_descriptions.vtt" srclang="en"]
  [able-track kind="chapters" src="path_to_chapters.vtt" srclang="en"]
[/able-player]


== Next Steps ==
1. Reconsider shortcode implementation, as currently it offers no advantage whatsoever over HTML.
1. Provide a user interface by which authors can select and configure default options through WordPress.
1. Interface directly with existing WordPress media libraries so users can select their media files and other assets rather than typing in URLs.
1. Test whether and how to pass third party shortcodes to select HTML attributes rather than URLs, and add relevant documentation to this README.

== Changelog ==

= 0.1 =
* Initial version

= 0.1.1 =
* Add support for shortcodes



