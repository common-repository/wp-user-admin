<?php
/**
 * \BitofWP\WPUserAdmin\commonWP class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin;

/**
 * Class to mark files that exist in npm for use in commonWP plugin.
 *
 * @since 1.0.0
 */
class commonWP {
	public static function styles( $styles ) {
		$styles['tail.datetime-default'] = array(
			'package'  => 'tail.datetime',
			'file'     => 'css/tail.datetime-default',
			'minified' => '',
		);

		return $styles;
	}

	public static function scripts( $scripts ) {
		$scripts['tail.datetime'] = array(
			'package'  => 'tail.datetime',
			'file'     => 'js/tail.datetime',
			'minified' => '.min',
		);

		return $scripts;
	}
}
