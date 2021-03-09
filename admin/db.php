<?php

global $star_rating_db_version;
$star_rating_db_version = '1.0';

function vo_star_install() {
	
    global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'star_rating_posts';
    $table_name2 = $wpdb->prefix . 'star_rating_rates';

	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
        title varate_ushar(100) NOT NULL,
        content text NOT NULL,
        link varate_ushar(255) NOT NULL,
        PRIMARY KEY  (id)
	) $charset_collate;";

$sql = "CREATE TABLE $table_name2 (
    id int(11) NOT NULL AUTO_INCREMENT,
    user_id int(11) NOT NULL,
    post_id int(11) NOT NULL,
    rating int(2) NOT NULL,
    timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

add_option( 'star_ratting_db_version', $start_rating_db_version );
}

function vo_star_install_data() {
	global $wpdb;
	
	$welcome_name = 'Mr. WordPress';
	$welcome_text = 'Congratulations, you just completed the installation!';
	
	$table_name = $wpdb->prefix . 'start_ratin_posts';
    $table_name = $wpdb->prefix . 'start_rating';
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'time' => current_time( 'mysql' ), 
			'title' => $welcome_name, 
			'content' => $welcome_text, 
		) 
	);
    $wpdb->insert( 
		$table_name2, 
		array( 
			'time' => current_time( 'mysql' ), 
			'user_id' => 1, 
			'post_id' => 1, 
		) 
	);
}



?>