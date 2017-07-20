<?php
/*
Plugin Name: XTRA Settings
Plugin URI: https://wordpress.org/plugins/xtra-settings/
Description: All useful hidden settings of Wordpress
Version: 1.4.6
Author: fures
Author URI: http://www.fures.hu/xtra-settings/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//. Defines
define( 'XTRA_VERSION', '1.4.6' );
define( 'XTRA_PLUGIN', __FILE__ );
define( 'XTRA_PLUGIN_BASENAME', plugin_basename( XTRA_PLUGIN ) );
	$wpc = str_ireplace(ABSPATH,"",WP_CONTENT_DIR);
define( 'XTRA_WPCONTENT_BASENAME', $wpc );
	$upload_dir = wp_upload_dir();
	$upld = $upload_dir['basedir'];
define( 'XTRA_UPLOAD_DIR', $upld );
	$upld = $upload_dir['baseurl'];
define( 'XTRA_UPLOAD_URL', $upld );


//. Remove deprecated functions
update_option( 'xtra_browserbugs', 0 );
//update_option( 'xtra_require_featured_image', 0 );
//update_option( 'xtra_disallow_duplicate_posttitles', 0 );



//. Register the activation hooks
register_activation_hook( __FILE__, 'xtra_activate' );
register_deactivation_hook( __FILE__, 'xtra_deactivate' );

//. Admin menu
add_action('admin_menu', 'xtra_menu');
function xtra_menu() {
	add_menu_page( 'XTRA Settings', 'XTRA Settings', 'manage_options', 'xtra', 'xtra_html_page', 'dashicons-lightbulb', 81 );
}
function xtra_html_page() {
	global $wpdb;
	include('admin-setting.php');
}

//. Include ajax.php
include_once('ajax.php');

//. Activate - deactivate
if( !function_exists( 'xtra_activate' ) ) {
	function xtra_activate($networkwide) {
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					_xtra_activate();
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		_xtra_activate();
	}
	function _xtra_activate() {
		xtra_dir_index_disable(1);
		update_option( 'xtra_remove_version_number', 1 );
		update_option( 'xtra_suppress_login_errors', 1 );
		xtra_deflates(1);
		xtra_image_expiry(1);
		xtra_remove_etags(1);
		return true;
	}
	function xtra_deactivate($networkwide) {
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					_xtra_deactivate();
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		_xtra_deactivate();
	}
	function _xtra_deactivate() {
		$names = get_option( 'xtra_deactivate_names' );
		if (!is_array($names) || count($names)<10 ) {
			$names = array(
				'xtra_deflates',
				'xtra_browserbugs',
				'xtra_image_expiry',
				'xtra_remove_etags',
				'xtra_redir_bots',
				'xtra_dir_index_disable',
				'xtra_protect_xss',
				'xtra_protect_pageframing',
				'xtra_protect_cotentsniffing',
				'xtra_block_external_post',
				'xtra_WPcache',
				'xtra_autosave_interval',
				'xtra_empty_trash',
				'xtra_debug',
				'xtra_disable_debug_display',
				'xtra_debug_log'
			);
			update_option( 'xtra_deactivate_names', $names );
		}
		foreach ($names as $name) {
			if (function_exists($name)) $name(0); //call functions dynamically with FALSE
		}
		return true;
	}
}

//. Monitor new blog create if multisite
function xtra_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
	global $wpdb;
	if ( ! function_exists( 'is_plugin_active_for_network' ) && is_multisite() ) {
		// need to include the plugin library for the is_plugin_active function
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	if (is_plugin_active_for_network( XTRA_PLUGIN_BASENAME )) {
		$old_blog = $wpdb->blogid;
		switch_to_blog($blog_id);
		_shiba_activate();
		switch_to_blog($old_blog);
	}
}
add_action( 'wpmu_new_blog', 'xtra_new_blog', 10, 6);

//. Add settings link on plugin page
function xtra_settings_link ($links) {
	$settings_link = '<a href="admin.php?page=xtra">'.__('Settings', 'xtra-settings').'</a>';
	array_unshift($links, $settings_link);
	return $links;
}
$xtra_plugin = XTRA_PLUGIN_BASENAME;
add_filter("plugin_action_links_$xtra_plugin", 'xtra_settings_link' );

function xtra_load_admin_scripts($hook) {
        if($hook != 'toplevel_page_xtra') {
                return;
        }
        wp_enqueue_style( 'xtra_wp_admin_css', plugins_url('/assets/css/admin.css', __FILE__) );
		if(isset($_POST['xtra_submit_options'])) {
			xtra_check_nonce();
			update_option( 'xtra_opt_light', $_POST['xtra_opt_light'] );
			update_option( 'xtra_opt_vertical', $_POST['xtra_opt_vertical'] );
		}
		if ( get_option('xtra_opt_light') )
			wp_enqueue_style( 'xtra_wp_admin_tabs_css', plugins_url('/assets/css/admin-tabs.css', __FILE__) );
		if ( get_option('xtra_opt_vertical') )
			wp_enqueue_style( 'xtra_wp_admin_tabs_vertical_css', plugins_url('/assets/css/admin-tabs-vertical.css', __FILE__) );
		
		wp_enqueue_script( 'xtra_admin_tabs_js', plugin_dir_url( __FILE__ ) . '/assets/js/admin-tabs.js', array( 'jquery' ), '1.0.0', true );
		
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		
		
		
		wp_enqueue_script( 'xtra_compress_script', plugin_dir_url( __FILE__ ) . '/assets/js/compress.js', array( 'jquery' ), '1.0.0' );
		wp_localize_script( 'xtra_compress_script', 'xtra_vars', array(
				'_wpnonce' => wp_create_nonce( 'xtra_ajax_nonce' ),
				'compress_complete' => esc_html__( 'Compression Complete', 'xtra' ),
				'invalid_response' => esc_html__( 'Invalid ajax response. Check the console for errors.', 'xtra' ),
			)
		);
		
		
		
		
}
add_action( 'admin_enqueue_scripts', 'xtra_load_admin_scripts' );



function xtra_admin_message() {
	$xtra_msg = get_option( 'xtra_msg', 0 );
	if($xtra_msg!=1){
		echo '<div id="xtra_admin_message" class="notice-warning settings-error notice is-dismissible">
		<p><strong>We enabled some Security and Speed settings. Check the <a href="admin.php?page=xtra&first=1">XTRA Settings</a> page!</strong> </p>
		</div>';
	}
}
add_action( 'admin_notices', 'xtra_admin_message' );



//---HELPER functions----

// Delete All Options starting with 'xtra_'
function xtra_delete_all_options() {
	$all_options = wp_load_alloptions();
	foreach( $all_options as $name => $value ) {
		if(stripos($name, 'xtra_')===0) delete_option( $name );
	}
	xtra_deactivate();
}

// Verify nonce
function xtra_check_nonce()
{
	$xtra_nonce = $_REQUEST['_wpnonce'];
	if ( ! wp_verify_nonce( $xtra_nonce, 'mynonce' ) ) {
		echo 'This request could not be verified.';
		exit;
	}
	//if ( !current_user_can('administrator') ) {
	if ( !current_user_can('manage_options') ) {
		echo 'This page is only for administrators.';
		exit;
	}
}

function xtra_find_img_src() {
global $post;
    if (!$img = xtra_find_image_id($post->ID)) {
        if ($img = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches))
            $img = $matches[1][0];
	}
    //if (is_int($img)) {
    if (ctype_digit($img)) {
        $img = wp_get_attachment_image_src($img);
        $img = $img[0];
    }
	$img = preg_replace('{-\d+x\d+\.}','.',$img);
    return $img;
}
function xtra_find_image_id($post_id) {
    if (!$img_id = get_post_thumbnail_id ($post_id)) {
        $attachments = get_children(array(
            'post_parent' => $post_id,
            'post_type' => 'attachment',
            'numberposts' => 1,
            'post_mime_type' => 'image'
        ));
        if (is_array($attachments)) foreach ($attachments as $a)
            $img_id = $a->ID;
    }
    if ($img_id)
        return $img_id;
    return false;
}

function xtra_get_excerpt() {
	global $post;
	$excerpt = strip_tags($post->post_content);
//echo "<pre>".$excerpt."</pre>";
	$excerpt = strip_shortcodes($excerpt);
	$excerpt = htmlentities($excerpt, null, 'utf-8');
	$excerpt = str_replace(array("\n","\r","\t","&nbsp;")," ",$excerpt);
	$excerpt = preg_replace('/\s+/', ' ',$excerpt);
	$excerpt = html_entity_decode($excerpt, null, 'utf-8');
	$excerpt = mb_substr($excerpt, 0, 125, 'UTF-8');
	return $excerpt;
}

function xtra_get_taglist() {
	global $post;
	$tag_list = get_the_terms( $post->ID, 'post_tag' );
	if( $tag_list ) {
		foreach( $tag_list as $tag )
			$tag_array[] = $tag->name;
		return implode(', ', $tag_array);
	}
}

function xtra_is_apache(){
	if ( stripos($_SERVER["SERVER_SOFTWARE"],"apache")!==FALSE ) return true;
	return false;
}

function xtra_get_image_id($file_url) {
	if (strpos($fule_url,"://")!==FALSE)
		$file_path = ltrim(str_replace(wp_upload_dir()['baseurl'], '', $file_url), '/');
	else
		$file_path = ltrim(str_replace(wp_upload_dir()['basedir'], '', $file_url), '/');

	global $wpdb;
	$statement = $wpdb->prepare("SELECT `ID` 
		FROM {$wpdb->posts} AS posts 
		JOIN {$wpdb->postmeta} AS meta on meta.`post_id`=posts.`ID` 
		WHERE 
			posts.`guid` LIKE '%%%s' 
		OR (
			meta.`meta_key`='_wp_attached_file' 
			AND meta.`meta_value` LIKE '%%%s'
		)
		;",
		$file_path,
		$file_path);

	$attachment = $wpdb->get_col($statement);

	//return $statement;

	if (count($attachment) < 1) {
		return false;
	}

	//return $attachment[0]; //ID
	return implode(", ",array_unique($attachment));
}

function xtra_get_image_ids() {
	
	//$file_path = wp_upload_dir()['baseurl']."/";
	$file_path = "\.(jpe?g|gif|png)$";

	global $wpdb;
	$statement = $wpdb->prepare("SELECT ID,guid,meta_value 
		FROM {$wpdb->posts} AS posts 
		JOIN {$wpdb->postmeta} AS meta on meta.`post_id`=posts.`ID` 
		WHERE 
			posts.`guid` REGEXP '%s' 
		OR (
			meta.`meta_key`='_wp_attached_file' 
			AND meta.`meta_value` REGEXP '%s'
		)
		;",
		$file_path,
		$file_path);

	$attachment = $wpdb->get_results($statement);

//echo "<pre>".count($attachment)." img_postids: ".print_r($attachment,1)."</pre>";
	//return $attachment;
	//return $statement;

	if (count($attachment) < 1) {
		return false;
	}
	
	$arr = array();
	
	foreach ($attachment as $row) {
		preg_match("#[^\"']*?\.(jpe?g|gif|png)#",$row->guid,$matches);
		$tkey = xtra_clean_tkey($matches[0]);
		if ($tkey && !InStr($arr[$tkey],$row->ID) )
			$arr[$tkey] .= $row->ID.",";
		
		preg_match("#[^\"']*?\.(jpe?g|gif|png)#",$row->meta_value,$matches);
		$tkey = xtra_clean_tkey($matches[0]);
		if ($tkey && !InStr($arr[$tkey],$row->ID) )
			$arr[$tkey] .= $row->ID.",";		
	}

	return $arr;
}

function xtra_clean_tkey($tkey) {
	$upldirname = basename(wp_upload_dir()['basedir']);
	$tkey = str_ireplace(wp_upload_dir()['basedir']."/","",$tkey);
	$tkey = str_ireplace(wp_upload_dir()['baseurl']."/","",$tkey);
	$tkey = preg_replace("#.*$upldirname/#i","",$tkey);
	return $tkey;
}

function InStr($haystack,$needle,$casesensitive=false) {
	if ($casesensitive) {
		if (str_replace($needle,"",$haystack)==$haystack) $ret=false;
		else $ret=true;
	}
	else {
		if (!is_array($needle)) $needle = mb_convert_case($needle,MB_CASE_LOWER,"UTF-8");
		if (!is_array($haystack)) $haystack = mb_convert_case($haystack,MB_CASE_LOWER,"UTF-8");

		if (str_ireplace($needle,"",$haystack)==$haystack) $ret=false;
		else $ret=true;
	}
	return $ret;
}

function xtra_remove_empty_subfolders($path)
{
	$empty=true;
	foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file) {
		if (is_dir($file)) {
			if (!xtra_remove_empty_subfolders($file)) $empty=false;
		}
		else {
			$empty=false;
		}
	}
	if ($empty) rmdir($path);
	return $empty;
}

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @return array $sizes Data for all currently-registered image sizes.
 */
function xtra_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	return $sizes;
}
//---Functions--------------

function xtra_getSizes($size,$dec=2){
	$size_r = '';
	if($size==0){
		$size_r = "0";
	}
	else if($size<1024){
		$size_r = $size. ' B';
	}
	else if($size>=1024 && $size<(1024*1024)){
		$size_r = round($size/(1024),$dec). ' KB';
	}
	else{
		$size_r = round($size/(1024*1024),$dec). ' MB';
	}
	return $size_r;
}

function xtra_table_optimize($tables){
	global $wpdb;
	$status = true;
	$sql_opt = "OPTIMIZE TABLE ".$tables;
	$wpdb->query($sql_opt);
	return $status;
}

function xtra_table_remove($type){
	global $wpdb;
	$status = true;
	if($type==1){
		$sql = "DELETE FROM `$wpdb->posts` WHERE post_type = 'revision'";
		$wpdb->query( $sql );
	}
	else if($type==2){
		$sql = "DELETE FROM `$wpdb->posts` WHERE post_status = 'auto-draft'";
		$wpdb->query( $sql );
	}
	else if($type==3){
		$sql = "DELETE FROM `$wpdb->posts` WHERE post_status = 'trash'";
		$wpdb->query( $sql );
		//$sql = "DELETE asi FROM  `$wpdb->postmeta`  asi LEFT JOIN  `$wpdb->posts`  wp ON wp.ID = asi.post_id WHERE wp.ID IS NULL";
		//$wpdb->query( $sql );
	}
	else if($type==4){
		$sql = "DELETE FROM `$wpdb->comments` WHERE comment_approved = 'spam'";
		$wpdb->query( $sql );
	}
	else if($type==5){
		$sql = "DELETE FROM `$wpdb->comments` WHERE comment_approved = 'trash'";
		$wpdb->query( $sql );
	}
	else if($type==6){
		$sql = "DELETE FROM `$wpdb->options` WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'";
		$wpdb->query( $sql );
		$sql = "DELETE FROM `$wpdb->options` WHERE option_name rlike 'wpseo_sitemap_[0-9]*_cache_validator'";
		$wpdb->query( $sql );
	}
	else if($type==7){
		xtra_purge_transients('7 days',true);
		return true;
	}

	return $status;
}
function xtra_table_remove_count($type){
	global $wpdb;
	if($type==1){
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_type = 'revision'" );
	}
	else if($type==2){
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_status = 'auto-draft'" );
	}
	else if($type==3){
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_status = 'trash'" );
		//$count += $wpdb->get_var( "SELECT COUNT(asi) FROM  `$wpdb->postmeta`  asi LEFT JOIN  `$wpdb->posts`  wp ON wp.ID = asi.post_id WHERE wp.ID IS NULL" );
	}
	else if($type==4){
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM `$wpdb->comments` WHERE comment_approved = 'spam'" );
	}
	else if($type==5){
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM `$wpdb->comments` WHERE comment_approved = 'trash'" );
	}
	else if($type==6){
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM `$wpdb->options` WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'" );
		$count += $wpdb->get_var( "SELECT COUNT(*) FROM `$wpdb->options` WHERE option_name rlike 'wpseo_sitemap_[0-9]*_cache_validator'" );
	}
	else if($type==7){
		return xtra_purge_transients('7 days',false,'count');
	}

	return $count;
}
function xtra_table_remove_show($type){
	global $wpdb;
	if($type==1){
		$resarr = $wpdb->get_results( "SELECT * FROM `$wpdb->posts` WHERE post_type = 'revision'" );
	}
	else if($type==2){
		$resarr = $wpdb->get_results( "SELECT * FROM `$wpdb->posts` WHERE post_status = 'auto-draft'" );
	}
	else if($type==3){
		$resarr = $wpdb->get_results( "SELECT * FROM `$wpdb->posts` WHERE post_status = 'trash'" );
		//$resarr2 = $wpdb->get_results( "SELECT asi FROM  `$wpdb->postmeta`  asi LEFT JOIN  `$wpdb->posts`  wp ON wp.ID = asi.post_id WHERE wp.ID IS NULL" );
	}
	else if($type==4){
		$resarr = $wpdb->get_results( "SELECT * FROM `$wpdb->comments` WHERE comment_approved = 'spam'" );
	}
	else if($type==5){
		$resarr = $wpdb->get_results( "SELECT * FROM `$wpdb->comments` WHERE comment_approved = 'trash'" );
	}
	else if($type==6){
		$resarr = $wpdb->get_results( "SELECT * FROM `$wpdb->options` WHERE option_name LIKE '_site_transient_browser_%' OR option_name LIKE '_site_transient_timeout_browser_%' OR option_name LIKE '_transient_feed_%' OR option_name LIKE '_transient_timeout_feed_%'" );
		$resarr2 = $wpdb->get_results( "SELECT * FROM `$wpdb->options` WHERE option_name rlike 'wpseo_sitemap_[0-9]*_cache_validator'" );
		$resarr = array_merge($resarr,$resarr2);
	}
	else if($type==7){
		return xtra_purge_transients('7 days',false,'show');
	}

	$txt = "";
	foreach ($resarr as $row) {
		foreach ($row as $key=>$fld) {
			if (str_ireplace(array("name","title"),"",$key) != $key || preg_match("#id$#i",$key))
				$txt .= $fld.", ";
			//$txt .= "[$key]=".$fld.", ";
		}
		$txt .= "\n------------------\n";
	}

	return $txt;
}

function xtra_purge_transients($older_than = '7 days', $safemode = true, $just_count_or_show="") {
	global $wpdb;

	$older_than_time = strtotime('-' . $older_than);
	if ($older_than_time > time() || $older_than_time < 1) {
		return false;
	}

	$transients = $wpdb->get_col( $wpdb->prepare( "SELECT REPLACE(option_name, '_transient_timeout_', '') AS transient_name FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_timeout\__%%' AND option_value < %s", $older_than_time) );
	if ($safemode) {
		foreach($transients as $transient) {
			get_transient($transient);
		}
	} else {
		$options_names = array();
		foreach($transients as $transient) {
			$options_names[] = '_transient_' . $transient;
			$options_names[] = '_transient_timeout_' . $transient;
		}
		if ($options_names) {
			$options_names = array_map(array($wpdb, 'escape'), $options_names);
			$options_names = "'". implode("','", $options_names) ."'";

			if ($just_count_or_show=='count') return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name IN ({$options_names})" );
			else if ($just_count_or_show=='show') {
				$resarr = $wpdb->get_results( "SELECT * FROM {$wpdb->options} WHERE option_name IN ({$options_names})" );
				$txt = "";
				foreach ($resarr as $row) {
					foreach ($row as $key=>$fld) {
						if (str_ireplace(array("name","title"),"",$key) != $key || preg_match("#id$#i",$key))
							$txt .= $fld.", ";
					}
					$txt .= "\n------------------\n";
				}
				return $txt;
			}
			else $result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name IN ({$options_names})" );
			if (!$result) {
				return false;
			}
		}
	}

	return 0;
}


//---ob_starts---------------------
function xtra_buffer_start() { ob_start("ob_start_callback"); }
function xtra_buffer_end() { ob_end_flush(); }
add_action('after_setup_theme', 'xtra_buffer_start');
add_action('shutdown', 'xtra_buffer_end');
function ob_start_callback($buffer) {
	if (is_admin()) return $buffer;
	if (get_option( 'xtra_related_posts', 0 )) 					$buffer = xtra_related_posts($buffer);
	if (get_option( 'xtra_share_buttons', 0 )) 					$buffer = xtra_share_buttons($buffer);
	if (get_option( 'xtra_remove_double_title_meta', 0 )) 		$buffer = xtra_remove_double_title_meta($buffer);
	if (get_option( 'xtra_img_alt', 0 )) 						$buffer = xtra_img_alt($buffer);
	if (get_option( 'xtra_minify_html', 0 )) 					$buffer = xtra_minify_html($buffer);
	
	// wonderplugin-slider jsfolder correction
	if (stripos($buffer,'/engine/"')!==FALSE) $buffer = preg_replace(
		'# data-jsfolder=".*?/plugins/wonderplugin-slider(-lite)?/engine/" style="display:none#i',
		' style="display:none',
	$buffer);
	
	return $buffer;
}


if (get_option( 'xtra_minify_html', 0 )) {
	function xtra_minify_html($buffer) {
		$initial = strlen($buffer);

		$single = '{(\s|^)//(.*)$}';
		$buffer = preg_replace($single, '/* \\2 */', $buffer);

		$block = '{/\*[\s\S]*?\*/}';
		$buffer = preg_replace($block, '', $buffer);

		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);
		$replace = array(
			'>',
			'<',
			'\\1'
		);
		$buffer = preg_replace($search, $replace, $buffer);
		$final = strlen($buffer);
		//$savings = round((($initial-$final)/$initial*100), 2);
		return $buffer;
	}
}



// IMG alt
if( get_option('xtra_img_alt') ) {
	function xtra_img_alt( $content ) {
		preg_match('#<title>(.*?)</title>#i',$content,$tit);
		$title = $tit[1];
		if ( strpos($title, get_bloginfo('name'))===FALSE ) $title .= " | " . get_bloginfo('name');
		preg_match_all('/<img (.*?)\/>/i', $content, $images);
		if(!is_null($images)) {
			foreach($images[1] as $index => $value) {
				preg_match('/ alt=["\'](.*?)["\']/i', $value, $img);
				if(!isset($img[1]) || $img[1] == '') {
					$new_img = str_ireplace('<img', '<img alt="'.$title.'"', $images[0][$index]);
					//$new_img = str_ireplace('<img', '<img title="'.$title.'"', $new_img);
					$new_img = str_ireplace(array(' alt=""'," alt=''"), "", $new_img);
					$content = str_ireplace($images[0][$index], $new_img, $content);
				}
			}
		}
		return $content;
	}
	//add_filter( 'the_content', 'xtra_img_alt', 99 );
}


// Remove double title

if( get_option('xtra_remove_double_title_meta') ) {
	function xtra_remove_double_title_meta($buffer) {

		$hed = substr($buffer,0,stripos($buffer,"</head>")+7);
		$buffer = str_replace($hed,'',$buffer);

			$hed = preg_replace( '#(\<title>.*?\</title>)(.*?)(\<title>.*?\</title>)#umis', '$1$2', $hed );

		return $hed.$buffer;
	}
}






//---FILE: wp-config--------------

function xtra_wpconfig( $bool, $optName, $insertion, $remove="" ){
	$file_path = ABSPATH . 'wp-config.php';
	$insertion = xtra_mytrim($insertion);
	$regex_safe_insertion = str_replace(array(".","?","*","+","(",")"),array("\.","\?","\*","\+","\(","\)"),$insertion);
	$remove = '/' . substr($regex_safe_insertion,0,strpos($regex_safe_insertion,",")) . ".*?;" . '/s';
	$curr = xtra_extract_from_markers( $file_path, "XTRA Settings" );
	$currstr = implode("\n",$curr);
	if ($remove) $currstr = preg_replace($remove,"",$currstr); //remove remove
	$currstr = xtra_mytrim($currstr);
	$currarr = array();

	if ($bool) {
		if ($currstr) $currstr .= "\n";
		$currstr .= $insertion; //add insertion
	}
	if ($currstr) $currarr = explode("\n",$currstr);
	$status = xtra_insert_with_markers( $file_path, "XTRA Settings", $currarr );
	if ($status) update_option( $optName, $bool);
	xtra_deactivate_names_add( $optName );
	return $status;
}

function xtra_insert_with_markers( $filename, $marker, $insertion ) {
    if ( ! file_exists( $filename ) ) {
        if ( ! is_writable( dirname( $filename ) ) ) {
            return false;
        }
        if ( ! touch( $filename ) ) {
            return false;
        }
    } elseif ( ! is_writeable( $filename ) ) {
        return false;
    }

    if ( ! is_array( $insertion ) ) {
        $insertion = explode( "\n", $insertion );
    }

    $start_marker = "// BEGIN {$marker}";
    $end_marker   = "// END {$marker}";

    $fp = fopen( $filename, 'r+' );
    if ( ! $fp ) {
        return false;
    }

    // Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
    flock( $fp, LOCK_EX );

    $lines = array();
    while ( ! feof( $fp ) ) {
        $lines[] = rtrim( fgets( $fp ), "\r\n" );
    }

	if ( !in_array($start_marker,$lines) ) { //ne elõre tegyük, hanem hátra!
		$ltxt = implode("\n",$lines);
		$xpos = strpos($ltxt,"<?php");
		if ($xpos!==false) {
			$xpos += 5;
			$ltxt2 = substr($ltxt,0,$xpos) . "\n\n" . $start_marker . "\n" . $end_marker . "\n\n" . substr($ltxt,$xpos);
			$lines = explode("\n",$ltxt2);
		}
	}

    // Split out the existing file into the preceding lines, and those that appear after the marker
    $pre_lines = $post_lines = $existing_lines = array();
    $found_marker = $found_end_marker = false;
    foreach ( $lines as $line ) {
        if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
            $found_marker = true;
            continue;
        } elseif ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
            $found_end_marker = true;
            continue;
        }
        if ( ! $found_marker ) {
            $pre_lines[] = $line;
        } elseif ( $found_marker && $found_end_marker ) {
            $post_lines[] = $line;
        } else {
            $existing_lines[] = $line;
        }
    }

    // Check to see if there was a change
    if ( $existing_lines === $insertion ) {
        flock( $fp, LOCK_UN );
        fclose( $fp );

        return true;
    }

    // Generate the new file data
    $new_file_data = implode( "\n", array_merge(
        $pre_lines,
        array( $start_marker ),
        $insertion,
        array( $end_marker ),
        $post_lines
    ) );

    // Write to the start of the file, and truncate it to that length
    fseek( $fp, 0 );
    $bytes = fwrite( $fp, $new_file_data );
    if ( $bytes ) {
        ftruncate( $fp, ftell( $fp ) );
    }
    fflush( $fp );
    flock( $fp, LOCK_UN );
    fclose( $fp );

    return (bool) $bytes;
}

function xtra_extract_from_markers( $filename, $marker ) {
    $result = array ();

    if (!file_exists( $filename ) ) {
        return $result;
    }

    if ( $markerdata = explode( "\n", implode( '', file( $filename ) ) ));
    {
        $state = false;
        foreach ( $markerdata as $markerline ) {
            if (strpos($markerline, '// END ' . $marker) !== false)
                $state = false;
            if ( $state )
                $result[] = $markerline;
            if (strpos($markerline, '// BEGIN ' . $marker) !== false)
                $state = true;
        }
    }

    return $result;
}




//---FILE: htaccess--------------

function xtra_htacc( $bool, $optName, $insertion ){
	if (!xtra_is_apache()) return true;
	$file_path = ABSPATH . '.htaccess';
	$insertion = xtra_mytrim($insertion);
	$regex_safe_insertion = str_replace(array(".","?","*","+","(",")"),array("\.","\?","\*","\+","\(","\)"),$insertion);
	$remove = "/" . substr($regex_safe_insertion,0,strpos($regex_safe_insertion,"\n")) . ".*?" . '#' . "/s";
//echo "<pre>".$remove."</pre>";
	$curr = extract_from_markers( $file_path, "XTRA Settings" );
	$currstr = implode("\n",$curr);
	if ($remove) $currstr = preg_replace($remove,"",$currstr); //remove remove
//echo "<pre>".$currstr."</pre><hr>";
	$currstr = xtra_mytrim($currstr);
	$currarr = array();

	if ($bool) {
		if ($currstr) $currstr .= "\n";
		$currstr .= $insertion; //add insertion
	}
	if ($currstr) $currarr = explode("\n",$currstr);
	$status = insert_with_markers( $file_path, "XTRA Settings", $currarr );
	if ($status) update_option( $optName, $bool);
	xtra_deactivate_names_add( $optName );
	return $status;
}
function xtra_mytrim($str) {
	$str = str_replace("\r","",$str); //remove \r
	$str = str_replace("\t","",$str); //remove \t
	for($i=1;$i<=3;$i++) $str = str_replace("\n\n","\n",$str); //remove empty lines
	$str = trim($str);
	return $str;
}
function xtra_deactivate_names_add($name) {
	$names = get_option( 'xtra_deactivate_names', array() );
	if (!in_array($name,$names)) update_option( 'xtra_deactivate_names', array_merge($names,array($name)) );
}





//---Security-------


//---Apache Server Hardening

function xtra_dir_index_disable( $bool ){
	$opt = "xtra_dir_index_disable";
	$ins = '#---Disable Apache Dir-Index pages
Options -Indexes
#';
	return xtra_htacc($bool,$opt,$ins);
}

function xtra_protect_xss( $bool ){
//Protect against XSS attacks
	$opt = "xtra_protect_xss";
	$ins = '#---Protect against XSS
<IfModule mod_headers.c>
Header set X-XSS-Protection "1; mode=block"
</IfModule>
#';
	return xtra_htacc($bool,$opt,$ins);
}

function xtra_protect_pageframing( $bool ){
//Protect against page-framing and click-jacking
	$opt = "xtra_protect_pageframing";
	$ins = '#---Protect against page-framing and click-jacking
<IfModule mod_headers.c>
Header always append X-Frame-Options SAMEORIGIN
</IfModule>
#';
	return xtra_htacc($bool,$opt,$ins);
}

function xtra_protect_cotentsniffing( $bool ){
//Protect against content-sniffing
	$opt = "xtra_protect_cotentsniffing";
	$ins = '#---Protect against content-sniffing
<IfModule mod_headers.c>
Header set X-Content-Type-Options nosniff
</IfModule>
#';
	return xtra_htacc($bool,$opt,$ins);
}

function xtra_block_external_post( $bool ){
//Block external POST
	$opt = "xtra_block_external_post";
	$ins = '#---Block external POST
<IfModule mod_rewrite.c>
RewriteCond %{REQUEST_METHOD} POST
RewriteCond %{REQUEST_URI} (wp-comments-post|wp-login)\.php [NC]
RewriteCond %{HTTP_REFERER} !(.*)'.str_ireplace(array("http://","https://"),"",site_url()).' [NC,OR]
RewriteCond %{HTTP_USER_AGENT} ^$
RewriteRule .* - [L]
</IfModule>
#';
	return xtra_htacc($bool,$opt,$ins);
}

function xtra_redir_bots( $bool ){
	$opt = "xtra_redir_bots";
	if ($bool) $txt = stripslashes_deep(sanitize_text_field($_POST[$opt.'_text']));
	$ins = '#---Redirect bots to referring URL
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_URI} wp-login.php [NC,OR]
RewriteCond %{REQUEST_URI} xmlrpc.php [NC]
RewriteCond '.$txt.'
RewriteRule (.*) http://%{REMOTE_ADDR}/ [R=301,L]
</IfModule>
#';
	return xtra_htacc($bool,$opt,$ins);
}


//---WP Security settings

//Remove WordPress Version Number
if (get_option( 'xtra_remove_version_number', 0 )) {
	function xtra_remove_version() {
	return '';
	}
	add_filter('the_generator', 'xtra_remove_version');
	remove_action('wp_head', 'wp_generator');
}

//. Don’t display login errors
if (get_option( 'xtra_suppress_login_errors', 0 )) {
	add_filter('login_errors',create_function('$a', "return null;"));
}

//Disable RSS Feeds
if (get_option( 'xtra_fb_disable_feed', 0 )) {
	function xtra_fb_disable_feed() {
		wp_die( __('No feed available,please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!') );
	}
	add_action('do_feed', 'xtra_fb_disable_feed', 1);
	add_action('do_feed_rdf', 'xtra_fb_disable_feed', 1);
	add_action('do_feed_rss', 'xtra_fb_disable_feed', 1);
	add_action('do_feed_rss2', 'xtra_fb_disable_feed', 1);
	add_action('do_feed_atom', 'xtra_fb_disable_feed', 1);
}

//. Disable XML-RPC
$xtra_xmldis_enable = get_option( 'xtra_disable_xmlrpc', 0 );
if ($xtra_xmldis_enable) add_filter('xmlrpc_enabled', '__return_false');

//. Protect From Malicious URL Requests
if (get_option( 'xtra_protect_from_bad_requests', 0 )) {
	//if (strlen($_SERVER['REQUEST_URI']) > 255 ||
	if (stripos($_SERVER['REQUEST_URI'], "eval(") ||
	stripos($_SERVER['REQUEST_URI'], "CONCAT") ||
	stripos($_SERVER['REQUEST_URI'], "UNION+SELECT") ||
	stripos($_SERVER['HTTP_USER_AGENT'], "libwww")!==FALSE ||
	stripos($_SERVER['HTTP_USER_AGENT'], "Wget")!==FALSE ||
	stripos($_SERVER['HTTP_USER_AGENT'], "EmailSiphon")!==FALSE ||
	stripos($_SERVER['HTTP_USER_AGENT'], "EmailWolf")!==FALSE ||
	stripos($_SERVER['REQUEST_URI'], "base64")) {
		@header("HTTP/1.1 414 Request-URI Too Long");
		@header("Status: 414 Request-URI Too Long");
		@header("Connection: Close");
		@exit;
	}
}




//---Speed-------

//---Apache Compression

function xtra_deflates( $bool ){
	$opt = "xtra_deflates";
	$ins = '#---Enable deflates
<IfModule mod_deflate.c>
AddOutputFilterByType DEFLATE text/plain
AddOutputFilterByType DEFLATE text/html
AddOutputFilterByType DEFLATE text/xml
AddOutputFilterByType DEFLATE text/x-component
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE text/javascript
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE application/x-javascript
AddOutputFilterByType DEFLATE application/xml
AddOutputFilterByType DEFLATE application/xhtml+xml
AddOutputFilterByType DEFLATE application/rss+xml
AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
AddOutputFilterByType DEFLATE application/font-opentype
AddOutputFilterByType DEFLATE application/font-truetype
AddOutputFilterByType DEFLATE application/font-otf
AddOutputFilterByType DEFLATE application/font-ttf
AddOutputFilterByType DEFLATE application/x-font-opentype
AddOutputFilterByType DEFLATE application/x-font-truetype
AddOutputFilterByType DEFLATE application/x-font-otf
AddOutputFilterByType DEFLATE application/x-font-ttf
AddOutputFilterByType DEFLATE font/opentype
AddOutputFilterByType DEFLATE font/truetype
AddOutputFilterByType DEFLATE font/otf
AddOutputFilterByType DEFLATE font/ttf
AddOutputFilterByType DEFLATE image/svg+xml
AddOutputFilterByType DEFLATE image/x-icon
AddOutputFilter       DEFLATE woff woff2
BrowserMatch ^Mozilla/4 gzip-only-text/html
BrowserMatch ^Mozilla/4\.0[678] no-gzip
BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
Header append Vary User-Agent
</IfModule>
#';
	xtra_browserbugs(0);
	return xtra_htacc($bool,$opt,$ins);
}

// deprecated as of 1.4.4 - included in xtra_deflates
function xtra_browserbugs( $bool ){
$bool = false;
	$opt = "xtra_browserbugs";
	$ins = '#---Remove browser bugs (only needed for really old browsers)
<IfModule mod_deflate.c>
BrowserMatch ^Mozilla/4 gzip-only-text/html
BrowserMatch ^Mozilla/4\.0[678] no-gzip
BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
Header append Vary User-Agent
</IfModule>
#';
	return xtra_htacc($bool,$opt,$ins);
}

function xtra_image_expiry( $bool ){
	$opt = "xtra_image_expiry";
	$ins = '#---Image Expiration
<IfModule mod_expires.c>
ExpiresActive On
ExpiresDefault A0
ExpiresByType image/gif "access plus 1 month"
ExpiresByType image/png "access plus 1 month"
ExpiresByType image/jpg "access plus 1 month"
ExpiresByType image/jpeg "access plus 1 month"
ExpiresByType image/ico "access plus 1 month"
ExpiresByType image/x-icon "access plus 1 month"
ExpiresByType image/svg+xml "access plus 1 month"
ExpiresByType text/css "access plus 1 month"
ExpiresByType text/javascript "access plus 1 month"
ExpiresByType application/javascript "access plus 1 month"
ExpiresByType application/x-javascript "access plus 1 month"
ExpiresByType font/truetype "access plus 1 month"
ExpiresByType font/opentype "access plus 1 month"
ExpiresByType application/x-font-woff "access plus 1 month"
ExpiresByType application/vnd.ms-fontobject "access plus 1 month"
<filesMatch "\.(jpg|jpeg|png|gif|js|css|swf|ico|ttf|otf|eot|svg|woff|woff2)$">
    ExpiresActive on
    ExpiresDefault "access plus 1 month"
</filesMatch>
</IfModule>
<IfModule mod_headers.c>
<filesMatch "\.(ico|jpe?g|png|gif|swf)$">
Header set Cache-Control "public"
</filesMatch>
<filesMatch "\.(css)$">
Header set Cache-Control "public"
</filesMatch>
<filesMatch "\.(woff|woff2)$">
Header set Cache-Control "public"
</filesMatch>
<filesMatch "\.(js)$">
Header set Cache-Control "private"
</filesMatch>
<filesMatch "\.(x?html?|php)$">
Header set Cache-Control "private, must-revalidate"
</filesMatch>
</IfModule>
#';
	return xtra_htacc($bool,$opt,$ins);
}

function xtra_remove_etags( $bool ){
	$opt = "xtra_remove_etags";
	$ins = '#---Remove ETags
FileETag none
#';
	return xtra_htacc($bool,$opt,$ins);
}


//---WP Cache Settings

function xtra_WPcache( $bool ){
	$opt = "xtra_WPcache";
	$ins = "define('WP_CACHE', ".(($bool)?"true":"false").");";
	return xtra_wpconfig($bool,$opt,$ins);
}

if (get_option( 'xtra_remove_query_strings', 0 )) {
	function xtra_remove_script_version( $src ){
	$parts = explode( '?', $src );
	return $parts[0];
	}
	add_filter( 'script_loader_src', 'xtra_remove_script_version', 15, 1 );
	add_filter( 'style_loader_src', 'xtra_remove_script_version', 15, 1 );
}


//---Memory and PHP Exec

//. xtra_memory_limit
if (get_option( 'xtra_memory_limit', 0 )) {
	$num = get_option( 'xtra_memory_limit_num', 128 );
	@ini_set( 'memory_limit' , $num.'M' );
}

//. xtra_upload_max_filesize
if (get_option( 'xtra_upload_max_filesize', 0 )) {
	$num = get_option( 'xtra_upload_max_filesize_num', 32 );
	@ini_set( 'upload_max_filesize' , $num.'M' );
	@ini_set( 'upload_max_size' , $num.'M' );
	@ini_set( 'post_max_size', $num.'M');
}

//. xtra_max_execution_time
if (get_option( 'xtra_max_execution_time', 0 )) {
	$num = get_option( 'xtra_max_execution_time_num', 60 );
	@ini_set( 'max_execution_time', $num );
}




//---SEO---

// Add description and keyword meta tag in posts/pages
if( get_option('xtra_meta_description') ) {
	function xtra_meta_description() {
		if (is_admin()) return;
		global $post;
		$exc = xtra_get_excerpt();
		if (!$exc) $exc = get_bloginfo('name')." - ".get_bloginfo('description');
		$exc = htmlspecialchars_decode($exc);

		$tls = xtra_get_taglist();
		if (!$tls) {
			$tlsa = array();
			foreach( explode(' ',$exc ) as $word) {
				if ( preg_match('/([&,\.-]|\&\w+;)/',$word) ) continue;
				if ( !in_array($word,$tlsa) ) $tlsa[] = $word;
			}
			$tls = implode(",", $tlsa);
		}
		$tls = htmlspecialchars_decode($tls);

		echo '<meta name="keywords" content="'.$tls.'" />'."\r\n";
		echo '<meta name="description" content="'.$exc.'" />'."\r\n";
	}
	add_action( 'wp_head', 'xtra_meta_description' );
}

//Redirect 404 to Home
if (get_option( 'xtra_WPTime_redirect_404_to_homepage', 0 )) {
	function xtra_WPTime_redirect_404_to_homepage(){
		if( is_404() ){
			wp_redirect( get_bloginfo('url'), 301 );
			exit();
		}
	}
	add_action('template_redirect', 'xtra_WPTime_redirect_404_to_homepage');
}

//Redirect Attachments to Post
if (get_option( 'xtra_attachment_redirect_to_post', 0 )) {
	function xtra_attachment_redirect_to_post(){
		global $post;
		if ( is_attachment() && isset($post->post_parent) && is_numeric($post->post_parent) && ($post->post_parent != 0) ) {
			wp_redirect( get_permalink( $post->post_parent ), 301 );
			exit();
			wp_reset_postdata();
		}
	}
	add_action('template_redirect', 'xtra_attachment_redirect_to_post');
}

// REL External
if( get_option('xtra_rel_external') ) {
	function xtra_rel_external( $content ) {
		$content = str_replace('target="_self"', '', $content);
		$content = str_replace('target="_blank"', 'rel="external" target="_blank"', $content);
		return $content;
	}
	add_filter( 'the_content', 'xtra_rel_external' );
}

// Defer js parsing
if (get_option( 'xtra_defer_parsing_of_js', 0 )) {
	if (!is_admin()) {
		function xtra_defer_parsing_of_js ( $url ) {
			if ( FALSE === strpos( $url, '.js' ) ) return $url;
			// Exceptions
				if ( strpos( $url, 'jquery.js' )!==FALSE ) return $url;
				if ( preg_match( '#mapsvg(\.min)?\.js#i', $url ) ) return $url;
			return "$url' defer onload='";
		}
		add_filter( 'clean_url', 'xtra_defer_parsing_of_js', 11, 1 );
	}
}

//. Move all JS from header to footer
if (get_option( 'xtra_move_all_js_to_footer', 0 )) {
	if(!is_admin()) {
		function xtra_move_all_js_to_footer() {
			remove_action('wp_head', 'wp_print_scripts');
			remove_action('wp_head', 'wp_print_head_scripts', 9);
			remove_action('wp_head', 'wp_enqueue_scripts', 1);
			add_action('wp_footer', 'wp_print_scripts', 5);
			add_action('wp_footer', 'wp_enqueue_scripts', 5);
			add_action('wp_footer', 'wp_print_head_scripts', 5);
		}
		add_action('wp_enqueue_scripts', 'xtra_move_all_js_to_footer');
	}
}




//---Social---

if( !is_admin() && get_option('xtra_facebook_og_metas') ) {
	function xtra_facebook_og_metas() {
		global $post;
		if( ( is_single() || is_page() ) && !is_front_page() ) {
			?>
<meta property="og:type" content="article">
<meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>">
<link property="og:url" href="<?php echo get_permalink(); ?>">
<meta property="og:title" content="<?php single_post_title(''); ?>">
<meta property="og:description" content="<?php echo xtra_get_excerpt(); ?>">
<meta property="og:image" content="<?php echo xtra_find_img_src(); ?>">
			<?php
		}
		else {
			?>
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>">
<meta property="og:url" content="<?php echo home_url(); ?>">
<meta property="og:title" content="<?php get_bloginfo('name'); ?>">
<meta property="og:description" content="<?php echo mb_substr(get_bloginfo('description'),0,125,'UTF-8'); ?>">
<meta property="og:image" content="<?php echo xtra_find_img_src(); ?>">
			<?php
		}
	}
	add_action('wp_head', 'xtra_facebook_og_metas');
}

if( !is_admin() && get_option('xtra_twitter_metas') ) {
	function xtra_twitter_metas() {
		global $post;
		if( ( is_single() || is_page() ) && !is_front_page() ) {
			?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php single_post_title(''); ?>">
<meta name="twitter:description" content="<?php echo xtra_get_excerpt(); ?>">
<meta name="twitter:site" content="@<?php echo get_option('xtra_twitter_metas_text'); ?>">
<meta name="twitter:image" content="<?php echo xtra_find_img_src(); ?>">
			<?php
		}
		else {
			?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php get_bloginfo('name'); ?>">
<meta name="twitter:description" content="<?php echo mb_substr(get_bloginfo('description'),0,125,'UTF-8'); ?>">
<meta name="twitter:site" content="@<?php echo get_option('xtra_twitter_metas_text'); ?>">
<meta name="twitter:image" content="<?php echo xtra_find_img_src(); ?>">
			<?php
		}
	}
	add_action('wp_head', 'xtra_twitter_metas');
}

if( !is_admin() && get_option('xtra_facebook_sdk') ) {
	function xtra_facebook_sdk() {
		$lang = get_locale();
		if (!$lang) $lang = "en_US";
		if( ( is_single() || is_page() ) && !is_front_page() ) {
			?>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/<?php echo $lang; ?>/sdk.js#xfbml=1&version=v2.6";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
			<?php
		}
	}
	//add_action('wp_head', 'xtra_facebook_sdk');
	add_action('wp_footer', 'xtra_facebook_sdk');
	function xtra_facebook_sdk2() {
		if( ( is_single() || is_page() ) && !is_front_page() ) {
			?>
			<div id="fb-root"></div>
			<?php
		}
	}
	add_action('the_post', 'xtra_facebook_sdk2');
}




//---Share buttons---

if( get_option('xtra_share_buttons') ) {
	function xtra_add_social_share_icons_css() {
		wp_enqueue_style( 'xtra_social_share_buttons_css', plugin_dir_url( __FILE__ ) . 'assets/css/social-share-buttons.css' );
	}
	add_action( 'wp_enqueue_scripts', 'xtra_add_social_share_icons_css' );
	function xtra_stylesheet_installed($array_css)
	{
		global $wp_styles;
		foreach( $wp_styles->queue as $style )
		{
			foreach ($array_css as $css)
			{
				if (false !== strpos( $wp_styles->registered[$style]->src, $css ))
					return 1;
			}
		}
		return 0;
	}
	function xtra_add_fa_css(){
		global $xtra_fa;
		$font_awesome = array('font-awesome', 'fontawesome');
		$xtra_fa = false;
		if (xtra_stylesheet_installed($font_awesome) === 0) {
			 wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
		}
		if (xtra_stylesheet_installed($font_awesome) !== 0)
			$xtra_fa = true;
	}
	add_action( 'wp_enqueue_scripts', 'xtra_add_fa_css', 99 );

	function xtra_get_social_share_icons() {
		global $xtra_fa;
		$tit = get_option('xtra_share_buttons_text', 'Share on:');
		$zoom = get_option('xtra_share_buttons_num', 100);
		$shape = get_option('xtra_share_buttons_shape', 0);
		if ($shape==0) $sty = "border-radius: 0px;";
		if ($shape==1) $sty = "border-radius: 5px;";
		if ($shape==2) $sty = "border-radius: 50%;";

		$html = "<div class='xtra-social-share-wrap'>";
		$html .= "<h3>$tit</h3>";
		global $post;
		$url = get_permalink($post->ID);
		$url = esc_url($url);
		$shares = array(
			'facebook'		=>	array('Facebook',	'fa-facebook',		'http://www.facebook.com/sharer.php?u='.$url),
			'twitter'		=>	array('Twitter',	'fa-twitter',		'https://twitter.com/share?url='.$url),
			'linkedin'		=>	array('LinkedIn',	'fa-linkedin',		'http://www.linkedin.com/shareArticle?url='.$url),
			'pinterest'		=>	array('Pinterest',	'fa-pinterest',		"javascript:void((function()%7Bvar%20e=document.createElement(\"script\");e.setAttribute(\"type\",\"text/javascript\");e.setAttribute(\"charset\",\"UTF-8\");e.setAttribute(\"src\",\"//assets.pinterest.com/js/pinmarklet.js?r=\"+Math.random()*99999999);document.body.appendChild(e)%7D)());"),
			'tumblr'		=>	array('tumblr',		'fa-tumblr',		'http://www.tumblr.com/share/link?url='.$url),
			'gplus'			=>	array('Google+',	'fa-google-plus',	'https://plus.google.com/share?url='.$url),
			'reddit'		=>	array('Reddit',		'fa-reddit',		'http://reddit.com/submit?url='.$url),
			'buffer'		=>	array('Buffer',		'fa-share-alt',		'https://bufferapp.com/add?url='.$url),
		);
		foreach ($shares as $slg=>$set) {
			if (get_option('xtra_share_'.$slg))
				$html .= "
					<a target='_blank' title='".$set[0]."' href='".$set[2]."'>
						<div class='".$slg."' style='zoom:".$zoom."% !important;".$sty."'>
							<span>".($xtra_fa?"<i style='zoom:101% !important;' class='fa ".$set[1]."'></i>":$set[0])."</span>
						</div>
					</a>";
		}
		$html .= "<div class='clear'></div></div>";
		return $html;
	}

	function xtra_share_buttons($buffer) {
		if (!is_singular()) return $buffer;
		if (is_front_page() && !get_option('xtra_share_buttons_homepage')) return $buffer;
		if (is_page() && !get_option('xtra_share_buttons_pages')) return $buffer;
		if (is_single() && !get_option('xtra_share_buttons_posts')) return $buffer;
		
		$plac = get_option('xtra_share_buttons_place', 0);
		if (!$plac) return $buffer;

		$hed = substr($buffer,0,stripos($buffer,"</head>")+7);
		$buffer = str_replace($hed,'',$buffer);

		$html = xtra_get_social_share_icons();
		$html = utf8_encode($html); // $html comes from ansi text (this file)
		
		if ($plac == 1) { //after text
			$buffer = preg_replace( array(
				'#</div>\s*</article>(?!.*</div>\s*</article>)#umis',
			), array( $html . '</div></article>' ), $buffer, 1 ); //only the last occurence: negative lookahead (?!...)
		}
		else if ($plac == 2) { //before text
			$buffer = preg_replace( array(
				'#(\<div[^>]*?entry-content)#umi',
			), array( $html . '$1' ), $buffer, 1 ); //only the 1st occurence
		}
		else if ($plac == 3) { //before title
			$buffer = preg_replace( array(
				'#(\<header[^>]*?entry-header)#umi',
			), array( $html . '$1' ), $buffer, 1 ); //only the 1st occurence
		}
		else if ($plac == 4) { //after atricle
			if (preg_match('#\<nav[^>]*?nav-below.*?\</nav>#umis',$buffer)) {
				$buffer = preg_replace( array(
					'#(\<nav[^>]*?nav-below.*?\</nav>)#umis',
				), array( '$1' . $html ), $buffer, 1 ); //only the 1st occurence
			}
			else {
				$buffer = preg_replace( array(
					'#</div>\s*</article>(?!.*</div>\s*</article>)#umis',
				), array( '</div></article>' . $html ), $buffer, 1 ); //only the last occurence: negative lookahead (?!...)
			}
		}
		else if ($plac == 5) { //shortcode: [xtra_share_buttons]
			$buffer = preg_replace( array(
				'#\[xtra_share_buttons\]#umi',
			), array( $html ), $buffer, 1 ); //only the 1st occurence
		}
		return $hed.$buffer;
	}
}




//---WP Settings---

//Remove admin bar on front-end
if (get_option( 'xtra_remove_admin_bar', 0 )) {
	add_action('after_setup_theme', 'xtra_remove_admin_bar');
	function xtra_remove_admin_bar() {
		if ( !is_admin() ) {
			show_admin_bar(false);
			if ( get_option( 'xtra_remove_admin_bar_excl_adm', 0 ) && current_user_can('administrator') )
				show_admin_bar(true);
			if ( get_option( 'xtra_remove_admin_bar_excl_edt', 0 ) && current_user_can('editor') )
				show_admin_bar(true);
			if ( get_option( 'xtra_remove_admin_bar_excl_aut', 0 ) && current_user_can('author') )
				show_admin_bar(true);
		}
	}
}

//Check the Remember Me checkbox automatically:
if (get_option( 'xtra_login_checked_remember_me', 0 )) {
	function xtra_login_checked_remember_me() {
		add_filter( 'login_footer', 'xtra_rememberme_checked' );
	}
	add_action( 'init', 'xtra_login_checked_remember_me' );
	function xtra_rememberme_checked() {
		echo "<script>document.getElementById('rememberme').checked = true;</script>";
	}
}

//Set auth cookie expiration for Remember Me
if (get_option( 'xtra_keep_me_logged_in_for', 0 )) {
	add_filter( 'auth_cookie_expiration', 'xtra_keep_me_logged_in_for' );
	function xtra_keep_me_logged_in_for( $expirein ) {
		$days = get_option( 'xtra_keep_me_logged_in_for_text', '60' );
		return ($days*24*60*60); // 60 days in seconds
	}
}

// Change default WP email sender name and address
if (get_option( 'xtra_doEmailNameFilter', 0 )) {
	add_filter('wp_mail_from_name', 'xtra_doEmailNameFilter');
	function xtra_doEmailNameFilter($email_from){
		$text = get_option( 'xtra_doEmailNameFilter_text', '' );
		if($text && $email_from === "WordPress")
			return $text;
		else
			return $email_from;
	}
}
if (get_option( 'xtra_doEmailFilter', 0 )) {
	add_filter('wp_mail_from', 'xtra_doEmailFilter');
	function xtra_doEmailFilter($email_address){
		$text = get_option( 'xtra_doEmailFilter_text', '' );
		if($text && $email_address === "wordpress@kompozitor.hu")
			return $text;
		else
			return $email_address;
	}
}

//. xtra_remove_WPemoji
//As of WordPress 4.2, by default WordPress includes support for Emojis. Great if that is your cup of tea, but if not, you might want to remove the additional resources Emoji support adds to your webpages.
if (get_option( 'xtra_remove_WPemoji', 0 )) {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7);
	remove_action( 'wp_print_styles', 'print_emoji_styles');
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}

//custom jpeg quality
if (get_option( 'xtra_custom_jpeg_quality', 0 )) {
	function xtra_custom_jpeg_quality( $quality, $context ) {
		$num = get_option( 'xtra_custom_jpeg_quality_num', 85 );
		return $num;
	}
	add_filter( 'jpeg_quality', 'xtra_custom_jpeg_quality', 10, 2 );
}

//---Auto-Resize Image Uploads---
if( get_option('xtra_auto_resize_upload') ) {
	function xtra_handle_upload($params) {
		$maxW = get_option('xtra_auto_resize_upload_num',0);
		$maxH = get_option('xtra_auto_resize_upload_pnum',0);
		$xtra_crop_image = false;
		$force_keep_new_file_even_if_bigger = true;
		
		if ( $params['type'] == 'image/bmp' && $xtra_bmp_to_jpg ) {
			$params = xtra_convert_to_jpg( 'bmp', $params );
		}
		if ( $params['type'] == 'image/png' && $xtra_png_to_jpg ) {
			$params = xtra_convert_to_jpg( 'png', $params );
		}
		$oldPath = $params['file'];
		if ( ( ! is_wp_error( $params ) ) && is_writable( $oldPath ) && in_array( $params['type'], array( 'image/png', 'image/gif', 'image/jpeg' ) ) ) {
			list( $oldW, $oldH ) = getimagesize( $oldPath );
			if ( ( $oldW > $maxW && $maxW > 0 ) || ( $oldH > $maxH && $maxH > 0 ) ) {
				$quality = get_option('xtra_custom_jpeg_quality_num',82);
				if ( $oldW > $maxW && $maxW > 0 && $oldH > $maxH && $maxH > 0 && $xtra_crop_image ) {
					$newW = $maxW;
					$newH = $maxH;
				} else {
					list( $newW, $newH ) = wp_constrain_dimensions( $oldW, $oldH, $maxW, $maxH );
				}
				remove_filter( 'wp_image_editors', 'ewww_image_optimizer_load_editor', 60 );
				$resizeResult = xtra_wpeditor_image_resize( $oldPath, $quality, $newW, $newH, $xtra_crop_image, null, null );
				if ( function_exists( 'ewww_image_optimizer_load_editor' ) ) {
					add_filter( 'wp_image_editors', 'ewww_image_optimizer_load_editor', 60 );
				}
				if ( $resizeResult && ! is_wp_error( $resizeResult ) ) {
					$newPath = $resizeResult;
					if ( is_file( $newPath ) ) {
						if ( $force_keep_new_file_even_if_bigger || filesize( $newPath ) <  filesize( $oldPath ) ) {
							// we saved some file space. remove original and replace with resized image
							unlink( $oldPath );
							rename( $newPath, $oldPath );
						} else {
							// the resized image is actually bigger in filesize (most likely due to jpg quality).
							// keep the old one and just get rid of the resized image
							unlink( $newPath );
						}
					}
					else {
						// file not found, resize didn't work
						// remove the old image so we don't leave orphan files hanging around
						unlink( $oldPath );
						
						$params = wp_handle_upload_error( $oldPath ,
							sprintf( esc_html__( "XTRA was unable to resize this image for an unknown reason. If you think you have discovered a bug, please report it on the XTRA support forum: %s", 'xtra' ), 'https://wordpress.org/support/plugin/xtra-settings' ) );

					}
				} else if ( $resizeResult === false ) {
					return $params;
				} else {
					// resize didn't work, likely because the image processing libraries are missing
					// remove the old image so we don't leave orphan files hanging around
					unlink( $oldPath );
					
					$params = wp_handle_upload_error( $oldPath ,
						sprintf( esc_html__( "XTRA was unable to resize this image for the following reason: %s. If you continue to see this error message, you may need to install missing server library components. If you think you have discovered a bug, please report it on the XTRA support forum: %s", 'xtra' ), $resizeResult->get_error_message(), 'https://wordpress.org/support/plugin/xtra-settings' ) );

				}
			}
		}
		return $params;
	}
	add_filter( 'wp_handle_upload', 'xtra_handle_upload' );
	function xtra_convert_to_jpg( $type, $params )
	{
		$img = null;
		if ( $type == 'bmp' ) {
			$img = imagecreatefrombmp( $params['file'] );
		} elseif ( $type == 'png' ) {
			if( ! function_exists( 'imagecreatefrompng' ) ) {
				return wp_handle_upload_error( $params['file'], esc_html__( 'XTRA requires the GD library to convert PNG images to JPG', 'xtra' ) );
			}
			$img = imagecreatefrompng( $params['file'] );
			// convert png transparency to white
			$bg = imagecreatetruecolor( imagesx( $img ), imagesy( $img ) );
			imagefill( $bg, 0, 0, imagecolorallocate( $bg, 255, 255, 255 ) );
			imagealphablending( $bg, TRUE );
			imagecopy($bg, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
		}
		else {
			return wp_handle_upload_error( $params['file'], esc_html__( 'Unknown image type specified in xtra_convert_to_jpg', 'xtra' ) );
		}
		// we need to change the extension from the original to .jpg so we have to ensure it will be a unique filename
		$uploads = wp_upload_dir();
		$oldFileName = basename($params['file']);
		$newFileName = basename(str_ireplace(".".$type, ".jpg", $oldFileName));
		$newFileName = wp_unique_filename( $uploads['path'], $newFileName );
		
		$quality = get_option('xtra_custom_jpeg_quality_num',82);
		
		if ( imagejpeg( $img, $uploads['path'] . '/' . $newFileName, $quality ) ) {
			// conversion succeeded.  remove the original bmp/png & remap the params
			unlink($params['file']);
			$params['file'] = $uploads['path'] . '/' . $newFileName;
			$params['url'] = $uploads['url'] . '/' . $newFileName;
			$params['type'] = 'image/jpeg';
		}
		else {
			unlink($params['file']);
			return wp_handle_upload_error( $oldPath,
					sprintf( esc_html__( "XTRA was unable to process the %s file. If you continue to see this error you may need to disable the conversion option in the XTRA settings.", 'xtra' ), $type ) );
		}
		return $params;
	}
}

//---WP-Auto-update-------

//. All Auto-update
$xtra_all_autoupdate = get_option( 'xtra_all_autoupdate', 0 );
if ($xtra_all_autoupdate==1) {
	add_filter( 'automatic_updater_disabled', '__return_false' );
}
if ($xtra_all_autoupdate==-1) {
	add_filter( 'automatic_updater_disabled', '__return_true' );
	add_filter('pre_site_transient_update_core','xtra_remove_core_updates');
	add_filter('pre_site_transient_update_plugins','xtra_remove_core_updates');
	add_filter('pre_site_transient_update_themes','xtra_remove_core_updates');
}

//. core Auto-update
/*
$xtra_core_autoupdate = get_option( 'xtra_core_autoupdate', 0 );
if ($xtra_core_autoupdate==1) {
	add_filter( 'auto_update_core', '__return_true' );
}
if ($xtra_core_autoupdate==-1) {
	add_filter( 'auto_update_core', '__return_false' );
	add_filter('pre_site_transient_update_core','xtra_remove_core_updates');
}
*/

//. core-major Auto-update
$xtra_core_autoupdate_any = 0;
if (get_option( 'xtra_core_autoupdate_major', 0 ) == 1) {
	add_filter( 'allow_major_auto_core_updates', '__return_true' );
	$xtra_core_autoupdate_any++;
}
if (get_option( 'xtra_core_autoupdate_major', 0 ) == -1) {
	add_filter( 'allow_major_auto_core_updates', '__return_false' );
}
//. core-minor Auto-update
if (get_option( 'xtra_core_autoupdate_minor', 0 ) == 1) {
	add_filter( 'allow_minor_auto_core_updates', '__return_true' );
	$xtra_core_autoupdate_any++;
}
if (get_option( 'xtra_core_autoupdate_minor', 0 ) == -1) {
	add_filter( 'allow_minor_auto_core_updates', '__return_false' );
}
//. core-dev Auto-update
if (get_option( 'xtra_core_autoupdate_dev', 0 ) == 1) {
	add_filter( 'allow_dev_auto_core_updates', '__return_true' );
	$xtra_core_autoupdate_any++;
}
if (get_option( 'xtra_core_autoupdate_dev', 0 ) == -1) {
	add_filter( 'allow_dev_auto_core_updates', '__return_false' );
}
if (!$xtra_core_autoupdate_any) {
	add_filter('pre_site_transient_update_core','xtra_remove_core_updates');
}

//. plugin Auto-update
$xtra_plugin_autoupdate = get_option( 'xtra_plugin_autoupdate', 0 );
if ($xtra_plugin_autoupdate==1) {
	function xtra_auto_update_specific_plugins ( $update, $item ) {
		// Array of plugin slugs to exclude from auto-update
		$plugins = array (
			'akismet',
			'buddypress',
		);
		$plugins = get_option( 'xtra_plugins_exclude', array() );
		if ( in_array( $item->slug, $plugins ) ) {
			//return true; // Always update plugins in this array
			return false; // Never update plugins in this array
		} else {
			//return $update; // Else, use the normal API response to decide whether to update or not
			return true; // Always update plugins in this array
		}
	}
	add_filter( 'auto_update_plugin', 'xtra_auto_update_specific_plugins', 10, 2 );
//	add_filter( 'auto_update_plugin', '__return_true' );
}
if ($xtra_plugin_autoupdate==-1) {
	add_filter( 'auto_update_plugin', '__return_false' );
	add_filter('pre_site_transient_update_plugins','xtra_remove_core_updates');
}

//. theme Auto-update
$xtra_theme_autoupdate = get_option( 'xtra_theme_autoupdate', 0 );
if ($xtra_theme_autoupdate==1) {
	add_filter( 'auto_update_theme', '__return_true' );
}
if ($xtra_theme_autoupdate==-1) {
	add_filter( 'auto_update_theme', '__return_false' );
	add_filter('pre_site_transient_update_themes','xtra_remove_core_updates');
}

//. translation Auto-update
$xtra_translation_autoupdate = get_option( 'xtra_translation_autoupdate', 0 );
if ($xtra_translation_autoupdate==1) {
	add_filter( 'auto_update_translation', '__return_true' );
}
if ($xtra_translation_autoupdate==-1) {
	add_filter( 'auto_update_translation', '__return_false' );
}

function xtra_remove_core_updates(){
	global $wp_version;
	return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,'updates' => array());
}




//---Maintenance mode---

//Maintenance mode
if (get_option( 'xtra_maintenance', 0 )) {
	// Activate WordPress Maintenance Mode
	function xtra_maintenance(){
		if(!current_user_can('edit_themes') || !is_user_logged_in()){
			$title = get_option( 'xtra_maintenance_title', 'Website under Maintenance' );
			$text = get_option( 'xtra_maintenance_text', 'We are performing scheduled maintenance. We will be back online shortly!' );
			wp_die('<h1 style="color:red">'.$title.'</h1><br />'.$text);
		}
	}
	add_action('get_header', 'xtra_maintenance');
}




//---Debug mode-------

function xtra_debug( $bool ){
	$opt = "xtra_debug";
	$ins = "define('WP_DEBUG', ".(($bool)?"true":"false").");";
	return xtra_wpconfig($bool,$opt,$ins);
}
if (get_option( 'xtra_debug', 0 )) {
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

function xtra_disable_debug_display( $bool ){
	$opt = "xtra_disable_debug_display";
	$ins = "define('WP_DEBUG_DISPLAY', ".((!$bool)?"true":"false").");";
	return xtra_wpconfig($bool,$opt,$ins);
}
if (get_option( 'xtra_disable_debug_display', 0 )) {
	@ini_set( 'display_errors', false );
}

function xtra_debug_log( $bool ){
	$opt = "xtra_debug_log";
	$ins = "define('WP_DEBUG_LOG', ".(($bool)?"true":"false").");";
	return xtra_wpconfig($bool,$opt,$ins);
}

if ( !defined( WP_DEBUG ) || !WP_DEBUG ) {
	if ( file_exists(WP_CONTENT_DIR ."/debug.log") ) unlink( WP_CONTENT_DIR ."/debug.log" );
}




//---Posts---


//---Posting

//Revisions
if (get_option( 'xtra_revisions_to_keep', 0 )) {
	function xtra_revisions_to_keep( $revisions, $post ) {
		$pnum = get_option( 'xtra_revisions_to_keep_pnum', 99 );
		$num = get_option( 'xtra_revisions_to_keep_num', 99 );

		if ( 'page' == $post->post_type )
			return $pnum;
		else
			return $num;
	}
	add_filter( 'wp_revisions_to_keep', 'xtra_revisions_to_keep', 10, 2 );
}

function xtra_autosave_interval( $bool ){
	$opt = "xtra_autosave_interval";
	if ($bool) $txt = stripslashes_deep(sanitize_text_field($_POST[$opt.'_num']));
	$ins = "define('AUTOSAVE_INTERVAL', $txt);";
	return xtra_wpconfig($bool,$opt,$ins);
}

function xtra_empty_trash( $bool ){
	$opt = "xtra_empty_trash";
	if ($bool) $txt = stripslashes_deep(sanitize_text_field($_POST[$opt.'_num']));
	$ins = "define('EMPTY_TRASH_DAYS', $txt);";
	return xtra_wpconfig($bool,$opt,$ins);
}

//. Require a Featured Image
if (get_option( 'xtra_require_featured_image', 0 )) {
	//add_action('save_post', 'xtra_check_thumbnail');
	add_action('publish_post', 'xtra_check_thumbnail');
	add_action('admin_notices', 'xtra_thumbnail_error');
	function xtra_check_thumbnail($post_id) {
		// change to any custom post type
		if(get_post_type($post_id) != 'post')
			return;
		if ( !has_post_thumbnail( $post_id ) ) {
			// set a transient to show the users an admin message
			set_transient( "has_post_thumbnail", "no" );
			// unhook this function so it doesn't loop infinitely
			//remove_action('save_post', 'xtra_check_thumbnail');
			remove_action('publish_post', 'xtra_check_thumbnail');
			// update the post set it to draft
			wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));
			//add_action('save_post', 'xtra_check_thumbnail');
			add_action('publish_post', 'xtra_check_thumbnail');
		} else {
			delete_transient( "has_post_thumbnail" );
		}
	}
	function xtra_thumbnail_error()
	{
		// check if the transient is set, and display the error message
		if ( get_transient( "has_post_thumbnail" ) == "no" ) {
			echo "<div id='message' class='error'><p><strong>You must select Featured Image. Your Post is saved but it can not be published.</strong></p></div>";
			delete_transient( "has_post_thumbnail" );
		}
	}
}

if (get_option( 'xtra_auto_featured_image', 0 )) {
	function xtra_auto_featured_image() {
		global $post;
		$already_has_thumb = has_post_thumbnail($post->ID);
		if (!$already_has_thumb) {
			$attachments = get_children(array(
				'post_parent' => $post->ID, 
				'post_status' => 'inherit', 
				'post_type' => 'attachment', 
				'post_mime_type' => 'image', 
				'order' => 'ASC', 
				'orderby' => 'menu_order'
			));
			if ($attachments) {
				foreach ($attachments as $attachment) {
					set_post_thumbnail($post->ID, $attachment->ID);
					break;
				}
			}
		}
	}
	add_action('the_post', 'xtra_auto_featured_image');
	add_action('save_post', 'xtra_auto_featured_image');
	add_action('draft_to_publish', 'xtra_auto_featured_image');
	add_action('new_to_publish', 'xtra_auto_featured_image');
	add_action('pending_to_publish', 'xtra_auto_featured_image');
	add_action('future_to_publish', 'xtra_auto_featured_image');
}

//Allow PHP in Default Text Widgets
if (get_option( 'xtra_php_in_textwidgets', 0 )) {
	add_filter('widget_text', 'xtra_php_in_textwidgets', 99);
	function xtra_php_in_textwidgets($text) {
		if (strpos($text, '<' . '?') !== false) {
			ob_start();
			eval('?' . '>' . $text);
			$text = ob_get_contents();
			ob_end_clean();
		}
		return $text;
	}
}

//. Enable shortcodes in text widgets
if (get_option( 'xtra_shortcode_in_textwidgets', 0 )) {
	add_filter('widget_text','do_shortcode');
}

//Disallow Duplicate Post Titles
if (get_option( 'xtra_disallow_duplicate_posttitles', 0 )) {
	function xtra_disallow_duplicate_posttitles($messages) {
		global $post;
		global $wpdb ;
		$title = $post->post_title;
		$post_id = $post->ID ;
		$wtitlequery = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_title = '{$title}' AND ID != {$post_id} " ;

		$wresults = $wpdb->get_results( $wtitlequery) ;

		if ( $wresults ) {
			$error_message = 'This title is already used. Post cannot be published. Please choose another';
			add_settings_error('post_has_links', '', $error_message, 'error');
			settings_errors( 'post_has_links' );
			$post->post_status = 'draft';
			wp_update_post($post);
			return;
		}
		return $messages;

	}
	//add_action('post_updated_messages', 'xtra_disallow_duplicate_posttitles');
	add_action('publish_post', 'xtra_disallow_duplicate_posttitles');
}


//---HTML Content Changes

//Open ALL links in a new tab
if (get_option( 'xtra_link_new_tab', 0 )) {
	function xtra_link_new_tab($text) {
		$surl = home_url();
		$return = $text;
		$return = str_ireplace('target="_blank"', 				'', 						$return);
		$return = str_ireplace('href=', 						'target="_blank" href=', 	$return);
		$return = str_ireplace('target="_blank" href="'.$surl, 	'href="'.$surl, 			$return);
		$return = str_ireplace('target="_blank" href=\''.$surl, 'href=\''.$surl, 			$return);
		$return = str_ireplace('target="_blank" href="#', 		'href="#', 					$return);
		$return = str_ireplace('target="_blank" href=\'#', 		'href=\'#', 				$return);
		//$return = str_ireplace(' target="_blank">', 			'>', 						$return);
		return $return;
	}
	add_filter('the_content', 'xtra_link_new_tab', 19);
	add_filter('comment_text', 'xtra_link_new_tab', 19);
}

//Add self-link to all images
if (get_option( 'xtra_attachment_image_link_filter', 0 )) {
	add_filter( 'the_content', 'xtra_attachment_image_link_filter', 8 );
	function xtra_attachment_image_link_filter( $content ) {
		if (is_admin()) return $content;
		if (is_front_page()) return $content;
		if (!is_singular()) return $content;
		if (get_the_title() == 'Posts') return $content;

	//	$upload_dir = XTRA_UPLOAD_DIR;
	//	$upld = $upload_dir.'/';
	//	$upld = str_ireplace(ABSPATH,site_url()."/",$upld);
		$upload_dir = XTRA_UPLOAD_URL;
		$upld = $upload_dir.'/';
	
		$upld = ""; //for ALL images - not just the ones in upload_dir
		
		$content = preg_replace(
				'#<a[^>]*?href=[^>]*("|\')[^>]*>\s*<img([^>]*)src="([^>]*)'.$upld.'(.*?)</a>#umis',
				'<img$2src="$3'.$upld.'$4',
		$content);
		$content = preg_replace(
			'#<img([^>]*)src="([^>]*)'.$upld.'([^>]*)("|\')([^>]*)>#umis',
			'<a href="$2'.$upld.'$3" data-rel="lightbox-gallery-postimages"><img$1src="$2'.$upld.'$3"$5></a>',
		$content);
		return $content;
	}
}

// Highlight Post Color by Status
if (get_option( 'xtra_posts_status_color', 0 )) {
	function xtra_posts_status_color() {
		$html = '
			<style>
			.status-draft{background:'.get_option('xtra_posts_status_color1','#FCE3F2').' !important;}
			.status-pending{background:'.get_option('xtra_posts_status_color2','#f2e0ab').' !important;}
			.status-future{background:'.get_option('xtra_posts_status_color3','#C6EBF5').' !important;}
			.status-private{background:'.get_option('xtra_posts_status_color4','#b49de0').' !important;}
			.status-publish{/* no background keep wp alternating colors */}
			</style>
		';
		echo $html;
		return;
	}
	add_action('admin_footer','xtra_posts_status_color');
}

if( get_option('xtra_notify_author_on_publish') ) {
	function xtra_notify_author_on_publish($post_id){
		$post_author_id = get_post_field( 'post_author', $post_id );
		$email_address = get_the_author_meta('user_email', $post_author_id);

		$subject = get_option('xtra_notify_author_on_publish_s_text','Your post is published.');
		$body = get_option('xtra_notify_author_on_publish_text','Thank you for your submission!');

		wp_mail($email_address, $subject, $body);
	}
	add_action('publish_post','xtra_notify_author_on_publish');
}


//---Related posts---

if( get_option('xtra_related_posts') ) {
	function xtra_add_related_posts_css() {
		wp_enqueue_style( 'xtra_related_posts_css', plugin_dir_url( __FILE__ ) . 'assets/css/related-posts.css' );
	}
	add_action( 'wp_enqueue_scripts', 'xtra_add_related_posts_css' );
	
	function xtra_get_related_posts($num=4,$title="Related Posts",$size=150) {
	// Related Posts: Based on current post's category, tags. And it shouldnt show the current post.
		global $post;
		$postcat = get_the_category( $post->ID ); 	// Geting the current post's category lists if exists.
		$category_ids = $postcat[0]->term_id;
		$excl_cats = get_option('xtra_categories_exclude', array());
		$post_tags = wp_get_post_tags($post->ID, array( 'fields' => 'ids' ));	// Geting the current post's tags lists if exists.
		$related_post = array(
			'post_type' =>'post',
			'category__in' => $category_ids,
			'category__not_in' => $excl_cats,
			'tag__in' => $post_tags,
			'posts_per_page'=>$num,
			'orderby' => 'rand',
			'post__not_in' => array($post->ID)
		);
		$the_query_related_post = new WP_Query( $related_post );
		if ( $the_query_related_post->found_posts < $num ) {
			$related_post = array(
				'post_type' =>'post',
				'category__in' => $category_ids,
				'category__not_in' => $excl_cats,
				'posts_per_page'=>$num,
				'orderby' => 'rand',
				'post__not_in' => array($post->ID)
			);
			$the_query_related_post = new WP_Query( $related_post );
		}
		global $wp_query;
		$tmp_query = $wp_query;	// Put default query object in a temp variable
		$wp_query = null;	// Now wipe it out completely
		$wp_query = $the_query_related_post;	// Re-populate the global with our custom query

		if ( $the_query_related_post->have_posts() ) {
			$html .= '
			<div class="xtra_rp_wrap">
				<div class="xtra_rp_content">
					<h3 class="related_post_title">'.$title.'</h3>
					<ul class="xtra_rp">
			';
			while ( $the_query_related_post->have_posts() ) {
				$the_query_related_post->the_post();
				$thumb = get_the_post_thumbnail($the_query_related_post->ID, array($size,$size));
				$altt = get_the_title();
				preg_match('/ title=["\'](.*?)["\']/i', $thumb, $img);
				if(!isset($img[1]) || $img[1] == '') $thumb = str_ireplace('<img','<img title="'.$altt.'"',$thumb);
				$thumb = str_ireplace(array(' title=""'," title=''"), "", $thumb);
				if ( strpos($altt, get_bloginfo('name'))===FALSE ) $altt .= " | " . get_bloginfo('name');				
				preg_match('/ alt=["\'](.*?)["\']/i', $thumb, $img);
				if(!isset($img[1]) || $img[1] == '') $thumb = str_ireplace('<img','<img alt="'.$altt.'"',$thumb);
				$thumb = str_ireplace(array(' alt=""'," alt=''"), "", $thumb);
				$html .= '
				<li>
					<a href="'.get_the_permalink().'">'.$thumb.'</a>
					<a href="'.get_the_permalink().'" class="xtra_rp_title" style="max-width:'.$size.'px !important;">'.get_the_title().'</a>
				</li>
				';
			}
			$html .= '
					</ul>
				</div>
			</div>
			';
			wp_reset_postdata();
		}
		
		// Restore original query object
		$wp_query = null;
		$wp_query = $tmp_query;
		return $html;
	}

	function xtra_related_posts($buffer) {
		if (!is_singular()) return $buffer;
		if (is_front_page() && !get_option('xtra_related_posts_homepage')) return $buffer;
		if (is_page() && !get_option('xtra_related_posts_pages')) return $buffer;
		if (is_single() && !get_option('xtra_related_posts_posts')) return $buffer;

		$plac = get_option('xtra_related_posts_place', 0);
		$num = get_option('xtra_related_posts_num', 0);
		$tit = get_option('xtra_related_posts_text', '');
		$siz = get_option('xtra_related_posts_size_num', 150);
		if (!$plac) return $buffer;
		if (!$num) return $buffer;
		
		$hed = substr($buffer,0,stripos($buffer,"</head>")+7);
		$buffer = str_replace($hed,'',$buffer);
		
		$html = xtra_get_related_posts($num,$tit,$siz);
		//$html = utf8_encode($html); // $html comes from UTF-8 text (database)

		if ($plac == 1) { //after text
			$buffer = preg_replace( array(
				'#</div>\s*</article>(?!.*</div>\s*</article>)#umis',
			), array( $html . '</div></article>' ), $buffer, 1 ); //only the last occurence: negative lookahead (?!...)
		}
		else if ($plac == 2) { //before text
			$buffer = preg_replace( array(
				'#(\<div[^>]*?entry-content)#umi',
			), array( $html . '$1' ), $buffer, 1 ); //only the 1st occurence
		}
		else if ($plac == 3) { //before title
			$buffer = preg_replace( array(
				'#(\<header[^>]*?entry-header)#umi',
			), array( $html . '$1' ), $buffer, 1 ); //only the 1st occurence
		}
		else if ($plac == 4) { //after atricle
			if (preg_match('#\<nav[^>]*?nav-below.*?\</nav>#umis',$buffer)) {
				$buffer = preg_replace( array(
					'#(\<nav[^>]*?nav-below.*?\</nav>)#umis',
				), array( '$1' . $html ), $buffer, 1 ); //only the 1st occurence
			}
			else {
				$buffer = preg_replace( array(
					'#</div>\s*</article>(?!.*</div>\s*</article>)#umis',
				), array( '</div></article>' . $html ), $buffer, 1 ); //only the last occurence: negative lookahead (?!...)
			}
		}
		else if ($plac == 5) { //shortcode: [xtra_related_posts]
			$buffer = preg_replace( array(
				'#\[xtra_related_posts\]#umi',
			), array( $html ), $buffer, 1 ); //only the 1st occurence
		}
		return $hed.$buffer;
	}
}





//---Database---

// EXAMPLE:   EXPORT_TABLES("localhost","user","pass","db_name" );
		//optional: 5th parameter - to backup specific tables only: array("mytable1","mytable2",...)
		//optional: 6th parameter - backup filename
		// IMPORTANT NOTE for people who try to change strings in SQL FILE before importing, MUST READ:  goo.gl/2fZDQL
// https://github.com/tazotodua/useful-php-scripts

function xtra_EXPORT_TABLES($host,$user,$pass,$name,       $tables=false, $backup_name=false){
	set_time_limit(3000); $mysqli = new mysqli($host,$user,$pass,$name); $mysqli->select_db($name); $mysqli->query("SET NAMES 'utf8'");
	$queryTables = $mysqli->query('SHOW TABLES'); while($row = $queryTables->fetch_row()) { $target_tables[] = $row[0]; }	if($tables !== false) { $target_tables = array_intersect( $target_tables, $tables); }
	$content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `".$name."`\r\n--\r\n\r\n\r\n";
	foreach($target_tables as $table){
		if (empty($table)){ continue; }
		$result	= $mysqli->query('SELECT * FROM `'.$table.'`');  	$fields_amount=$result->field_count;  $rows_num=$mysqli->affected_rows; 	$res = $mysqli->query('SHOW CREATE TABLE '.$table);	$TableMLine=$res->fetch_row();
		$content .= "\n\n".$TableMLine[1].";\n\n";   $TableMLine[1]=str_ireplace('CREATE TABLE `','CREATE TABLE IF NOT EXISTS `',$TableMLine[1]);
		for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) {
			while($row = $result->fetch_row())	{ //when started (and every after 100 command cycle):
				if ($st_counter%100 == 0 || $st_counter == 0 )	{$content .= "\nINSERT INTO ".$table." VALUES";}
					$content .= "\n(";    for($j=0; $j<$fields_amount; $j++){ $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); if (isset($row[$j])){$content .= '"'.$row[$j].'"' ;}  else{$content .= '""';}	   if ($j<($fields_amount-1)){$content.= ',';}   }        $content .=")";
				//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
				if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";}	$st_counter=$st_counter+1;
			}
		} $content .="\n\n\n";
	}
	$content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
	$backup_name = $backup_name ? $backup_name : $name.'___('.date('H-i-s').'_'.date('d-m-Y').').sql';
	ob_get_clean(); header('Content-Type: application/octet-stream');  header("Content-Transfer-Encoding: Binary");  header('Content-Length: '. (function_exists('mb_strlen') ? mb_strlen($content, '8bit'): strlen($content)) );    header("Content-disposition: attachment; filename=\"".$backup_name."\"");
	echo $content; exit;
}

// EXAMPLE:	IMPORT_TABLES("localhost","user","pass","db_name", "my_baseeee.sql"); //TABLES WILL BE OVERWRITTEN
	// P.S. IMPORTANT NOTE for people who try to change/replace some strings  in SQL FILE before importing, MUST READ:  https://goo.gl/2fZDQL

function xtra_IMPORT_TABLES($host,$user,$pass,$dbname, $sql_file_OR_content){
	set_time_limit(3000);
	$SQL_CONTENT = (strlen($sql_file_OR_content) > 300 ?  $sql_file_OR_content : file_get_contents($sql_file_OR_content)  );
	$allLines = explode("\n",$SQL_CONTENT);
	$mysqli = new mysqli($host, $user, $pass, $dbname); if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();}
		$zzzzzz = $mysqli->query('SET foreign_key_checks = 0');	        preg_match_all("/\nCREATE TABLE(.*?)\`(.*?)\`/si", "\n". $SQL_CONTENT, $target_tables); foreach ($target_tables[2] as $table){$mysqli->query('DROP TABLE IF EXISTS '.$table);}         $zzzzzz = $mysqli->query('SET foreign_key_checks = 1');    $mysqli->query("SET NAMES 'utf8'");
	$templine = '';	// Temporary variable, used to store current query
	foreach ($allLines as $line)	{											// Loop through each line
		if (substr($line, 0, 2) != '--' && $line != '') {$templine .= $line; 	// (if it is not a comment..) Add this line to the current segment
			if (substr(trim($line), -1, 1) == ';') {		// If it has a semicolon at the end, it's the end of the query
				if(!$mysqli->query($templine)){ print('Error performing query \'<strong>' . $templine . '\': ' . $mysqli->error . '<br /><br />');  }  $templine = ''; // set variable to empty, to start picking up the lines after ";"
			}
		}
	}	return 'Importing finished. Now, Delete the import file.';
}









//---Others---


?>