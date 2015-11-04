<?php
/**
 * Project: wp-admin-notices
 * File: WP_Admin_Notices.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 1/11/2015
 * Time: 8:30 μμ
 * Since: 2.0.0
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Pan\Notices;


	/**
	 * Singleton class of WP_Admin_Notices
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 */
/**
 * Class WP_Admin_Notices
 *
 * @package Pan\Notices
 * @author  Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since   2.0.0
 */
class WP_Admin_Notices {
	/**
	 * $_POST or $_GET request var name for killing sticky notices
	 */
	const KILL_STICKY_NTC_VAR = 'rm_ntc';
	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var WP_Admin_Notices
	 */
	protected static $instance = null;

	/**
	 * Name of the array that will be stored in DB
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $noticesArrayName = 'WPAdminNotices';

	/**
	 * Notices array as loaded from DB
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $notices = array();

	/**
	 * Constructor (private since this is a singleton)
	 */
	private function __construct() {
		$this->loadNotices();
	}

	/**
	 * Loads notices from DB
	 */
	private function loadNotices() {
		$notices = get_option( $this->noticesArrayName );
		if ( is_array( $notices ) ) {
			$this->notices = $notices;
		}
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @since 1.0.0
	 * @return WP_Admin_Notices
	 */
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Action hook to display notices.
	 * Just echoes notices that should be displayed.
	 */
	public function displayNotices() {
		foreach ( $this->notices as $ntcId => $notice ) {
			/* @var WP_Notice $notice */
			if ( $this->isTimeToDisplayNtc( $notice ) ) {
				echo $notice->getContentFormatted();
				$notice->incrementDisplayedTimes();
			}
			if ( $this->isTimeToKillNtc( $notice ) ) {
				unset( $this->notices[ $ntcId ] );
			}
		}
		$this->storeNotices();
	}

	/**
	 * @param WP_Notice $notice
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	private function isTimeToKillNtc( WP_Notice $notice ) {
		if ( $notice->isSticky() ) {
			return $this->isTimeToKillStickyNtc( $notice );
		}

		$ntcUsers = $notice->getUsers();
		if ( empty( $ntcUsers ) ) {
			return $this->ntcExceededMaxTimesToDisplay( $notice );
		}

		$displayedSum = 0;
		foreach ( $ntcUsers as $userId ) {
			$displayedSum += $notice->maybeInitDisplayedToUsers( $userId );
		}

		if ( ( count( $notice->getUsers() ) * $notice->getTimes() ) <= $displayedSum ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if it is time to die for sticky notices
	 *
	 * @param WP_Notice $notice
	 *
	 * @return bool
	 * @throws \BadMethodCallException if this method is called for a non sticky message
	 *
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	private function isTimeToKillStickyNtc( WP_Notice &$notice ) {
		if ( ! $notice->isSticky() ) {
			throw new \BadMethodCallException( __METHOD__ . ' should not be called for non sticky notices' );
		}

		if ( ! isset( $_REQUEST[ self::KILL_STICKY_NTC_VAR ] ) || $_REQUEST[ self::KILL_STICKY_NTC_VAR ] != $notice->getId() ) {
			return false;
		}

		$ntcUsers = $notice->getUsers();

		if ( isset( $ntcUsers[ get_current_user_id() ] ) ) {
			unset( $ntcUsers[ get_current_user_id() ] );
			$notice->setUsers( $ntcUsers );
		}

		return empty( $ntcUsers );
	}

	/**
	 * Checks if is time to display a notice
	 *
	 * @param WP_Notice $notice
	 *
	 * @return bool
	 */
	private function isTimeToDisplayNtc( WP_Notice $notice ) {
		return $this->isTimeToDisplayNtcForScreen( $notice )
		       && $this->isTimeToDisplayNtcForUser( $notice )
		       && ! $this->ntcExceededMaxTimesToDisplay( $notice );
	}

	/**
	 * @param WP_Notice $notice
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	private function isTimeToDisplayNtcForScreen( WP_Notice $notice ) {
		$screens = $notice->getScreens();
		if ( ! empty( $screens ) ) {
			$curScreen = get_current_screen();
			if ( ! is_array( $screens ) || ! in_array( $curScreen->id, $screens ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param WP_Notice $notice
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	private function isTimeToDisplayNtcForUser( WP_Notice $notice ) {
		$usersArray = $notice->getUsers();
		if ( ! empty( $usersArray ) ) {
			$curUser = get_current_user_id();
			if ( ! is_array( $usersArray ) || ! in_array( $curUser,
					$usersArray ) || $usersArray[ $curUser ] >= $notice->getTimes()
			) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param WP_Notice $notice
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	private function ntcExceededMaxTimesToDisplay( WP_Notice $notice ) {
		if ( $notice->isSticky() ) {
			return false;
		}

		return $notice->getTimes() > $notice->getDisplayedTimes();
	}

	/**
	 * Stores notices in DB
	 */
	private function storeNotices() {
		update_option( $this->noticesArrayName, $this->notices );
	}

	/**
	 * Deletes a notice
	 *
	 * @param int $notId The notice unique id
	 */
	public function deleteNotice( $notId ) {
		foreach ( $this->notices as $key => $notice ) {
			/* @var WP_Notice $notice */
			if ( $notice->getId() === $notId ) {
				unset( $this->notices[ $key ] );
				break;
			}
		}
		$this->storeNotices();
	}

	/**
	 * Adds a notice to be displayed
	 *
	 * @param WP_Notice $notice
	 */
	public function addNotice( WP_Notice &$notice ) {
		$this->notices[] = $notice;
		$this->storeNotices();
	}

}