<?php
namespace MyReservationPlugin\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings
 * Handles reservation settings in the admin panel.
 */
class Settings {

    /**
     * Constructor to initialize the settings page.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Adds a submenu under 'My Reservations' for settings.
     */
    public function add_menu_page(): void {
        add_submenu_page(
            'my_reservations',
            esc_html__( 'Settings', 'my-reservation-plugin' ),
            esc_html__( 'Settings', 'my-reservation-plugin' ),
            'manage_options',
            'reservation-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Registers settings and fields.
     */
    public function register_settings(): void {
        register_setting( 'my_reservation_settings_group', 'reservation_admin_email', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_email',
            'default'           => get_option( 'admin_email' ),
        ));

        register_setting( 'my_reservation_settings_group', 'enable_payment', array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default'           => false,
        ));

        register_setting( 'my_reservation_settings_group', 'slot_colors', array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_colors' ),
            'default'           => array(
                'reserved'  => '#FF0000',
                'available' => '#008000',
                'selected'  => '#0000FF',
            ),
        ));

        register_setting( 'my_reservation_settings_group', 'form_fields', array(
            'type'              => 'array',
            'sanitize_callback' => array( $this, 'sanitize_form_fields' ),
            'default'           => array(
                'name'     => true,
                'email'    => true,
                'country'  => false,
                'phone'    => false,
                'whatsapp' => false,
                'gender'   => false,
                'upload'   => false,
            ),
        ));

        add_settings_section( 'my_reservation_main', esc_html__( 'Reservation Settings', 'my-reservation-plugin' ), '', 'reservation-settings' );

        add_settings_field( 'reservation_admin_email', esc_html__( 'Admin Email', 'my-reservation-plugin' ), array( $this, 'email_field_callback' ), 'reservation-settings', 'my_reservation_main' );

        add_settings_field( 'enable_payment', esc_html__( 'Enable Payment', 'my-reservation-plugin' ), array( $this, 'payment_field_callback' ), 'reservation-settings', 'my_reservation_main' );

        add_settings_field( 'slot_colors', esc_html__( 'Slot Colors', 'my-reservation-plugin' ), array( $this, 'colors_field_callback' ), 'reservation-settings', 'my_reservation_main' );

        add_settings_field( 'form_fields', esc_html__( 'Form Fields', 'my-reservation-plugin' ), array( $this, 'form_fields_callback' ), 'reservation-settings', 'my_reservation_main' );
    }

    /**
     * Sanitizes the color settings.
     *
     * @param array $input The input color values.
     * @return array Sanitized color values.
     */
    public function sanitize_colors( array $input ): array {
        $sanitized_colors = array();
        foreach ( $input as $key => $color ) {
            $sanitized_colors[ $key ] = sanitize_hex_color( $color );
        }
        return $sanitized_colors;
    }

    /**
     * Sanitizes the form fields settings.
     *
     * @param array $input The input fields.
     * @return array Sanitized fields.
     */
    public function sanitize_form_fields( array $input ): array {
        return array_map( 'rest_sanitize_boolean', $input );
    }

    /**
     * Displays the email input field.
     */
    public function email_field_callback(): void {
        $email = get_option( 'reservation_admin_email', get_option( 'admin_email' ) );
        echo '<input type="email" name="reservation_admin_email" value="' . esc_attr( $email ) . '" class="regular-text">';
    }

    /**
     * Displays the payment enable/disable checkbox.
     */
    public function payment_field_callback(): void {
        $enabled = get_option( 'enable_payment', false );
        echo '<input type="checkbox" name="enable_payment" value="1" ' . checked( 1, $enabled, false ) . '>';
    }

    /**
     * Displays the color selection fields.
     */
    public function colors_field_callback(): void {
        $colors = get_option( 'slot_colors', array( 'reserved' => '#FF0000', 'available' => '#008000', 'selected' => '#0000FF' ) );
        ?>
        <label><?php esc_html_e( 'Reserved', 'my-reservation-plugin' ); ?>: <input type="color" name="slot_colors[reserved]" value="<?php echo esc_attr( $colors['reserved'] ); ?>"></label><br>
        <label><?php esc_html_e( 'Available', 'my-reservation-plugin' ); ?>: <input type="color" name="slot_colors[available]" value="<?php echo esc_attr( $colors['available'] ); ?>"></label><br>
        <label><?php esc_html_e( 'Selected', 'my-reservation-plugin' ); ?>: <input type="color" name="slot_colors[selected]" value="<?php echo esc_attr( $colors['selected'] ); ?>"></label>
        <?php
    }

    /**
     * Displays the form fields selection.
     */
    public function form_fields_callback(): void {
        $fields = get_option( 'form_fields', array( 'name' => true, 'email' => true, 'country' => false, 'phone' => false, 'whatsapp' => false, 'gender' => false, 'upload' => false ) );
        $field_labels = array(
            'name'     => esc_html__( 'Name', 'my-reservation-plugin' ),
            'email'    => esc_html__( 'Email', 'my-reservation-plugin' ),
            'country'  => esc_html__( 'Country', 'my-reservation-plugin' ),
            'phone'    => esc_html__( 'Phone', 'my-reservation-plugin' ),
            'whatsapp' => esc_html__( 'WhatsApp', 'my-reservation-plugin' ),
            'gender'   => esc_html__( 'Gender', 'my-reservation-plugin' ),
            'upload'   => esc_html__( 'Upload File', 'my-reservation-plugin' ),
        );

        foreach ( $field_labels as $key => $label ) {
            ?>
            <label>
                <input type="checkbox" name="form_fields[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( 1, isset( $fields[ $key ] ) ? $fields[ $key ] : false ); ?>>
                <?php echo esc_html( $label ); ?>
            </label><br>
            <?php
        }
    }

    /**
     * Renders the settings page.
     */
    public function render_settings_page(): void {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Reservation Settings', 'my-reservation-plugin' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'my_reservation_settings_group' );
                do_settings_sections( 'reservation-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
