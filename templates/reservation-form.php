<?php
if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

use MyReservationPlugin\Helpers\Helper;

// Get available slots from the database
global $wpdb;
$table_slots = $wpdb->prefix . 'reservation_slots';
$available_slots = $wpdb->get_results("SELECT id, slot_time FROM $table_slots WHERE status = 'available'");

?>

<div class="reservation-form-container">
    <h2>Book Your Reservation</h2>
    
    <form id="reservation-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        
        <!-- Name -->
        <p>
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" required>
        </p>

        <!-- Email -->
        <p>
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required>
        </p>

        <!-- Calendar for Slot Selection -->
        <p>
            <label>Select a Time Slot</label>
            <div id="calendar"></div>
            <input type="hidden" name="slot_id" id="slot_id" required>
        </p>

        <!-- File Upload -->
        <p>
            <label for="file">Upload Document (Optional)</label>
            <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.jpg,.png">
        </p>

        <!-- Hidden Fields -->
        <input type="hidden" name="action" value="submit_reservation">
        <input type="hidden" name="reservation_nonce" value="<?php echo wp_create_nonce('submit_reservation'); ?>">

        <!-- Submit Button -->
        <p>
            <button type="submit" class="reservation-submit-button">Reserve & Pay</button>
        </p>

    </form>

</div>
