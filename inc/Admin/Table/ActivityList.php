<?php
/**
 * \BitofWP\WPUserAdmin\Admin\Table\ActivityList class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin\Table;

use WP_Query;
use BitofWP\WPUserAdmin\ObjectType\Activity;
use BitofWP\WPUserAdmin\Utils;
use BitofWP\WPUserAdmin\Admin\Table\AbstractList;
use Exception;

/**
 * Class to create activities table.
 *
 * @since 1.0.0
 */
class ActivityList extends AbstractList {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct( array(
			'singular' => 'activity',
			'plural'   => 'activities',
			'ajax'     => false,
		) );
	}

	/**
	 * Get a list of columns.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @return array An associative array containing column information.
	 */
	public function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'description' => _x( 'Description', 'Column label', 'wp-user-admin' ),
			'time'        => _x( 'Time', 'Column label', 'wp-user-admin' ),
		);

		return $columns;
	}

	/**
	 * Process bulk actions.
	 *
	 * @since 1.0.0
	 */
	public function process_bulk_action() {
		$action      = $this->current_action();
		$request_ids = isset( $_REQUEST['request_id'] ) ? wp_parse_id_list( wp_unslash( $_REQUEST['request_id'] ) ) : array();
		$count       = 0;

		if ( $request_ids ) {
			check_admin_referer( 'bulk-activities' );
		}

		switch ( $action ) {
			case 'delete':
				foreach ( $request_ids as $request_id ) {
					if ( wp_delete_post( $request_id, true ) ) {
						$count ++;
					}
				}

				add_settings_error(
					'bulk_action',
					'bulk_action',
					/* translators: %d: number of activities */
					sprintf( _n( 'Deleted %d activity', 'Deleted %d activities', $count, 'wp-user-admin' ), $count ),
					'updated'
				);
				break;
		}
	}

	/**
	 * Text displayed when no activities are available.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		_e( 'No acivitites are avaliable.', 'wp-user-admin' );
	}

	public function column_desctiption( $item ) {
		return $item->description;
	}

	public function column_time( $item ) {
		return sprintf(
			'<span class="wpua-time-holder" data-timestamp="%1$s">%2$s</span>',
			get_the_date( 'U', $item->post ),
			get_the_date( Utils::get_full_date_format(), $item->post )
		);
	}

	/**
	 * Prepare items to output.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		);

		$this->items    = array();
		$posts_per_page = $this->get_items_per_page( 'wpua_job_requests_per_page' );
		$args           = array(
			'post_type'      => Activity::POST_TYPE,
			'posts_per_page' => $posts_per_page,
			'offset'         => isset( $_REQUEST['paged'] ) ? max( 0, absint( $_REQUEST['paged'] ) - 1 ) * $posts_per_page : 0,
			'post_status'    => 'any',
		);

		$filter_by_user_id = '';
		$date_query        = array();

		if ( isset( $_REQUEST['wpua_activity_later_than_timestamp'] ) && ! empty( $_REQUEST['wpua_activity_later_than_timestamp'] ) ) {
			$date_query[] = array(
				'after' => date_i18n( 'c', absint( $_REQUEST['wpua_activity_later_than_timestamp'] ) ),
			);
		}

		if ( isset( $_REQUEST['wpua_activity_sooner_than_timestamp'] ) && ! empty( $_REQUEST['wpua_activity_sooner_than_timestamp'] ) ) {
			$date_query[] = array(
				'before' => date_i18n( 'c', absint( $_REQUEST['wpua_activity_sooner_than_timestamp'] ) ),
			);
		}

		if ( isset( $_REQUEST['wpua-user-dropdown'] ) && is_numeric( $_REQUEST['wpua-user-dropdown'] ) ) {
			$filter_by_user_id = $_REQUEST['wpua-user-dropdown'];
		}

		if ( $date_query ) {
			$args['date_query'] = $date_query;
		}

		$requests_query = new WP_Query( $args );
		$requests       = $requests_query->posts;

		foreach ( $requests as $request ) {
			$job = new Activity();
			try {
				$job->load_from_post( $request->ID );
			} catch ( Exception $e ) {
				// Silently continue.
				continue;
			}

			if ( $filter_by_user_id && $filter_by_user_id != $job->object->user->ID ) {
				continue;
			}

			$this->items[] = $job;
		}

		$this->items = array_filter( $this->items );

		$this->set_pagination_args(
			array(
				'total_items' => $requests_query->found_posts,
				'per_page'    => $posts_per_page,
			)
		);
	}

	/**
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		wp_enqueue_style( 'tail.datetime-default' );
		wp_enqueue_style( 'schedule-role-change-form' );
		wp_enqueue_script( 'activities-filter-form' );
		?>
		<div class="alignleft actions">
			<?php
			$r = array(
				'id'              => 'wpua-user-dropdown',
				'name'            => 'wpua-user-dropdown',
				'multi'           => 1,
				'show_option_all' => __( 'All users', 'wp-user-admin' ),
				'role__not_in'    => Utils::disallowed_roles(),
			);
			wp_dropdown_users( $r );
			?>
			<input type="text" placeholder="<?php esc_attr_e( 'Later than...', 'wp-user-admin' ); ?>" id="wpua_activity_later_than" name="wpua_activity_later_than" />
			<input type="text" placeholder="<?php esc_attr_e( 'Sooner than...', 'wp-user-admin' ); ?>" id="wpua_activity_sooner_than" name="wpua_activity_sooner_than" />
			<input type="hidden" id="wpua_activity_later_than_timestamp" name="wpua_activity_later_than_timestamp" value="" />
			<input type="hidden" id="wpua_activity_sooner_than_timestamp" name="wpua_activity_sooner_than_timestamp" value="" />
			<?php submit_button( __( 'Filter', 'wp-user-admin' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) ); ?>
		</div>
		<?php
	}
}
