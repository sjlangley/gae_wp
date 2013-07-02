<?php
/**
  * task handler-- publishes the post with the given post_id
 */

syslog(LOG_DEBUG, "in publish_task.php");

require_once('wp-config.php');


if (isset($_POST['post_id'])) {
	$post_id = $_POST['post_id'];
	syslog(LOG_DEBUG, "post id: $post_id");
	// get the post associated with the given post_id
    $post = get_post($post_id);
  	$post_status = get_post_status( $post );
	if ($post_status == 'future') {
		// only publish the post if it has a 'future' status.
		// TODO -- check that the post date hasn't been changed in the interim.
		wp_publish_post( $post );
	}
}
else {
	syslog(LOG_WARNING, "could not get post id.");
}

