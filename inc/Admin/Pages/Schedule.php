<?php
/**
 * \BitofWP\WPUserAdmin\Admin\Pages\Schedule class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin\Pages;

use BitofWP\WPUserAdmin\Admin\Table\JobList;
use BitofWP\WPUserAdmin\Singleton;
use BitofWP\WPUserAdmin\Admin\Form\Schedule as ScheduleForm;
use Exception;

/**
 * Class to display page with jobs table and schedule form.
 *
 * @since 1.0.0
 */
class Schedule {
	use Singleton;

	/**
	 * Display page content.
	 *
	 * @since 1.0.0
	 */
	public function display() {
		if ( ! current_user_can( 'promote_users' ) || ! current_user_can( 'list_users' ) ) {
			wp_die( __( 'Sorry, you are not allowed to view scheduled jobs on this site.', 'wp-user-admin' ) );
		}

		try {
			$handled = ScheduleForm::get_instance()->handle_submission();

			if ( true === $handled ) {
				add_settings_error(
					'privacy_action_email_retry',
					'privacy_action_email_retry',
					__( 'Role change scheduled successfully.', 'wp-user-admin' ),
					'updated'
				);
			}
		} catch ( Exception $e ) {
			add_settings_error(
				'username_or_email_for_privacy_request',
				'username_or_email_for_privacy_request',
				$e->getMessage(),
				'error'
			);
		}

		// "Borrow" xfn.js for now so we don't have to create new files.
		wp_enqueue_script( 'xfn' );
		wp_enqueue_script( 'time-formatter' );

		$jobs_table = new JobList();

		$jobs_table->process_bulk_action();
		$jobs_table->prepare_items();

		?>
		<?php settings_errors(); ?>

		<?php ScheduleForm::get_instance()->display_on_page(); ?>

		<?php $jobs_table->views(); ?>

		<form method="post">
			<?php $jobs_table->display(); ?>
		</form>
		<?php
	}
}
