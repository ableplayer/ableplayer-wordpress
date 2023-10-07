<?php
/**
 * Able Player WordPress plugin, accessible HTML5 media player
 *
 * @package     Able Player
 * @author      Terrill Thompson
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: Able Player
 * Plugin URI:  https://github.com/ableplayer/ableplayer-wordpress
 * Description: This plug-in uses Able Player, an open-source fully-accessible cross-browser HTML5 media player, to embed audio or video within your WordPress page.
 * Author:      Terrill Thompson
 * Author URI:  http://terrillthompson.com
 * Text Domain: ableplayer
 * License:     MIT
 * License URI: https://github.com/ableplayer/ableplayer-wordpress/blob/master/LICENSE
 * Domain Path: lang
 * Version:     1.2
 */

// Configure debugging mode.
define( 'ABLEPLAYER_DEBUG', false );

// Get current version number.
define( 'ABLEPLAYER_VERSION', '1.2' );

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
	// Register/enqueue other dependencies.
	wp_enqueue_script( 'js-cookie', plugins_url( 'thirdparty', __FILE__ ) . '/js.cookie.js', array( 'jquery' ) );
	wp_enqueue_script( 'vimeo', 'https://player.vimeo.com/api/player.js' );

	// if the environment is production, use minified files. Otherwise, inherit the value of SCRIPT_DEBUG.
	$is_production = ( function_exists( 'wp_get_environment_type' ) && wp_get_environment_type() === 'production' ) ? true : SCRIPT_DEBUG;

	$js_file  = ( $is_production ) ? 'ableplayer.min.4.5.0.js' : 'ableplayer.4.5.0.js';
	$css_file = ( $is_production ) ? 'ableplayer.min.4.5.0.css' : 'ableplayer.4.5.0.css';
	/**
	 * Filter the Able Player JS URL.
	 *
	 * @hook able_player_js
	 *
	 * @param {string} $url URL to Able Player root directory.
	 * @param {bool}   $is_production True if environment is designated as production.
	 *
	 * @return string
	 */
	$js_dir = apply_filters( 'able_player_js', plugins_url( 'build', __FILE__ ) . '/' . $js_file, $is_production );
	/**
	 * Filter the Able Player CSS URL.
	 *
	 * @hook able_player_css
	 *
	 * @param {string} $url URL to Able Player root directory.
	 * @param {bool}   $is_production True if environment is designated as production.
	 *
	 * @return string
	 */
	$css_dir = apply_filters( 'able_player_css', plugins_url( 'build', __FILE__ ) . '/' . $css_file, $is_production );

	// Enqueue Able Player script and CSS.
	wp_enqueue_script( 'ableplayer', $js_dir, array( 'jquery' ), ABLEPLAYER_VERSION );
	wp_enqueue_style( 'ableplayer', $css_dir, array(), ABLEPLAYER_VERSION );

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
			'autoplay'         => 'false',
			'preload'          => 'auto',
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
			'nowplaying'       => 'false',
			'skin'             => '2020',
		),
		$atts,
		'ableplayer'
	);

	// output.
	if ( ! ( $all_atts['youtube-id'] || $all_atts['vimeo-id'] ) ) {
		// required fields are missing.
		return false;
	} else {
		// build a video player.
		$o  = '<video ';
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
		if ( ! empty( $all_atts['width'] ) ) {
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
			$o .= 'data-volume="' . $all_atts['volume'] . '"';
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
			// run shortcode parser recursively.
			$o .= do_shortcode( $content );
		}

		// end media tag.
		$o .= '</video>';

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
 * @param bool|string|int $var A boolean-like value that should be treated as boolean.
 *
 * @return bool
 */
function ableplayer_is_true( $var ) {
	// check for all variations that might be considered 'true'.
	if ( '1' === $var || 'yes' === $var || 'true' === $var || 1 === $var || true === $var ) {
		return true;
	} else {
		return false;
	}
}
