<?php
/**
 * \BitofWP\WPUserAdmin\Admin\Pages\Settings class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin\Pages;

use BitofWP\WPUserAdmin\Singleton;
use BitofWP\WPUserAdmin\Admin\Table\ActivityList;

/**
 * Class to display page with "Settings" tab.
 *
 * @since 1.0.0
 */
class Settings {
	use Singleton;

	/**
	 * Display page content.
	 *
	 * @since 1.0.0
	 */
	public function display() {
		if ( ! current_user_can( 'list_users' ) ) {
			wp_die( __( 'Sorry, you are not allowed to view settings on this site.', 'wp-user-admin' ) );
		}

		?>
		<form method="post" action="options.php">
			<?php
			do_settings_sections( 'wp-user-admin' );
			settings_fields( 'wp-user-admin' );

			submit_button( __( 'Save', 'wp-user-admin' ), 'secondary', 'submit', false );
			?>
		</form>
		<?php
	}
}
