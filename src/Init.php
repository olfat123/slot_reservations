<?php
namespace MyReservationPlugin;

use MyReservationPlugin\Database\Tables;
use MyReservationPlugin\Admin\Settings;
use MyReservationPlugin\Frontend\Shortcode;
use MyReservationPlugin\Frontend\AjaxHandler;
use MyReservationPlugin\WooCommerce\CheckoutHandler;
use MyReservationPlugin\Helpers\Email;
use MyReservationPlugin\Admin\ReservationDetails;
use MyReservationPlugin\Frontend\ReservationHandler;


class Init {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ) );
        add_action( 'woocommerce_before_order_notes', function() {
            if ( ! empty( $_GET['reservation_id'] ) ) {
                echo '<input type="hidden" name="reservation_id" value="' . esc_attr( $_GET['reservation_id'] ) . '">';
            }
        } );
        add_action( 'woocommerce_checkout_update_order_meta', function( $order_id ) {
            if ( isset( $_POST['reservation_id'] ) ) {
                $order = wc_get_order( $order_id );
                $order->update_meta_data( 'reservation_id', sanitize_text_field( $_POST['reservation_id'] ) );
                $order->save();
            }
        });

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
        new ReservationHandler();

    }

    public function enqueue_admin_scripts_and_styles() {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-timepicker', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', array('jquery', 'jquery-ui-datepicker'), null, true);

        wp_enqueue_style('jquery-ui-css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
        wp_enqueue_style('jquery-ui-timepicker-css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css');

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
