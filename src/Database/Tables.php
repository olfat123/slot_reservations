<?php
namespace MyReservationPlugin\Database;

defined( 'ABSPATH' ) || exit;

class Tables {
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_slots = $wpdb->prefix . 'reservation_slots';
        $table_reservations = $wpdb->prefix . 'reservation_reservations';

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql1 = "CREATE TABLE $table_slots (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slot_time DATETIME NOT NULL,
            duration INT NOT NULL DEFAULT 15,
            status ENUM('available', 'reserved') DEFAULT 'available'
        ) $charset_collate;";
        dbDelta($sql1);

        // Create reservations table
        $sql2 = "CREATE TABLE $table_reservations (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            country VARCHAR(255) NOT NULL,
            region VARCHAR(255) NOT NULL,
            whatsapp VARCHAR(255) NOT NULL,
            slot_id BIGINT UNSIGNED NOT NULL,
            file_path VARCHAR(255),
            order_id BIGINT UNSIGNED,
            status ENUM('pending', 'paid') DEFAULT 'pending',
            FOREIGN KEY (slot_id) REFERENCES $table_slots(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql2);
    }
}
