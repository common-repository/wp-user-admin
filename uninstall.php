<?php
/**
 * Uninstall procedure for WP User Admin.
 *
 * @package WP_User_Admin
 * @subpackage Uninstall
 * @since 1.0.0
 */

// Exit if accessed directly or not on uninstall.
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Only remove plugin data if requested.
if ( 'on' !== get_option( 'wpua_remove_on_uninstall' ) ) {
	return;
}

@set_time_limit( 0 );

$wpua_get_posts = function() {
	return get_posts( array(
		'post_type'      => array( 'wpua_job', 'wpua_activity' ),
		'post_status'    => get_post_stati(),
		'posts_per_page' => 20,
	) );
};

$wpua_posts = $wpua_get_posts();

// Get posts chunk by chunk.
while ( ! empty( $wpua_posts ) ) {
	// Loop through all posts and delete them.
	foreach ( $wpua_posts as $wpua_post ) {
		wp_delete_post( $wpua_post->ID );
	}

	$wpua_posts = $wpua_get_posts();
}

// Delete settings.
$wpua_settings = array(
	'wpua_remove_on_uninstall',
	'wpua_enable_notification',
	'wpua_role_change_notification_text',
	'wpua_role_restore_notification_text',
);

foreach ( $wpua_settings as $wpua_setting ) {
	delete_option( $wpua_setting );
}
