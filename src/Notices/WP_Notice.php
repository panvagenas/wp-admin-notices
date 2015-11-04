<?php
/**
 * Project: wp-admin-notices
 * File: WP_Notice.php
 * User: Panagiotis Vagenas <pan.vagenas@gmail.com>
 * Date: 1/11/2015
 * Time: 8:31 μμ
 * Since: 2.0.0
 * Copyright: 2015 Panagiotis Vagenas
 */

namespace Pan\Notices;

use Pan\Notices\Formatters\Formatter;
use Pan\Notices\Formatters\WordPress;
use Pan\Notices\Formatters\WordPressSticky;


/**
 * Abstract class of a notice
 *
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
abstract class WP_Notice {
	/**
	 * Notice Type Error
	 */
	const TYPE_ERROR = 'error';
	/**
	 * Notice Type Updated
	 */
	const TYPE_UPDATED = 'updated';
	/**
	 *  Notice Type Updated Nag
	 */
	const TYPE_UPDATED_NAG = 'update-nag';
	/**
	 * Notice message to be displayed
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * Title of the notice. This is optional.
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Notice type
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * In which screens the notice to be displayed
	 *
	 * @var array
	 */
	protected $screens;

	/**
	 * Unique identifier for notice
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * Number of times to be displayed
	 *
	 * @var int
	 */
	protected $times = 1;

	/**
	 * User ids this notice should be displayed
	 *
	 * @var array
	 */
	protected $users = array();

	/**
	 * Number of times this message is displayed
	 *
	 * @var int
	 */
	protected $displayedTimes = 0;

	/**
	 * Keeps track of how many times and to
	 * which users this notice is displayed
	 *
	 * @var array
	 */
	protected $displayedToUsers = array();
	/**
	 * @var Formatter
	 */
	protected $formatter;
	/**
	 * @var bool
	 */
	protected $sticky = false;

	/**
	 *
	 * @param string $content Content to be displayed
	 * @param string $title   Title of the notice, optional default is empty string.
	 * @param string $type    Type of the notice, must be one of {@link self::TYPE_UPDATED}, {@link self::TYPE_ERROR},
	 *                        {@link self::TYPE_UPDATED_NAG}. Defaults to {@link self::TYPE_UPDATED}.
	 * @param int    $times   How many times this notice will be displayed
	 * @param array  $screens The admin screens this notice will be displayed into (empty for all screens)
	 * @param array  $users   Array of users this notice concerns (empty for all users)
	 */
	public function __construct(
		$content,
		$title = '',
		$type = self::TYPE_UPDATED,
		$times = 1,
		$screens = array(),
		$users = array()
	) {
		$this->id = uniqid( md5( $content ), true );

		$this->content = $this->setContent( $content );
		$this->title   = $this->setTitle( $title );
		$this->screens = $this->setScreens( (array) $screens );
		$this->times   = $this->setTimes( $times );
		$this->users   = $this->setUsers( (array) $users );

		if ( ! in_array( $type, array( self::TYPE_UPDATED_NAG, self::TYPE_UPDATED, self::TYPE_ERROR ) ) ) {
			$type = self::TYPE_UPDATED;
		}
		$this->type = $type;
	}

	/**
	 * Get the content of the notice
	 *
	 * @return string Formatted content
	 */
	public function getContentFormatted() {
		if ( ! $this->formatter ) {
			$this->formatter = $this->getDefaultFormatter();
		}

		return $this->formatter->formatOutput( $this );
	}

	/**
	 * @return WordPress|WordPressSticky
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	protected function getDefaultFormatter() {
		if ( $this->isSticky() ) {
			return new WordPressSticky();
		}

		return new WordPress();
	}

	/**
	 * Get the notice string un-formatted
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 *
	 * @param string $content
	 *
	 * @return $this
	 */
	public function setContent( $content ) {
		$this->content = (string) $content;

		return $this;
	}

	/**
	 * Increment displayed times of the notice
	 *
	 * @return $this
	 */
	public function incrementDisplayedTimes() {
		$this->displayedTimes ++;

		$userId = get_current_user_id();

		$this->displayedToUsers[ $userId ] = $this->maybeInitDisplayedToUsers( $userId ) + 1;

		return $this;
	}

	/**
	 * Initializes value in {@link $this::displayedToUsers} for $userId
	 *
	 * @param int $userId
	 *
	 * @return int The current value for the specified user after initialization
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function maybeInitDisplayedToUsers( $userId ) {
		if ( ! array_key_exists( $userId, $this->displayedToUsers ) ) {
			$this->displayedToUsers[ $userId ] = 0;
		}

		return $this->displayedToUsers[ $userId ];
	}

	/**
	 * Checks if the notice should me destroyed
	 *
	 * @return boolean True iff notice is deprecated
	 * @deprecated as of v2.0.1
	 */
	public function isTimeToDie() {
		if ( empty( $this->users ) ) {
			return $this->displayedTimes >= $this->times;
		}

		$displayedSum = 0;
		foreach ( $this->users as $userId ) {
			$displayedSum += $this->maybeInitDisplayedToUsers( $userId );
		}
		if ( ( count( $this->users ) * $this->times ) <= $displayedSum ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the screens for the notice to be displayed
	 *
	 * @return string Current screens slug
	 */
	public function getScreens() {
		return $this->screens;
	}

	/**
	 * Set the screens the notice will be displayed
	 *
	 * @param array $screens
	 *
	 * @return $this
	 */
	public function setScreens( $screens ) {
		$this->screens = (array) $screens;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 *
	 * @return int
	 */
	public function getTimes() {
		return $this->times;
	}

	/**
	 *
	 * @param int $times
	 *
	 * @return $this
	 */
	public function setTimes( $times ) {
		$this->times = (int) $times;

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getUsers() {
		return $this->users;
	}

	/**
	 *
	 * @param array $users
	 *
	 * @return $this
	 */
	public function setUsers( Array $users ) {
		$this->users = (array) $users;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function getDisplayedTimes() {
		return $this->displayedTimes;
	}

	/**
	 *
	 * @param int $displayedTimes
	 *
	 * @return $this
	 */
	public function setDisplayedTimes( $displayedTimes ) {
		$this->displayedTimes = (int) $displayedTimes;

		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getDisplayedToUsers() {
		return $this->displayedToUsers;
	}

	/**
	 *
	 * @param array $displayedToUsers
	 *
	 * @return $this
	 */
	public function setDisplayedToUsers( $displayedToUsers ) {
		$this->displayedToUsers = (array) $displayedToUsers;

		return $this;
	}

	/**
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 *
	 * @return $this
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	public function setTitle( $title ) {
		$this->title = (string) $title;

		return $this;
	}

	/**
	 * @return Formatter
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	public function getFormatter() {
		return $this->formatter;
	}

	/**
	 * @param Formatter $formatter
	 *
	 * @return $this
	 * @throws \InvalidArgumentException If $formatter isn't an instanceof {@link \Pan\Notices\Formatters\Formatter}
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	public function setFormatter( $formatter ) {
		if ( ! ( $formatter instanceof Formatter ) ) {
			throw new \InvalidArgumentException( 'Notice Formatter must be an instance of Pan\Notices\\Formatters\\Formatter' );
		}
		$this->formatter = $formatter;

		return $this;
	}

	/**
	 * @return string
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return boolean
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function isSticky() {
		return $this->sticky;
	}

	/**
	 * @param boolean $sticky
	 *
	 * @return $this
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  TODO ${VERSION}
	 */
	public function setSticky( $sticky ) {
		$this->sticky = (bool) $sticky;

		return $this;
	}
}