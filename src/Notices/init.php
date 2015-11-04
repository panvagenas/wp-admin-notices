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

/**
 * Hook action to admin init
 */
if ( ! has_action( 'admin_init', array( 'Pan\Notices\WP_Admin_Notices', 'getInstance' ) ) ) {
	add_action( 'admin_init', array( 'Pan\Notices\WP_Admin_Notices', 'getInstance' ) );
}