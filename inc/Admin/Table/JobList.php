<?php
/**
 * \BitofWP\WPUserAdmin\Admin\JobList class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin\Table;

use WP_Query;
use BitofWP\WPUserAdmin\ObjectType\Job;
use BitofWP\WPUserAdmin\Utils;
use BitofWP\WPUserAdmin\Admin\Table\AbstractList;
use Exception;

/**
 * Class to create jobs table.
 *
 * @since 1.0.0
 */
class JobList extends AbstractList {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct( array(
			'singular' => 'job',
			'plural'   => 'jobs',
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
			'cb'       => '<input type="checkbox" />',
			'user'     => _x( 'User', 'Column label', 'wp-user-admin' ),
			'new_role' => _x( 'New Role', 'Column label', 'wp-user-admin' ),
			'time'     => _x( 'Time', 'Column label', 'wp-user-admin' ),
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
			check_admin_referer( 'bulk-jobs' );
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
					/* translators: %d: number of jobs */
					sprintf( _n( 'Deleted %d job', 'Deleted %d jobs', $count, 'wp-user-admin' ), $count ),
					'updated'
				);
				break;
		}
	}

	/**
	 * Text displayed when no scheduled jobs are available.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		_e( 'No scheduled jobs are avaliable.', 'wp-user-admin' );
	}

	public function column_user( $item ) {
		$cell = '<strong>' . $item->user->display_name . '</strong>';

		$child_job_id = $item->get_meta( 'child_job_id' );
		if ( $child_job_id ) {
			$child_job = new Job();
			try {
				$child_job->load_from_post( $child_job_id );

				$text = sprintf(
					/* translators: 1: new role name 3: date and time */
					__( 'Returns to %1$s on %2$s.', 'wp-user-admin' ),
					$child_job->role,
					sprintf(
						'<span class="wpua-time-holder" data-timestamp="%1$s">%2$s</span>',
						$child_job->time,
						date_i18n( Utils::get_full_date_format(), $child_job->time )
					)
				);

				$cell .= '<div>' . $text . '</div>';
			} catch ( Exception $e ) {
				// Silently continue.
			}
		}

		return $cell;
	}

	public function column_new_role( $item ) {
		return $item->role;
	}

	public function column_time( $item ) {
		return sprintf(
			'<span class="wpua-time-holder" data-timestamp="%1$s">%2$s</span>',
			$item->time,
			date_i18n( Utils::get_full_date_format(), $item->time )
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
			'post_type'      => Job::POST_TYPE,
			'posts_per_page' => $posts_per_page,
			'offset'         => isset( $_REQUEST['paged'] ) ? max( 0, absint( $_REQUEST['paged'] ) - 1 ) * $posts_per_page : 0,
			'post_status'    => 'any',
			'meta_query'     => array(
				array(
					'key'   => '_status',
					'value' => 'scheduled',
				),
			),
		);

		$requests_query = new WP_Query( $args );
		$requests       = $requests_query->posts;
		$skip_item_ids  = array();

		foreach ( $requests as $request ) {
			if ( in_array( $request->ID, $skip_item_ids, false ) ) {
				continue;
			}

			$job = new Job();
			try {
				$job->load_from_post( $request->ID );
			} catch ( Exception $e ) {
				// Silently continue.
				continue;
			}

			$child_job_id = $job->get_meta( 'child_job_id' );
			if ( $child_job_id ) {
				$skip_item_ids[] = $child_job_id;
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
}
