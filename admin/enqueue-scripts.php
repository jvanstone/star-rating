
<?php
/**
 * Enqueueing Scripts
 *
 * @return void
 */
function rate_us_rating_scripts() {
	wp_enqueue_style( 'rating', plugin_dir_url( __FILE__ ) . '../rating.css', array(), '', 'screen' );
	wp_register_script( 'rating-js', plugin_dir_url( __FILE__ ) . '../assets/js/rating.js', array( 'jquery' ), '', true );
	wp_localize_script( 'rating-js', 'rate_us_object', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'rate_us_rating' ),
		'text'     => array(
			'close_rating' => __( 'Close Rating', 'rate-us' ),
			'rate_it'      => __( 'Rate this Issue', 'rate-us' ),
			'choose_rate'  => __( 'Choose a Rate', 'rate-us' ),
			'submitting'   => __( 'Submitting...', 'rate-us' ),
			'thank_you'    => __( 'Thank you for rating us.', 'rate-us' ),
			'submit'       => __( 'Submit', 'rate-us' ),
		),
	));
	wp_enqueue_script( 'rating-js' );
}
add_action( 'wp_ajax_submit_rating', 'rate_us_submit_rating' );
add_action( 'wp_ajax_nopriv_submit_rating', 'rate_us_submit_rating' );

/**
 * Enqueue the Dashicons script
 *
 * */
add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );
function load_dashicons_front_end() {
	wp_enqueue_style( 'dashicons' );
}
