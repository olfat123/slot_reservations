<?php
namespace MyReservationPlugin\Frontend;

class Shortcode {
    public function __construct() {
        add_shortcode( 'reservation_form', array( $this, 'render_form' ) );
        add_action( 'woocommerce_order_status_completed', array( $this, 'my_update_reservation_status' ), 10, 1 );
        add_action( 'woocommerce_order_status_processing', array( $this, 'my_update_reservation_status' ), 10, 1 );
        add_action( 'init', array( $this, 'my_reservation_start_session' ), 1 );
        add_action( 'init', array( $this, 'reservation_thank_you_endpoint' ) );
        add_action( 'template_redirect', array( $this, 'reservation_thank_you_template' ) );
    }

    public function render_form() {
        ob_start();
        include plugin_dir_path( __FILE__ ) . '../../templates/reservation-form.php';
        return ob_get_clean();
    }

    public function my_update_reservation_status( $order_id ) {
        global $wpdb;
    
        // Get the reservation ID from order meta
        $reservation_id = get_post_meta( $order_id, 'reservation_id', true );
        if ( !$reservation_id ) {
            return; // No reservation linked to this order
        }
    
        // Define table names
        $table_reservations = $wpdb->prefix . 'reservation_reservations';
        $table_slots        = $wpdb->prefix . 'reservation_slots';
    
        // Get the slot ID linked to this reservation
        $reservation = $wpdb->get_row( $wpdb->prepare( "SELECT slot_id FROM $table_reservations WHERE id = %d", $reservation_id ) );
    
        if ( $reservation ) {
            $slot_id = $reservation->slot_id;
    
            // Update reservation status and link the order ID
            $wpdb->update(
                $table_reservations,
                array( 'status' => 'reserved', 'order_id' => $order_id ),
                array( 'id'     => $reservation_id )
            );
    
            // Mark the slot as reserved
            $wpdb->update(
                $table_slots,
                array( 'status' => 'reserved' ),
                array( 'id'     => $slot_id )
            );
    
        }
    }
    
    public function my_reservation_start_session() {
        if ( PHP_SESSION_NONE === session_status()  ) {
            session_start();
        }
    }

    public function reservation_thank_you_endpoint() {
        add_rewrite_endpoint( 'reservation-thank-you', EP_ROOT | EP_PAGES );
    }

    public function reservation_thank_you_template() {
        if ( get_query_var( 'reservation-thank-you', false ) !== false ) {
            include plugin_dir_path( __FILE__ ) . '../../templates/thank-you.php';
            exit;
        }
    }

}
