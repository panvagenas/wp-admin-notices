<?php
/**
 * Project: wp-admin-notices
 * File: InitializationTest.phpp
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 5/11/2015
 * Time: 12:20 πμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Pan\Notices\Tests;

use Pan\Notices\WP_Admin_Notices;

class InitializationTest extends \WP_UnitTestCase {
	public function testWpAdminNoticesInstantiation() {
		do_action('admin_init');
		$wpAdminNotices = WP_Admin_Notices::getInstance();
		$this->assertTrue( $wpAdminNotices instanceof WP_Admin_Notices );

		$anotherInstance = WP_Admin_Notices::getInstance();

		$this->assertSame( $wpAdminNotices, $anotherInstance );
	}

	public function testActionsAreSet() {
		$this->assertFalse( is_int(has_action( 'admin_notices',
				array( WP_Admin_Notices::getInstance(), 'displayNotices' ) )) );

		do_action('admin_init');

		$this->assertTrue( is_int(has_action( 'admin_notices',
				array( WP_Admin_Notices::getInstance(), 'displayNotices' ) )) );
	}
}