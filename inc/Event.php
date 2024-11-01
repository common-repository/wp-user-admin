<?php
/**
 * \BitofWP\WPUserAdmin\Event class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin;

use BitofWP\WPUserAdmin\ObjectType\Job;
use Exception;
use BitofWP\WPUserAdmin\ObjectType\Activity;

/**
 * Class to execute cron event.
 *
 * @since 1.0.0
 */
class Event {
	public static function change_role( $job_id ) {
		$job = new Job();
		try {
			$job->load_from_post( $job_id );
			$job->change_role();
		} catch ( Exception $e ) {
			try {
				$activity = new Activity();
				$activity->set_action( 'missed_schedule' );
				$activity->set_object_id( $job_id );
				$activity->save();
			} catch ( Exception $e ) {
				// Do nothing this time.
			}
		}
	}
}
