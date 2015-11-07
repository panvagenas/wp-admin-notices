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

use Pan\Notices\Formatters\FormatterInterface;
use Pan\Notices\Formatters\WordPress;
use Pan\Notices\Formatters\WordPressSticky;


/**
 * Abstract class of a notice
 *
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 */
class WP_Notice {
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
	 * Array indexes are the user ids this notice should be displayed, values are
	 * the displayed times for each user.
	 *
	 * Index `0` yields total displayed times
	 *
	 * `[
	 *      0 => $totalDisplayedTimesForUser,
	 *      $userId => $displayedTimesForUser,
	 *      ...
	 * ]`
	 *
	 * @var array
	 */
	protected $users = array( 0 => 0 );

	/**
	 * Array indexes are role ids this notice should be displayed, values are the
	 * displayed times for each role.
	 *
	 * Index `0` yields total displayed times
	 *
	 * `[
	 *      0 => $totalDisplayedTimesForRole,
	 *      $roleId => $displayedTimesForRole,
	 *      ...
	 * ]`
	 *
	 * @var array
	 */
	protected $roles = array( 0 => 0 );

	/**
	 * @var FormatterInterface
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
	 * @param array  $screens The admin screen ids this notice will be displayed into (empty for all screens)
	 * @param array  $users   Array of user ids this notice concerns (empty for all users)
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

		$this->setContent( $content );
		$this->setTitle( $title );
		$this->setScreens( (array) $screens );
		$this->setTimes( $times );

		foreach ( $users as $userId ) {
			$this->addUser( $userId );
		}

		if ( ! in_array( $type, array( self::TYPE_UPDATED_NAG, self::TYPE_UPDATED, self::TYPE_ERROR ) ) ) {
			$type = self::TYPE_UPDATED;
		}
		$this->type = $type;
	}

	/**
	 * @return int
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function countUsers() {
		return count( $this->users ) - 1;
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
	 * @since  2.0.1
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
		$this->users[0] ++;

		$user = get_user_by( 'ID', get_current_user_id() );

		if ( $this->hasUser( $user->ID ) ) {
			$this->users[ $user->ID ] ++;
		}

		$this->roles[0]++;

		foreach ( $user->roles as $role ) {
			if ( $this->hasRole( $role ) ) {
				$this->roles[ $role ] ++;
			}
		}

		return $this;
	}

	/**
	 * Initializes value in {@link $this::displayedToUsers} for $userId
	 *
	 * @param int $userId
	 *
	 * @return int The current value for the specified user after initialization
	 * @author     Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since      2.0.1
	 * @deprecated 2.1.0
	 */
	public function maybeInitDisplayedToUsers( $userId ) {
		if ( ! $this->hasUser( $userId ) ) {
			$this->addUser( $userId );
		}

		return $this->users[ $userId ];
	}

	/**
	 * @return int
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function getDisplayedTimes() {
		return $this->getDisplayedTimesForUser( 0 );
	}

	/**
	 * @param $userId
	 *
	 * @return int
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function getDisplayedTimesForUser( $userId ) {
		return $this->hasUser( $userId ) ? $this->users[ $userId ] : 0;
	}

	/**
	 * @param int $userId
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function exceededMaxTimesToDisplayForUser( $userId ) {
		$userId = (int) $userId;

		if ( $this->hasUser( $userId ) && $this->users[ $userId ] < $this->times ) {
			return false;
		}

		return true;
	}

	/**
	 * @param $role
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.1.0
	 */
	public function exceededMaxTimesForRole( $role ) {
		$role = (string) $role;

		if ( $this->hasRole( $role ) && $this->roles[ $role ] < $this->times ) {
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function exceededMaxTimesToDisplay() {
		$excMaxTimesForUsers = $this->users[0] >= $this->times;
		if ( $this->countUsers() > 0 ) {
			$usersCounter = 0;
			foreach ( array_keys( $this->getUsers() ) as $userId ) {
				if ( $this->exceededMaxTimesToDisplayForUser( $userId ) ) {
					$usersCounter ++;
				}
			}

			$excMaxTimesForUsers = $usersCounter >= $this->countUsers();
		}

		$excMaxTimesForRoles = $this->roles[0] >= $this->times;
		if ( $this->countRoles() > 0 ) {
			$rolesCounter = 0;
			foreach ( array_keys( $this->getRoles() ) as $role ) {
				if ( $this->exceededMaxTimesForRole( $role ) ) {
					$rolesCounter ++;
				}
			}

			$excMaxTimesForRoles = $rolesCounter >= $this->countRoles();
		}


		return $excMaxTimesForUsers && $excMaxTimesForRoles;
	}

	/**
	 * @param $userId
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function hasUser( $userId ) {
		return array_key_exists( (int) $userId, $this->users );
	}

	/**
	 * @param $userId
	 *
	 * @return $this
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function addUser( $userId ) {
		$userId = (int) $userId;

		if ( ! $this->hasUser( $userId ) ) {
			$this->users[ $userId ] = 0;
		}

		return $this;
	}

	/**
	 * @param $userId
	 *
	 * @return $this
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function removeUser( $userId ) {
		$userId = (int) $userId;

		if ( $userId > 0 && $this->hasUser( $userId ) ) {
			unset( $this->users[ $userId ] );
		}

		return $this;
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
		$users = $this->users;
		unset( $users[0] );

		return $users;
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
	 * @return FormatterInterface
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	public function getFormatter() {
		return $this->formatter;
	}

	/**
	 * @param FormatterInterface $formatter
	 *
	 * @return $this
	 * @throws \InvalidArgumentException If $formatter isn't an instanceof {@link
	 *                                   \Pan\Notices\Formatters\FormatterInterface}
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.0
	 */
	public function setFormatter( $formatter ) {
		if ( ! ( $formatter instanceof FormatterInterface ) ) {
			throw new \InvalidArgumentException( 'Notice FormatterInterface must be an instance of Pan\Notices\\Formatters\\FormatterInterface' );
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
	 * @since  2.0.1
	 */
	public function isSticky() {
		return $this->sticky;
	}

	/**
	 * @param boolean $sticky
	 *
	 * @return $this
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.0.1
	 */
	public function setSticky( $sticky ) {
		$this->sticky = (bool) $sticky;

		return $this;
	}

	/**
	 * @param $role
	 *
	 * @return $this
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.1.0
	 */
	public function addRole( $role ) {
		$role = (string) $role;
		if ( ! $this->hasRole( $role ) ) {
			$this->roles[ $role ] = 0;
		}

		return $this;
	}

	/**
	 * @param $role
	 *
	 * @return $this
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.1.0
	 */
	public function removeRole( $role ) {
		$role = (string) $role;

		if ( $role != 0 && $this->hasRole( $role ) ) {
			unset( $this->roles[ $role ] );
		}

		return $this;
	}

	/**
	 * @param array $roles
	 *
	 * @return bool
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.1.0
	 */
	public function hasRole( $roles ) {
		$roles = (array) $roles;

		foreach ( $roles as $role ) {
			if ( array_key_exists( $role, $this->roles ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return int
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.1.0
	 */
	public function countRoles() {
		return count( $this->roles ) - 1;
	}

	/**
	 * @return array
	 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
	 * @since  2.1.0
	 */
	public function getRoles() {
		return array_diff_key( $this->roles, array( 0 ) );
	}
}