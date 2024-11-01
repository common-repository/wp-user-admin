<?php
/**
 * \BitofWP\WPUserAdmin\Admin\Table\AbstractList class.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin\Table;

use WP_List_Table;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class to create abstract object table.
 *
 * @since 1.0.0
 */
abstract class AbstractList extends WP_List_Table implements InterfaceList {
	/**
	 * Get a list of sortable columns.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default sortable columns.
	 */
	protected function get_sortable_columns() {
		return array();
	}

	/**
	 * Get a list of bulk actions,
	 *
	 * Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * @return array An associative array containing all the bulk actions.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'delete' => _x( 'Delete', 'List table bulk action', 'wp-user-admin' ),
		);

		return $actions;
	}

	/**
	 * Default column handler.
	 *
	 * @since 1.0.0
	 *
	 * @param AbstractObject $item        Item being shown.
	 * @param string         $column_name Name of column being shown.
	 * @return string Default column output.
	 */
	public function column_default( $item, $column_name ) {
		$cell_value = $item->$column_name;

		return $cell_value;
	}

	/**
	 * Checkbox column.
	 *
	 * @since 1.0.0
	 *
	 * @param AbstractObject $item Item being shown.
	 * @return string Checkbox column markup.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="request_id[]" value="%1$s" /><span class="spinner"></span>', esc_attr( $item->id ) );
	}
}
