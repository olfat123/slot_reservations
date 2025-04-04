<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

namespace MyReservationPlugin\Admin;

class Settings {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
    }

    public function add_menu_page() {
        add_submenu_page(
            'my_reservations',
            esc_html__( 'Settings', 'my-reservation-plugin' ),
            esc_html__( 'Settings', 'my-reservation-plugin' ),
            'manage_options',
            'reservation-settings',
            array( $this, 'render_settings_page' )
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
                ?>
            </form>
    

        </div>

        <?php
    }
}
