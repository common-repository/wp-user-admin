<?php
/**
 * \BitofWP\WPUserAdmin\Admin\Form\Schedule class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin\Form;

use BitofWP\WPUserAdmin\Scheduler;
use Exception;
use BitofWP\WPUserAdmin\Singleton;
use BitofWP\WPUserAdmin\Utils;

/**
 * Class to display and handle form for scheduling jobs.
 *
 * @since 1.0.0
 */
class Schedule {
	use Singleton;

	/**
	 * \WP_User object of user that receives role change.
	 *
	 * @since 1.0.0
	 * @var \WP_User
	 */
	public $user;

	/**
	 * Whether current display is edit profile screen.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $is_profile_page;

	public static function display_on_page() {
		$instance = static::get_instance();

		$instance->is_profile_page = false;

		?>
		<form method="post">
			<?php $instance->display(); ?>
			<?php submit_button( __( 'Schedule', 'wp-user-admin' ), 'secondary', 'submit', false ); ?>
		</form>
		<?php
	}

	public static function display_on_profile( $user ) {
		$instance = static::get_instance();

		$instance->is_profile_page = true;
		$instance->user            = $user;

		$instance->display();
	}

	public function display() {
		wp_enqueue_style( 'tail.datetime-default' );
		wp_enqueue_style( 'schedule-role-change-form' );
		wp_enqueue_script( 'schedule-role-change-form' );
		?>
		<h3><?php esc_html_e( 'Schedule New Role Change', 'wp-user-admin' ); ?></h3>

		<table class="form-table">
			<?php if ( ! $this->is_profile_page ) : ?>
				<?php wp_enqueue_script( 'users-autocomplete' ); ?>
				<tr>
					<th><label for="wpua_new_role_user"><?php esc_html_e( 'Username or email address', 'wp-user-admin' ); ?></label></th>
					<td>
						<input type="text" class="regular-text" id="wpua_new_role_user" name="wpua_new_role_user" />
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<th><label for="wpua_new_role"><?php esc_html_e( 'New role', 'wp-user-admin' ); ?></label></th>
				<td>
					<select name="wpua_new_role" id="wpua_new_role">
						<option value=""></option>
						<?php
						add_filter( 'editable_roles', array( $this, 'remove_disallowed_roles' ) );
						wp_dropdown_roles();
						remove_filter( 'editable_roles', array( $this, 'remove_disallowed_roles' ) );
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="wpua_tail_start_datetime"><?php esc_html_e( 'Change role at', 'wp-user-admin' ); ?></label></th>
				<td>
					<span class="dashicons dashicons-calendar-alt wpua-calendar-icon"></span>
					<input type="text" placeholder="<?php esc_attr_e( 'Select time', 'wp-user-admin' ); ?>" id="wpua_tail_start_datetime" name="wpua_tail_start_datetime" />
					<span class="dashicons dashicons-dismiss wpua-dismiss-icon" id="wpua_tail_start_datetime_dismiss" title="<?php esc_attr_e( 'Clear time', 'wp-user-admin' ); ?>"></span>
				</td>
			</tr>
			<tr>
				<th><label for="wpua_tail_start_datetime"><?php _e( 'Revert to old role at <span class="description">(optional)</span>', 'wp-user-admin' ); ?></label></th>
				<td>
					<span class="dashicons dashicons-calendar-alt wpua-calendar-icon"></span>
					<input type="text" placeholder="<?php esc_attr_e( 'Select time', 'wp-user-admin' ); ?>" id="wpua_tail_end_datetime" name="wpua_tail_end_datetime" />
					<span class="dashicons dashicons-dismiss wpua-dismiss-icon" id="wpua_tail_end_datetime_dismiss" title="<?php esc_attr_e( 'Clear time', 'wp-user-admin' ); ?>"></span>
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'wpua_schedule_role_change', 'wpua_schedule_role_change' ); ?>
		<?php $user_id = $this->is_profile_page ? $this->user->ID : ''; ?>
		<input type="hidden" id="wpua_action" name="wpua_action" value="wpua_schedule_role_change" />
		<input type="hidden" id="wpua_new_role_user_id" name="wpua_new_role_user_id" value="<?php echo esc_attr( $user_id ); ?>" />
		<input type="hidden" id="wpua_start_datetime" name="wpua_start_datetime" value="" />
		<input type="hidden" id="wpua_end_datetime" name="wpua_end_datetime" value="" />
		<?php
	}

	public static function handle_profile_submission( $user_id ) {
		$instance = static::get_instance();

		$instance->is_profile_page = true;
		$instance->user            = get_user_by( 'id', $user_id );

		try {
			$instance->handle_submission();
		} catch ( Exception $e ) {
			// Silently continue, for now.
			return;
		}
	}

	/**
	 * Save form submission.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If there is an error during saving.
	 *
	 * @return bool True if form is saved, false if no saving is needed.
	 */
	public function handle_submission() {
		$action = isset( $_POST['wpua_action'] ) ? wp_unslash( $_POST['wpua_action'] ) : '';

		if ( 'wpua_schedule_role_change' !== $action ) {
			return false;
		}

		check_admin_referer( 'wpua_schedule_role_change', 'wpua_schedule_role_change' );

		$fields = array(
			'wpua_new_role_user_id',
			'wpua_new_role',
			'wpua_start_datetime',
			'wpua_revert_role',
			'wpua_end_datetime',
		);

		$submitted = false;

		foreach ( $fields as $field ) {
			$$field = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
		}

		$user = get_user_by( 'id', $wpua_new_role_user_id );

		if ( false === $user ) {
			throw new Exception( __( 'User has not been set.', 'wp-user-admin' ) );
		}

		$scheduler = new Scheduler();
		$scheduler->set_user_id( $user->ID );
		$scheduler->set_start_role( $wpua_new_role );
		$scheduler->set_start_time( $wpua_start_datetime );

		if ( $wpua_end_datetime ) {
			$scheduler->set_end_role( current( $user->roles ) );
			$scheduler->set_end_time( $wpua_end_datetime );
		}
		$scheduler->schedule();

		return true;
	}

	public function remove_disallowed_roles( $roles ) {
		$dissalowed_roles = Utils::disallowed_roles();

		foreach ( $roles as $role => $details ) {
			if ( in_array( $role, $dissalowed_roles, true ) ) {
				unset( $roles[ $role ] );
			}
		}

		return $roles;
	}
}
