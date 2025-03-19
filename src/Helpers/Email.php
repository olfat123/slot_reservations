<?php
namespace MyReservationPlugin\Helpers;

class Email {

    /**
     * Send an email to the admin when a new reservation is created.
     *
     * @param string $name Customer name.
     * @param string $email Customer email.
     * @param string $slot_time Reserved slot time.
     */
    public static function send_admin_notification( string $name, string $email, string $slot_time ) {
        $admin_email = get_option( 'admin_email' );
        $subject     = "New Reservation Received";
        $message     = "
            <h2>New Reservation Details</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Slot Time:</strong> $slot_time</p>
        ";
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>'
        );

        wp_mail( $admin_email, $subject, $message, $headers );
    }
}
