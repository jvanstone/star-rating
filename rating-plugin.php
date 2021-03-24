<?php
/**
 * Plugin Name: "Rate Us" Content Reviewer
 * Description: Plugin for Rating Content
 * Author: Jason Vanstone
 * Author URI: http://www.vanstoneonline.com
 * Textdomain: rate-us
 */

if( ! defined( 'ABSPATH' ) ) {
	return;
}


include plugin_dir_path( __FILE__ ) . 'admin/display-content.php';
include plugin_dir_path( __FILE__ ) . 'admin/options-menu.php';
include plugin_dir_path( __FILE__ ) . 'admin/register-settings.php';
include plugin_dir_path( __FILE__ ) . 'admin/check-ratings.php';
include plugin_dir_path( __FILE__ ) . 'admin/enqueue-scripts.php';
include plugin_dir_path( __FILE__ ) . 'admin/submit-rating.php';


/**
 * Render Rating shows up on page.
 *
 * @return void
 */
function rate_us_rating_render() {

	$rating_values = 5;
	?>

	<div id="contentRating" class="rate-us-rating ">
		<button type="button" id="toggleRating" class="active">
			<span class="text">
				<?php esc_html_e( 'Rate this Issue!', 'rate-us' ); ?>
			</span>
			<span class="arrow"></span>
		</button>
		<div id="entryRating" class="rate-us-rating-content">
			<div class="errors" id="ratingErrors"></div>
			<ul>
				<?php for( $i = 1; $i <= $rating_values; $i++ ) {
					echo '<li>';
						echo '<input type="radio" name="ratingValue" value="' . $i . '" id="rating' . $i . '"/>';

						echo '<label for="rating' . $i . '">';
							echo $i;
						echo '</label>';
					echo '</li>';
				}
				?>

			</ul>
			
			<button type="button" data-rate="<?php echo get_the_id(); ?>"id="submitRating"><?php esc_html_e( 'Submit', 'rate-us' ); ?></button>
		</div>
	</div>
	<?php
}
