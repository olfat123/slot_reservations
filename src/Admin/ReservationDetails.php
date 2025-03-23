<?php
namespace MyReservationPlugin\Admin;

use MyReservationPlugin\Helpers\Helper;

class ReservationDetails {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
    }

    public function add_menu_page() {
        add_submenu_page(
            null,
            'Reservation Details',
            'Reservation Details',
            'manage_options',
            'reservation-details',
            array( $this, 'render_details_page' )
        );
    }

    public function render_details_page() {
        if ( ! isset( $_GET[ 'id' ] ) || ! is_numeric( $_GET[ 'id' ] ) ) {
            echo '<div class="error"><p>Invalid Reservation ID</p></div>';
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'reservation_reservations';
        $reservation_id = intval( $_GET['id'] );

        $reservation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $reservation_id ) );

        if ( ! $reservation ) {
            echo '<div class="error"><p>Reservation not found.</p></div>';
            return;
        }

        $slot_time = Helper::get_slot_time( $reservation->slot_id );
        
        echo '<div class="wrap">';
        echo '<h1>Reservation Details</h1>';
        echo '<table class="form-table">
                <tr><th>Name:</th><td>' . esc_html( $reservation->name ) . '</td></tr>
                <tr><th>Email:</th><td>' . esc_html( $reservation->email ) . '</td></tr>
                <tr><th>Country:</th><td>' . esc_html( $reservation->country ) . '</td></tr>
                <tr><th>Region:</th><td>' . esc_html( $reservation->region ) . '</td></tr>
                <tr><th>Whatsapp:</th><td>' . esc_html( $reservation->whatsapp ) . '</td></tr>
                <tr><th>Slot Time:</th><td>' . esc_html( $slot_time ) . '</td></tr>
                <tr><th>Status:</th><td>' . esc_html( ucfirst( $reservation->status ) ) . '</td></tr>
                <tr><th>Order ID:</th><td>' . ( $reservation->order_id ? esc_html( $reservation->order_id ) : 'N/A' ) . '</td></tr>';
                if ( ! empty( $reservation->file_path ) ) {
                    $file_url = esc_url( $reservation->file_path );
                    $file_name = basename( $file_url );
                    echo '<tr><th>Uploaded File:</th>
                            <td><a href="' . $file_url . '" target="_blank">' . esc_html($file_name) . '</a></td>
                          </tr>';
                } else {
                    echo '<tr><th>Uploaded File:</th><td>No file uploaded</td></tr>';
                }
        echo '</table>';
        echo '</div>';
    }
}
