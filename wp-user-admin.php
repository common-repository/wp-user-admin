<?php
/**
 * Plugin Name: WP User Admin
 * Description: User Role Scheduler
 * Author:      WPRepublic
 * Author URI:  https://wprepublic.com
 * Version:     1.0.1
 * Text Domain: wp-user-admin
 * Domain Path: /languages/
 *
 * @package WP_User_Admin
 */

// Check minimum required PHP version.
if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
	return;
}

/**
 * Autoloader for WP User Admin classes.
 *
 * @param string $class Name of the class.
 */
function wpua_autoloader( $class ) {
	$prefix = 'BitofWP\\WPUserAdmin\\';

	$base_dir = __DIR__ . '/inc/';

	// Does the class use the namespace prefix?
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		// No, move to the next registered autoloader.
		return;
	}

	// Get the relative class name.
	$relative_class = substr( $class, $len );

	/*
	 * Replace the namespace prefix with the base directory, replace namespace
	 * separators with directory separators in the relative class name, append
	 * with .php.
	 */
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	// If the file exists, require it.
	if ( file_exists( $file ) ) {
		require $file;
	}
}
spl_autoload_register( 'wpua_autoloader' );

/**
 * Version of WP User Admin plugin.
 *
 * @since 1.0.0
 * @var string
 */
define( 'WP_USER_ADMIN_VERSION', '1.0.0-alpha-6' );

/*
 * Initialize a plugin.
 *
 * Load class when all plugins are loaded
 * so that other plugins can overwrite it.
 */
add_action( 'plugins_loaded', array( 'BitofWP\WPUserAdmin\Main', 'get_instance' ), 10 );
