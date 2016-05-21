<?php
/*
Plugin Name: Able Player
Plugin URI: https://github.com/ableplayer-wordpress
Description: Accessible HTML5 media player
Contributors: terrillthompson
Tags: html5, media, audio, video, accessibility
Version: 0.1
Stable tag: 0.1
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
  wp_enqueue_script( 'modernizer', plugins_url('thirdparty',__FILE__).'/modernizr.custom.js');
  wp_enqueue_script( 'js-cookie', plugins_url('thirdparty',__FILE__).'/js.cookie.js',array('jquery'));

  // Register/enqueue Able Player script
  wp_enqueue_script( 'ableplayer', plugins_url('build',__FILE__).'/ableplayer.js',array('jquery'));

  // Register/enqueue Able Player CSS (two options; uncomment the one you prefer)

  // Use minimized CSS that's included in Able Player build
  // wp_enqueue_style( 'ableplayer', plugins_url('build',__FILE__).'/ableplayer.min.css');

  // Use human-readable CSS if you intend to change the styles and customize the player
  wp_enqueue_style( 'ableplayer', plugins_url('styles',__FILE__).'/ableplayer.css');

}
add_action( 'wp_enqueue_scripts', 'ableplayer_enqueue_scripts');

function ableplayer_head() {

  // add a variable that points to the current plugin directory
  echo "<script>\n";
  echo 'var pluginUrl = "'.plugins_url('',__FILE__).'";'."\n";
  echo "</script>\n";
}
add_action('wp_head','ableplayer_head',3);
?>