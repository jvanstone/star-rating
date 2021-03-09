<?php

function my_plugin_create_db() {
  global $wpdb;
  $version = get_option( 'my_plugin_version', '1.0' );
  // ...
}

?>