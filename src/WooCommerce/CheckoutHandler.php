<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

namespace MyReservationPlugin\WooCommerce;

class CheckoutHandler {
    public function redirect_to_checkout( int $reservation_id ) {
        if ( ! function_exists( 'WC' ) ) {
            return;
        }

        $this->process_checkout( $reservation_id );
    }

    private function process_checkout( int $reservation_id ) {

        wp_redirect(home_url('/reservation-thank-you/'));

        
        // if ( is_null( WC()->cart ) ) {
        //     wc_load_cart();
        // }

        // $products = wc_get_products( array(
        //     'name' => 'Reservation'
        // ) );

        // if ( count( $products ) >= 1 ) {
        //     $product = $products[0];
        // }

        // $product_id = $product->get_id();
        // WC()->cart->empty_cart();
        // WC()->cart->add_to_cart( $product_id );
        // $checkout_url = wc_get_checkout_url() . '?reservation_id=' . $reservation_id;
        // wp_safe_redirect( $checkout_url );
        // exit;
    }
}
