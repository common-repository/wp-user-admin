<?php
/**
 * \BitofWP\WPUserAdmin\Admin\Table\InterfaceList interface.
 *
 * @package WP_User_Admin
 * @since 1.0.0
 */

namespace BitofWP\WPUserAdmin\Admin\Table;

/**
 * Object table interface.
 *
 * @since 1.0.0
 */
interface InterfaceList {
	/**
	 * Get a list of columns.
	 *
	 * The format is:
	 * 'internal-name' => 'Title'
	 *
	 * This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text.
	 *
	 * The 'cb' column is treated differently than the rest.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 */
	public function get_columns();

	/**
	 * Process bulk actions.
	 *
	 * @since 1.0.0
	 */
	public function process_bulk_action();

	/**
	 * Text displayed when no customer data is available.
	 *
	 * @since 1.0.0
	 */
	public function no_items();

	/**
	 * Prepare items to output.
	 *
	 * @since 1.0.0
	 */
	public function prepare_items();
}
