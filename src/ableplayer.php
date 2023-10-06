<?php
/*
Plugin Name: Able Player
Plugin URI: https://github.com/ableplayer/ableplayer-wordpress
Version: 1.1
Author Name: Terrill Thompson
Author URI: http://terrillthompson.com
Contributors: terrillthompson
Description: This plug-in uses Able Player, an open-source fully-accessible cross-browser HTML5 media player, to embed audio or video within your WordPress page.
License: MIT
*/

// Configure debugging mode.
define( 'ABLEPLAYER_DEBUG', false );

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
function ableplayer_enqueue_scripts(){
	// Documentation: http://codex.wordpress.org/Function_Reference/wp_enqueue_script.
	// Register/enqueue common scripts that can be called automatically with just their handles (as of WP 3.5).
	wp_enqueue_script( 'jquery' );

	// Register/enqueue other dependencies
	wp_enqueue_script( 'js-cookie', plugins_url( 'thirdparty', __FILE__ ) . '/js.cookie.js', array( 'jquery' ) );
	wp_enqueue_script( 'vimeo', 'https://player.vimeo.com/api/player.js' );

	// Register/enqueue Able Player JavaScript (if debugging, unminified)
	$js_dir = apply_filters( 'able_player_js', plugins_url( 'build', __FILE__ ) );
	// Register/enqueue Able Player CSS (if debugging, unminified))
	$css_dir = apply_filters( 'able_player_css', plugins_url( 'build', __FILE__ ) );

	$is_production_environment = ( function_exists( 'wp_get_environment_type' ) && wp_get_environment_type() == 'production' ) ? true : SCRIPT_DEBUG;
	if ( SCRIPT_DEBUG === true || ! $is_production_environment ) {
		// JS Option 2: human-readable, for debugging.
		wp_enqueue_script( 'ableplayer', $js_dir . '/ableplayer.js', array( 'jquery' ) );

		// CSS Option 2: human-readable; use this if you intend to change the styles and customize the player.
		wp_enqueue_style( 'ableplayer', $css_dir .'/ableplayer.css');
	} else {
		// JS Option 1: minified, for production.
		wp_enqueue_script( 'ableplayer', $js_dir . '/ableplayer.min.js', array( 'jquery' ) );

		// CSS Option 1: minified, for production.
		wp_enqueue_style( 'ableplayer', $css_dir . '/ableplayer.min.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'ableplayer_enqueue_scripts' );

/**
 * Add support for [ableplayer] shortcode.
 *
 * @param array  $atts Array of shortcode parameters.
 * @param string $content Content between shortcode opening and closing tags, if any.
 *
 * @return string.
 */
function ableplayer_shortcode( $atts, $content = null ) {
	// Each of the following attributes can be passed with the [ableplayer] shortcode
	// 'id' and 'type' (video or audio) is required

	// normalize attribute keys, lowercase
	$atts = array_change_key_case((array)$atts, CASE_LOWER);

	// build complete array of all attributes; defaults will be overridden with user values
	$all_atts = shortcode_atts([
	'id' => ableplayer_get_unique_id(),
		'youtube-id' => '',
		'youtube-desc-id' => '',
		'youtube-nocookie' => '',
		'vimeo-id' => '',
		'vimeo-desc-id' => '',
		'autoplay' => 'false',
		'preload' => 'auto',
		'loop' => 'false',
		'playsinline' => 'true',
		'hidecontrols' => 'false',
		'poster' => '',
		'width' => '',
		'height' => '',
		'heading' => '',
		'speed' => 'animals',
		'start' => '',
		'volume' => '',
		'seekinterval' => '',
		'nowplaying' => 'false',
		'skin' => '2020'
	], $atts );

	// output
	if ( ! ( $all_atts['youtube-id'] || $all_atts['vimeo-id'] ) ) {
		// required fields are missing.
		return false;
	} else {
		// build a video player.
		$o = '<video ';
		$o .= ' id="' . $all_atts['id'] . '"';
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
		if ( ! empty( $all_atts['preload'] ) ) {
		  $o .= ' preload="' . $all_atts['preload'] . '"';
		}
		if ( ! empty( $all_atts['poster'] ) ) {
		  $o .= ' poster="' . $all_atts['poster'] . '"';
		}
		if ( ! empty($all_atts['width'] ) ) {
		  $o .= ' width="' . $all_atts['width'] . '"';
		}
		if ( ! empty( $all_atts['height'] ) ) {
		  $o .= ' height="' . $all_atts['height'] . '"';
		}
		if ( ! empty( $all_atts['poster'] ) ) {
		  $o .= ' poster="' . $all_atts['poster'] . '"';
		}
		if ( ! empty( $all_atts['heading'] ) ) {
		  $o .= ' data-heading-level="' . $all_atts['heading'] . '"';
		}
		if ( ! empty( $all_atts['speed'] ) ) {
		  $o .= ' data-speed-icons="' . $all_atts['speed'] . '"';
		}
		if ( ! empty( $all_atts['start'] ) ) {
		  $o .= ' data-start-time="' . $all_atts['start'] . '"';
		}
		if ( ! empty( $all_atts['volume'] ) ) {
		  $o .=  'data-volume="' . $all_atts['volume'] . '"';
		}
		if ( ! empty( $all_atts['seekinterval'] ) ) {
		  $o .= ' data-seek-interval="' . $all_atts['seekinterval'] . '"';
		}
		if ( ! empty( $all_atts['nowplaying'] ) ) {
		  $o .= ' data-show-now-playing="' . $all_atts['nowplaying'] . '"';
		}
		if ( ! empty( $all_atts['skin'] ) ) {
		  $o .= ' data-skin="' . $all_atts['skin'] . '"';
		}
		if ( ! empty( $all_atts['youtube-id'] ) ) {
		  $o .= ' data-youtube-id="' . $all_atts['youtube-id'] . '"';
		}
		if ( ! empty( $all_atts['youtube-desc-id'] ) ) {
		  $o .= ' data-youtube-desc-id="' . $all_atts['youtube-desc-id'] . '"';
		}
		if ( ! empty( $all_atts['youtube-nocookie'] ) ) {
		  $o .= ' data-youtube-nocookie="' . $all_atts['youtube-nocookie'] . '"';
		}
		if ( ! empty( $all_atts['vimeo-id'] ) ) {
		  $o .= ' data-vimeo-id="' . $all_atts['vimeo-id'] . '"';
		}
		if ( ! empty( $all_atts['vimeo-desc-id'] ) ) {
		  $o .= ' data-vimeo-desc-id="' . $all_atts['vimeo-desc-id'] . '"';
		}
		$o .= '>';

		// enclosing tags.
		if ( ! is_null( $content ) ) {
			// run shortcode parser recursively
			$o .= do_shortcode( $content );
		}

		// end media tag.
		$o .= '</video>';

		return $o;
	}
}
add_shortcode('ableplayer', 'ableplayer_shortcode');

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
		update_option('able_player_count', $this_player, false);
		return 'able_player_' . $this_player;
	} else {
		// there's already at least one player
		$this_player = $num_players + 1;
		update_option( 'able_player_count', $this_player, false);
		return 'able_player_' . $this_player;
	}
}

/**
 * Test whether a given able player property is true.
 *
 * @param bool|string|int $var A boolean-like value that should be treated as boolean.
 *
 * @return bool
 */
function ableplayer_is_true( $var ) {
	// check for all variations that might be considered 'true'.
	if ( $var === '1' || $var === 'yes' || $var === 'true' || $var === 1 || $var === true ) {
		return true;
	} else {
		return false;
	}
}
