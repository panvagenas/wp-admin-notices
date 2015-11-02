<?php

/**
 * Project: wp-admin-notices
 * File: BasicsTest.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 1/11/2015
 * Time: 8:47 μμ
 * Since: 2.0.0
 * Copyright: 2015 Panagiotis Vagenas
 */
class SampleTest extends WP_UnitTestCase{
	public function test(){
		$this->assertTrue(\Notices\WP_Admin_Notices::getInstance() instanceof \Notices\WP_Admin_Notices);
	}
}