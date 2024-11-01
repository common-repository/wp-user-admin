<?php
/**
 * \BitofWP\WPUserAdmin\Settings class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin;

use BitofWP\WPUserAdmin\Utils;

/**
 * Class to get plugin settings.
 *
 * @since 1.0.0
 */
class Settings {
	/**
	 * Check whether remove on uninstall is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_remove_on_uninstall_enabled() {
		$value = get_option( 'wpua_remove_on_uninstall' );

		if ( 'on' === $value ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check whether notifications are enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_notification_enabled() {
		$value = get_option( 'wpua_enable_notification' );

		if ( 'on' === $value ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get text of notification about scheduled role change.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_role_change_notification_text() {
		$default = __( 'Hello ###USER###,

Your user role will be changed at ###TIME###.

Regards,
###SITENAME###
###SITEURL###', 'wp-user-admin'
		);

		$db_value = get_option( 'wpua_role_change_notification_text' );

		if ( ! $db_value ) {
			return $default;
		}

		return $db_value;
	}

	/**
	 * Get formatted text of notification about scheduled role change.
	 *
	 * @since 1.0.0
	 *
	 * @param BitofWP\WPUserAdmin\ObjectType\Job $job Job object.
	 * @return string $content
	 */
	public static function get_formatted_role_change_notification_text( $job ) {
		$content = static::get_role_change_notification_text();

		$user      = $job->user->display_name;
		$time      = date_i18n( Utils::get_full_date_format(), $job->time );
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$site_url  = home_url();

		$content = str_replace( '###USER###', $user, $content );
		$content = str_replace( '###TIME###', $time, $content );
		$content = str_replace( '###SITENAME###', $site_name, $content );
		$content = str_replace( '###SITEURL###', esc_url_raw( $site_url ), $content );

		return $content;
	}

	/**
	 * Get text of notification about restored role.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_role_restore_notification_text() {
		$default = __( 'Hello ###USER###,

Your user role was just restored to original user role.

Regards,
###SITENAME###
###SITEURL###', 'wp-user-admin'
		);

		$db_value = get_option( 'wpua_role_restore_notification_text' );

		if ( ! $db_value ) {
			return $default;
		}

		return $db_value;
	}

	/**
	 * Get formatted text of notification about restored role.
	 *
	 * @since 1.0.0
	 *
	 * @param BitofWP\WPUserAdmin\ObjectType\Job $job Job object.
	 * @return string $content
	 */
	public static function get_formatted_role_restore_notification_text( $job ) {
		$content = static::get_role_restore_notification_text();

		$user      = $job->user->display_name;
		$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$site_url  = home_url();

		$content = str_replace( '###USER###', $user, $content );
		$content = str_replace( '###SITENAME###', $site_name, $content );
		$content = str_replace( '###SITEURL###', esc_url_raw( $site_url ), $content );

		return $content;
	}
}
