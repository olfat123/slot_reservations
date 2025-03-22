<?php
namespace MyReservationPlugin\Frontend;

class AjaxHandler {
    public function __construct() {
        add_action( 'wp_ajax_fetch_available_slots', array( $this, 'fetch_available_slots' ) );
        add_action( 'wp_ajax_nopriv_fetch_available_slots', array( $this, 'fetch_available_slots' ) );
        add_action( 'wp_ajax_get_slots', array( $this, 'get_slots' ) );
        add_action( 'wp_ajax_add_slot', array( $this, 'add_slot' ) );
        add_action( 'wp_ajax_remove_slot', array( $this, 'remove_slot' ) );
        add_action( 'wp_ajax_update_slot_status', array( $this, 'update_slot_status' ) );
    }

    public function update_slot_status() {
        if (!isset($_POST['slot_id'], $_POST['new_status']) || !wp_verify_nonce($_POST['nonce'], 'admin_ajax_nonce')) {
            wp_send_json_error(['message' => 'Invalid request']);
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservation_slots';
    
        $slot_id = intval($_POST['slot_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
    
        // Define allowed statuses
        $valid_statuses = ['available', 'reserved', 'blocked'];
        if (!in_array($new_status, $valid_statuses, true)) {
            wp_send_json_error(['message' => 'Invalid status']);
        }
    
        // Update the slot status
        $updated = $wpdb->update(
            $table_name,
            ['status' => $new_status],
            ['id' => $slot_id],
            ['%s'],
            ['%d']
        );
    
        if ($updated !== false) {
            wp_send_json_success(['message' => 'Slot status updated successfully']);
        } else {
            wp_send_json_error(['message' => 'Failed to update slot status']);
        }
    }

    public function fetch_available_slots() {
        check_ajax_referer( 'fetch_slots_nonce', 'nonce' );

        global $wpdb;
        $table = $wpdb->prefix . 'reservation_slots';
        $slots = $wpdb->get_results( "SELECT id, slot_time, duration, status FROM $table" );

        $events = array();
        foreach ( $slots as $slot ) {
            $is_reserved = $slot->status === 'reserved';
            $color       = $is_reserved ? "#eee" : "green"; 
            $className   = $is_reserved ? "reserved" : "available";
            $duration    =  $slot->duration;
            $start_time  = strtotime( $slot->slot_time );
            $end_time    = $start_time + ( 60 * $duration );
error_log('duration '. $duration);
            $events[] = array(
                'id'        => $slot->id,
                'title'     => date( "g:i A", $start_time ) . " - " . date( "g:i A", $end_time ),
                'start'     => date( "Y-m-d\TH:i:s", $start_time ),
                'end'       => date( "Y-m-d\TH:i:s", $end_time ), // Add end time
                'color'     => $color,
                'className' => $className,
                'reserved'  => $is_reserved,
            );
        }

        wp_send_json_success( $events );
    }

    public function get_slots() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'reservation_slots';

        $slots = $wpdb->get_results("SELECT * FROM $table_name ORDER BY slot_time ASC");

        wp_send_json_success($slots);
    }

    public function add_slot() {
        if ( ! isset( $_POST['slot_time'] ) || !wp_verify_nonce( $_POST['nonce'], 'admin_ajax_nonce' ) ) {
            wp_send_json_error( array( 'message' => 'Invalid request' ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservation_slots';

        $slot_time = sanitize_text_field( $_POST['slot_time'] );
        $duration  = sanitize_text_field( $_POST['slot_duration'] );
        $wpdb->insert( $table_name, array( 'slot_time' => $slot_time, 'duration' => $duration, 'status' => 'available' ) );

        wp_send_json_success( array( 'message' => 'Slot added!' ) );
    }

    public function remove_slot() {
        if (!isset($_POST['slot_id']) || !wp_verify_nonce($_POST['nonce'], 'admin_ajax_nonce')) {
            wp_send_json_error(['message' => 'Invalid request']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservation_slots';

        $wpdb->delete($table_name, ['id' => intval($_POST['slot_id'])]);

        wp_send_json_success(['message' => 'Slot removed!']);
    }
}

new AjaxHandler();
