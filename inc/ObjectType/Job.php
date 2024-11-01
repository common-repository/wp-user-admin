<?php
/**
 * \BitofWP\WPUserAdmin\ObjectType\Job class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\ObjectType;

use BitofWP\WPUserAdmin\ObjectType\AbstractObject;
use BitofWP\WPUserAdmin\ObjectType\Activity;
use Exception;
use BitofWP\WPUserAdmin\Utils;
use BitofWP\WPUserAdmin\Settings;
use BitofWP\WPUserAdmin\Notification;

/**
 * Class to create Job object.
 *
 * @since 1.0.0
 */
class Job extends AbstractObject {
	/**
	 * Role name for role change.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $role;

	/**
	 * Unix timestamp of role change.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $time;

	/**
	 * \WP_User object of user that receives role change.
	 *
	 * @since 1.0.0
	 * @var \WP_User
	 */
	public $user;

	const POST_TYPE = 'wpua_job';

	/**
	 * Load object from post.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If loading from post was unsuccessful.
	 *
	 * @param int $post_id ID of object’s post.
	 */
	public function load_from_post( $post_id ) {
		$this->set_post_id( $post_id );

		$this->set_user_id( $this->get_meta( 'user_id' ) );

		$this->time = $this->get_meta( 'time' );
		$this->role = $this->get_meta( 'role' );
	}

	/**
	 * Set $user property.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If there was a problem retrieving user object.
	 *
	 * @param int $user_id ID of user.
	 */
	public function set_user_id( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		if ( false !== $user ) {
			$this->user = $user;
		} else {
			throw new Exception( __( 'There was a problem retrieving user.', 'wp-user-admin' ) );
		}
	}

	/**
	 * Set $role property.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If role doesn’t exist or isn’t allowed.
	 *
	 * @param string $role Role name of new role.
	 */
	public function set_role( $role ) {
		$disallowed_roles = Utils::disallowed_roles();

		if ( ! wp_roles()->is_role( $role ) ) {
			throw new Exception( __( 'Role does not exists.', 'wp-user-admin' ) );
		}

		if ( in_array( $role, $disallowed_roles, true ) ) {
			throw new Exception( __( 'Role is not allowed.', 'wp-user-admin' ) );
		}

		$this->role = $role;
	}

	/**
	 * Set $time property.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If Unix Epoch timestamp was not passed.
	 *
	 * @param int $time Time of role change.
	 */
	public function set_time( $time ) {
		if ( is_numeric( $time ) ) {
			$this->time = (int) $time;
		} else {
			throw new Exception( __( 'Time was not set.', 'wp-user-admin' ) );
		}
	}

	/**
	 * Save new object and its meta values.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If there is an error during saving.
	 */
	public function change_role() {
		$this->user->set_role( $this->role );
		$this->set_meta( 'status', 'completed' );

		if ( is_numeric( $this->get_meta( 'parent_job_id' ) ) && Settings::is_notification_enabled() ) {
			Notification::send_role_restore( $this );
		}

		$activity = new Activity();
		$activity->set_action( 'role_change' );
		$activity->set_object_id( $this->id );
		$activity->save();
	}

	/**
	 * Save new object and its meta values.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If there is an error during saving.
	 */
	public function save() {
		try {
			$this->insert();
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}

		$this->set_meta( 'user_id', $this->user->ID );
		$this->set_meta( 'time', $this->time );
		$this->set_meta( 'role', $this->role );
		$this->set_meta( 'status', 'scheduled' );
	}
}
