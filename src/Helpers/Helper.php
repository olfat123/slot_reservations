<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

namespace MyReservationPlugin\Helpers;

class Helper {
    /**
     * Get the time of a specific slot based on its ID.
     *
     * @param int $slot_id The ID of the slot.
     * @return string|null The slot time in 'Y-m-d H:i:s' format or null if not found.
     */
    public static function get_slot_time( int $slot_id ): ?string {
        global $wpdb;
        $table = $wpdb->prefix . 'reservation_slots';

        return $wpdb->get_var( $wpdb->prepare( "SELECT slot_time FROM $table WHERE id = %d", $slot_id ) );
    }
}
