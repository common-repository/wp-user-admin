<?php
/**
 * \BitofWP\WPUserAdmin\Singleton trait.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin;

/**
 * Singleton pattern.
 *
 * @since 1.0.0
 *
 * @link http://www.sitepoint.com/using-traits-in-php-5-4/
 */
trait Singleton {
	/**
	 * Instantiate called class.
	 *
	 * @since 1.0.0
	 *
	 * @staticvar bool|object $instance
	 *
	 * @return object $instance Instance of called class.
	 */
	public static function get_instance() {
		static $instance = false;

		if ( false === $instance ) {
			$instance = new static();
		}

		return $instance;
	}
}
