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
        $slots = $wpdb->get_results( "SELECT id, slot_time, status FROM $table" );

        $events = array();
        foreach ( $slots as $slot ) {
            $color = ($slot->status === 'reserved') ? "#eee" : "green"; // Grey for reserved, green for available
            $className = ($slot->status === 'reserved') ? "reserved" : "available"; // Add class

            $events[] = array(
                'id'    => $slot->id,
                'title' => date( "g:i A", strtotime( $slot->slot_time ) ),
                'start' => $slot->slot_time,
                'color' => $color,
                'className' => $className
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
        if (!isset($_POST['slot_time']) || !wp_verify_nonce($_POST['nonce'], 'admin_ajax_nonce')) {
            wp_send_json_error(['message' => 'Invalid request']);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'reservation_slots';

        $slot_time = sanitize_text_field($_POST['slot_time']);
        $wpdb->insert($table_name, ['slot_time' => $slot_time, 'status' => 'available']);

        wp_send_json_success(['message' => 'Slot added!']);
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
