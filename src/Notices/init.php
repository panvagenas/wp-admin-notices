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

//
//if ( ! has_action( 'admin_init', '\Pan\Notices\addAdminNoticesAction' ) ) {
//	add_action( 'admin_init',  '\Pan\Notices\addAdminNoticesAction');
//}
//
//if(!function_exists('\Pan\Notices\addAdminNoticesAction')) {
//	/**
//	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
//	 * @since  TODO ${VERSION}
//	 */
//	function addAdminNoticesAction() {
//		add_action( 'admin_notices', array( WP_Admin_Notices::getInstance(), 'displayNotices' ) );
//	}
//}

if(defined('WPINC')) {
	$displayNtcCallback = array( WP_Admin_Notices::getInstance(), 'displayNotices' );

	if ( ! has_action( 'admin_notices', $displayNtcCallback ) ) {
		add_action( 'admin_notices', $displayNtcCallback );
	}

	$dismissNtcCallback = array( WP_Admin_Notices::getInstance(), 'ajaxDismissNotice' );

	if ( ! has_action( 'admin_notices', $dismissNtcCallback ) ) {
		add_action( 'wp_ajax_'.WP_Admin_Notices::KILL_STICKY_NTC_AJAX_ACTION, $dismissNtcCallback );
	}
}