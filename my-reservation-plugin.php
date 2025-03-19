<?php
/**
 * Plugin Name: My Reservation Plugin
 * Description: A custom reservation plugin integrated with WooCommerce.
 * Version: 1.0.0
 * Author: Olfat Hakeem
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
    error_log('Activating plugin...');

    // Ensure database tables are created
    (new Tables())->create_tables();

    // Additional activation logic if needed
}
register_activation_hook( __FILE__, 'my_reservation_plugin_activate' );

// Register deactivation hook
function my_reservation_plugin_deactivate() {
    error_log('Deactivating plugin...');
}
register_deactivation_hook( __FILE__, 'my_reservation_plugin_deactivate' );