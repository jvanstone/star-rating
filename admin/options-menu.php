<?php
/**
 * Top Level Menu and submenu
 */
function rate_us_rating_options_page()
{
    // add top level menu page
   	add_menu_page(
        __( 'Ratings', 'rate_us' ),
        __( 'Ratings', 'rate_us' ),
        'manage_options',
        'rate_us_rating',
        'rate_us_rating_page_html',
        'dashicons-star-empty'
    );

   	add_submenu_page( 
   	 	'rate_us_rating', 
   	 	__( 'Settings', 'rate_us' ), 
   	 	__( 'Settings', 'rate_us' ), 
   	 	'manage_options', 
   	 	'rate_us_rating_settings', 
   	 	'rate_us_rating_settings_html'
   	 );
}
add_action('admin_menu', 'rate_us_rating_options_page');

?>