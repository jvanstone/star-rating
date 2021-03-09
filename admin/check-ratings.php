<?php
/**
 * Checking for Rating
 * @return void 
 */
function rate_us_check_for_rating() {
  
    $rating_types = get_option( 'rate_us_rating_types', array() );

    if( is_array( $rating_types ) && count( $rating_types ) > 0 && is_singular( $rating_types ) ) { 

        $rate_id = get_the_id();
        $ratingCookie = isset( $_COOKIE['rate_us_rating'] ) ? unserialize( base64_decode( $_COOKIE['rate_us_rating'] ) ) : array();
        if( ! in_array( $rate_id, $ratingCookie ) ) { 
            // This content has not been rated yet by that user s
            add_action( 'wp_enqueue_scripts', 'rate_us_rating_scripts');
            add_action( 'wp_footer', 'rate_us_rating_render' );
        } 
    }
    
}
add_action( 'template_redirect', 'rate_us_check_for_rating' );

?>