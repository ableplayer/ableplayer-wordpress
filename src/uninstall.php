<?php
/**
 * Uninstall Able Player
 *
 * @category Core
 * @package  Able Player for WordPress
 * @author   Joe Dolson
 * @license  GPLv3
 * @link     https://xposterpro.com
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
} else {
    delete_option( 'able_player_count' );
    delete_option( 'able_show_playground_intro' );
    delete_option( 'ableplayer_settings' );
}
