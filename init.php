<?php
/**
 * Able Player WordPress Plugin
 *
 * @package     AblePlayer
 * @author      Terrill Thompson
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: Able Player
 * Plugin URI:  https://github.com/ableplayer/ableplayer-wordpress
 * Description: Able Player is a fully accessible cross-browser HTML5 media player for audio and video.
 * Author:      Terrill Thompson
 * Author URI:  http://terrillthompson.com
 * Text Domain: ableplayer
 * License:     MIT
 * License URI: https://github.com/ableplayer/ableplayer-wordpress/blob/master/LICENSE
 * Domain Path: lang
 * Version:     1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include( dirname( __FILE__ ) . '/src/ableplayer.php' );

register_activation_hook( __FILE__, 'ableplayer_activation' );
register_deactivation_hook( __FILE__, 'ableplayer_plugin_deactivated' );
