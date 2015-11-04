<?php
/**
 * Project: wp-admin-notices
 * File: init.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 1/11/2015
 * Time: 9:07 μμ
 * Since: 2.0.0
 * Copyright: 2015 Panagiotis Vagenas
 */
namespace Pan\Notices;

if ( ! has_action( 'admin_init', '\Pan\Notices\addAdminNoticesAction' ) ) {
	add_action( 'admin_init',  '\Pan\Notices\addAdminNoticesAction');
}

if(!function_exists('\Pan\Notices\addAdminNoticesAction')) {
	/**
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	function addAdminNoticesAction() {
		add_action( 'admin_notices', array( WP_Admin_Notices::getInstance(), 'displayNotices' ) );
	}
}