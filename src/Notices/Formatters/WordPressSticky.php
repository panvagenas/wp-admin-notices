<?php
/**
 * Project: wp-admin-notices
 * File: WordPressSticky.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 4/11/2015
 * Time: 9:16 πμ
 * Since: TODO ${VERSION}
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Pan\Notices\Formatters;


use Pan\Notices\WP_Notice;

/**
 * Class WordPressSticky
 *
 * @package Pan\Notices\Formatters
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   TODO ${VERSION}
 */
class WordPressSticky extends Formatter {
	/**
	 * @param WP_Notice $notice
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function formatOutput( WP_Notice $notice ) {
		$out = "
		<div style=\"position: relative;\" class=\"{$notice->getType()}\">
			<h4 style=\"margin-top: 4px; margin-bottom: 0px;\">{$notice->getTitle()}</h4>
			<p>
				{$notice->getContent()}
				<a href=\"{{ dismissLink }}\" style=\"font-size: 150%; position: absolute; right: 5px; top: -5px;\">×</a>
			</p>
		</div>
		";

		return $out;
	}
}