<?php
/**
  *
 */

syslog(LOG_DEBUG, "in publish_task.php");

require_once('wp-config.php');
syslog(LOG_DEBUG, "in publish_task.php, loaded wp-config.php");

if (isset($_POST['post_id'])) {
	$post_id = $_POST['post_id'];
	syslog(LOG_DEBUG, "post id: $post_id");
    $post = get_post($post_id);
  	$post_status = get_post_status( $post );
	if ($post_status == 'future') {
		wp_publish_post( $post );
	}
}
else {
	syslog(LOG_WARNING, "could not get post id.");
}

