<?php
/**
 * Template Name: Thank You Page
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}
get_header();
?>
 <div class="container">
    <div class="check-icon"></div>
    <h1>Thank You!</h1>
    <p>Your reservation has been successfully completed. We'll send you a confirmation email with all the details shortly. If you have any questions, feel free to contact us.</p>
    <a href="<?php echo home_url(); ?>" class="btn">Back to Home</a>
</div>

<?php
get_footer();
