<?php
namespace MyReservationPlugin\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Class Email
 *
 * Handles email notifications for the reservation system.
 */
class Email {

    /**
     * Send an email to the admin when a new reservation is created.
     *
     * @param string $slot_time   Reserved slot time.
     * @param array  $args         Array containing reservation details.
     * 
     * @return void
     */
    public static function send_admin_notification( string $slot_time, array $args ): void {
        $admin_email = 'info@prestigiodental.com';
        $subject     = 'New Reservation Received';
        $message     = '
            <h2>New Reservation Details</h2>
            <p><strong>Name:</strong>' . $args['name'] . '</p>
            <p><strong>Email:</strong>' . $args['email'] . '</p>
            <p><strong>Country:</strong>' . $args['country'] . '</p>
            <p><strong>Region:</strong>' . $args['region'] . '</p>
            <p><strong>Language:</strong>' . $args['language'] . '</p>
            <p><strong>Whatsapp:</strong>' . $args['whatsapp'] . '</p>
            <p><strong>Slot Time:</strong>' . $slot_time . '</p>
            <p><strong>Attachment:</strong><a href="' . $args['file_path'] . '">' . $args['file_path'] . '</a></p>
        ';

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>'
        );

        $attachments = [];

        if ( $file_path && file_exists( $file_path ) ) {
            $attachments[] = $file_path;
        }

        wp_mail( $admin_email, $subject, $message, $headers, $attachments );
    }
}
