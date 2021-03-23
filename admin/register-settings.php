<?php
/**
 * Registering Settings for Rating Settings
 */
function rate_us_ratings_settings_init()
{
	// Registering the setting 'rate_us_rating_types' for the page 'rate_us_rating_settings'
	register_setting( 'rate_us_rating_settings', 'rate_us_rating_types' );
 
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
	if (! current_user_can( 'manage_options' )) {
		return;
	}

	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "rate_us_rating_settings"
			settings_fields( 'rate_us_rating_settings' );

			// output setting sections and their fields
			do_settings_sections( 'rate_us_rating_settings' );

			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}
?>