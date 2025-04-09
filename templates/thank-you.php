<?php
/**
 * Template Name: Thank You Page
 */

defined( 'ABSPATH' ) || exit;

get_header();

?>
 <div class="thank-you-container">
    <div class="check-icon"></div>
    <h1><?php echo esc_html__( 'Thank You!', 'my-reservation-plugin' ) ?>,</h1>
    <p>
        <?php echo esc_html__( 'Your reservation has been successfully completed. We`ll send you a confirmation email with all the details shortly. If you have any questions, feel free to contact us.', 'my-reservation-plugin' ) ?>
    </p>
    <a href="<?php echo home_url(); ?>" class="btn">Back to Home</a>
</div>

<?php
get_footer();
