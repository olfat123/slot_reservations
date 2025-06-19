<?php
namespace MyReservationPlugin\Frontend;

defined( 'ABSPATH' ) || exit;

use MyReservationPlugin\Helpers\Email;
use MyReservationPlugin\WooCommerce\CheckoutHandler;

class ReservationHandler {
    public function __construct() {
        add_action( 'admin_post_submit_reservation', array( $this, 'handle_submission' ) );
        add_action( 'admin_post_nopriv_submit_reservation', array( $this, 'handle_submission' ) );
    }

    public function handle_submission() {

        if ( !isset( $_POST['reservation_nonce'] ) || !wp_verify_nonce( $_POST['reservation_nonce'], 'submit_reservation' ) ) {
            wp_die('Security check failed');
        }

        $_SESSION['reservation_errors'] = []; // Initialize errors

        // Get and validate required fields
        $name     = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $country  = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : '';
        $region   = isset($_POST['region']) ? sanitize_text_field($_POST['region']) : '';
        $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : '';
        $whatsapp = isset($_POST['whatsapp']) ? sanitize_text_field($_POST['whatsapp']) : '';
        $email    = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $slot_id  = isset($_POST['slot_id']) ? intval($_POST['slot_id']) : 0;

        if ( empty( $name ) ) {
            $_SESSION['reservation_errors']['name'] = 'Full Name is required.';
        }
        if ( empty( $country ) ) {
            $_SESSION['reservation_errors']['country'] = 'Country is required.';
        }
        if ( empty( $region ) ) {
            $_SESSION['reservation_errors']['region'] = 'Region is required.';
        }
        if ( empty( $language ) ) {
            $_SESSION['reservation_errors']['language'] = 'Language is required.';
        }
        if ( empty( $whatsapp ) ) {
            $_SESSION['reservation_errors']['whatsapp'] = 'Whatsapp number is required.';
        }
        if ( empty( $email ) ) {
            $_SESSION['reservation_errors']['email'] = 'Email Address is required.';
        }
        if ( empty( $slot_id ) ) {
            $_SESSION['reservation_errors']['slot_id'] = 'Please select a time slot.';
        }
        if ( ! preg_match( '/^\+?\d{7,15}$/', $whatsapp ) ) {
            $errors['whatsapp'] = "Please enter a valid phone number (e.g., +1234567890)";
        }
        // Store old data
        $_SESSION['reservation_old_data'] = $_POST;

        if ( ! empty( $_SESSION['reservation_errors'] ) ) {
            wp_redirect( $_SERVER['HTTP_REFERER'] ); // Redirect back to form
            exit;
        }


        global $wpdb;
        $table_reservations = $wpdb->prefix . 'reservation_reservations';
        $table_slots = $wpdb->prefix . 'reservation_slots';
        
        $file_url = '';
        if ( ! empty( $_FILES['file']['name'] ) ) {
            $uploaded_file = $_FILES['file'];

            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploaded_file, $upload_overrides );

            if ( $movefile && ! isset( $movefile['error'] ) ) {
                $file_url = esc_url_raw( $movefile['url'] );
            } else {
                error_log( "File Upload Error: " . $movefile['error'] );
            }
        }

        // Insert reservation
        $wpdb->insert( $table_reservations, array(
            'name'      => $name,
            'country'   => $country,
            'region'    => $region,
            'language'  => $language,
            'whatsapp'  => $whatsapp,
            'email'     => $email,
            'slot_id'   => $slot_id,
            'file_path' => $file_url,
            'status'    => 'complete'
        ) );

        $reservation_id = $wpdb->insert_id;

        // Mark the slot as reserved
        $wpdb->update(
            $table_slots,
            array( 'status' => 'reserved' ),
            array( 'id'     => $slot_id )
        );
        $slot_time = $wpdb->get_var(
            $wpdb->prepare( "SELECT slot_time FROM $table_slots WHERE id = %d", $slot_id )
        );
        Email::send_admin_notification( $name, $email, $slot_time );

        // Redirect to WooCommerce Checkout
        ( new CheckoutHandler() )->redirect_to_checkout( $reservation_id );
    }
}
