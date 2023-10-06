<?php
/**
 * Class Tests_AblePlayer_General
 *
 * @package AblePlayer
 */

/**
 * Sample test case.
 */
class Tests_AblePlayer_General extends WP_UnitTestCase {
	/**
	 * Verify not in debug mode.
	 */
	public function test_ableplayer_not_in_debug_mode() {
		// Verify that the constant ABLEPLAYER_DEBUG is false.
		$this->assertFalse( ABLEPLAYER_DEBUG );
	}
}
