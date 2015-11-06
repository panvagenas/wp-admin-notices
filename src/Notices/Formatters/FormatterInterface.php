<?php
/**
 * Project: wp-admin-notices
 * File: FormatterInterface.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 1/11/2015
 * Time: 9:04 μμ
 * Since: 2.0.0
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Pan\Notices\Formatters;


use Pan\Notices\WP_Notice;

/**
 * Ifc FormatterInterface
 *
 * @package Pan\Notices\Formatters
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   2.0.0
 */
interface FormatterInterface {
	/**
	 * Returns the output of the notice formatted
	 *
	 * @param WP_Notice $notice
	 *
	 * @return mixed
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	function formatOutput( WP_Notice $notice );
}