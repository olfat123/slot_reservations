<?php
namespace MyReservationPlugin;

use MyReservationPlugin\Database\Tables;
use MyReservationPlugin\Admin\Settings;
use MyReservationPlugin\Frontend\Shortcode;
use MyReservationPlugin\Frontend\AjaxHandler;
use MyReservationPlugin\WooCommerce\CheckoutHandler;
use MyReservationPlugin\Helpers\Email;
use MyReservationPlugin\Admin\ReservationDetails;


class Init {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ) );

        if ( is_admin() ) {
            require_once plugin_dir_path( __FILE__ ) . 'Admin/Reservations.php';
            new \MyReservationPlugin\Admin\Reservations();
        }

        // Initialize components
        new Tables();
        new Settings();
        new Shortcode();
        new CheckoutHandler();
        new Email();
        new ReservationDetails();
        new AjaxHandler();

    }

    public function enqueue_admin_scripts_and_styles() {
        wp_enqueue_script( 'admin-slots-js', plugin_dir_url( __FILE__ ) . '../assets/js/admin-slots.js', array( 'jquery' ), null, true );

        wp_localize_script( 'admin-slots-js', 'calendarData', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'admin_ajax_nonce' ),
        ) );
    }
    public function enqueue_scripts_and_styles() {
        wp_enqueue_script( 'jquery' );

        wp_enqueue_script( 'moment-js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js', array( 'jquery' ), null, true );

        // Load FullCalendar v5
        wp_enqueue_style( 'fullcalendar-css', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.4/main.min.css' );
        wp_enqueue_script( 'fullcalendar-js', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.4/main.min.js', array( 'jquery' ), null, true );

        // Enqueue your custom script (ensure it loads after FullCalendar)
        wp_enqueue_script( 'availability-calendar-js', plugin_dir_url( __FILE__ ) . '../assets/js/availability-calendar.js', array( 'jquery', 'fullcalendar-js' ), null, true );
        
        // Enqueue your custom styles
        wp_enqueue_style( 'reservation-form-css', plugin_dir_url( __FILE__ ) . '../assets/css/reservation-form.css' );

        // Pass AJAX URL to JS
        wp_localize_script( 'availability-calendar-js', 'calendarData', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'fetch_slots_nonce' ),
        ) );
        
    }

}
