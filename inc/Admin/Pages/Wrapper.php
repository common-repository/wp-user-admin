<?php
/**
 * \BitofWP\WPUserAdmin\Admin\Pages\Wrapper class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin\Pages;

use BitofWP\WPUserAdmin\Singleton;
use BitofWP\WPUserAdmin\Admin\Pages\Activity;
use BitofWP\WPUserAdmin\Admin\Pages\Schedule;
use BitofWP\WPUserAdmin\Admin\Pages\Settings;

/**
 * Class to display WP User Admin page.
 *
 * @since 1.0.0
 */
class Wrapper {
	use Singleton;

	public function __construct() {
		$this->register();
	}

	public function register() {
		add_menu_page( __( 'WP User Admin', 'wp-user-admin' ), __( 'WP User Admin', 'wp-user-admin' ), 'list_users', 'wp-user-admin', array( $this, 'display' ) );
	}

	/**
	 * Display page content.
	 *
	 * @since 1.0.0
	 */
	public function display() {
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'schedule';

		?>
		<div class="wrap nosubsub">
			<h1><?php esc_html_e( 'WP User Admin', 'wp-user-admin' ); ?></h1>
			<hr class="wp-header-end" />

			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'wp-user-admin', 'tab' => 'settings' ) ), admin_url( 'admin.php' ) ); ?>" class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Settings', 'wp-user-admin' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'wp-user-admin', 'tab' => 'schedule' ) ), admin_url( 'admin.php' ) ); ?>" class="nav-tab <?php echo $tab === 'schedule' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Jobs Schedule', 'wp-user-admin' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'wp-user-admin', 'tab' => 'activity' ) ), admin_url( 'admin.php' ) ); ?>" class="nav-tab <?php echo $tab === 'activity' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Jobs Log', 'wp-user-admin' ); ?></a>
			</h2>

			<?php
			switch ( $tab ) {
				case 'activity':
					Activity::get_instance()->display();
					break;
				case 'settings':
					Settings::get_instance()->display();
					break;
				case 'schedule':
				default:
					Schedule::get_instance()->display();
					break;
			}
			?>
		</div>
		<?php
	}
}
