<?php
/**
 * \BitofWP\WPUserAdmin\Utils class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin;

use BitofWP\WPUserAdmin\ObjectType\Job;

/**
 * Class to schedule new role change.
 *
 * @since 1.0.0
 */
class Utils {
	/**
	 * Get full date format string accepted by PHP.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_full_date_format() {
		/* translators: full date and time format, see https://secure.php.net/date */
		return _x( 'F j, Y @ H:i:s', 'full date and time format', 'wp-user-admin' );
	}

	/**
	 * Get full date format string accepted by Moment.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_moment_full_date_format() {
		/* translators: full date and time format, see http://momentjs.com/docs/#/displaying/format/ */
		return _x( 'MMMM DD, YYYY @ HH:mm:ss', 'full date and time format', 'wp-user-admin' );
	}

	public static function disallowed_roles() {
		return apply_filters( 'wpua_disallowed_roles', array( 'administrator' ) );
	}

	/**
	 * Check if user has scheduled role changes.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id ID of user to check.
	 * @return bool
	 */
	public static function has_user_scheduled_jobs( $user_id ) {
		$args = array(
			'post_type'      => Job::POST_TYPE,
			'meta_query'     => array(
				array(
					'key'   => '_user_id',
					'value' => (int) $user_id,
				),
				array(
					'key'   => '_status',
					'value' => 'scheduled',
				),
			),
		);

		$jobs = get_posts( $args );

		if ( $jobs ) {
			return true;
		} else {
			return false;
		}
	}
}
