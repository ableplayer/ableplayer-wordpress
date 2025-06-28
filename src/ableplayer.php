<?php
/**
 * Able Player, accessible HTML5 media player
 *
 * @package     Able Player
 * @author      Joe Dolson
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name:  Able Player, accessible HTML5 media player
 * Plugin URI:  https://github.com/ableplayer/ableplayer-wordpress
 * Description: This plug-in uses Able Player, an open-source fully-accessible cross-browser HTML5 media player, to embed audio or video within your WordPress page.
 * Author:      Joe Dolson
 * Author URI:  https://www.joedolson.com
 * Text Domain: ableplayer
 * License:     MIT
 * License URI: https://github.com/ableplayer/ableplayer-wordpress/blob/master/LICENSE
 * Version:     2.0.1
 */

// Configure debugging mode.
define( 'ABLEPLAYER_DEBUG', false );

// Get current version number.
define( 'ABLEPLAYER_VERSION', '2.0.1' );

require_once plugin_dir_path( __FILE__ ) . 'inc/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/generator.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/kses.php';

register_activation_hook( __FILE__, 'ableplayer_activation' );
/**
 * Configure plugin on activation.
 */
function ableplayer_activation() {
	// Handle plugin activation.
}

register_deactivation_hook( __FILE__, 'ableplayer_plugin_deactivated' );
/**
 * On plugin deactivation.
 */
function ableplayer_plugin_deactivated() {
	// Handle plugin deactivation.
}

/**
 * Load styles and scripts to head.
 */
function ableplayer_enqueue_scripts() {
	$debug   = ( SCRIPT_DEBUG || ABLEPLAYER_DEBUG ) ? true : false;
	$version = ABLEPLAYER_VERSION;
	$version = ( $debug ) ? $version . '-' . wp_rand( 1000, 9999 ) : $version;
	// Register/enqueue other dependencies.
	$cookie_js = ( $debug ) ? '/js.cookie.js' : '/js.cookie.min.js';
	wp_enqueue_script( 'js-cookie', plugins_url( 'thirdparty', __FILE__ ) . $cookie_js, array(), $version, true );
	wp_enqueue_style( 'ableplayer-video', plugins_url( 'assets', __FILE__ ) . '/css/media.css', array(), $version );
	$media_js = ( $debug ) ? 'media.js' : 'media.min.js';
	wp_register_script( 'ableplayer-video', plugins_url( 'assets', __FILE__ ) . '/js/' . $media_js, array(), $version, true );
	wp_localize_script(
		'ableplayer-video',
		'ableplayer',
		array(
			'settings' => ableplayer_get_settings(),
		)
	);

	$js_file  = ( $debug ) ? 'ableplayer.js' : 'ableplayer.min.js';
	$css_file = ( $debug ) ? 'styles/ableplayer.css' : 'build/ableplayer.min.css';
	/**
	 * Filter the Able Player JS URL.
	 *
	 * @hook able_player_js
	 *
	 * @param {string} $url URL to Able Player root directory.
	 * @param {bool}   $debug True if environment is in debugging.
	 *
	 * @return {string}
	 */
	$js_dir = apply_filters( 'able_player_js', plugins_url( 'build', __FILE__ ) . '/' . $js_file, $debug );
	/**
	 * Filter the Able Player CSS URL.
	 *
	 * @hook able_player_css
	 *
	 * @param {string} $url URL to Able Player root directory.
	 * @param {bool}   $debug True if environment is debugging.
	 *
	 * @return {string}
	 */
	$css_dir = apply_filters( 'able_player_css', plugins_url( '', __FILE__ ) . '/' . $css_file, $debug );

	$dependencies = array( 'js-cookie', 'jquery', 'ableplayer-video' );
	/**
	 * Filter the Able Player script dependencies.
	 *
	 * @hook ableplayer_dependencies
	 *
	 * @param {array} $dependencies Array of scripts required by the main Able Player script.
	 * @param {bool}  $debug True if environment is in debugging mode.
	 *
	 * @return {array}
	 */
	$dependencies = apply_filters( 'ableplayer_dependencies', $dependencies, $debug );
	wp_enqueue_script( 'ableplayer', $js_dir, $dependencies, $version, true );
	wp_enqueue_style( 'ableplayer', $css_dir, array(), $version );
}
add_action( 'wp_enqueue_scripts', 'ableplayer_enqueue_scripts' );

/**
 * Enqueue admin JS and CSS.
 */
function ableplayer_admin_scripts() {
	$version  = ABLEPLAYER_VERSION;
	$version  = ( SCRIPT_DEBUG ) ? $version . '-' . wp_rand( 1000, 9999 ) : $version;
	$admin_js = ( SCRIPT_DEBUG ) ? 'admin.js' : 'admin.min.js';
	wp_enqueue_script( 'ableplayer-js', plugins_url( '/assets/js/' . $admin_js, __FILE__ ), array( 'jquery', 'wp-a11y', 'clipboard' ), $version, true );
	wp_localize_script(
		'ableplayer-js',
		'ableplayer',
		array(
			'posterTitle'      => 'Select Poster Image',
			'sourceTitle'      => 'Select Video',
			'captionsTitle'    => 'Select Captions',
			'subtitlesTitle'   => 'Select Subtitles',
			'descriptionTitle' => 'Select Description',
			'chaptersTitle'    => 'Select Chapters',
			'buttonName'       => 'Choose Media',
			'thumbHeight'      => '100',
			'removed'          => __( 'Selected media removed', 'ableplayer' ),
			'homeUrl'          => home_url(),
		)
	);
	if ( function_exists( 'wp_enqueue_media' ) && ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
	wp_enqueue_style( 'ableplayer', plugins_url( '/assets/css/admin.css', __FILE__ ), array(), $version );
}
add_action( 'admin_enqueue_scripts', 'ableplayer_admin_scripts' );

/**
 * Self-documenting array of Able Player attributes.
 *
 * @return array
 */
function able_player_parameters() {
	/**
	 * 'default': Default value in shortcode.
	 * 'description': Explanation of field and value expected.
	 * 'parameter': Parameter to use in output if not `data-{key}`.
	 * 'options': Available options if fixed choices available. (If only one, checkbox input; multiple, select).
	 */
	$params = array(
		'id'                  => array(
			'default'     => ableplayer_get_unique_id(),
			'description' => __( 'Automatically generated unique ID for an Able Player shortcode.', 'ableplayer' ),
		),
		'allow-fullscreen'    => array(
			'default'     => '',
			'description' => __( 'If set to “false” the player will not include a fullscreen button', 'ableplayer' ),
			'options'     => array( 'false' ),
		),
		'autoplay'            => array(
			'default'     => 'false',
			'description' => __( 'Play media automatically when page loads. For accessibility reasons, this is not recommended unless user is sure to expect media to automatically start.', 'ableplayer' ),
			'parameter'   => 'autoplay',
			'options'     => array( 'true' ),
		),
		'captions-position'   => array(
			'default'     => 'below',
			'description' => __( 'Specify default position of captions relative to the video. Users can override this.', 'ableplayer' ),
			'options'     => array( 'below', 'overlay' ),
		),
		'heading'             => array(
			'default'     => '',
			'description' => __( 'Able Player injects an off-screen HTML heading “Media Player” at the top of the player so screen reader users can easily find the player. It automatically assigns a heading level that is one level deeper than the closest parent heading. This attribute can be used to manually set the heading level.', 'ableplayer' ),
			'options'     => array(
				'h1'   => '1',
				'h2'   => '2',
				'h3'   => '3',
				'h4'   => '4',
				'h5'   => '5',
				'h6'   => '6',
				'Omit' => '0',
			),
		),
		'height'              => array(
			'default'     => '',
			'description' => __( 'Height of the video in pixels. Defaults to height of container.', 'ableplayer' ),
			'parameter'   => 'height',
		),
		'hidecontrols'        => array(
			'default'     => '',
			'description' => __( 'Hide controls during playback. Controls are visibly hidden but still accessible to assistive technologies. Controls reappear if user presses any key or moves the mouse over the video player region.', 'ableplayer' ),
			'parameter'   => 'data-hide-controls',
			'options'     => array( 'true' ),
		),
		'lang'                => array(
			'default'     => '',
			'description' => __( 'Specify language of the player using 2-character language code. Default "en" for English.', 'ableplayer' ),
		),
		'loop'                => array(
			'default'     => '',
			'description' => __( 'Loops and plays the media file repeatedly.', 'ableplayer' ),
			'parameter'   => 'loop',
			'options'     => array( 'true' ),
		),
		'nowplaying'          => array(
			'default'     => '',
			'description' => __( 'Include “Selected track” section within player; only applies when a playlist is present.', 'ableplayer' ),
			'parameter'   => 'data-show-now-playing',
			'options'     => array( 'true' ),
		),
		'playsinline'         => array(
			'default'     => 'true',
			'description' => __( 'Instructs supporting browsers to play the video “inline” within the web page. This is especially applicable on iPhones, which by default load the video in their own built-in video player.', 'ableplayer' ),
			'parameter'   => 'playsinline',
			'options'     => array( 'false' ),
		),
		'poster'              => array(
			'default'     => '',
			'description' => __( 'Path to an image file. Will be displayed in the player until the video is played.', 'ableplayer' ),
			'parameter'   => 'poster',
		),
		'preload'             => array(
			'default'     => 'auto',
			'description' => __( 'Tells the browser how much media to download when the page loads.', 'ableplayer' ),
			'parameter'   => 'preload',
			'options'     => array( 'auto', 'metadata' ),
		),
		'seekinterval'        => array(
			'default'     => '',
			'description' => __( 'Interval (in seconds) of forward and rewind buttons. By default, seek interval is intelligently calculated based on duration of the media.', 'ableplayer' ),
			'parameter'   => 'data-seek-interval',
		),
		'sign-src'            => array(
			'default'     => '',
			'description' => __( 'A path pointing to a synchronized sign language version of the video.', 'ableplayer' ),
		),
		'skin'                => array(
			'default'     => '2020',
			'description' => __( 'The default skin has two rows of controls, with the seekbar positioned in available space within the top row. The “2020” skin, introduced in version 4.2, has all buttons in one row beneath a full-width seekbar.', 'ableplayer' ),
			'options'     => array( '2020', 'legacy' ),
		),
		'speed'               => array(
			'default'     => 'animals',
			'description' => __( 'The default setting uses a turtle icon for slower and a rabbit icon for faster. Setting this to “arrows” uses arrows pointing up for faster and down for slower.', 'ableplayer' ),
			'parameter'   => 'data-speed-icons',
			'options'     => array( 'animals', 'arrows' ),
		),
		'steno-mode'          => array(
			'default'     => '',
			'description' => __( 'Allow keyboard shortcuts for controlling the player remotely within textarea form fields.', 'ableplayer' ),
			'options'     => array( 'true' ),
		),
		'start'               => array(
			'default'     => '',
			'description' => __( 'Time at which you want the media to start playing.', 'ableplayer' ),
			'parameter'   => 'data-start-time',
		),
		'transcript-div'      => array(
			'default'     => '',
			'description' => __( 'ID of a custom div in which to display an interactive transcript.', 'ableplayer' ),
		),
		'transcript-src'      => array(
			'default'     => '',
			'description' => __( 'ID of an external div that contains a pre-existing manually coded transcript. Able Player will parse this transcript and interact with it during playback', 'ableplayer' ),
		),
		'include-transcript'  => array(
			'default'     => '',
			'description' => __( 'Set to “false” to exclude transcript button from controller.', 'ableplayer' ),
			'options'     => array( 'true' ),
		),
		'transcript-title'    => array(
			'default'     => '',
			'description' => __( 'Override default transcript title (default is “Transcript”, or “Lyrics” if the data-lyrics-mode attribute is present)', 'ableplayer' ),
		),
		'lyrics-mode'         => array(
			'default'     => '',
			'description' => __( 'Forces a line break between and within captions in the transcript.', 'ableplayer' ),
			'options'     => array( 'true' ),
		),
		'chapters-div'        => array(
			'default'     => '',
			'description' => __( 'ID of an external div in which to display a list of chapters. The list of chapters is generated automatically if a chapters track is available in a WebVTT file. If this attribute is not provided and chapter are available, chapters will be displayed in a popup menu triggered by the Chapters button.', 'ableplayer' ),
		),
		'use-chapters-button' => array(
			'default'     => '',
			'description' => __( 'Set to “false” to exclude chapters button from controller.', 'ableplayer' ),
			'options'     => array( 'false' ),
		),
		'chapters-title'      => array(
			'default'     => '',
			'description' => __( 'Override default chapters title (default is “Chapters”)', 'ableplayer' ),
		),
		'chapters-default'    => array(
			'default'     => '',
			'description' => __( 'ID of default chapter (must correspond with the text or value immediately above the timestamp in your chapter’s WebVTT file). If this attribute is present, the media will be advanced to this start time.', 'ableplayer' ),
		),
		'seekbar-scope'       => array(
			'default'     => '',
			'description' => __( 'Default is “video” (seekbar represents full duration of video); if set to “chapter” seekbar represents the duration of the current chapter only', 'ableplayer' ),
			'options'     => array( 'chapter' ),
		),
		'search'              => array(
			'default'     => '',
			'description' => __( 'Search terms to search for within the caption tracks, separated by a space.', 'ableplayer' ),
		),
		'search-lang'         => array(
			'default'     => '',
			'description' => __( '2-character language code of caption or subtitle track to search.', 'ableplayer' ),
		),
		'search-div'          => array(
			'default'     => '',
			'description' => __( 'ID of external container in which to display search results', 'ableplayer' ),
		),
		'vimeo-id'            => array(
			'default'     => '',
			'description' => __( 'ID or URL of a video on Vimeo.', 'ableplayer' ),
		),
		'vimeo-desc-id'       => array(
			'default'     => '',
			'description' => __( 'ID or URL of an alternate described version of a video on Vimeo', 'ableplayer' ),
		),
		'width'               => array(
			'default'     => '',
			'description' => __( 'Width of the video in pixels.', 'ableplayer' ),
			'param'       => 'width',
		),
		'volume'              => array(
			'default'     => '7',
			'description' => __( 'Set the default volume from 0 to 10; default is 7 to avoid overpowering screen reader audio)', 'ableplayer' ),
			'options'     => array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ),
		),
		'youtube-id'          => array(
			'default'     => '',
			'description' => __( 'YouTube ID or a URL to a YouTube video page.', 'ableplayer' ),
		),
		'youtube-desc-id'     => array(
			'default'     => '',
			'description' => __( 'YouTube ID or URL of an alternative described version of a video.', 'ableplayer' ),
		),
		'youtube-nocookie'    => array(
			'default'     => '',
			'description' => __( 'If set to “true” the YouTube video will be embedded using the “youtube-nocookie.com” host.', 'ableplayer' ),
			'options'     => array( 'true' ),
		),
	);
	/**
	 * Filter the default values, options, and descriptions for all Able Player shortcode parameters. Not used yet in 1.2.0.
	 *
	 * @hook ableplayer_parameters
	 *
	 * @param {array} $params Array of default parameters. The array is a multidimensional array with the shortcode
	 *                        attribute as a key with array of `default` value, `description`, available `options`,
	 *(                       and alternate `parameter` name if the output isn't `data-{key}`.
	 *
	 * @return {array} Array of parameters.
	 */
	return apply_filters( 'ableplayer_default_parameters', $params );
}

/**
 * Remove Media Element scripts.
 */
function ableplayer_remove_scripts() {
	if ( ! is_admin() ) {
		$elements = ableplayer_get_settings( 'disable_elements' );
		if ( 'true' === $elements ) {
			wp_deregister_script( 'mediaelement' );
			wp_deregister_script( 'wp-playlist' );
		}
	}
}
add_action( 'init', 'ableplayer_remove_scripts' );

/**
 * Add support for [ableplayer] shortcode.
 *
 * @param array  $atts Array of shortcode parameters.
 * @param string $content Content between shortcode opening and closing tags, if any.
 *
 * @return string.
 */
function ableplayer_shortcode( $atts, $content = null ) {
	// Each of the following attributes can be passed with the [ableplayer] shortcode.
	// 'id' and 'type' (video or audio) is required.

	// normalize attribute keys, lowercase.
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );

	// build complete array of all attributes; defaults will be overridden with user values.
	$all_atts = shortcode_atts(
		array(
			'id'               => ableplayer_get_unique_id(),
			'youtube-id'       => '',
			'youtube-desc-id'  => '',
			'youtube-nocookie' => '',
			'vimeo-id'         => '',
			'vimeo-desc-id'    => '',
			'media-id'         => '',
			'media-desc-id'    => '',
			'media-asl-id'     => '',
			'captions'         => '',
			'subtitles'        => '',
			'descriptions'     => '',
			'chapters'         => '',
			'autoplay'         => 'false',
			'preload'          => 'metadata',
			'loop'             => 'false',
			'playsinline'      => 'true',
			'hidecontrols'     => 'false',
			'poster'           => '',
			'width'            => '',
			'height'           => '',
			'heading'          => '',
			'speed'            => 'animals',
			'start'            => '',
			'volume'           => '',
			'seekinterval'     => '',
			'nowplaying'       => '',
			'skin'             => '',
		),
		$atts,
		'ableplayer'
	);

	if ( '' !== $all_atts['vimeo-id'] ) {
		$debug   = ( SCRIPT_DEBUG || ABLEPLAYER_DEBUG ) ? true : false;
		$version = ABLEPLAYER_VERSION;
		$version = ( $debug ) ? $version . '-' . wp_rand( 1000, 9999 ) : $version;
		wp_enqueue_script( 'vimeo-player', 'https://player.vimeo.com/api/player.js', array( 'ableplayer' ), $version, true );
	}

	$source     = '';
	$datasource = '';
	if ( ! ( $all_atts['youtube-id'] || $all_atts['vimeo-id'] || $all_atts['media-id'] ) ) {
		// Shortcode must have one of YouTube, Vimeo, or local video source.
		return false;
	} else {
		if ( $all_atts['media-id'] ) {
			// If Video ID is set but is not a valid URL, return.
			$media_id = ( is_numeric( $all_atts['media-id'] ) ) ? wp_get_attachment_url( $all_atts['media-id'] ) : $all_atts['media-id'];
			if ( ! $media_id ) {
				return false;
			} else {
				$type   = get_post_mime_type( $all_atts['media-id'] );
				$type   = ( 'video/quicktime' ) ? 'video/mp4' : $type;
				$source = '<source type="' . esc_attr( $type ) . '" src="' . esc_url( $media_id ) . '"%datasrc%>' . PHP_EOL;
			}
		}
		if ( $all_atts['media-desc-id'] ) {
			// If Video ID is set but is not a valid URL, return.
			$media_desc_id = ( is_numeric( $all_atts['media-desc-id'] ) ) ? wp_get_attachment_url( $all_atts['media-desc-id'] ) : $all_atts['media-desc-id'];
			if ( $media_desc_id ) {
				$datatype    = get_post_mime_type( $all_atts['media-desc-id'] );
				$datatype    = ( 'video/quicktime' ) ? 'video/mp4' : $datatype;
				$datasource .= ( $type === $datatype ) ? ' data-desc-src="' . esc_url( $media_desc_id ) . '"' : '';
			}
		}
		if ( $all_atts['media-asl-id'] ) {
			// If Video ID is set but is not a valid URL, return.
			$media_asl_id = ( is_numeric( $all_atts['media-asl-id'] ) ) ? wp_get_attachment_url( $all_atts['media-asl-id'] ) : $all_atts['media-desc-id'];
			if ( $media_asl_id ) {
				$datatype    = get_post_mime_type( $all_atts['media-asl-id'] );
				$datatype    = ( 'video/quicktime' ) ? 'video/mp4' : $datatype;
				$datasource .= ( $type === $datatype ) ? ' data-sign-src="' . esc_url( $media_asl_id ) . '"' : '';
			}
		}
		$source = str_replace( '%datasrc%', $datasource, $source );

		$tracks = array();
		$kinds  = array(
			'captions'     => __( 'Captions', 'ableplayer' ),
			'subtitles'    => __( 'Subtitles', 'ableplayer' ),
			'descriptions' => __( 'Audio Description', 'ableplayer' ),
			'chapters'     => __( 'Chapters', 'ableplayer' ),
		);
		// Switch locale to BCP47 syntax.
		$default_lang = str_replace( '_', '-', get_locale() );
		if ( $all_atts['captions'] || $all_atts['subtitles'] || $all_atts['descriptions'] || $all_atts['chapters'] ) {
			foreach ( $kinds as $kind => $default_label ) {
				$track = '';
				if ( ! empty( $all_atts[ $kind ] ) ) {
					$data = explode( '|', $all_atts[ $kind ] );
					if ( empty( $data[0] ) ) {
						continue;
					}
					// Optional lang and language.
					$srclang  = ( isset( $data[1] ) && ! empty( $data[1] ) ) ? $data[1] : $default_lang;
					$srclabel = ( isset( $data[2] ) && ! empty( $data[2] ) ) ? $data[2] : $default_label;
					$src      = ( is_numeric( $data[0] ) ) ? wp_get_attachment_url( $data[0] ) : $data[0];
					$track    = '<track kind="' . $kind . '" srclang="' . esc_attr( $srclang ) . '" srclabel="' . esc_attr( $srclabel ) . '" src="' . esc_url( $src ) . '">';
					$tracks[] = $track;
				}
			}
		}

		// build a video player.
		$o  = '<video ';
		$o .= ' id="' . esc_attr( $all_atts['id'] ) . '"';
		$o .= ' data-able-player';
		if ( ableplayer_is_true( $all_atts['autoplay'] ) ) {
			$o .= ' autoplay';
		}
		if ( ableplayer_is_true( $all_atts['loop'] ) ) {
			$o .= ' loop';
		}
		if ( ableplayer_is_true( $all_atts['playsinline'] ) ) {
			$o .= ' playsinline';
		}
		if ( ableplayer_is_true( $all_atts['hidecontrols'] ) ) {
			$o .= ' data-hide-controls';
		}
		$preload = ( in_array( $all_atts['preload'], array( 'auto', 'metadata', 'none' ), true ) ) ? $all_atts['preload'] : '';
		if ( $preload ) {
			$o .= ' preload="' . esc_attr( $preload ) . '"';
		}
		if ( ! empty( $all_atts['poster'] ) ) {
			// Allow passing an attachment ID as poster.
			$poster = $all_atts['poster'];
			if ( is_numeric( $poster ) ) {
				$poster = wp_get_attachment_image_url( $poster, 'large' );
			}
			$o .= ' poster="' . esc_attr( $poster ) . '"';
		}
		if ( ! empty( $all_atts['width'] ) ) {
			$o .= ' width="' . esc_attr( $all_atts['width'] ) . '"';
		}
		if ( ! empty( $all_atts['height'] ) ) {
			$o .= ' height="' . esc_attr( $all_atts['height'] ) . '"';
		}
		if ( ! empty( $all_atts['heading'] ) ) {
			$o .= ' data-heading-level="' . esc_attr( $all_atts['heading'] ) . '"';
		}
		if ( ! empty( $all_atts['speed'] ) ) {
			$o .= ' data-speed-icons="' . esc_attr( $all_atts['speed'] ) . '"';
		}
		if ( ! empty( $all_atts['start'] ) ) {
			$o .= ' data-start-time="' . esc_attr( $all_atts['start'] ) . '"';
		}
		if ( ! empty( $all_atts['volume'] ) ) {
			$o .= 'data-volume="' . esc_attr( $all_atts['volume'] ) . '"';
		}
		if ( ! empty( $all_atts['seekinterval'] ) ) {
			$o .= ' data-seek-interval="' . esc_attr( $all_atts['seekinterval'] ) . '"';
		}
		if ( ! empty( $all_atts['nowplaying'] ) ) {
			$o .= ' data-show-now-playing="' . esc_attr( $all_atts['nowplaying'] ) . '"';
		}
		if ( ! empty( $all_atts['skin'] ) && '2020' !== $all_atts['skin'] ) {
			$o .= ' data-skin="' . esc_attr( $all_atts['skin'] ) . '"';
		}
		if ( ! empty( $all_atts['youtube-id'] ) ) {
			$o .= ' data-youtube-id="' . esc_attr( $all_atts['youtube-id'] ) . '"';
		}
		if ( ! empty( $all_atts['youtube-desc-id'] ) ) {
			$o .= ' data-youtube-desc-id="' . esc_attr( $all_atts['youtube-desc-id'] ) . '"';
		}
		if ( ! empty( $all_atts['youtube-nocookie'] ) ) {
			$o .= ' data-youtube-nocookie="' . esc_attr( $all_atts['youtube-nocookie'] ) . '"';
		}
		if ( ! empty( $all_atts['vimeo-id'] ) ) {
			$o .= ' data-vimeo-id="' . esc_attr( $all_atts['vimeo-id'] ) . '"';
		}
		if ( ! empty( $all_atts['vimeo-desc-id'] ) ) {
			$o .= ' data-vimeo-desc-id="' . esc_attr( $all_atts['vimeo-desc-id'] ) . '"';
		}
		$o .= '>' . PHP_EOL;

		$o .= $source;

		if ( ! empty( $tracks ) ) {
			$o .= implode( PHP_EOL, $tracks );
		}
		// enclosing tags.
		if ( ! is_null( $content ) ) {
			// run shortcode parser recursively.
			$o .= do_shortcode( $content );
		}

		// end media tag.
		$o .= PHP_EOL . '</video>';

		return $o;
	}
}
add_shortcode( 'ableplayer', 'ableplayer_shortcode' );

/**
 * Get unique ID for a specific Ableplayer instance.
 *
 * @return string
 */
function ableplayer_get_unique_id() {
	// use add_option(), get_option(), update_option(), & delete_option()
	// to track the number of Able Player instances on the current page
	// return a unique id for each new player instance,
	// using the form 'able_player_1', 'able_player_2', etc.
	$this_player = 1;
	$num_players = get_option( 'able_player_count' );
	if ( empty( $num_players ) ) {
		update_option( 'able_player_count', $this_player, false );

		return 'able_player_' . $this_player;
	} else {
		// there's already at least one player.
		$this_player = $num_players + 1;
		update_option( 'able_player_count', $this_player, false );

		return 'able_player_' . $this_player;
	}
}

/**
 * Test whether a given able player property is true.
 *
 * @param bool|string|int $condition A boolean-like value that should be treated as boolean.
 *
 * @return bool
 */
function ableplayer_is_true( $condition ) {
	// check for all variations that might be considered 'true'.
	if ( '1' === $condition || 'yes' === $condition || 'true' === $condition || 1 === $condition || true === $condition ) {
		return true;
	} else {
		return false;
	}
}
