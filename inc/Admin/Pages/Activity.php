<?php
/**
 * \BitofWP\WPUserAdmin\Admin\Pages\Activity class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin\Pages;

use BitofWP\WPUserAdmin\Singleton;
use BitofWP\WPUserAdmin\Admin\Table\ActivityList;

/**
 * Class to display page with activities table.
 *
 * @since 1.0.0
 */
class Activity {
	use Singleton;

	/**
	 * Display page content.
	 *
	 * @since 1.0.0
	 */
	public function display() {
		if ( ! current_user_can( 'list_users' ) ) {
			wp_die( __( 'Sorry, you are not allowed to view activities on this site.', 'wp-user-admin' ) );
		}

		// "Borrow" xfn.js for now so we don't have to create new files.
		wp_enqueue_script( 'xfn' );
		wp_enqueue_script( 'time-formatter' );

		$activities_table = new ActivityList();

		$activities_table->process_bulk_action();
		$activities_table->prepare_items();

		?>
		<?php settings_errors(); ?>

		<?php $activities_table->views(); ?>

		<form method="post">
			<?php $activities_table->display(); ?>
		</form>
		<?php
	}
}
