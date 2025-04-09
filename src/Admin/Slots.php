<?php
namespace MyReservationPlugin\Admin;

defined( 'ABSPATH' ) || exit;

class Slots {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
    }

    public function add_menu_page() {
        // Submenu: Slots
        add_submenu_page(
            'my_reservations',
            esc_html__( 'Slots', 'my-reservation-plugin' ),
            esc_html__( 'Slots', 'my-reservation-plugin' ),
            'manage_options',
            'reservation-slots',
            array( $this, 'render_slots_page' )
        );
    }

    public function render_slots_page() {
        ?>
        <div class="wrap">
            <h1>Reservation Slots</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('my_reservation_settings_group');
                do_settings_sections('my_reservation_settings_group');
                ?>
            </form>
    
            <h2>Manage Available Slots</h2>
            <button id="add-slot" class="button button-primary">Add Slot</button>
            <br>
            <table class="widefat" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th>Slot Time</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="slot-list">
                    <!-- Slots will be loaded here via AJAX -->
                </tbody>
            </table>
        </div>
    
        <!-- Popup Modal for Adding Slot -->
        <div id="slot-modal" title="Add Slot" style="display:none;">
            <p>
                <label for="slot-time">Select Slot Time:</label>
                <input type="text" id="slot-time" name="slot_time" class="regular-text" required>
            </p>
            <p>
                <label for="slot-duration">Duration (in minutes):</label>
                <input type="number" id="slot-duration" name="slot_duration" class="regular-text" min="15" step="15" required>
            </p>
            <button id="save-slot" class="button button-primary">Save Slot</button>
        </div>
        <?php
    }
}
