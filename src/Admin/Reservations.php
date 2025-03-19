<?php
/**
 * Class Reservations
 *
 * @package MyReservationPlugin
 */

namespace MyReservationPlugin\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Reservations
 */
class Reservations {

	/**
	 * Reservations constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Add the refunds menu page
	 */
	public function add_menu_page(): void {
		add_menu_page(
			esc_html__( 'Reservations', 'mighty-kids' ),
			esc_html__( 'Reservations', 'mighty-kids' ),
			'manage_options',
			'reservations',
			array( $this, 'render_page' ),
			'dashicons-money',
			20
		);
	}

	/**
	 * Enqueue necessary scripts
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'jquery-ui-datepicker' );
	}

	/**
	 * Render the refunds page
	 */
	public function render_page(): void {
		$nonce = isset( $_GET['mk_refunds_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['mk_refunds_nonce'] ) ) : '';

		if ( ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'mk_refunds_action' ) ) {
			wp_die( esc_html__( 'Invalid nonce specified.', 'mighty-kids' ) );
		}

		$reservations_list_table = new ReservationsListTable();
		$reservations_list_table->prepare_items();
		// $filter_values = $refunds_list_table->get_filter_values();
		// $order_id      = $filter_values['order_id'];
		// $product_id    = $filter_values['product_id'];
		// $start_date    = $filter_values['start_date'];
		// $end_date      = $filter_values['end_date'];

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Reservations', 'mighty-kids' ); ?>
			</h1>

			<form method="get" action="">
				<input type="hidden" name="page" value="mk-refunds" />
				<?php wp_nonce_field( 'mk_refunds_action', 'mk_refunds_nonce' ); ?>

				<!-- <label for="order_id"><?php esc_html_e( 'Order ID:', 'mighty-kids' ); ?></label>
				<input type="text" id="order_id" name="order_id" value="<?php echo esc_attr( $order_id ); ?>" />

				<label for="product_id"><?php esc_html_e( 'Product ID:', 'mighty-kids' ); ?></label>
				<input type="text" id="product_id" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />

				<label for="start_date"><?php esc_html_e( 'Start Date:', 'mighty-kids' ); ?></label>
				<input type="date" id="start_date" name="start_date" value="<?php echo esc_attr( $start_date ); ?>" />

				<label for="end_date"><?php esc_html_e( 'End Date:', 'mighty-kids' ); ?></label>
				<input type="date" id="end_date" name="end_date" value="<?php echo esc_attr( $end_date ); ?>" />

				<button type="submit" class="button"><?php esc_html_e( 'Filter', 'mighty-kids' ); ?></button> -->
			</form>

			<form method="post">
				<?php
				// Display the table.
				$reservations_list_table->display();
				?>
			</form>
		</div>
		<?php
	}
}
