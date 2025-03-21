<?php
if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Retrieve errors and old input values
$errors = $_SESSION['reservation_errors'] ?? [];
$data = $_SESSION['reservation_old_data'] ?? [];

// Clear session errors after displaying them
unset($_SESSION['reservation_errors']);
unset($_SESSION['reservation_old_data']);
?>

<div class="reservation-form-container">
    <h2>Book Your Reservation</h2>
    
    <form id="reservation-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        
        <!-- Name -->
        <p>
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" value="<?php echo esc_attr($data['name'] ?? ''); ?>" required>
            <?php if (isset($errors['name'])): ?>
                <span class="error-message"><?php echo esc_html($errors['name']); ?></span>
            <?php endif; ?>
        </p>

        <!-- Email -->
        <p>
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" value="<?php echo esc_attr($data['email'] ?? ''); ?>" required>
            <?php if (isset($errors['email'])): ?>
                <span class="error-message"><?php echo esc_html($errors['email']); ?></span>
            <?php endif; ?>
        </p>

        <!-- Calendar for Slot Selection -->
        <p>
            <label>Select a Time Slot</label>
            <div id="calendar"></div>
            <input type="hidden" name="slot_id" id="slot_id" value="<?php echo esc_attr($data['slot_id'] ?? ''); ?>" required>
            <?php if (isset($errors['slot_id'])): ?>
                <span class="error-message"><?php echo esc_html($errors['slot_id']); ?></span>
            <?php endif; ?>
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
