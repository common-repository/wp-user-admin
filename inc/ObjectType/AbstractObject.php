<?php
/**
 * \BitofWP\WPUserAdmin\Object\AbstractObject class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\ObjectType;

use Exception;

/**
 * Class to create abstract object.
 *
 * @since 1.0.0
 */
abstract class AbstractObject {
	/**
	 * ID of object’s post.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $id;

	/**
	 * \WP_Post object of this object’s post.
	 *
	 * @since 1.0.0
	 * @var \WP_Post
	 */
	public $post;

	public static function register() {
		$args = array(
			'supports'              => false,
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => false,
			'show_in_menu'          => false,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => false,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'rewrite'               => false,
			'capability_type'       => 'post',
		);

		register_post_type( static::POST_TYPE, $args );
	}

	/**
	 * Save new object as a post.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If there is an error during saving.
	 */
	public function insert() {
		$post_id = wp_insert_post( array(
			'post_type'   => static::POST_TYPE,
			'post_status' => 'publish',
		) );

		if ( ! is_int( $post_id ) || 0 === $post_id ) {
			throw new Exception( __( 'Object could not be saved.', 'wp-user-admin' ) );
		}

		$this->set_post_id( $post_id );
	}

	public function set_meta( $name, $value ) {
		update_post_meta( $this->post->ID, "_{$name}", $value );
	}

	public function get_meta( $name ) {
		return get_post_meta( $this->post->ID, "_{$name}", true );
	}

	/**
	 * Set $id and $post properties.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If Unix Epoch timestamp was not passed.
	 *
	 * @param int $post_id ID of object’s post.
	 */
	public function set_post_id( $post_id ) {
		$this->id = $post_id;

		$post = get_post( $this->id );

		if ( is_null( $post ) ) {
			throw new Exception( __( 'There was a problem retrieving post.', 'wp-user-admin' ) );
		}

		$this->post = $post;
	}
}
