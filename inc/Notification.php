<?php
/**
 * \BitofWP\WPUserAdmin\Notification class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin;

use BitofWP\WPUserAdmin\Settings;

/**
 * Class to notify users about role change.
 *
 * @since 1.0.0
 */
class Notification {
	/**
	 * Send email notification about scheduled role change.
	 *
	 * @since 1.0.0
	 *
	 * @param BitofWP\WPUserAdmin\ObjectType\Job $job Job object.
	 * @return bool
	 */
	public static function send_role_change( $job ) {
		$subject = __( 'Scheduled Role Change', 'wp-user-admin' );
		$message = Settings::get_formatted_role_change_notification_text( $job );

		return wp_mail( $job->user->user_email, $subject, $message );
	}

	/**
	 * Send email notification about restored role.
	 *
	 * @since 1.0.0
	 *
	 * @param BitofWP\WPUserAdmin\ObjectType\Job $job Job object.
	 * @return bool
	 */
	public static function send_role_restore( $job ) {
		$subject = __( 'Restored Role', 'wp-user-admin' );
		$message = Settings::get_formatted_role_restore_notification_text( $job );

		return wp_mail( $job->user->user_email, $subject, $message );
	}
}
