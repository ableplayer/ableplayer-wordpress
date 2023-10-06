<?php
/**
 * Class Tests_My_Tickets_General
 *
 * @package My Tickets
 */

/**
 * Sample test case.
 */
class Tests_My_Tickets_General extends WP_UnitTestCase {
	/**
	 * Verify not in debug mode.
	 */
	public function test_mt_not_in_debug_mode() {
		// Verify that the constant MT_DEBUG is false.
		$this->assertFalse( MT_DEBUG );
	}
}
