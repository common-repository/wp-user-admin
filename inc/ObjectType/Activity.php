<?php
/**
 * \BitofWP\WPUserAdmin\Activity class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\ObjectType;

use Exception;
use WP_User;
use BitofWP\WPUserAdmin\ObjectType\Job;
use BitofWP\WPUserAdmin\ObjectType\AbstractObject;
use BitofWP\WPUserAdmin\Utils;

/**
 * Class to create Activity object.
 *
 * @since 1.0.0
 */
class Activity extends AbstractObject {
	const POST_TYPE = 'wpua_activity';

	/**
	 * Textual representation of this activity.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $description;

	/**
	 * Name of action of this activity.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $action;

	/**
	 * Object that this activity is for.
	 *
	 * @since 1.0.0
	 * @var AbstractObject
	 */
	public $object;

	/**
	 * ID of object that this activity is for.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $object_id;

	/**
	 * Load object from post.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If loading from post was unsuccessful.
	 *
	 * @param int $post_id ID of object’s post.
	 */
	public function load_from_post( $post_id ) {
		$this->set_post_id( $post_id );

		$this->action    = $this->get_meta( 'action' );
		$this->object_id = $this->get_meta( 'object_id' );

		switch ( $this->action ) {
			case 'role_change':
				$this->role_change_action();
				break;
			case 'scheduling':
				$this->scheduling_action();
				break;
			case 'missed_schedule':
				$this->missed_schedule_action();
				break;
		}
	}

	public function set_object_id( $id ) {
		$this->object_id = $id;
	}

	public function set_action( $action ) {
		$this->action = $action;
	}

	/**
	 * Set activities’ object and textual representation for role changing action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If activities’ object loading from post was unsuccessful.
	 */
	protected function role_change_action() {
		$this->object = new Job();
		$this->object->load_from_post( $this->object_id );

		$this->description = sprintf(
			/* translators: 1: user's display name 2: new role name */
			__( 'Role was changed for user %1$s to %2$s.', 'wp-user-admin' ),
			$this->object->user->display_name,
			get_role( $this->object->role )->name
		);
	}

	/**
	 * Set activities’ object and textual representation for scheduling action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If activities’ object loading from post was unsuccessful.
	 */
	protected function scheduling_action() {
		$this->object = new Job();
		$this->object->load_from_post( $this->object_id );

		$this->description = sprintf(
			/* translators: 1: user's display name 2: new role name 3: date and time */
			__( 'Scheduled new role change for user %1$s to %2$s on %3$s.', 'wp-user-admin' ),
			$this->object->user->display_name,
			get_role( $this->object->role )->name,
			sprintf(
				'<span class="wpua-time-holder" data-timestamp="%1$s">%2$s</span>',
				$this->object->time,
				date_i18n( Utils::get_full_date_format(), $this->object->time )
			)
		);
	}

	/**
	 * Set activities’ object and textual representation for missed schedule action.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If activities’ object loading from post was unsuccessful.
	 */
	protected function missed_schedule_action() {
		$this->object = new Job();
		$this->object->load_from_post( $this->object_id );

		$this->description = sprintf(
			/* translators: 1: user's display name 2: new role name */
			__( 'There was a scheduled role change for user %1$s to %2$s but change was unsuccessful.', 'wp-user-admin' ),
			$this->object->user->display_name,
			get_role( $this->object->role )->name
		);
	}

	/**
	 * Save new object and its meta values.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception If there is an error during saving.
	 */
	public function save() {
		try {
			$this->insert();
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}

		$this->set_meta( 'object_id', $this->object_id );
		$this->set_meta( 'action', $this->action );
	}
}
