<?php
/**
 * Runs on Uninstall of XTRA Settings
 *
 * @package   XTRA Settings
 * @author    fures
 * @license   GPL-2.0+
 * @link      https://wordpress.org/plugins/xtra-settings/
 */

// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly
}


if (function_exists('is_multisite') && is_multisite()) {
    // For multisite
	global $wpdb;
	$old_blog = $wpdb->blogid;
	$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
	foreach ($blogids as $blog_id) {
		switch_to_blog($blog_id);
		// Delete All Options starting with 'xtra_'
		$all_options = wp_load_alloptions();
		foreach( $all_options as $name => $value ) {
			if(stripos($name, 'xtra_')===0) delete_option( $name );
		}
	}
	switch_to_blog($old_blog);
}
else {
    // For Single site
	// Delete All Options starting with 'xtra_'
	$all_options = wp_load_alloptions();
	foreach( $all_options as $name => $value ) {
		if(stripos($name, 'xtra_')===0) delete_option( $name );
	}
}
?>
