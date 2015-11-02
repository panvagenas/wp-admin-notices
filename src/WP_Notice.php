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

namespace Notices;

use Notices\Formatters\Formatter;
use Notices\Formatters\WordPress;


/**
 * Abstract class of a notice
 *
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
abstract class WP_Notice {

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
	protected $screen;

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
	 *
	 * @param string $content Content to be displayed
	 * @param string $title   Title of the notice, optional default is empty string.
	 * @param int    $times   How many times this notice will be displayed
	 * @param array  $screen  The admin screens this notice will be displayed into (empty for all screens)
	 * @param array  $users   Array of users this notice concerns (empty for all users)
	 */
	public function __construct( $content, $title = '', $times = 1, Array $screen = array(), Array $users = array() ) {
		$this->content = $content;
		$this->title   = $title;
		$this->screen  = $screen;
		$this->id      = uniqid();
		$this->times   = $times;
		$this->users   = $users;
	}

	/**
	 * Get the content of the notice
	 *
	 * @return string Formatted content
	 */
	public function getContentFormatted(  ) {
		$formatter = $this->formatter ? $this->formatter : new WordPress();

		return $formatter->formatOutput($this);
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
		$this->content = $content;

		return $this;
	}

	/**
	 * Increment displayed times of the notice
	 *
	 * @return $this
	 */
	public function incrementDisplayedTimes() {
		$this->displayedTimes ++;

		if ( array_key_exists( get_current_user_id(), $this->displayedToUsers ) ) {
			$this->displayedToUsers[ get_current_user_id() ] ++;
		} else {
			$this->displayedToUsers[ get_current_user_id() ] = 1;
		}

		return $this;
	}

	/**
	 * Checks if the notice should me destroyed
	 *
	 * @return boolean True iff notice is deprecated
	 */
	public function isTimeToDie() {
		if ( empty( $this->users ) ) {
			return $this->displayedTimes >= $this->times;
		}

		$i = 0;
		foreach ( $this->users as $value ) {
			if ( isset( $this->displayedToUsers[ $value ] ) && $this->displayedToUsers[ $value ] >= $this->times ) {
				$i ++;
			}
		}
		if ( $i >= count( $this->users ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the current screen slug
	 *
	 * @return string Current screen slug
	 */
	public function getScreen() {
		return $this->screen;
	}

	/**
	 * Set the screens the notice will be displayed
	 *
	 * @param array $screen
	 *
	 * @return $this
	 */
	public function setScreen( Array $screen ) {
		$this->screen = $screen;

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
		$this->times = $times;

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
		$this->users = $users;

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
		$this->displayedTimes = $displayedTimes;

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
	public function setDisplayedToUsers( Array $displayedToUsers ) {
		$this->displayedToUsers = $displayedToUsers;

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
		$this->title = $title;

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
	 * @throws \InvalidArgumentException If $formatter isn't an instanceof {@link \Notices\Formatters\Formatter}
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	public function setFormatter( $formatter ) {
		if(!($formatter instanceof Formatter)){
			throw new \InvalidArgumentException('Notice Formatter must be an instance of Notices\\Formatters\\Formatter');
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
}