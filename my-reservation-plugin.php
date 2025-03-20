<?php
/**
 * Plugin Name: My Reservation Plugin
 * Description: A custom reservation plugin integrated with WooCommerce.
 * Version: 1.0.0
 * Author: Olfat Hakeem
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/vendor/autoload.php';

use MyReservationPlugin\Init;
use MyReservationPlugin\Database\Tables;

// Initialize the plugin
function my_reservation_plugin_init() {
    new Init();
}
add_action('plugins_loaded', 'my_reservation_plugin_init');

// Register activation hook properly
function my_reservation_plugin_activate() {
    error_log('Activating plugin...');

    // Ensure database tables are created
    (new Tables())->create_tables();

    // Additional activation logic if needed
}
register_activation_hook( __FILE__, 'my_reservation_plugin_activate' );

// Register deactivation hook
function my_reservation_plugin_deactivate() {
    error_log('Deactivating plugin...');
}
register_deactivation_hook( __FILE__, 'my_reservation_plugin_deactivate' );


add_action( 'woocommerce_order_status_completed', 'my_update_reservation_status', 10, 1 );
add_action( 'woocommerce_order_status_processing', 'my_update_reservation_status', 10, 1 );

function my_update_reservation_status( $order_id ) {
    global $wpdb;

    // Get the reservation ID from order meta
    $reservation_id = get_post_meta( $order_id, 'reservation_id', true );
    if ( !$reservation_id ) {
        return; // No reservation linked to this order
    }

    // Define table names
    $table_reservations = $wpdb->prefix . 'reservation_reservations';
    $table_slots        = $wpdb->prefix . 'reservation_slots';

    // Get the slot ID linked to this reservation
    $reservation = $wpdb->get_row( $wpdb->prepare( "SELECT slot_id FROM $table_reservations WHERE id = %d", $reservation_id ) );

    if ( $reservation ) {
        $slot_id = $reservation->slot_id;

        // Update reservation status and link the order ID
        $wpdb->update(
            $table_reservations,
            array( 'status' => 'reserved', 'order_id' => $order_id ),
            array( 'id'     => $reservation_id )
        );

        // Mark the slot as reserved
        $wpdb->update(
            $table_slots,
            array( 'status' => 'reserved' ),
            array( 'id'     => $slot_id )
        );

    }
}
