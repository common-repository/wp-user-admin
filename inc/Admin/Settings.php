<?php
/**
 * \BitofWP\WPUserAdmin\Admin\Settings class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin;

use BitofWP\WPUserAdmin\Singleton;
use BitofWP\WPUserAdmin\Settings as GlobalSettings;

/**
 * Class to register settings fields.
 *
 * @since 1.0.0
 */
class Settings {
	use Singleton;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->register();
	}

	/**
	 * Register settings section and fields.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		add_settings_field(
			'wpua_remove_on_uninstall',
			__( 'Remove plugin data on uninstall', 'wp-user-admin' ),
			array( $this, 'render_remove_on_uninstall_toggler' ),
			'wp-user-admin',
			'wpua-settings-section'
		);

		add_settings_section(
			'wpua-settings-section',
			__( 'WP User Admin', 'wp-user-admin' ),
			'__return_false',
			'wp-user-admin'
		);

		add_settings_field(
			'wpua_enable_notification',
			__( 'Enable notifications', 'wp-user-admin' ),
			array( $this, 'render_notification_toggler' ),
			'wp-user-admin',
			'wpua-settings-section'
		);

		add_settings_field(
			'wpua_role_change_notification_text',
			__( 'Role change notification text', 'wp-user-admin' ),
			array( $this, 'render_role_change_notification_text' ),
			'wp-user-admin',
			'wpua-settings-section'
		);

		add_settings_field(
			'wpua_role_restore_notification_text',
			__( 'Role restore notification text', 'wp-user-admin' ),
			array( $this, 'render_role_restore_notification_text' ),
			'wp-user-admin',
			'wpua-settings-section'
		);
	}

	/**
	 * Display toggler for remove data on uninstall settings field.
	 *
	 * @since 1.0.0
	 */
	public function render_remove_on_uninstall_toggler() {
		?>
		<label for="wpua_remove_on_uninstall">
			<input type="checkbox" id="wpua_remove_on_uninstall" class="regular-text ltr" name="wpua_remove_on_uninstall" <?php checked( GlobalSettings::is_remove_on_uninstall_enabled(), true ); ?> value="on" />
		</label>
		<?php
	}

	/**
	 * Display notification toggler settings field.
	 *
	 * @since 1.0.0
	 */
	public function render_notification_toggler() {
		?>
		<label for="wpua_enable_notification">
			<input type="checkbox" id="wpua_enable_notification" class="regular-text ltr" name="wpua_enable_notification" <?php checked( GlobalSettings::is_notification_enabled(), true ); ?> value="on" />
		</label>
		<?php
	}

	/**
	 * Display role change notification text settings field.
	 *
	 * @since 1.0.0
	 */
	public function render_role_change_notification_text() {
		?>
		<textarea id="wpua_role_change_notification_text" class="large-text ltr" name="wpua_role_change_notification_text" rows="10">
			<?php echo esc_textarea( GlobalSettings::get_role_change_notification_text(), true ); ?>
		</textarea>
		<?php
	}

	/**
	 * Display role restore notification text settings field.
	 *
	 * @since 1.0.0
	 */
	public function render_role_restore_notification_text() {
		?>
		<textarea id="wpua_role_restore_notification_text" class="large-text ltr" name="wpua_role_restore_notification_text" rows="10">
			<?php echo esc_textarea( GlobalSettings::get_role_restore_notification_text(), true ); ?>
		</textarea>
		<?php
	}
}
