<?php
add_action( 'wp_ajax_submit_rating', 'rate_us_submit_rating' );
add_action( 'wp_ajax_nopriv_submit_rating', 'rate_us_submit_rating' );
/**
 * Submitting Rating
 * @return string  JSON encoded array
 */
function rate_us_submit_rating() {
	check_ajax_referer( 'rate_us_rating', '_wpnonce', true );
	$result = array(
		'success' => 1,
		'message' => '',
	);

	$rating_cookie = isset( $_COOKIE['rate_us_rating'] ) ? unserialize( base64_decode( $_COOKIE['rate_us_rating'] ) ) : array();
	$rate_id = isset( $_POST['post_id'] ) ? wp_unslash( $_POST['post_id'] ) : 0;

	if ( ! $rating_cookie ) {
		$rating_cookie = array();
	}

	if ( $rate_id > 0 ) {

		if ( !in_array( $rate_id, $rating_cookie, true ) ) {

			$rate_value = isset( $_POST['rating'] ) ? wp_unslash( $_POST['rating'] ) : 0;
			if ( $rate_value > 0 ) {

				$success = add_post_meta( $rate_id, 'rate_us_rating', $rate_value );

				if ( $success ) {

					$result['message'] = __( 'Thank you for your rating!', 'rate-us' );
					$rating_cookie[] = $rate_id;
					$expire = time() + 30*DAY_IN_SECONDS;
					setcookie( 'rate_us_rating', base64_encode( serialize( $rating_cookie ) ), $expire, COOKIEPATH, COOKIE_DOMAIN );
					$_COOKIE['rate_us_rating'] = base64_encode( serialize( $rating_cookie ) );
				}
			} else {
				$result['success'] = 0;
				$result['message'] = __( 'Something went wrong. Try to rate later', 'rate-us' );
			}
		} else {
			$result['success'] = 0;
			$result['message'] = __( 'You have already rated this content.', 'rate-us' );
		}
	} else {
		$result['success'] = 0;
		$result['message'] = __( 'Something went wrong. Try to rate later', 'rate-us' );
	}

	echo wp_json_encode( $result );
	wp_die();
}
