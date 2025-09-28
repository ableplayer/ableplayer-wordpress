<?php
/**
 * Able Player WordPress plugin, accessible HTML5 media player
 *
 * @package     AblePlayer
 * @author      Joe Dolson
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: Able Player WordPress plugin, accessible HTML5 media player
 * Plugin URI:  https://github.com/ableplayer/ableplayer-wordpress
 * Description: Able Player is a fully accessible cross-browser HTML5 media player for audio and video.
 * Author:      Joe Dolson
 * Author URI:  https://www.joedolson.com
 * Text Domain: ableplayer
 * License:     MIT
 * License URI: https://github.com/ableplayer/ableplayer-wordpress/blob/master/LICENSE
 * Version:     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require __DIR__ . '/src/ableplayer.php';

register_activation_hook( __FILE__, 'ableplayer_activation' );
register_deactivation_hook( __FILE__, 'ableplayer_plugin_deactivated' );
