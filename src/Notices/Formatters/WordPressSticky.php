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


use Pan\Notices\WP_Admin_Notices;
use Pan\Notices\WP_Notice;

/**
 * Class WordPressSticky
 *
 * @package Pan\Notices\Formatters
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   TODO ${VERSION}
 */
class WordPressSticky implements FormatterInterface {
	/**
	 * @param WP_Notice $notice
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function formatOutput( WP_Notice $notice ) {
		$unqId = uniqid( preg_replace('/[^a-z0-9A-Z]/', '', $notice->getId()) );
		$out   = "
		<div style=\"position: relative;\" class=\"{$notice->getType()}\">
			<h4 style=\"margin-top: 4px; margin-bottom: 0;\">{$notice->getTitle()}</h4>
			<p>
				{$notice->getContent()}
				{$notice->getId()}
				<a id=\"{$unqId}\" href=\"#\" style=\"font-size: 150%; position: absolute; right: 5px; top: -5px; text-decoration: none;\">×</a>
			</p>
		</div>
		";

		$out .= $this->dismissibleScript( $notice, $unqId );

		return $out;
	}

	/**
	 * @param WP_Notice $notice
	 * @param string    $unqId
	 *
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	protected function dismissibleScript( WP_Notice $notice, $unqId ) {
		return '
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				var data = {
					"action": "' . WP_Admin_Notices::KILL_STICKY_NTC_AJAX_ACTION . '",
					"' . WP_Admin_Notices::KILL_STICKY_NTC_AJAX_NTC_ID_VAR . '": "' . $notice->getId() . '",
					"' . WP_Admin_Notices::KILL_STICKY_NTC_AJAX_NONCE_VAR . '": "' . wp_create_nonce( WP_Admin_Notices::KILL_STICKY_NTC_AJAX_ACTION ) . '"
				};

				var $notice = $("#' . $unqId . '").parent().parent();
				$("#' . $unqId . '").click(function(){
					jQuery.post(ajaxurl, data,
						function(){
							$notice.slideUp();
						}
					);
				});
			});
		</script>
		';
	}
}