<?php
/**
 * \namespace BitofWP\WPUserAdmin\Main class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin;

/**
 * Class with methods that initialize WP User Admin.
 *
 * This class hooks other parts of WP User Admin, and
 * other methods that are important for functioning
 * of WP User Admin.
 *
 * @since 1.0.0
 */
class Main {
	use Singleton;

	/**
	 * Constructor.
	 *
	 * This method is used to hook everything.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		static::hook();
	}

	/**
	 * Hook everything.
	 *
	 * @since 1.0.0
	 */
	public static function hook() {
		// phpcs:disable PEAR.Functions.FunctionCallSignature.SpaceBeforeCloseBracket, Generic.Functions.FunctionCallArgumentSpacing.TooMuchSpaceAfterComma, WordPress.Arrays.CommaAfterArrayItem.SpaceAfterComma, WordPress.Arrays.ArrayDeclarationSpacing.SpaceBeforeArrayCloser, Generic.Functions.FunctionCallArgumentSpacing.SpaceBeforeComma

		// Register custom post types.
		add_action( 'init',                       array( __NAMESPACE__ . '\ObjectType\Job',                        'register'                     ), 2        );
		add_action( 'init',                       array( __NAMESPACE__ . '\ObjectType\Activity',                   'register'                     ), 2        );

		add_action( 'init',                       array( __NAMESPACE__ . '\Main',                                  'register_settings'            ), 2        );

		add_action( 'wpua_change_role_event',     array( __NAMESPACE__ . '\Event',                                 'change_role'                  ), 2        );

		add_action( 'admin_menu',                 array( __NAMESPACE__ . '\Admin\Settings',                        'get_instance'                 ), 2        );

		add_action( 'admin_menu',                 array( __NAMESPACE__ . '\Admin\Pages\Wrapper',                   'get_instance'                 ), 2        );

		add_action( 'edit_user_profile',          array( __NAMESPACE__ . '\Admin\Form\Schedule',                   'display_on_profile'           ), 2        );
		add_action( 'edit_user_profile_update',   array( __NAMESPACE__ . '\Admin\Form\Schedule',                   'handle_profile_submission'    ), 2        );

		add_action( 'admin_enqueue_scripts',      array( __NAMESPACE__ . '\Main',                                  'register_admin_assets'        ), 2        );

		add_action( 'npm_packages_styles',        array( __NAMESPACE__ . '\commonWP',                              'styles'                       ), 2        );
		add_action( 'npm_packages_scripts',       array( __NAMESPACE__ . '\commonWP',                              'scripts'                      ), 2        );
		// phpcs:enable
	}

	public static function register_admin_assets() {
		// Use the .min version if SCRIPT_DEBUG is turned off.
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_style( 'tail.datetime-default', plugins_url( '/assets/css/tail.datetime-default.css', dirname( __FILE__ ) ), array(), '0.4.2' );
		wp_register_script( 'tail.datetime', plugins_url( "/assets/js/tail.datetime{$min}.js", dirname( __FILE__ ) ), array(), '0.4.2', true );

		wp_register_script( 'users-autocomplete', plugins_url( '/assets/js/users-autocomplete.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-autocomplete' ), '1', true );
		wp_localize_script( 'users-autocomplete', 'wpApiSettings', array(
			'endpoint' => esc_url_raw( rest_url() ),
			'nonce'    => wp_create_nonce( 'wp_rest' ),
		) );

		wp_register_style( 'schedule-role-change-form', plugins_url( '/assets/css/schedule-role-change-form.css', dirname( __FILE__ ) ), array(), '2' );
		wp_register_script( 'schedule-role-change-form', plugins_url( '/assets/js/schedule-role-change-form.js', dirname( __FILE__ ) ), array( 'jquery', 'tail.datetime' ), '3', true );
		wp_localize_script( 'schedule-role-change-form', 'wpuaSettings', array(
			'applyLabel'  => _x( 'Apply', 'button', 'wp-user-admin' ),
			'currentTime' => time(),
		) );

		wp_register_script( 'activities-filter-form', plugins_url( '/assets/js/activities-filter-form.js', dirname( __FILE__ ) ), array( 'jquery', 'tail.datetime' ), '1', true );
		wp_localize_script( 'activities-filter-form', 'wpuaFormFilterSettings', array(
			'applyLabel' => _x( 'Apply', 'button', 'wp-user-admin' ),
		) );

		if ( ! wp_script_is( 'moment', 'registered' ) ) {
			wp_register_script( 'moment', plugins_url( "/assets/js/moment{$min}.js", dirname( __FILE__ ) ), array(), '2.22.2', true );
		}

		wp_register_script( 'time-formatter', plugins_url( '/assets/js/time-formatter.js', dirname( __FILE__ ) ), array( 'jquery', 'moment' ), '1', true );
		wp_localize_script( 'time-formatter', 'timeFormatterSettings', array(
			'timeFormat' => Utils::get_moment_full_date_format(),
		) );
	}

	/**
	 * Register settings field.
	 *
	 * @since 1.0.0
	 */
	public static function register_settings() {
		register_setting( 'wp-user-admin', 'wpua_remove_on_uninstall', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		register_setting( 'wp-user-admin', 'wpua_enable_notification', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		register_setting( 'wp-user-admin', 'wpua_role_change_notification_text', array(
			'sanitize_callback' => 'sanitize_textarea_field',
		) );
		register_setting( 'wp-user-admin', 'wpua_role_restore_notification_text', array(
			'sanitize_callback' => 'sanitize_textarea_field',
		) );
	}
}
