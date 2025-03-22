<?php
/**
 * Plugin Name: IReservation Plugin
 * Description: Reservation plugin integrated with WooCommerce payment.
 * Version: 1.0.0
 * Author: ILamp Agency 
 * Author URI: https://ilampagency.com/
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once __DIR__ . '/vendor/autoload.php';

use MyReservationPlugin\Init;
use MyReservationPlugin\Database\Tables;

// Initialize the plugin
function my_reservation_plugin_init() {
    new Init();
}
add_action('plugins_loaded', 'my_reservation_plugin_init');

// Register activation hook properly
function my_reservation_plugin_activate() {
    ( new Tables() )->create_tables();
}
register_activation_hook( __FILE__, 'my_reservation_plugin_activate' );

// Register deactivation hook
function my_reservation_plugin_deactivate() {
    error_log('Deactivating plugin...');
}
register_deactivation_hook( __FILE__, 'my_reservation_plugin_deactivate' );
