<?php
add_action( 'wp_ajax_submit_rating', 'rate_us_submit_rating' );
add_action( 'wp_ajax_nopriv_submit_rating', 'rate_us_submit_rating' );
/**
 * Submitting Rating
 * @return string  JSON encoded array
 */
function rate_us_submit_rating() {
    check_ajax_referer( 'rate_us_rating', '_wpnonce', true );
    $result = array( 'success' => 1, 'message' => '' );

    $ratingCookie = isset( $_COOKIE['rate_us_rating'] ) ? unserialize( base64_decode( $_COOKIE['rate_us_rating'] ) ) : array();
    $rate_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
 
    if( ! $ratingCookie ) {
        $ratingCookie = array();
    }
    

    if( $rate_id > 0 ) {

        if( ! in_array( $rate_id, $ratingCookie ) ) {

            $rate_value = isset( $_POST['rating'] ) ? $_POST['rating'] : 0;
            if( $rate_value > 0 ) {
                
                $success = add_post_meta( $rate_id, 'rate_us_rating', $rate_value );
                
                if( $success ) {

                    $result['message'] = __( 'Thank you for rating!', 'rate_us' );
                    $ratingCookie[] = $rate_id;
                    $expire = time() + 30*DAY_IN_SECONDS;
                    setcookie( 'rate_us_rating', base64_encode(serialize( $ratingCookie )), $expire, COOKIEPATH, COOKIE_DOMAIN );
                    $_COOKIE['rate_us_rating'] = base64_encode(serialize( $ratingCookie ));
                }

            } else {
                $result['success'] = 0;
                $result['message'] = __( 'Something went wrong. Try to rate later', 'rate_us' );
            }

        } else {
            $result['success'] = 0;
            $result['message'] = __( 'You have already rated this content.', 'rate_us' );
        }
    } else {
        $result['success'] = 0;
        $result['message'] = __( 'Something went wrong. Try to rate later', 'rate_us' );
    }

    echo json_encode( $result );
    wp_die();
}
?>