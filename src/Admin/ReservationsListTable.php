<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

namespace MyReservationPlugin\Admin;

use MyReservationPlugin\Helpers\Helper;
use WP_List_Table;

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class ReservationsListTable
 */
class ReservationsListTable extends WP_List_Table {

    /**
     * ReservationsListTable constructor.
     */
    public function __construct() {
        global $hook_suffix; // Ensures WP admin environment is fully loaded

        parent::__construct(
            array(
                'singular' => 'reservation',
                'plural'   => 'reservations',
                'ajax'     => false,
            )
        );
    }

    /**
     * Set up the columns for the table.
     *
     * @return array
     */
    public function get_columns(): array {
        return array(
            'cb'      => '<input type="checkbox" />',
            'name'    => __( 'Name', 'mighty-kids-refund' ),
            'email'   => __( 'Email', 'mighty-kids-refund' ),
            'slot'    => __( 'Slot Time', 'mighty-kids-refund' ),
            'status'  => __( 'Status', 'mighty-kids-refund' ),
            'actions' => __( 'Actions', 'mighty-kids-refund' ),
        );
    }

    /**
     * Set up the sortable columns.
     *
     * @return array
     */
    public function get_sortable_columns(): array {
        return array(
            'name' => array( 'name', false ),
        );
    }

    /**
     * Set up the hidden columns.
     *
     * @return array
     */
    public function get_hidden_columns(): array {
        return array();
    }

    /**
     * Prepare the data for the table.
     */
    public function prepare_items() {
        global $wpdb;
        $table = $wpdb->prefix . 'reservation_reservations';

        $columns               = $this->get_columns();
		$hidden_columns        = $this->get_hidden_columns();
		$sortable              = $this->get_sortable_columns();
		$primary               = 'title';
		$this->_column_headers = array( $columns, $hidden_columns, $sortable, $primary );

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $offset = ( $current_page - 1 ) * $per_page;

        $data = $wpdb->get_results( "SELECT * FROM $table LIMIT $per_page OFFSET $offset", ARRAY_A );
        $total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table" );

        $this->items = $data;
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page
        ]);
    }

    /**
     * Define how the table rows should be displayed.
     *
     * @param array  $item An array of DB data.
     * @param string $column_name The name of the column.
     *
     * @return string
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'name':
            case 'email':
                return esc_html( $item[ $column_name ] );

            case 'slot':
                return esc_html( Helper::get_slot_time( $item['slot_id'] ) );

            case 'status':
                return esc_html( ucfirst( $item['status'] ) );

            case 'actions':
                $view_url = admin_url( 'admin.php?page=reservation-details&id=' . $item['id'] );
                return "<a href='$view_url' class='button'>View</a>";

            default:
                return '';
        }
    }

    /**
     * Add a checkbox to the first column.
     *
     * @param array $item An array of DB data.
     *
     * @return string
     */
    public function column_cb( $item ): string {
        return sprintf(
            '<input type="checkbox" name="reservation[]" value="%s" />',
            esc_attr( $item['id'] )
        );
    }
}
