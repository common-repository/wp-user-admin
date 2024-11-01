<?php
/**
 * \BitofWP\WPUserAdmin\Scheduler class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin;

use Exception;
use WP_User;
use BitofWP\WPUserAdmin\ObjectType\Job;
use BitofWP\WPUserAdmin\ObjectType\Activity;
use BitofWP\WPUserAdmin\Utils;
use BitofWP\WPUserAdmin\Notification;
use BitofWP\WPUserAdmin\Settings;

/**
 * Class to schedule new role change.
 *
 * @since 1.0.0
 */
class Scheduler {
	/**
	 * Role name for first role change job.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $start_role;

	/**
	 * Role name for second role change job.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $end_role;

	/**
	 * Unix timestamp of first role change job.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $start_time;

	/**
	 * Unix timestamp of second role change job.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $end_time;

	/**
	 * \WP_User object of user that receives role change.
	 *
	 * @since 1.0.0
	 * @var \WP_User
	 */
	public $user;

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

		if ( false === $user ) {
			throw new Exception( __( 'There was a problem retrieving user.', 'wp-user-admin' ) );
		}

		if ( Utils::has_user_scheduled_jobs( $user_id ) ) {
			throw new Exception( __( 'This user already has scheduled role changes.', 'wp-user-admin' ) );
		}

		$this->user = $user;
	}

	/**
	 * Set $start_role property.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If role doesn’t exist or isn’t allowed.
	 *
	 * @param string $role Role name of new role.
	 */
	public function set_start_role( $role ) {
		$disallowed_roles = Utils::disallowed_roles();

		if ( ! wp_roles()->is_role( $role ) ) {
			throw new Exception( __( 'Role does not exists.', 'wp-user-admin' ) );
		}

		if ( in_array( $role, $disallowed_roles, true ) ) {
			throw new Exception( __( 'Role is not allowed.', 'wp-user-admin' ) );
		}

		$this->start_role = $role;
	}

	/**
	 * Set $end_role property.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If role doesn’t exist or isn’t allowed.
	 *
	 * @param string $role Role name of new role.
	 */
	public function set_end_role( $role ) {
		$disallowed_roles = Utils::disallowed_roles();

		if ( ! wp_roles()->is_role( $role ) ) {
			throw new Exception( __( 'Role does not exists.', 'wp-user-admin' ) );
		}

		if ( in_array( $role, $disallowed_roles, true ) ) {
			throw new Exception( __( 'Role is not allowed.', 'wp-user-admin' ) );
		}

		$this->end_role = $role;
	}

	/**
	 * Set $start_time property.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If Unix Epoch timestamp was not passed.
	 *
	 * @param int $time Time of role change.
	 */
	public function set_start_time( $time ) {
		if ( ! is_numeric( $time ) ) {
			throw new Exception( __( 'Scheduled time of role change was not set.', 'wp-user-admin' ) );
		}

		$time = (int) $time;

		if ( time() > $time ) {
			throw new Exception( __( 'Scheduled time of role change is set to the past.', 'wp-user-admin' ) );
		}

		$this->start_time = $time;
	}

	/**
	 * Set $end_time property.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If Unix Epoch timestamp was not passed.
	 *
	 * @param int $time Time of role change.
	 */
	public function set_end_time( $time ) {
		if ( ! is_numeric( $time ) ) {
			throw new Exception( __( 'Scheduled time of role change was not set.', 'wp-user-admin' ) );
		}

		$time = (int) $time;

		if ( time() > $time ) {
			throw new Exception( __( 'Scheduled time of role change is set to the past.', 'wp-user-admin' ) );
		}

		$this->end_time = $time;
	}

	/**
	 * Schedule a new role change jobs.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If required parameters are not set or
	 *                   there is an error during saving new job or activity.
	 */
	public function schedule() {
		if ( ! ( $this->user instanceof WP_User ) ) {
			throw new Exception( __( 'User has not been set.', 'wp-user-admin' ) );
		}

		if ( ! $this->start_time ) {
			throw new Exception( __( 'Scheduled time of role change has not been set.', 'wp-user-admin' ) );
		}

		if ( ! $this->start_role ) {
			throw new Exception( __( 'New role has not been set.', 'wp-user-admin' ) );
		}

		$this->verify_time_order();

		$start_job = $this->create_job( 'start' );

		if ( Settings::is_notification_enabled() ) {
			Notification::send_role_change( $start_job );
		}

		if ( ! empty( $this->end_time ) && ! empty( $this->end_role ) ) {
			$end_job = $this->create_job( 'end' );

			$start_job->set_meta( 'child_job_id', $end_job->id );
			$end_job->set_meta( 'parent_job_id', $start_job->id );
		}
	}

	/**
	 * Creates a new role change job.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If there is an error during saving of job or activity.
	 *
	 * @param string $when Whether job should be created for starting or ending role change.
	 * @return Job.
	 */
	public function create_job( $when ) {
		$time_key = "{$when}_time";
		$role_key = "{$when}_role";

		$job = new Job();
		$job->set_user_id( $this->user->ID );
		$job->set_time( $this->$time_key );
		$job->set_role( $this->$role_key );
		$job->save();

		wp_schedule_single_event( $this->$time_key, 'wpua_change_role_event', array( $job->id ) );

		$activity = new Activity();
		$activity->set_action( 'scheduling' );
		$activity->set_object_id( $job->id );
		$activity->save();

		return $job;
	}

	/**
	 * Verify that scheduled time of first role change is before time of second.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If time of second role change is before first.
	 */
	protected function verify_time_order() {
		if ( ! empty( $this->start_time ) && ! empty( $this->end_time ) ) {
			if ( $this->start_time > $this->end_time ) {
				throw new Exception( __( 'Scheduled time of second role change is before scheduled time of first role change.', 'wp-user-admin' ) );
			}
		}
	}
}
