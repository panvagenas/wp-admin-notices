<?php
/**
 * Project: wp-admin-notices
 * File: WP_UpdateNag_Notice.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 1/11/2015
 * Time: 8:33 μμ
 * Since: 2.0.0
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Notices\Types;


use Notices\WP_Notice;

/**
 * Class WP_UpdateNag_Notice
 *
 * @package Notices\Types
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   2.0.0
 */
class WP_UpdateNag_Notice extends WP_Notice {
	/**
	 * @var string
	 */
	protected $type = 'update-nag';
}