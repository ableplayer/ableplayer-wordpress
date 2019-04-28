<?php
/*
Plugin Name: Able Player
Plugin URI: https://github.com/ableplayer-wordpress
Description: Accessible HTML5 media player
Contributors: terrillthompson
Tags: html5, media, audio, video, accessibility
Version: 0.1.1
Requires at least: 2.6
Tested up to: 4.5
License: MIT
License URI: https://github.com/ableplayer-wordpress/LICENSE
*/

/*
 *
 * Disable the feature in WordPress that wraps everything (including Able Player code)
 * in <p> tags
 *
 * NOTE: This will affect ALL content on site, and may have undesirable consequences
 * Therefore it's commented out by default. Test before using.
 *
 *
*/
// remove_filter ('the_content', 'wpautop');

/*
 *
 * load styles and scripts to head
 *
*/

function ableplayer_enqueue_scripts(){

  // Documentation:
  // http://codex.wordpress.org/Function_Reference/wp_enqueue_script

  // Register/enqueue common scripts that can be called automatically with just their handles (as of WP 3.5)
  wp_enqueue_script( 'jquery' );

  // Register/enqueue other dependencies
  wp_enqueue_script( 'js-cookie', plugins_url('thirdparty',__FILE__).'/js.cookie.js',array('jquery'));

  // Register/enqueue Able Player JavaScript (two options; uncomment the one you prefer)

  // JS Option 1: minified, for production
  // wp_enqueue_script( 'ableplayer', plugins_url('build',__FILE__).'/ableplayer.min.js',array('jquery'));

  // JS Option 2: human-readable, for debugging
  wp_enqueue_script( 'ableplayer', plugins_url('build',__FILE__).'/ableplayer.js',array('jquery'));

  // Register/enqueue Able Player CSS (two options; uncomment the one you prefer)

  // CSS Option 1: minified, for production
  wp_enqueue_style( 'ableplayer', plugins_url('build',__FILE__).'/ableplayer.min.css');

  // CSS Option 2: human-readable; use this if you intend to change the styles and customize the player
  // wp_enqueue_style( 'ableplayer', plugins_url('styles',__FILE__).'/ableplayer.css');

}
add_action( 'wp_enqueue_scripts', 'ableplayer_enqueue_scripts');


/*
 *
 * Add support for [able-player] shortcode
 *
 *
*/

function able_player_shortcode( $atts,$content=null ) {

	// Each of the following attributes can be passed with the [able-player] shortcode
	// 'id' and 'type' (video or audio) is required

  // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);

  // build complete array of all attributes; defaults will be overridden with user values
  $all_atts = shortcode_atts([
    'id' => get_unique_id(),
		'type' => '',
		'autoplay' => 'false',
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
		'nowplaying' => 'false'
	], $atts );

  // output
  if (!$all_atts['type']) {
    // required fields are missing
    return false;
  }
  else {
    $type = $all_atts['type'];
    if (!($type == 'audio' || $type == 'video')) {
      // type is not a supported value
      return false;
    }
    else {
      // build a media player!
      $o = '<'.$type;
      $o .= ' id="'.$all_atts['id'].'"';
      $o .= ' data-able-player';
      $o .= ' preload="auto"';
      if (is_true($all_atts['autoplay'])) {
        $o .= ' autoplay';
      }
      if (is_true($all_atts['loop'])) {
        $o .= ' loop';
      }
      if (is_true($all_atts['playsinline'])) {
        $o .= ' playsinline';
      }
      if (is_true($all_atts['hidecontrols'])) {
        $o .= ' data-hide-controls';
      }
      if (!empty($all_atts['poster'])) {
        $o .= ' poster="'.$all_atts['poster'].'"';
      }
      if (!empty($all_atts['width'])) {
        $o .= ' width="'.$all_atts['width'].'"';
      }
      if (!empty($all_atts['height'])) {
        $o .= ' height="'.$all_atts['height'].'"';
      }
      if (!empty($all_atts['poster'])) {
        $o .= ' poster="'.$all_atts['poster'].'"';
      }
      if (!empty($all_atts['heading'])) {
        $o .= ' data-heading-level="'.$all_atts['heading'].'"';
      }
      if (!empty($all_atts['speed'])) {
        $o .= ' data-speed-icons="'.$all_atts['speed'].'"';
      }
      if (!empty($all_atts['start'])) {
        $o .= ' data-start-time="'.$all_atts['start'].'"';
      }
      if (!empty($all_atts['volume'])) {
        $o .=  'data-volume="'.$all_atts['volume'].'"';
      }
      if (!empty($all_atts['seekinterval'])) {
        $o .= ' data-seek-interval="'.$all_atts['seekinterval'].'"';
      }
      if (!empty($all_atts['shownowplaying'])) {
        $o .= ' data-show-now-playing="'.$all_atts['shownowplaying'].'"';
      }
      $o .= '>';

      // enclosing tags
      if (!is_null($content)) {
        // run shortcode parser recursively
        $o .= do_shortcode($content);
      }

      // end media tag
      $o .= '</'.$type.'>';

      // return output
      // To display HTML <audio> or <video> code (for debugging), uncomment first return statement
      // For production, uncomment second return statement
      // return esc_html($o);
      return $o;
    }
  }
}
add_shortcode('able-player', 'able_player_shortcode');

/*
 *
 * Add support for [able-source] shortcode
 *
 *
*/

function able_source_shortcode( $atts,$content=null ) {

	// Each of the following attributes can be passed with the [able-source] shortcode
	// Either 'src', youtube-id, or vimeo-id is required

  $all_atts = shortcode_atts([
		'src' => '',
		'type' => '',
		'desc-src' => '',
		'sign-src' => '',
		'youtube-id' => '',
		'youtube-desc-id' => '',
		'youtube-nocookie' => '',
		'vimeo-id' => '',
		'vimeo-desc-id' => ''
	], $atts );

  // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);

  // output
  if (!($all_atts['src'] || $all_atts['youtube-id'] || $all_atts['youtube-desc-id'])) {
    // there is no media source
    return false;
  }
  else {
    // build a source tag
    $o = '<source';
    if (!empty($all_atts['src'])) {
      $o .= ' src="'.$all_atts['src'].'"';
    }
    if (!empty($all_atts['type'])) {
      $o .= ' type="'.$all_atts['type'].'"';
    }
    if (!empty($all_atts['desc-src'])) {
      $o .= ' data-desc-src="'.$all_atts['desc-src'].'"';
    }
    if (!empty($all_atts['sign-src'])) {
      $o .= ' data-sign-src="'.$all_atts['sign-src'].'"';
    }
    if (!empty($all_atts['youtube-id'])) {
      $o .= ' data-youtube-id="'.$all_atts['youtube-id'].'"';
    }
    if (!empty($all_atts['youtube-desc-id'])) {
      $o .= ' data-youtube-desc-id="'.$all_atts['youtube-desc-id'].'"';
    }
    if (!empty($all_atts['youtube-nocookie'])) {
      $o .= ' data-youtube-nocookie="'.$all_atts['youtube-nocookie'].'"';
    }
    if (!empty($all_atts['vimeo-id'])) {
      $o .= ' data-vimeo-id="'.$all_atts['vimeo-id'].'"';
    }
    if (!empty($all_atts['vimeo-desc-id'])) {
      $o .= ' data-vimeo-desc-id="'.$all_atts['vimeo-desc-id'].'"';
    }
    $o .= '>';
  }
  return $o;
}
add_shortcode('able-source', 'able_source_shortcode');

/*
 *
 * Add support for [able-track] shortcode
 *
 *
*/

function able_track_shortcode( $atts,$content=null ) {

	// Each of the following attributes can be passed with the [able-track] shortcode
	// 'kind' and 'src' are required

  $all_atts = shortcode_atts([
		'kind' => '',
		'src' => '',
		'srclang' => '',
		'label' => ''
	], $atts );

  // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);

  // output
  if (!($all_atts['kind'] && $all_atts['src'])) {
    // required fields are missing
    return false;
  }
  else {
    // build a track tag
    $o = '<track';
    $o .= ' kind="'.$all_atts['kind'].'"';
    $o .= ' src="'.$all_atts['src'].'"';
    if (!empty($all_atts['srclang'])) {
      $o .= ' srclang="'.$all_atts['srclang'].'"';
    }
    if (!empty($all_atts['label'])) {
      $o .= ' label="'.$all_atts['label'].'"';
    }
    $o .= '>';
    return $o;
  }
}
add_shortcode('able-track', 'able_track_shortcode');

/*
 *
 * Supporting functions
 *
*/

function get_unique_id() {

  // use add_option(), get_option(), update_option(), & delete_option()
  // to track the number of Able Player instances on the current page
  // return a unique id for each new player instance,
  // using the form 'able_player_1', 'able_player_2', etc.
  $this_player = 1;
  $num_players = get_option('able-player-count');
  if (empty($num_players)) {
    add_option('able-player-count',$player_count);
    return 'able-player-'.$this_player;
  }
  else {
    // there's already at least one player
    $this_player = $num_players + 1;
    update_option('able-player-count',$this_player);
    return 'able-player-'.$this_player;
  }
}

function is_true($var) {

  // $var is a Boolean parameter
  // check for all variations that might be considered 'true'
  // return true if... well, true
  if ($var == '1' || $var == 'yes' || $var == 'true') {
    return true;
  }
  else {
    return false;
  }
}

?>