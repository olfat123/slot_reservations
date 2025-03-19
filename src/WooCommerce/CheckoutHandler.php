<?php
namespace MyReservationPlugin\WooCommerce;

class CheckoutHandler {
    public function redirect_to_checkout( int $reservation_id ) {
        $product_id = 123; // Pre-created WooCommerce product
        $cart_url = wc_get_checkout_url();
        WC()->cart->empty_cart();
        WC()->cart->add_to_cart( $product_id );
        wp_redirect( $cart_url );
        exit;
    }
}
