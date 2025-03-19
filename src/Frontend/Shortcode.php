<?php
namespace MyReservationPlugin\Frontend;

class Shortcode {
    public function __construct() {
        add_shortcode( 'reservation_form', array( $this, 'render_form' ) );
    }

    public function render_form() {
        ob_start();
        include plugin_dir_path( __FILE__ ) . '../../templates/reservation-form.php';
        return ob_get_clean();
    }
}
