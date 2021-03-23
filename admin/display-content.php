<?php
/**
 * The page to display all rated content
 * @return void
 */

function rate_us_rating_page_html() {
	// check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;

	// SQL query to get all the content which has the meta key 'rate_us_rating'. Group the content by the ID and get an average rating on each
	$sql  = " SELECT * FROM ( SELECT p.post_title 'title', p.guid 'link', post_id, AVG(meta_value) AS rating, count(meta_value) 'count' FROM {$wpdb->prefix}postmeta pm";
	$sql .= " LEFT JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id";
	$sql .= " where meta_key = 'rate_us_rating' group by post_id ) as ratingTable ORDER BY rating DESC";

	$result = $wpdb->get_results( $sql, 'ARRAY_A' );

	?>
	<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<div id="poststuff">
			<table class="form-table widefat">
				<thead>
					<tr>
						<td>
							<strong><?php esc_html_e( 'Content', 'rate-us' ); ?></strong>
						</td>
						<td>
							<strong><?php esc_html_e( 'Rating', 'rate-us' ); ?></strong>
						</td>
						<td>
							<strong><?php esc_html_e( 'No. of Ratings', 'rate-us' ); ?></strong>
						</td>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ( $result as $row ) {
							echo '<tr>';
							echo '<td>' . $row['title'] . '<br/><a href="' . $row['link'] . '" target="_blank">' . __( 'View the Content', 'rate-us' ) . '</a></td>';
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
