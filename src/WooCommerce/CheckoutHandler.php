<?php
namespace MyReservationPlugin\WooCommerce;

class CheckoutHandler {
    public function redirect_to_checkout( int $reservation_id ) {
        if ( ! function_exists( 'WC' ) ) {
            return;
        }

        $this->process_checkout( $reservation_id );
    }

    private function process_checkout( int $reservation_id ) {
        if ( is_null( WC()->cart ) ) {
            wc_load_cart();
        }

        $product_id = 67; // WooCommerce product ID
        WC()->cart->add_to_cart( $product_id );
        $checkout_url = wc_get_checkout_url() . '?reservation_id=' . $reservation_id;
        wp_safe_redirect( $checkout_url );
        exit;
    }
}
