<?php
namespace MyReservationPlugin\Admin;

class Settings {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
    }

    public function add_menu_page() {
        add_menu_page(
            'Reservation Settings',
            'Reservation Settings',
            'manage_options',
            'reservation-settings',
            array( $this, 'render_settings_page' ),
            'dashicons-calendar-alt',
            20
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Reservation Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('my_reservation_settings_group');
                do_settings_sections('my_reservation_settings_group');
                submit_button();
                ?>
            </form>

            <h2>Manage Available Slots</h2>
            <button id="add-slot">Add Slot</button>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Slot Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="slot-list">
                    <!-- Slots will be loaded here via AJAX -->
                </tbody>
            </table>
        </div>
        <?php
    }
}
