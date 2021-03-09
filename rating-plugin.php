
<?php
/**
 * Plugin Name: "Rate Us" Content Reviewer
 * Description: Plugin for the tutorial
 * Author: Jason Vanstone
 * Author URI: http://www.vanstoneonline.com
 * Textdomain: rate_us
 */
if( ! defined( 'ABSPATH' ) ) {
	return;
} 

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


/**
 * The page to display all rated content
 * @return void 
 */
function rate_us_rating_page_html() {
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    global $wpdb;

    // SQL query to get all the content which has the meta key 'rate_us_rating'. Group the content by the ID and get an average rating on each
    $sql = "SELECT * FROM ( SELECT p.post_title 'title', p.guid 'link', post_id, AVG(meta_value) AS rating, count(meta_value) 'count' FROM {$wpdb->prefix}postmeta pm";
    $sql .= " LEFT JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id";
    $sql .= " where meta_key = 'rate_us_rating' group by post_id ) as ratingTable ORDER BY rating DESC";
    
    $result = $wpdb->get_results( $sql, 'ARRAY_A' );
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div id="poststuff">
            <table class="form-table widefat">
                <thead>
                    <tr>
                        <td>
                            <strong><?php _e( 'Content', 'rate_us' ); ?></strong>
                        </td>
                        <td>
                            <strong><?php _e( 'Rating', 'rate_us' ); ?></strong>
                        </td>
                        <td>
                           <strong><?php _e( 'No. of Ratings', 'rate_us' ); ?></strong>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ( $result as $row ) {
                            echo '<tr>';
                                echo '<td>' . $row['title'] . '<br/><a href="' . $row['link'] . '" target="_blank">' . __( 'View the Content', 'rate_us' ) . '</a></td>';
                                echo '<td>' . round( $row['rating'], 2 ) . '</td>';
                                echo '<td>' . $row['count'] . '</td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

/**
 * Registering Settings for Rating Settings
 */
function rate_us_ratings_settings_init()
{
    // Registering the setting 'rate_us_rating_types' for the page 'rate_us_rating_settings'
    register_setting( 'rate_us_rating_settings', 'rate_us_rating_types');
 
    // Registering the section 'rate_us_rating_section' for the page 'rate_us_rating_settings'
    add_settings_section(
        'rate_us_rating_section',
        '',
        '',
        'rate_us_rating_settings'
    );
 
    // Registering the field for the setting 'rate_us_rating_types' on the page 'rate_us_rating_settings' under section 'rate_us_rating_section'
    add_settings_field(
        'rate_us_rating_types', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __('Show Rating on Content:', 'wporg'),
        'rate_us_rating_types_html',
        'rate_us_rating_settings',
        'rate_us_rating_section',
        [
            'label_for'         => 'rate_us_rating_pages',
            'class'             => 'wporg_row',
            'wporg_custom_data' => 'custom',
        ]
    );
}
add_action('admin_init', 'rate_us_ratings_settings_init');


/**
 * Get all Custom Post Types that are available publicly
 * For each of those add a checkbox to choose 
 * @param  array $args 
 * @return void       
 */
function rate_us_rating_types_html( $args ) {   
    $post_types = get_post_types( array( 'public' => true ), 'objects' );
    
    // get the value of the setting we've registered with register_setting()
    $rating_types = get_option('rate_us_rating_types', array());
    
    if( ! empty( $post_types ) ) {
        foreach ( $post_types as $key => $value ) {
            $isChecked = in_array( $key, $rating_types );
            echo '<input ' . ( $isChecked ? 'checked="checked"' : '' ) . ' type="checkbox" name="rate_us_rating_types[]" value="' . $key . '" /> ' . $value->label . '<br/>';
        }
    }
}

/**
 * Displaying the form with our Rating settings
 * @return void 
 */
function rate_us_rating_settings_html() {
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "rate_us_rating_settings"
            settings_fields('rate_us_rating_settings');
    
            // output setting sections and their fields
            do_settings_sections('rate_us_rating_settings');
    
            // output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}


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


/**
 * Enqueueing Scripts
 * @return void 
 */
function rate_us_rating_scripts() { 
    wp_enqueue_style( 'rating-css', plugin_dir_url( __FILE__ ) . 'rating.css', array(), '', 'screen' );
    wp_register_script( 'rating-js', plugin_dir_url( __FILE__ ) . 'assets/js/rating.js', array('jquery'), '', true );
    wp_localize_script( 'rating-js', 'rate_us_object', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'rate_us_rating' ),
        'text'     => array(
            'close_rating' => __( 'Close Rating', 'rate_us' ),
            'rate_it' => __( 'Rate It', 'rate_us' ),
            'choose_rate' => __( 'Choose a Rate', 'rate_us' ),
            'submitting' => __( 'Submitting...', 'rate_us' ),
            'thank_you' => __( 'Thank you for rating us.', 'rate_us' ),
            'submit' => __( 'Submit', 'rate_us' ),
        )
    ));
    wp_enqueue_script( 'rating-js' );
}



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
    
    $ratingCookie = array();
    if( $rate_id > 0 ) {

        if( ! in_array( $rate_id, $ratingCookie ) ) {

            $rate_value = isset( $_POST['rating'] ) ? $_POST['rating'] : 0;
            if( $rate_value > 0 ) {
                
                $success = add_post_meta( $rate_id, 'rate_us_rating', $rate_value );
                
                if( $success ) {

                    $result['message'] = __( 'Thank you for rating us. You can also <a href="/contact-us/>send us a comment!</a> ', 'rate_us' );
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


/**
 * Render Rating
 * @return void 
 */
function rate_us_rating_render() {
     
    $ratingValues = 5;
    ?>
   
    <div id="contentRating" class="rate_us-rating ">
        <button type="button" id="toggleRating" class="active">
            <span class="text">
                <?php _e( 'Rate this Issue!', 'rate_us' ); ?>
            </span>
            <span class="arrow"></span>
        </button> 
        <div id="entryRating" class="rate_us-rating-content">
            <div class="errors" id="ratingErrors"></div>
            <ul>
                <?php for( $i = 1; $i <= $ratingValues; $i++ ) {
                    echo '<li>';
                        echo '<input type="radio" name="ratingValue" value="' . $i . '" id="rating' . $i . '"/>';;
                        
                        echo '<label for="rating' . $i . '">';
                            echo $i;
                        echo '</label>';
                    echo '</li>';
                }
                ?>
                 
            </ul>
            <button type="button" data-rate="<?php echo get_the_id(); ?>"id="submitRating"><?php _e( 'Submit', 'rate_us' ); ?></button>
        </div>
    </div>
    <?php
}