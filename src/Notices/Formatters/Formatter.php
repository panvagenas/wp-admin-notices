<?php
/**
 * Project: wp-admin-notices
 * File: Formatter.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 1/11/2015
 * Time: 9:04 μμ
 * Since: 2.0.0
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Pan\Notices\Formatters;


use Pan\Notices\WP_Notice;

/**
 * Class Formatter
 *
 * @package Pan\Notices\Formatters
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   2.0.0
 */
abstract class Formatter {
	/**
	 * Returns the output of the notice formatted
	 *
	 * @param WP_Notice $notice
	 *
	 * @return mixed
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	abstract function formatOutput( WP_Notice $notice );
}