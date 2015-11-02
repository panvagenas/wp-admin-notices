<?php
/**
 * Project: wp-admin-notices
 * File: WordPress.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 1/11/2015
 * Time: 9:04 μμ
 * Since: 2.0.0
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Notices\Formatters;


use Notices\WP_Notice;

/**
 * Class WordPress
 *
 * @package Notices\Formatters
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   2.0.0
 */
class WordPress extends Formatter {
	/**
	 * @param WP_Notice $notice
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	public function formatOutput( WP_Notice $notice ) {
		$before = '<div class="' . $notice->getType() . '">';
		if ( $notice->getTitle() ) {
			$before .= "<h4>{$notice->getTitle()}</h4>";
		}
		$before .= '<p>';
		$after = '</p></div>';

		return $before . $notice->getContent() . $after;
	}
}