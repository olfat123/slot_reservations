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
     * @param string $name        Customer name.
     * @param string $email       Customer email.
     * @param string $slot_time   Reserved slot time.
     * @param string|null $file_path Optional full path to the attachment file.
     * 
     * @return void
     */
    public static function send_admin_notification( string $name, string $email, string $slot_time, string $file_path = '' ): void {
        $admin_email = 'info@prestigiodental.com';
        $subject     = 'New Reservation Received';
        $message     = '
            <h2>New Reservation Details</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Slot Time:</strong> $slot_time</p>
            <p><strong>Attachment:</strong> <a href="' . $file_path . '">' . $file_path . '</a></p>
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
