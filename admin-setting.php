<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


function xtra_make_dataset() {
global $xtra_pageload_time;
$xtra_sets = array(
	'xtra_dir_index_disable' 				=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'Apache Server Hardening'	,'label' => 'Disable Apache directory views <br/>Disables file listing in directories. <br/>(Writes in .htaccess file)'	,'submitX' => 1	),
	'xtra_protect_xss' 						=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'Apache Server Hardening'	,'label' => 'Protect against XSS attacks<br/>Hardening your HTTP response header. <br/>(Writes in .htaccess file)'	,'submitX' => 1	),
	'xtra_protect_pageframing' 				=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'Apache Server Hardening'	,'label' => 'Protect against Page-Framing and Click-Jacking <br/>Hardening your HTTP response header. <br/>(Writes in .htaccess file)'	,'submitX' => 1	),
	'xtra_protect_cotentsniffing' 			=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'Apache Server Hardening'	,'label' => 'Protect against Content-Sniffing <br/>Hardening your HTTP response header. <br/>(Writes in .htaccess file)'	,'submitX' => 1	),
	'xtra_block_external_post' 				=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'Apache Server Hardening'	,'label' => 'Block external POST requests <br/>Be careful - it might disable some external apps access. <br/>(Writes in .htaccess file)'	,'submitX' => 1	),
	'xtra_redir_bots' 						=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'Apache Server Hardening'	,'label' => 'Redirect some bots to referring URL<br/>This is an invalid user-agent string, used by many bad bots. <br/>(Writes in .htaccess file)'	,'submitX' => 1	),
	'xtra_redir_bots_text' 					=> array( 'default' => '%{HTTP_USER_AGENT} rv:40\.0.*Firefox/40\.1$'	,'tab' => 'Security'	,'section' => 'Apache Server Hardening'	,'label' => ''	),
	
	'xtra_remove_version_number' 			=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'WP Security Settings'	,'label' => 'Remove WordPress Version Number from HTML Head'	),
	'xtra_suppress_login_errors' 			=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'WP Security Settings'	,'label' => 'Dont display WP login errors'	),
	'xtra_fb_disable_feed' 					=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'WP Security Settings'	,'label' => 'Disable all type of RSS Feeds'	),
	'xtra_disable_xmlrpc' 					=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'WP Security Settings'	,'label' => 'Disable XML-RPC access<br/>Be careful - it might disable some external apps access like e.g. Jetpack.'	),
	'xtra_protect_from_bad_requests' 		=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'WP Security Settings'	,'label' => 'Protect from some malicious URL requests<br/>(URLs including eval(), CONCAT, UNION+SELECT, base64 or UserAgent with libwww, Wget, EmailSiphon, EmailWolf)'	),

	'xtra_online_security_tools' 			=> array( 'default' => 0	,'tab' => 'Security'	,'section' => 'Online Security Tools'	,'label' => 'Check your site with online security tools'	),

	'xtra_deflates' 						=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'Apache Compression and Caching'	,'label' => 'Enable GZIP compression: deflate <br/>(Writes in .htaccess file)'	,'submitX' => 1	),
//	'xtra_browserbugs' 						=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'Apache Compression and Caching'	,'label' => 'Remove very old browser bugs <br/>(Writes in .htaccess file)'	,'submitX' => 1	),
	'xtra_image_expiry' 					=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'Apache Compression and Caching'	,'label' => 'Set file cache expirations to 1 month <br/>(css, js, images, fonts) <br/>(Writes in .htaccess file)'	,'submitX' => 1	),
	'xtra_remove_etags' 					=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'Apache Compression and Caching'	,'label' => 'Remove ETags <br/>Reduces the size of the HTTP headers. <br/>(Writes in .htaccess file)'	,'submitX' => 1	),

	'xtra_WPcache'			 				=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'WP Cache Settings'	,'label' => 'Enable WordPress Cache<br/>Includes wp-cache.php in settings. <br/>(Writes in wp-config file)'	,'submitX' => 1	),
	'xtra_remove_query_strings' 			=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'WP Cache Settings'	,'label' => 'Remove query strings from script and css filenames<br/>(those files with .css?ver=2.11 endings)'	),

	'xtra_memory_limit' 					=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'Memory and PHP Execution'	,'label' => 'PHP Memory limit<br/>(Only if your hosting/server allows it)'	),
	'xtra_memory_limit_num'					=> array( 'default' => 128	,'tab' => 'Speed'	,'section' => 'Memory and PHP Execution'	,'label' => '-MB'	,'current' => ini_get('memory_limit').((get_option('xtra_memory_limit') && get_option('xtra_memory_limit_num')!=ini_get('memory_limit'))?' - obviously, does not work':'')	),
	'xtra_upload_max_filesize' 				=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'Memory and PHP Execution'	,'label' => 'PHP Upload max file-size<br/>(Only if your hosting/server allows it)'	),
	'xtra_upload_max_filesize_num'			=> array( 'default' => 32	,'tab' => 'Speed'	,'section' => 'Memory and PHP Execution'	,'label' => '-MB'	,'current' => ini_get('upload_max_filesize').((get_option('xtra_upload_max_filesize') && get_option('xtra_upload_max_filesize_num')!=ini_get('upload_max_filesize'))?' - obviously, does not work':'')	),
	'xtra_max_execution_time' 				=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'Memory and PHP Execution'	,'label' => 'PHP Max execution time<br/>(Only if your hosting/server allows it)'	),
	'xtra_max_execution_time_num'			=> array( 'default' => 60	,'tab' => 'Speed'	,'section' => 'Memory and PHP Execution'	,'label' => '-sec'	,'current' => ini_get('max_execution_time').((get_option('xtra_max_execution_time') && get_option('xtra_max_execution_time_num')!=ini_get('max_execution_time'))?' - obviously, does not work':'')	),

	'xtra_minify_html'						=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'Minifier'	,'label' => 'HTML Minifier <br/>Remove extra spaces from between HTML tags. Also remove comments from inline js and css.<br/>(Works 99% of the cases...)'	),

	'xtra_online_speed_tools' 				=> array( 'default' => 0	,'tab' => 'Speed'	,'section' => 'Online Speed Tools'	,'label' => 'Check your site speed with online tools'	),

	'xtra_meta_description' 				=> array( 'default' => 0	,'tab' => 'SEO'	,'section' => 'SEO Settings'	,'label' => 'Add Meta Description and Keywords<br/>to all posts and pages HTML head using post excerpt and tags'	),
	'xtra_WPTime_redirect_404_to_homepage' 	=> array( 'default' => 0	,'tab' => 'SEO'	,'section' => 'SEO Settings'	,'label' => 'Redirect page-not-found (404) to Homepage'	),
	'xtra_attachment_redirect_to_post' 		=> array( 'default' => 0	,'tab' => 'SEO'	,'section' => 'SEO Settings'	,'label' => 'Redirect Attachment pages to their Parent Post'	),
	'xtra_rel_external' 					=> array( 'default' => 0	,'tab' => 'SEO'	,'section' => 'SEO Settings'	,'label' => 'Add rel="external"<br/>to any URL with target="_blank" attribute in post contents'	),
	'xtra_img_alt'		 					=> array( 'default' => 0	,'tab' => 'SEO'	,'section' => 'SEO Settings'	,'label' => 'Add missing alt="..." attribute to images<br/>in pages and post contents'	),
	'xtra_remove_double_title_meta'			=> array( 'default' => 0	,'tab' => 'SEO'	,'section' => 'SEO Settings'	,'label' => 'Remove double title<br/>meta tag in HTML headers (caused by some SEO plugins)'	),
	
	'xtra_defer_parsing_of_js'				=> array( 'default' => 0	,'tab' => 'SEO'	,'section' => 'JavaScript Settings'	,'label' => 'Defer parsing of all JS<br/>Test your site carefully to check correct functioning!'	),
	'xtra_move_all_js_to_footer'			=> array( 'default' => 0	,'tab' => 'SEO'	,'section' => 'JavaScript Settings'	,'label' => 'Move all JS to the footer<br/>Test your site carefully to check correct functioning!'	),

	'xtra_online_seo_tools'					=> array( 'default' => 0	,'tab' => 'SEO'	,'section' => 'SEO Online Tools'	,'label' => 'Use online SEO tools to further optimize your site'	),

	'xtra_facebook_og_metas' 				=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Social Media Settings'	,'label' => 'Add Facebook Open Graph (OG)<br/>meta-tags to all posts and pages HTML head using post title, excerpt and thumbnail image'	),
	'xtra_twitter_metas'	 				=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Social Media Settings'	,'label' => 'Add Twitter Cards<br/>meta-tags to posts and pages HTML head using post data and thumbnails'	),
	'xtra_twitter_metas_text' 				=> array( 'default' => ''	,'tab' => 'Social'	,'section' => 'Social Media Settings'	,'label' => 'Your Twitter username: '	),
	'xtra_facebook_sdk' 					=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Social Media Settings'	,'label' => 'Add Facebook JS SDK<br/>to be able to use native Like/Share buttons and show Facebook Page iFrame'	),

	'xtra_share_buttons' 					=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons settings'	,'label' => 'Enable Share Buttons feature'	),
	'xtra_share_buttons_text' 				=> array( 'default' => 'Share on:'	,'tab' => 'Social'	,'section' => 'Share buttons settings'	,'label' => '<br/>Block Title:'	),
	'xtra_share_buttons_num' 				=> array( 'default' => 100	,'tab' => 'Social'	,'section' => 'Share buttons settings'	,'label' => '<br/>Icon Zoom:-%'	),
	'xtra_share_buttons_posts' 				=> array( 'default' => 1	,'tab' => 'Social'	,'section' => 'Share buttons settings'	,'label' => 'Show on Posts?'	),
	'xtra_share_buttons_pages' 				=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons settings'	,'label' => 'Show on Pages?'	),
	'xtra_share_buttons_homepage' 			=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons settings'	,'label' => 'Show on Homepage?'	),
	'xtra_share_buttons_place' 				=> array( 'default' => 1	,'tab' => 'Social'	,'section' => 'Share buttons settings'	,'label' => 'Position'	),
	'xtra_share_buttons_shape' 				=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons settings'	,'label' => 'Shape'	),
	
	'xtra_share_facebook' 					=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons'	,'label' => 'Facebook share button'	),
	'xtra_share_twitter' 					=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons'	,'label' => 'Twitter share button'	),
	'xtra_share_linkedin' 					=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons'	,'label' => 'LinkedIn share button'	),
	'xtra_share_pinterest' 					=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons'	,'label' => 'Pinterest share button'	),
	'xtra_share_tumblr' 					=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons'	,'label' => 'tumblr share button'	),
	'xtra_share_gplus' 						=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons'	,'label' => 'Google+ share button'	),
	'xtra_share_reddit' 					=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons'	,'label' => 'Reddit share button'	),
	'xtra_share_buffer' 					=> array( 'default' => 0	,'tab' => 'Social'	,'section' => 'Share buttons'	,'label' => 'Buffer share button'	),

	'xtra_remove_admin_bar' 				=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Remove Admin Bar <br/>on front-end for everybody'	),
	'xtra_remove_admin_bar_excl_adm'		=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => '&nbsp; &bull; except for Admins'	),
	'xtra_remove_admin_bar_excl_edt'		=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => '&nbsp; &bull; except for Editors'	),
	'xtra_remove_admin_bar_excl_aut'		=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => '&nbsp; &bull; except for Authors'	),
	'xtra_login_checked_remember_me' 		=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Auto-Check Remember-Me<br/>checkbox automatically at login'	),
	'xtra_keep_me_logged_in_for' 			=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Set Login Expiration<br/>days for Remember-Me auth cookie at login'	),
	'xtra_keep_me_logged_in_for_text' 		=> array( 'default' => 60	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => '-days'	),
	'xtra_doEmailNameFilter' 				=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Change default WP email Sender Name'	),
	'xtra_doEmailNameFilter_text' 			=> array( 'default' => ''	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => ''	),
	'xtra_doEmailFilter' 					=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Change default WP email Sender Address'	),
	'xtra_doEmailFilter_text' 				=> array( 'default' => ''	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => ''	),
	'xtra_php_in_textwidgets' 				=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Allow PHP code in text widgets'	),
	'xtra_shortcode_in_textwidgets' 		=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Enable Shortcodes in text widgets'	),
	'xtra_remove_WPemoji' 					=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Remove support for WP emoji<br/>(As of WordPress 4.2, by default WordPress includes support for Emojis. Great if that is your cup of tea, but if not, you might want to remove the additional resources Emoji support adds to your webpage.)'	),
	'xtra_custom_jpeg_quality' 				=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Custom JPEG quality<br/>(By default, in order to save space, Wordpress compresses uploaded JPG images with 82% quality ratio.)'	),
	'xtra_custom_jpeg_quality_num' 			=> array( 'default' => 82	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => '-%'	),
	'xtra_auto_resize_upload'			 	=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Auto-Resize Image Uploads<br/>to maximum width/height px'	),
	'xtra_auto_resize_upload_num'		 	=> array( 'default' => 1600	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Max width:'	),
	'xtra_auto_resize_upload_pnum'		 	=> array( 'default' => 1600	,'tab' => 'WP Settings'	,'section' => 'Wordpress Mods'	,'label' => 'Max height:'	),

	'xtra_all_autoupdate' 					=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Auto-Update'	,'level' => 0 ,'def' => 'ON' 	,'label' => 'Overall Auto-update Feature'	),
	'xtra_core_autoupdate_major'			=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Auto-Update'	,'level' => 1 ,'def' => 'OFF' 	,'label' => '&bull; Core: Major version'	),
	'xtra_core_autoupdate_minor'			=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Auto-Update'	,'level' => 1 ,'def' => 'ON' 	,'label' => '&bull; Core: Minor version'	),
	'xtra_core_autoupdate_dev'				=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Auto-Update'	,'level' => 1 ,'def' => 'OFF' 	,'label' => '&bull; Core: Development version'	),
	'xtra_theme_autoupdate' 				=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Auto-Update'	,'level' => 1 ,'def' => 'OFF' 	,'label' => '&bull; Themes'	),
	'xtra_translation_autoupdate' 			=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Auto-Update'	,'level' => 1 ,'def' => 'ON' 	,'label' => '&bull; Translations'	),
	'xtra_plugin_autoupdate' 				=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Auto-Update'	,'level' => 1 ,'def' => 'OFF' 	,'label' => '&bull; Plugins'	),
	'xtra_autoupdate_cron_buttons' 			=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Auto-Update'	,'level' => 1 					,'label' => 'Trigger Auto-Update Now'	),

	'xtra_maintenance'						=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Maintenance mode'	,'label' => 'Enable Maintenance mode<br/>(Locks down public access to the website - except for admins with the right to "edit_themes")'	),
	'xtra_maintenance_title'				=> array( 'default' => 'Website under Maintenance'	,'tab' => 'WP Settings'	,'section' => 'Wordpress Maintenance mode'	,'label' => '<br/>Title:'	),
	'xtra_maintenance_text'					=> array( 'default' => 'We are performing scheduled maintenance. We will be back online shortly!'	,'tab' => 'WP Settings'	,'section' => 'Wordpress Maintenance mode'	,'label' => '<br/>Text:'	),

	'xtra_debug'				 			=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Debug mode'	,'label' => 'Enable Debug mode<br/>(Writes in wp-config file)'	,'submitX' => 1	),
	'xtra_debug_text'		 				=> array( 'default' => ''	,'tab' => 'WP Settings'	,'section' => 'Wordpress Debug mode'	,'label' => 'Error Reporting Level:'	),
	'xtra_disable_debug_display'		 	=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Debug mode'	,'label' => 'Disable Debug display on screen<br/>(Writes in wp-config file)'	,'submitX' => 1	),
	'xtra_debug_log'		 				=> array( 'default' => 0	,'tab' => 'WP Settings'	,'section' => 'Wordpress Debug mode'	,'label' => 'Set Debug logging to '.( (file_exists(XTRA_WPCONTENT_BASENAME.'/debug.log')) ? ('<a target="_blank" href="'.site_url()."/".XTRA_WPCONTENT_BASENAME.'/debug-log">/'.XTRA_WPCONTENT_BASENAME.'/debug.log</a>') : (XTRA_WPCONTENT_BASENAME.'/debug.log') ).'<br/>(Writes in wp-config file)'	,'submitX' => 1	),

	'xtra_revisions_to_keep' 				=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => 'Limit revisions <br/>of posts or pages to keep'	),
	'xtra_revisions_to_keep_num' 			=> array( 'default' => 99	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => '-for pages &nbsp;&nbsp;&nbsp;'	),
	'xtra_revisions_to_keep_pnum' 			=> array( 'default' => 99	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => '-for others'	),
	'xtra_autosave_interval' 				=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => 'Auto-Save interval <br/>when editing<br/>(Writes in wp-config file)'	,'submitX' => 1	),
	'xtra_autosave_interval_num' 			=> array( 'default' => 160	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => '-sec'	),
	'xtra_empty_trash'		 				=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => 'Empty trash days <br/>for deleted posts<br/>(Writes in wp-config file)'	,'submitX' => 1	),
	'xtra_empty_trash_num'		 			=> array( 'default' => 30	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => '-days'	),
	'xtra_require_featured_image' 			=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => 'Require a Featured Image <br/>when publishing posts'	),
	'xtra_auto_featured_image' 				=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => 'Auto-add Featured Image <br/>if missing from the 1st image in the post'	),
	'xtra_disallow_duplicate_posttitles' 	=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => 'Disallow Duplicate Post Titles <br/>when publishing posts'	),
	'xtra_posts_status_color'			 	=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => 'Highlight Post Color by Status<br/>in Posts Admin screen'	),
	'xtra_notify_author_on_publish'		 	=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => 'Email notify author when his post has been published'	),
	'xtra_notify_author_on_publish_s_text'	=> array( 'default' => 'Your post is published.'	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => '<br/>Subject:'	),
	'xtra_notify_author_on_publish_text'	=> array( 'default' => 'Thank you for your submission!'	,'tab' => 'Posts'	,'section' => 'Posting'	,'label' => '<br/>Text:'	),

	'xtra_related_posts' 					=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Related Posts'	,'label' => 'Enable Related Posts feature'	),
	'xtra_related_posts_text' 				=> array( 'default' => 'Related posts'	,'tab' => 'Posts'	,'section' => 'Related Posts'	,'label' => '<br/>Block Title:'	),
	'xtra_related_posts_num' 				=> array( 'default' => 4	,'tab' => 'Posts'	,'section' => 'Related Posts'	,'label' => '<br/>Show:-posts'	),
	'xtra_related_posts_size_num'			=> array( 'default' => 150	,'tab' => 'Posts'	,'section' => 'Related Posts'	,'label' => '<br/>Thumbnail size:-px'	),
	'xtra_related_posts_posts' 				=> array( 'default' => 1	,'tab' => 'Posts'	,'section' => 'Related Posts'	,'label' => 'Show on Posts?'	),
	'xtra_related_posts_pages' 				=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Related Posts'	,'label' => 'Show on Pages?'	),
	'xtra_related_posts_homepage' 			=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'Related Posts'	,'label' => 'Show on Homepage?'	),
	'xtra_related_posts_place' 				=> array( 'default' => 4	,'tab' => 'Posts'	,'section' => 'Related Posts'	,'label' => 'Position'	),
	
	'xtra_link_new_tab' 					=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'HTML Content Changes'	,'label' => 'Open all external links in new tab<br/>(Internal links: '.home_url().' - will not change)'	),
	'xtra_attachment_image_link_filter' 	=> array( 'default' => 0	,'tab' => 'Posts'	,'section' => 'HTML Content Changes'	,'label' => 'Add self-link to all uploaded images in posts<br/>(Also adds data-rel="lightbox-gallery-postimages" as an attribute)'	),
);
//	If an option needs to call its function -  add 'submitX'
//	If an option needs write access to a file - add 'writes in .htaccess file' or 'writes in wp-config file'
//	Options that write in files should be added to deactivate() in xtra.php
return $xtra_sets;
}

function xtra_get_tab_colors($key="") {
	$x = array(
		'Security'			=> array('color'=>'#86CC8C'),
		'Speed'				=> array('color'=>'#E1847C'),
		'SEO'				=> array('color'=>'#C485FF'),
		'Social'			=> array('color'=>'#FFC488'),
		'WP Settings'		=> array('color'=>'#00a0d2'),
		'Posts'				=> array('color'=>'#8292FF'),
	);
	if ($key) return $x[$key];
	return $x;
}

function xtra_get_section_icons($key="") {
	$x = array(
		'Apache Server Hardening'			=> 'shield',
		'WP Security Settings'				=> 'sos',
		'Apache Compression and Caching'	=> 'dashboard',
		'WP Cache Settings'					=> 'tickets',
		'Memory and PHP Execution'			=> 'desktop',
		'Minifier'							=> 'nametag',
		'SEO Settings'						=> 'admin-site',
		'Social Media Settings'				=> 'share',
		'Share buttons settings'			=> 'share-alt',
		'Share buttons'						=> 'thumbs-up',
		'Wordpress Mods'					=> 'wordpress',
		'Posting'							=> 'edit',
		'Wordpress Auto-Update'				=> 'backup',
		'Related Posts'						=> 'images-alt',
		'HTML Content Changes'				=> 'media-code',
		'Wordpress Maintenance mode'		=> 'admin-tools',
		'Wordpress Debug mode'				=> 'admin-settings',
		
		'Online Security Tools'				=> 'plus-alt',
		'Online Speed Tools'				=> 'plus-alt',
		'SEO Online Tools'					=> 'plus-alt',
	);
	if ($key) return $x[$key];
	return $x;
}

function xtra_make_html() {
global $xtra_seltab, $xtra_tabs;
global $xtra_error_level_string;
$xtra_sets = xtra_make_dataset();
$xtra_tabcols = xtra_get_tab_colors();

	$html = '';
	$oldset = '';
	$i = 0;
	$sec = 0;
	$tab = 0;
	$xtra_tabs = array();
	foreach ($xtra_sets as $setname => $set) {
		if (stripos($set['section'],'apache')!==FALSE && !xtra_is_apache()) continue;
		$i++;
		$stra = explode("-",$set['label']);
		$pre_label = $stra[0];
		$post_label = $stra[1];
		$txtw = '85%';
		if ( strlen($pre_label.$post_label)>12 ) $txtw = '80%';
		if ( strlen($pre_label.$post_label)>25 ) $txtw = '65%';
		if ( strlen($pre_label.$post_label)>35 ) $txtw = '50%';
		if ( str_ireplace(array('_num','_pnum','logged_in_for_text'),'',$setname)!=$setname ) $txtw = '50px';
		if ( str_ireplace(array('twitter_metas_text'),'',$setname)!=$setname ) $txtw = '110px';
		if ( str_ireplace(array('xtra_share_buttons_text'),'',$setname)!=$setname ) $txtw = '65%';
		$curval = get_option($setname,$set['default']);

		if ($set['tab'] != $oldtab) {
			//.. Tab
			$oldtab = $set['tab'];
			$tab = array_search($set['tab'],$xtra_tabs);
			if ($tab===false) {
				$xtra_tabs[] = $set['tab'];
				$tab = (string)(count($xtra_tabs)-1);
			}
		}
		if ($set['section'] != $oldset) {
			//.. Section
			if ($i>1) $html .= '</table></div>';
			$addi = "";
			if (stripos($set['section'],'apache')!==FALSE) $addi = '<span class="m-left-50 small">'.'(Only for Apache severs) &nbsp;&nbsp;&nbsp;Your server is: '.$_SERVER["SERVER_SOFTWARE"].'</span>';
			$html .= '<div class="xtra_box xtra_tabbes xtra_tabbeID_'.$tab.' '.(($xtra_seltab===$tab || $xtra_seltab==="*")?"active":"").'">';
			// Section - icons
			$dashicons = xtra_get_section_icons();
			$icn = "";
			if ($dicn = $dashicons[$set['section']]) $icn = ' class="icbefore dashicons-'.$dicn.'"';
			$html .= '<h2 '.$icn.'>'.$set['section'].$addi.'</h2>';
			$html .= '<table class="wp-list-table widefat fixed striped xtra">';
			$oldset = $set['section'];
			$sec++;
		}

		if ( substr($setname,-6) == "_title" || substr($setname,-5) == "_text" || substr($setname,-5) == "_pnum" || substr($setname,-4) == "_num" ) {
			//.. Extra text - input
			if ( xtra_writable($set['label']) ) {
				if ($setname=='xtra_debug_text') {
					if ($xtra_error_level_string)
						$html .= ' &nbsp;&nbsp;&nbsp;'.$pre_label.' '.esc_html($xtra_error_level_string).''.$post_label.'';
				}
				else if ($setname=='xtra_redir_bots_text') {
					$html .= ' &nbsp;&nbsp;&nbsp;<label for="'.$setname.'">'.$pre_label.'<input type="text" readonly="readonly" style="width:'.$txtw.';" name="'.$setname.'" id="'.$setname.'" value="'.esc_html($curval).'" />'.$post_label.'</label>';
				}
				else if ($setname=='xtra_twitter_metas_text') {
					$html .= ' &nbsp;&nbsp;&nbsp;<label for="'.$setname.'">'.$pre_label.'</label><span style="white-space:nowrap;font-size:150%;">@<input type="text" style="width:'.$txtw.';" name="'.$setname.'" id="'.$setname.'" value="'.esc_html($curval).'" /></span>';
				}
				else {
					$html .= ' &nbsp;&nbsp;&nbsp;<label for="'.$setname.'">'.$pre_label.'<input type="text" style="width:'.$txtw.';" name="'.$setname.'" id="'.$setname.'" value="'.esc_html($curval).'" />'.$post_label.'</label>';
				}
			}
		}
		else {
			//.. Label
			$clr = $xtra_tabcols[$set['tab']]['color'];
			if (!$clr) $clr = "#00a0d2";
			$left_indent = 20;
			$lvl = "";
			if ($set['level']) $lvl = "padding-left: ".(($set['level'])*$left_indent)."px;";
			$sty = 'style="border-left: 4px solid transparent;'.$lvl.'"';
			if ( $curval 
				|| strpos($set['section'],"Tools")!==FALSE 
				|| (strpos($set['label'],"Update Now")!==FALSE && get_option('xtra_all_autoupdate')!=-1) 
			) $sty = 'style="border-left: 4px solid '.$clr.';'.$lvl.'"';
			
			if ($i>1) $html .= '</td></tr>';
			$html .= '<tr><th '.$sty.' scope="row">';
			$html .= preg_replace("#<br.*?>#","<br/><span>",$set['label'])."</span>";
			if (stripos($set['section'],'Auto-Update')!==FALSE && stripos($set['label'],'Auto-Update Now')!==FALSE) {
				$crn = xtra_get_crons('array','wp_version_check','date');
				//if ($crn) $addi = '<span class="m-left-50 small">Cron: '.$crn.'</span>';
				if ($crn) $html .= '<br/><span>Next Auto-Update scheduled: '.$crn.' (Cron job)</span>';
			}
			$html .= '</th><td>';
				
			if ( !xtra_writable($set['label']) ) {
				// is NOT writable
				$html .= ' &nbsp;&nbsp;&nbsp;&nbsp;('.((stripos($set['label'],'htaccess'))?'htaccess':'wp-config.php').' is not writable!)';
			}
			else if ( $setname == 'xtra_online_security_tools' ) {
				// Security Tools
				$html .= '
				<a class="button button-small" target="_blank" href="https://www.google.com/transparencyreport/safebrowsing/diagnostic/index.html#url='.home_url().'">Google Transparency</a>
				<a class="button button-small" target="_blank" href="https://sitecheck.sucuri.net/results/'.home_url().'">Sucuri</a>
				<a class="button button-small" target="_blank" href="http://www.isithacked.com/check/'.home_url().'">Is It Hacked?</a>
				<a class="button button-small" target="_blank" href="http://www.urlvoid.com/scan/'.home_url().'">URLVoid</a>
				<a class="button button-small" target="_blank" href="http://www.unmaskparasites.com/security-report/?page='.home_url().'">Unmask Parasites</a>
				';
			}
			else if ( $setname == 'xtra_online_speed_tools' ) {
				// Speed Tools
				$html .= '
				<a class="button button-small" target="_blank" href="https://developers.google.com/speed/pagespeed/insights/?url='.home_url().'">Google PageSpeed</a>
				<a class="button button-small" target="_blank" href="https://gtmetrix.com/">GTmetrix</a>
				<a class="button button-small" target="_blank" href="https://tools.pingdom.com/">Pingdom</a>
				';
			}
			else if ( $setname == 'xtra_online_seo_tools' ) {
				// SEO Tools
				$html .= '
				<a class="button button-small" target="_blank" href="https://www.google.com/webmasters/tools/">Google Search Console</a>
				<a class="button button-small" target="_blank" href="https://analytics.google.com/">Google Analytics</a>
				<a class="button button-small" target="_blank" href="https://seositecheckup.com/">SeoSiteCheckup</a>
				';
			}
			else if ( $setname == 'xtra_autoupdate_cron_buttons' && get_option('xtra_all_autoupdate')!=-1 ) {
				// Auto-Update Start Button
				$html .= '
				<a class="button button-small" href="'.wp_nonce_url('?page=xtra&do=maybe_auto_update','mynonce').'">Start Auto-Update check Now !!!</a>
				';
			}
			else if ( $setname == 'xtra_share_buttons_place' ) {
				//... Share Buttons Position - Radio
				$html .= '
				<label for="'.$setname.'3"><input type="radio" name="'.$setname.'" id="'.$setname.'3" value="3" 	'.(($curval!=3)?'':'checked="checked"').' />before title</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'2"><input type="radio" name="'.$setname.'" id="'.$setname.'2" value="2" 	'.(($curval!=2)?'':'checked="checked"').' />before text</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'1"><input type="radio" name="'.$setname.'" id="'.$setname.'1" value="1" 	'.(($curval!=1)?'':'checked="checked"').' />after text</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'4"><input type="radio" name="'.$setname.'" id="'.$setname.'4" value="4" 	'.(($curval!=4)?'':'checked="checked"').' />after article</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'5"><input type="radio" name="'.$setname.'" id="'.$setname.'5" value="5" 	'.(($curval!=5)?'':'checked="checked"').' />shortcode: [xtra_share_buttons]</label>
				';
			}
			else if ( $setname == 'xtra_related_posts_place' ) {
				//... Related Posts Position - Radio
				$html .= '
				<label for="'.$setname.'3"><input type="radio" name="'.$setname.'" id="'.$setname.'3" value="3" 	'.(($curval!=3)?'':'checked="checked"').' />before title</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'2"><input type="radio" name="'.$setname.'" id="'.$setname.'2" value="2" 	'.(($curval!=2)?'':'checked="checked"').' />before text</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'1"><input type="radio" name="'.$setname.'" id="'.$setname.'1" value="1" 	'.(($curval!=1)?'':'checked="checked"').' />after text</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'4"><input type="radio" name="'.$setname.'" id="'.$setname.'4" value="4" 	'.(($curval!=4)?'':'checked="checked"').' />after article</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'5"><input type="radio" name="'.$setname.'" id="'.$setname.'5" value="5" 	'.(($curval!=5)?'':'checked="checked"').' />shortcode: [xtra_related_posts]</label>
				';
				if ( $setname == 'xtra_related_posts_place' ) {
					// Related Posts Exclude Categories - array
					$html .= '<tr><th '.$sty.' scope="row">Exclude Categories</th>
							<td><div class="h-150">
							';
					$tvala = get_option( 'xtra_categories_exclude', array() );
					foreach( get_categories() as $key => $cat ) {
						$categ = $cat->name;
						$slug = $cat->term_id;
						$tval = (in_array($slug,$tvala))?1:0;
						$html .= '<input class="exclude_checkbox" type="checkbox" name="xtra_categories_exclude[]" id="'.$slug.'" value="'.$slug.'" '.(($tval==0)?'':'checked="checked"').' />';
						$html .= '<label for="'.$slug.'" class="no-wrap;">'.$categ.'</label><br/>';
					}
					$html .= '</div>';
				}
			}
			else if ( $setname == 'xtra_share_buttons_shape' ) {
				//... Share Buttons Shape - Radio
				$html .= '
				<label for="'.$setname.'0"><input type="radio" name="'.$setname.'" id="'.$setname.'0" value="0" 	'.(($curval!=0)?'':'checked="checked"').' />square</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'1"><input type="radio" name="'.$setname.'" id="'.$setname.'1" value="1" 	'.(($curval!=1)?'':'checked="checked"').' />rounded</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'2"><input type="radio" name="'.$setname.'" id="'.$setname.'2" value="2" 	'.(($curval!=2)?'':'checked="checked"').' />circle</label>
				';
			}
			else if ( stripos($set['section'],'Wordpress Auto-Update')!==false ) {
				//... Auto-Update - Radio
				$html .= '
				<label for="'.$setname.'1"><input type="radio" name="'.$setname.'" id="'.$setname.'1" value="1" 	'.(($curval!=1)?'':'checked="checked"').' />Enable</label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'2"><input type="radio" name="'.$setname.'" id="'.$setname.'2" value="0" 	'.(($curval!=0)?'':'checked="checked"').' />WP default <span class="fix-35">('.$set['def'].')</span></label> &nbsp;&nbsp;&nbsp;
				<label for="'.$setname.'3"><input type="radio" name="'.$setname.'" id="'.$setname.'3" value="-1" 	'.(($curval!=-1)?'':'checked="checked"').' />Disable</label>
				';
				if ( $setname == 'xtra_plugin_autoupdate' ) {
					// Auto-Update Exclude Plugins - array
					$html .= '<tr><th '.$sty.' scope="row"><div style="padding-left:'.$left_indent.'px;">- Exclude Plugins from Auto-Update<br/><span>if Plugins Auto-Update (above) is enabled</span></div></th>
							<td><div class="h-150">
							';
					$tvala = get_option( 'xtra_plugins_exclude', array() );
					foreach( get_plugins() as $file => $pl ) { //$this->__pluginsFiles[$file] = $pl['Version'];
						$slug2 = dirname( plugin_basename( $file ) );
						$tval = (in_array($slug2,$tvala))?1:0;
						$html .= '<input class="exclude_checkbox" type="checkbox" name="xtra_plugins_exclude[]" id="'.$slug2.'" value="'.$slug2.'" '.(($tval==0)?'':'checked="checked"').' />';
						$html .= '<label for="'.$slug2.'" class="no-wrap;">'.$pl['Name'].'</label><br/>';
					}
					$html .= '</div>';
				}
			}
			else {
				//... Checkbox
				$html .= '<input type="checkbox" name="'.$setname.'" id="'.$setname.'" value="1" '.(($curval==0)?'':'checked="checked"').' />';
			}
		}
		//.. Current value
		if (isset($set['current'])) $html .= ' &nbsp;&nbsp;&nbsp;&nbsp;(current: '.$set['current'].')';
		//.. Additions
		if ( $setname == 'xtra_posts_status_color' ) {
			//... Highlight Post Color by Status
			$html .= '
			<table>
			<tr><td>Draft	<td style="padding:0;"><input 	type="text" class="xtra-color-picker" name="'.$setname.'1" id="'.$setname.'1" value="'.get_option('xtra_posts_status_color1','#FCE3F2').'" />
			<tr><td>Pending	<td style="padding:0;"><input 	type="text" class="xtra-color-picker" name="'.$setname.'2" id="'.$setname.'2" value="'.get_option('xtra_posts_status_color2','#f2e0ab').'" />
			<tr><td>Future	<td style="padding:0;"><input 	type="text" class="xtra-color-picker" name="'.$setname.'3" id="'.$setname.'3" value="'.get_option('xtra_posts_status_color3','#C6EBF5').'" />
			<tr><td>Private	<td style="padding:0;"><input 	type="text" class="xtra-color-picker" name="'.$setname.'4" id="'.$setname.'4" value="'.get_option('xtra_posts_status_color4','#b49de0').'" />
			</table>
			';
		}
	}
	$html .= '</td></tr>';
	$html .= '</table></div>';
return $html;
}


// Check is_writable
function xtra_writable($label) {
	if (stripos($label,'htaccess')!==false && !is_writable(ABSPATH . '.htaccess')) return false;
	if (stripos($label,'wp-config')!==false && !is_writable(ABSPATH . 'wp-config.php')) return false;
	return true;
}

function xtra_upload_dir( $dir ) {
    return array(
        'path'   => $dir['basedir'] . '/xtra',
        'url'    => $dir['baseurl'] . '/xtra',
        'subdir' => '/xtra',
    ) + $dir;
}


function xtra_error_level_tostring($intval, $separator = ',')
{
    $errorlevels = array(
        E_ALL => 'E_ALL',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        E_DEPRECATED => 'E_DEPRECATED',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_STRICT => 'E_STRICT',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_NOTICE => 'E_NOTICE',
        E_PARSE => 'E_PARSE',
        E_WARNING => 'E_WARNING',
        E_ERROR => 'E_ERROR');
    $result = '';
    foreach($errorlevels as $number => $name)
    {
        if (($intval & $number) == $number) {
            $result .= ($result != '' ? $separator : '').$name; }
    }
    return $result;
}

function xtra_longword($text,$nr=10,$sepa=" ") {
	$mytext=explode(" ",trim($text));
	$newtext=array();
	foreach($mytext as $k=>$txt) {
		if (strlen($txt)>$nr) {
			$txt=wordwrap($txt, $nr, $sepa, 1);
		}
		$newtext[]=$txt;
	}
	return implode(" ",$newtext);
}

function xtra_get_plugin_prefix($pl_file) {

	//get main plugin file content
	$plugin_path = WP_PLUGIN_DIR . "/" . substr( $pl_file,0,strrpos( $pl_file,"/" ) );
//echo "<h2>".$plugin_path."</h2>";
	foreach (glob($plugin_path . "/*.php") as $filepath) {
//echo "<pre>".$filepath."</pre>";
		//$filepath = WP_PLUGIN_DIR . "/" . $pl_file;
		$ftx .= implode( '', file( $filepath ) );
	}
	foreach (glob($plugin_path . "/*/*.php") as $filepath) {
//echo "<pre>".$filepath."</pre>";
		//$filepath = WP_PLUGIN_DIR . "/" . $pl_file;
		$ftx .= implode( '', file( $filepath ) );
	}
//echo "<hr>";

	$mtc = array();

	//get defines
	//preg_match_all("/define\(\s*['\"]([a-zA-Z0-9]+)_/i",$ftx,$matches);
	preg_match_all("/define\(\s*['\"](.+?)['\"]/i",$ftx,$matches);
	foreach ($matches[1] as $match) {
		$match = strtolower($match);
		if (!in_array($match,$mtc)) $mtc[] = $match;
	}

	//get update_options
	preg_match_all("/update_option\(\s*['\"](.+?)['\"]/i",$ftx,$matches);
	foreach ($matches[1] as $match) {
		$match = "".strtolower($match);
		if (!in_array($match,$mtc)) $mtc[] = $match;
	}

	$res = implode(', ',$mtc);
	$res2 = "|".implode('|',$mtc);

	//get the max count of chunks
	$max = array();
	$excl = array(
		"_default",
		"_php",
		"_filename",
		"_plugin_",
		"wp_debug",
		"wp_cache",
	);
	foreach ($mtc as $it) {
		$chs = explode("_",$it);
		$ch1 = $chs[0]."_";
		$ch2 = $chs[0]."_".$chs[1]."_";
		$ch3 = $chs[0]."_".$chs[1]."_".$chs[2]."";
		$n1 = substr_count($res2,"|".$ch1);
		$n2 = substr_count($res2,"|".$ch2);
		$n3 = substr_count($res2,"|".$ch3);
		if ($n3 > $max[$ch3] && str_ireplace($excl,"",$ch3)==$ch3) $max[$ch3."_"] = $n3;
		if ($n2 > $max[$ch2] && str_ireplace($excl,"",$ch2)==$ch2) $max[$ch2] = $n2;
		if ($n1 > $max[$ch1] && str_ireplace($excl,"",$ch1)==$ch1) $max[$ch1] = $n1;
	}
	arsort($max);

	//get the highest count with highest length as winner
	$xres = "";
	$oldval = "";
	$oldlevel = 0;
	foreach ($max as $key=>$val) {
		if ($key=="wp_") continue;
		if ($val>=$oldval && substr_count($key,"_")>$oldlevel) {
			$xres = "".rtrim($key,"_")."";
			$oldval = $val;
			$oldlevel = substr_count($key,"_");
		}
		else {
			break;
			$xres .= "".$key."($val), ";
		}
	}
	return $xres;
}

function xtra_get_images($ret="html",$filterHook="",$filterField="") {
global $xtra_get_images_count;
global $xtra_get_images_size;
global $xtra_images_list;
	$xtra_images_list = array();

	$upload_dir = XTRA_UPLOAD_DIR;
	$i = 0;

	$dir_iterator = new RecursiveDirectoryIterator($upload_dir);
	$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
	// could use CHILD_FIRST if you so wish
	foreach ($iterator as $file) {
		if ($file->isFile()) {
			$filename = $file->getFilename();
			$fullpath = $file->getPathname();
			$fullurl = str_ireplace(ABSPATH,home_url()."/",$fullpath);
			$relapath = str_ireplace(array($upload_dir."/","/".$filename),"",$fullpath);
			
			if ($relapath && !preg_match('{^\d{4}/}',$relapath)) continue;
			if (preg_match('{-\d+x\d+\.}i',$filename)) continue;
			if (!preg_match('{\.(jpg|jpeg|jpe|png|gif|bmp|tiff?|ico)$}i',$filename)) continue;
			
			$size = $file->getSize();
			$xtra_get_images_size += $size;
			$mtime = $file->getMTime();
			
			$key = $relapath."/".$filename;
			$xtra_images_list[$key]['filename'] = $filename;
			$xtra_images_list[$key]['fullpath'] = $fullpath;
			$xtra_images_list[$key]['fullurl'] = $fullurl;
			$xtra_images_list[$key]['relapath'] = $relapath;
			$xtra_images_list[$key]['size'] = $size;
			$xtra_images_list[$key]['mtime'] = $mtime;
			$i++;
		}
	}
	ksort($xtra_images_list);
	$i = 0;
	$img_postids = xtra_get_image_ids();
	foreach ($xtra_images_list as $row) {
			$restore = "";
			if ( file_exists(str_replace(XTRA_UPLOAD_DIR,XTRA_UPLOAD_DIR ."/xtra-img-backup",$row['fullpath'])) ) {
				//$restore = "<a target='_blank' title='restore from backup' href=''><div class='dashicons dashicons-upload'></div></a>";
				$restore = "<div title='has backup' class='dashicons dashicons-upload'></div>";
			}
			$postids = explode(",",$img_postids[$row['relapath']."/".$row['filename']]);
			$postlinks = array();
			foreach ( (array)$postids as $postid ) {
				if (!$postid) continue;
				$postlinks[] = "<a target='_blank' title='view post' href='".home_url()."/?p=$postid'><div class='dashicons dashicons-visibility'></div></a>";
				$postlinks[] = "<a target='_blank' title='edit' href='".site_url()."/wp-admin/post.php?post=$postid&action=edit'><div class='dashicons dashicons-welcome-write-blog'></div></a>";
			}
			//$postlinks = array_merge($postlinks,(array)$restore);
			$postlinkstxt = "";
			if (count($postlinks)) $postlinkstxt = implode(" ",$postlinks);
			$i++;
			list($imgw,$imgh) = getimagesize($row['fullpath']);
			$html .= "<tr>";
			$html .= "<td>".$row['relapath']."</td>";
			//$html .= "<th scope='row'><a target='_blank' title='view image' href='".$row['fullurl']."'>".	$row['filename']."</a></th>";
			$html .= "<td><a target='_blank' title='view image' href='".$row['fullurl']."' class='bold'>".	$row['filename']."</a></td>";
			$html .= "<td align='right'>$imgw &nbsp;&nbsp; x</td>";
			$html .= "<td>$imgh</td>";
			$html .= "<td>$restore</td>";
			$html .= "<td align='right'>".round($row['size']/1024,0)." KB</td>";
			$html .= "<td>".date("Y-m-d", $row['mtime'])."</td>";
			$html .= "<td>$postlinkstxt</td>";
			$html .= "<td style=''>".
				'<input type="checkbox" class="xtra_image_cb" name="delXimage'.$i.'" id="delXimage'.$i.'" value="'.$row['fullpath'].'" />'.
				"</td>";
			$html .= "</tr>";
	}
	//echo "<h2>".$upload_dir."</h2>";
	//echo "<pre>".$filepath."</pre>";
	$xtra_get_images_count = $i;
	if ($ret=="array") return $xtra_images_list;
	if ($ret=="html") return $html;
}

function xtra_get_crons($ret="html",$filterHook="",$filterField="") {
global $xtra_get_crons_count;
global $crons;
	$crons = array();
	$cronsa = get_option('cron');
	$i = 0;
	foreach ( (array)$cronsa as $timestamp => $cronhooks ) {
		if (!$timestamp) continue;
		foreach ( (array)$cronhooks as $hook => $keys ) {
			if (!$hook) continue;
			if ($filterHook && $hook!=$filterHook) continue;
			$tkeys = "";
			foreach ( (array)$keys as $k => $v ) {
				$i++;
				$crons['timestamp'] = $timestamp;
				if ($timestamp) $crons['date'] = date("Y-m-d H:i:s",$timestamp);
				$crons['hook'] = $hook;
				$crons['key'] = $k;
				$cron_info_text .= "<tr><th scope='row'>".date("Y-m-d H:i:s",$timestamp)."</th>";
				$cron_info_text .= "<td>".$hook."</td>";
				//$cron_info_text .= "<td>".$k."</td>";
				$cron_info_text .= '<input type="hidden" name="cronHook'.$i.'" value="'.$hook.'" />';
				if (is_array($v)) {
					foreach ($v as $k1 => $v1) {
						if (is_array($v1) && !empty($v1)) $tkeys .= "(".implode(", ",$v1)."), ";
						else if ($v1) $tkeys .= "$v1, ";
					}
				}
				else if ($v) $tkeys .= "$v, ";
			}
			$crons['options'] = $tkeys;
			$cron_info_text .= "<td>".$tkeys."</td>";
			$cron_info_text .= '<td style=""><input type="checkbox" name="delXcron'.$i.'" id="delXcron'.$i.'" value="1" /></td>';
			if ($filterHook && $filterField) return $crons[$filterField];
		}
	}
	$xtra_get_crons_count = $i;
	if ($ret=="array") return $crons;
	if ($ret=="html") return $cron_info_text;
}

//---DEFAULTS---
global $wpdb;
global $xtra_seltab, $xtra_tabs;
$xtra_seltab = "0";
global $xtra_pageload_time;
global $xtra_error_level_string;
$xtra_error_level_string = xtra_error_level_tostring(error_reporting(), ', ');

if ( ! function_exists( 'get_plugins' ) ) require_once ABSPATH . 'wp-admin/includes/plugin.php';

//---SUBMIT---

//. first click
if(@$_REQUEST['first']==1) update_option( 'xtra_msg', 1);

//. fetch, xfetch
if(isset($_POST['xtra_submit_last_seltab'])) $xtra_seltab = (string)$_POST['xtra_submit_last_seltab'];
if(@$_REQUEST['fetch']) $xtra_seltab = "database";
if(@$_REQUEST['xfetch']) $xtra_seltab = "database";
if(isset($_POST['xtra_submit_delcron'])) $xtra_seltab = "crons";
if(isset($_POST['xtra_submit_muteplugin'])) $xtra_seltab = "plugins";
if(isset($_POST['xtra_submit_unmuteplugin'])) $xtra_seltab = "plugins";
if(isset($_POST['xtra_submit_delimage'])) $xtra_seltab = "images";
if(isset($_POST['xtra_refresh_images'])) $xtra_seltab = "images";

//echo "<pre>".print_r($_REQUEST,1)."</pre>";
//. auto-update
if(isset($_GET['do'])) {
	xtra_check_nonce();
	if ($_GET['do'] == 'maybe_auto_update') {
		//do_action( 'wp_maybe_auto_update' ); //the action is after the html output
		$settings_status = '<div class="notice notice-success is-dismissible"><h3>Auto-Update started successfully in the background.</h3></div>';
	}
}
//. own options
if(isset($_POST['xtra_submit_options'])) {
	xtra_check_nonce();
	update_option( 'xtra_opt_light', $_POST['xtra_opt_light'] );
	update_option( 'xtra_opt_vertical', $_POST['xtra_opt_vertical'] );
	update_option( 'xtra_opt_show_images', $_POST['xtra_opt_show_images'] );
	update_option( 'xtra_opt_show_plugins', $_POST['xtra_opt_show_plugins'] );
}
//. CHANGES
if(isset($_POST['xtra_submit_changes'])) {
	xtra_check_nonce();
	$xtra_sets = xtra_make_dataset();
	if (is_array($_POST['xtra_plugins_exclude'])) {
		update_option( 'xtra_plugins_exclude', $_POST['xtra_plugins_exclude'] );
	}
	else {
		update_option( 'xtra_plugins_exclude', array() );
	}
	if (is_array($_POST['xtra_categories_exclude'])) {
		update_option( 'xtra_categories_exclude', $_POST['xtra_categories_exclude'] );
	}
	else {
		update_option( 'xtra_categories_exclude', array() );
	}
	foreach ($xtra_sets as $setname => $set) {
		if ($set['submitX']) {
			$statusa[$setname] = $setname($_POST[$setname]); // call function (update_option in function)
		} else {
			update_option( $setname, stripslashes_deep(sanitize_text_field($_POST[$setname])) ); // just update_option
		}
		if ($setname == 'xtra_posts_status_color') {
			update_option('xtra_posts_status_color1',sanitize_text_field($_POST['xtra_posts_status_color1']));
			update_option('xtra_posts_status_color2',sanitize_text_field($_POST['xtra_posts_status_color2']));
			update_option('xtra_posts_status_color3',sanitize_text_field($_POST['xtra_posts_status_color3']));
			update_option('xtra_posts_status_color4',sanitize_text_field($_POST['xtra_posts_status_color4']));
		}
	}
	if (in_array(false,$statusa)) {
		$settings_status = '<div class="notice notice-error is-dismissible"><h3>Error applying some settings.</h3><table class="wp-list-table fixed striped xtra">';
		foreach($statusa as $stsname=>$sts) {
			$settings_status .= "<tr><td>".$xtra_sets[$stsname]['label']."</td><td class='p-left-100'>".($sts?"OK":"Error")."</td></tr>";
		}
		$settings_status .= '</table></div>';
	} else {
		$settings_status = '<div class="notice notice-success is-dismissible"><h3>Settings saved successfully<small> &nbsp;&nbsp;&nbsp;(The effect may be visible at the next load)</small></h3></div>';
	}
}
//. Plugins
else if(isset($_POST['xtra_submit_muteplugin'])) {
	if ($_POST['xtra_submit_muteplugin_sure']!="1") {
		$settings_status = '<div class="notice notice-error is-dismissible"><h3>Sorry, you have not checked the "I am sure" checkbox.</h3>';
	}
	$settings_status = '<div class="notice notice-error is-dismissible"><h3>This function does not work yet.</h3>';
	
	/*
	$all_plugins = get_plugins();
	$mutedir = WP_PLUGIN_DIR ."/xtra-muted-plugins";
	//if ( !file_exists( $mutedir ) ) wp_mkdir_p( $mutedir );
	foreach ($all_plugins as $pl_file=>$pl_data) {
		if (!isset($pl_data['Name'])) continue;
		$i++;
		$settings_status .= $i.". ".$pl_file." : ".$_POST['muteXplugin'.$i]."<br/>";
		$pldir = dirname($pl_file);
		if ($_POST['muteXplugin'.$i]) {
			$from = WP_PLUGIN_DIR ."/".$pldir;
			$to = $mutedir."/".$pldir;
			$settings_status .= "...rename( $from, $to )<br/>";
		}
		else { 
			if ( file_exists( $mutedir."/".$pldir ) ) {
				$from = $mutedir."/".$pldir;
				$to = WP_PLUGIN_DIR ."/".$pldir;
				$settings_status .= "...rename( $from, $to )<br/>";
			}
		}
	}
	*/
	
	
	
	/*
	$val = $wpdb->get_var( "SELECT option_value FROM `$wpdb->options` WHERE option_name = 'active_plugins';" );
	preg_match('/i:\d+;s:\d+:"xtra.*?\.php";/i',$val,$matches);
	$me = $matches[0];
	$val_me = "a:1:{".$me."}";
	preg_match('/^a:(\d+)/i',$val,$matches);
	$anum = (int)$matches[1];
	$val_no_me = str_replace($me,"",$val);
	$val_no_me = preg_replace('/^a:(\d+)/i',"a:".($anum-1),$val_no_me);
	$settings_status = '<div class="notice notice-error is-dismissible"><h3>This function does not work yet.</h3>';
	//$settings_status .= "<hr>$val";
	//$settings_status .= "<hr>$me";
	//$settings_status .= "<hr>$val_me";
	//$settings_status .= "<hr>$val_no_me";
	// TBD...
	*/
	
	$settings_status .= '</div>';
}
else if(isset($_POST['xtra_submit_unmuteplugin'])) {
	if ($_POST['xtra_submit_unmuteplugin_sure']!="1") {
		$settings_status = '<div class="notice notice-error is-dismissible"><h3>Sorry, you have not checked the "I am sure" checkbox.</h3></div>';
	}
	$settings_status = '<div class="notice notice-error is-dismissible"><h3>This function does not work yet.</h3></div>';
	// TBD...
}
//. Crons
else if(isset($_POST['xtra_submit_delcron'])) {
	if ($_POST['xtra_submit_delcron_sure']!="1") {
		$settings_status = '<div class="notice notice-error is-dismissible"><h3>Sorry, you have not checked the "I am sure" checkbox.</h3></div>';
	}
	foreach ($_POST as $key => $val) {
		if (strpos($key,"delXcron")!==FALSE) {
			$num = str_replace("delXcron","",$key);
			$posthook = $_POST['cronHook'.$num];
			$i = 0;
			$cronsa = get_option('cron');
			foreach ( (array)$cronsa as $timestamp => $cronhooks ) {
				if (!$timestamp) continue;
				foreach ( (array)$cronhooks as $hook => $keys ) {
					if (!$hook) continue;
					$i++;
					if ($i==$num && $hook==$posthook) {
						$tkey = 0;
						foreach ( (array)$keys as $k => $v ) {
							$tkey = $k;
							break;
						}
						if (is_array($keys) && isset($keys[$tkey]['interval']) ) {
							//wp_clear_scheduled_hook( $hook, $keys );
							if ($_POST['xtra_submit_delcron_sure']=="1") {
								wp_unschedule_event( $timestamp, $hook, $keys );
								$settings_status = '<div class="notice notice-success is-dismissible"><h3>Cron job '.$hook.' (+args) deleted from '.date("Y-m-d H:i:s",$timestamp).'</h3></div>';
							}
							else
								$settings_status .= '<div class="notice notice-error is-dismissible"><h4>'.$hook.' (+args) - '.date("Y-m-d H:i:s",$timestamp).'</h4></div>';
						}
						else {
							//wp_clear_scheduled_hook( $hook );
							if ($_POST['xtra_submit_delcron_sure']=="1") {
								wp_unschedule_event( $timestamp, $hook, array() );
								$settings_status = '<div class="notice notice-success is-dismissible"><h3>Cron job '.$hook.' deleted from '.date("Y-m-d H:i:s",$timestamp).'</h3></div>';
							}
							else
								$settings_status .= '<div class="notice notice-error is-dismissible"><h4>'.$hook.' - '.date("Y-m-d H:i:s",$timestamp).'</h4></div>';
						}
					}
				}
			}
		}
	}
}
//. Images
else if(isset($_POST['xtra_submit_delimage'])) {
	if ($_POST['xtra_submit_delimage_sure']!="1") {
		$settings_status = '<div class="notice notice-error is-dismissible"><h3>Sorry, you have not checked the "I am sure" checkbox.</h3></div>';
	}
	$settings_status = '<div class="notice notice-error is-dismissible"><h3>This function does not work yet.</h3></div>';
	// TBD...
}
//. DB Backup
else if(isset($_POST['xtra_database_backup'])) {
	xtra_check_nonce();
	xtra_EXPORT_TABLES(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
}
//. DB Restore
else if(isset($_POST['xtra_database_restore'])) {
	xtra_check_nonce();
	if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
	if (!current_user_can('upload_files')) // Verify the current user can upload files
		wp_die(__('You do not have permission to upload files.'));
	$allowedMimes = array(
		'sql' => 'text/sql',
	);
	$fileInfo = wp_check_filetype(basename($_FILES['file']['name']), $allowedMimes);
	if (!empty($fileInfo['type'])) {
	add_filter( 'upload_dir', 'xtra_upload_dir' );
	$movefile = wp_handle_upload($_FILES['file'], array('test_form'=>false,'mimes'=>$allowedMimes) );
	remove_filter( 'upload_dir', 'xtra_upload_dir' );

		if ( $movefile && ! isset( $movefile['error'] ) ) {
			echo "File is valid, and was successfully uploaded.\n";
			//var_dump( $movefile );
		} else {
			echo $movefile['error'];
		}
	}
	//xtra_IMPORT_TABLES(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME,$movefile['file']);
	if (file_exists($movefile['file'])) unlink($movefile['file']);
	//$settings_status = '<div class="notice notice-success is-dismissible"><h3>Database restored successfully.</h3></div>';
	$settings_status = '<div class="notice notice-error is-dismissible"><h3>This function does not work yet.</h3></div>';
}
//. DB Optimize
else if(isset($_POST['xtra_optimize_submit'])) {
	xtra_check_nonce();
	$status = xtra_table_optimize($_POST['hid_tables']);
	if($status) {
		$settings_status = '<div class="notice notice-success is-dismissible"><h3>Database optimized successfully.</h3></div>';
	} else {
		$settings_status = '<div class="notice notice-error is-dismissible"><h3>Error occured while optimizing.</h3></div>';
	}
}
//. DB Cleanup
else if(isset($_POST['xtra_rem_opt_submit'])) {
	xtra_check_nonce();
	$status = xtra_table_remove($_POST['hid_twsirem']);
	if($status) {
		$settings_status = '<div class="notice notice-success is-dismissible"><h3>Removed successfully.</h3></div>';
	} else {
		$settings_status = '<div class="notice notice-error is-dismissible"><h3>Error occured while removing.</h3></div>';
	}
}
//. Reset Options
else if(isset($_POST['xtra_reset_opt_submit']) && $_POST['hid_reset_all']==1) {
	xtra_check_nonce();
	xtra_delete_all_options();
	$settings_status = '<div class="notice notice-success is-dismissible"><h3>All XTRA options deleted and reset successfully.</h3></div>';
}



//---HTML output---
?>
<div class="wrap">
	<h1>XTRA Settings<span class="m-left-30 small">Current version: <?php echo XTRA_VERSION;?></span></h1>
	<?php echo $settings_status;?>
    <div class="xtra_full_wr">
	<?php
	//---Left Column
	//echo "<pre>seltab = ".print_r($xtra_seltab,1)."</pre>";
	$vertical = "";
	if (get_option('xtra_opt_vertical')) $vertical = "vertical-tabs";
	?>
    <div class="xtra_left <?php echo $vertical; ?>">

	<h2 class="nav-tab-wrapper">
		<?php
		$thisHtml = xtra_make_html();
		$xtra_tabcols = xtra_get_tab_colors();
		//. Tabs
		$i = 0;
		foreach ($xtra_tabs as $thistab) {
			if ($thistab!=$oldtab) {
				$addi = "";
				if ($xtra_seltab===(string)$i) $addi = "nav-tab-active";
				$tsty = ' style="border-top: 4px solid '.$xtra_tabcols[$thistab]['color'].';"';
				echo '<a class="nav-tab '.$addi.'" href="" activate="'.$i.'"'.$tsty.'>'.$thistab.'</a>';
				$oldtab = $thistab;
				$i++;
			}
		}
		echo '<a class="nav-tab b-top-4-ccc '.($xtra_seltab=="database"?"nav-tab-active":"").'" href="" activate="database">Database</a>';
		
		$show_plugins 	= get_option('xtra_opt_show_plugins',1);
		$show_crons 	= 1;
		$show_images 	= get_option('xtra_opt_show_images',1);
		$show_all 		= 0;
		if ( $show_crons ) 		echo '<a class="nav-tab b-top-4-ccc '.($xtra_seltab=="crons"?"nav-tab-active":"").'" href="" activate="crons">Crons</a>';
		if ( $show_plugins ) 	echo '<a class="nav-tab b-top-4-ccc '.($xtra_seltab=="plugins"?"nav-tab-active":"").'" href="" activate="plugins">Plugins</a>';
		if ( $show_images ) 	echo '<a class="nav-tab b-top-4-ccc '.($xtra_seltab=="images"?"nav-tab-active":"").'" href="" activate="images">Images</a>';
		if ( $show_all ) 		echo '<a class="nav-tab b-top-4-ccc '.($xtra_seltab=="*"?"nav-tab-active":"").'" href="" activate="*">All</a>';
		?>
	</h2>


<div class="xtra-tab-wrapper">


	<form action="?page=xtra" method="post">
	<?php
	//. All Settings
	echo $thisHtml;
	wp_nonce_field( 'mynonce' );
	?>
    <div class="xtra_opt_submit butt-wrap <?php if (strlen($xtra_seltab)>1) echo "disp-none"; ?>">
	<input type="submit" name="xtra_submit_changes" value="Save All Options" class="button button-primary button-hero mybigbutt" /></div>
	<input type="hidden" name="xtra_submit_last_seltab" id="xtra_submit_last_seltab" value="<?php echo $xtra_seltab; ?>" />
	</form>


	<?php
	//. Crons
	if ( $show_crons ) {
	?>
	<?php

	$html = '<tr class="xtra-thead">
		<th scope="row">Timestamp</th>
		<td>Hook</td>
		<td>Options</td>
		<td><input type="checkbox" onclick="jQuery(\'#cronform input:checkbox\').not(this).not(\'#xtra_submit_delcron_sure\').prop(\'checked\', this.checked);"</td>
	</tr>
	';
	$html .= xtra_get_crons("html");
	global $xtra_get_crons_count;
	?>
	<div class="xtra_box xtra_tabbes xtra_tabbeID_crons <?php if ($xtra_seltab=="crons" || $xtra_seltab==="*") echo "active"; ?>">
		<h2 class="icbefore dashicons-clock">Listing <?php echo $xtra_get_crons_count; ?> Active Cron Jobs<span class="m-left-50 small"><?php echo "Server time: ".date("Y-m-d H:i:s");?></span></h2>
		<form action="?page=xtra" method="post" id="cronform">
		<table class="wp-list-table widefat striped xtra slimrow">
			<?php echo $html; ?>
		</table>
		<div class="butt-wrap">
		<label for="xtra_submit_delcron_sure"><input type="checkbox" name="xtra_submit_delcron_sure" id="xtra_submit_delcron_sure" value="1" />I am sure</label><br/><br/>
		<input type="submit" name="xtra_submit_delcron" value="Delete All Selected Cron Jobs" class="button button-primary button-hero mybigbutt" />
		</div>
		<?php wp_nonce_field( 'mynonce' ); ?>
		</form>
	</div>
	<?php
	}
	?>


	<?php
	//. Plugins
	if ( $show_plugins ) {
	?>
	<?php
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$all_plugins = get_plugins();
	//	[Name]	[PluginURI]	[Version]	[Description]	[Author]	[AuthorURI]	[TextDomain]	[DomainPath]	[Network]	[Title]	[AuthorName]
	$html = '<tr class="xtra-thead">
		<td></td>
		<th scope="row">Name</th>
		<td>Version</td>
		<td>Links</td>
		<td><input type="checkbox" onclick="jQuery(\'#pluginform input:checkbox\').not(this).not(\'#xtra_submit_delplugin_sure\').prop(\'checked\', this.checked);"</td>
	</tr>
	';
	$i = 0;
	$a = 0;
	foreach ($all_plugins as $pl_file=>$pl_data) {
		if (!isset($pl_data['Name'])) continue;
		$i++;
		$t1 = $t2 = $t3 = $t4 = $t5 = "";
		$t1 = $pl_data['Name'];
		$t2 = $pl_data['Version'];
		if ($pl_data['PluginURI']) $t3 = "<a target='_blank' title='Plugin Page' href='".$pl_data['PluginURI']."'><div class='dashicons dashicons-admin-plugins'></div></a>&nbsp;";
		if ($pl_data['AuthorURI']) $t3 .= "<a target='_blank' title='Author Page: ".$pl_data['Author']."' href='".$pl_data['AuthorURI']."'><div class='dashicons dashicons-admin-users'></div></a>&nbsp;";
		$t3 .= "<a target='_blank' title='Plugin File: ".$pl_file."'><div class='dashicons dashicons-media-default'></div></a>";
		$t4 = '<input type="checkbox" name="muteXplugin'.$i.'" id="muteXplugin'.$i.'" value="1" />';
		//$t5 = "".substr($pl_file,0,strpos($pl_file,"/"))."";

		//$t1 = 	xtra_longword($t1, 20);
		//$t5 = 	xtra_longword($t5, 20);
		if (false) $xres = xtra_get_plugin_prefix($pl_file);

		$sty = "";
		$sty2 = "border-left: 4px solid #00a0d2;";
		$curval = 1;
		$a++;
		if (!is_plugin_active($pl_file)) {
			$a--;
			$curval = 0;
			$sty .= "color:#BBB;";
			$sty2 = "";
			$t4 = "";
		}
		else if (stripos($pl_data['Name'],"XTRA")===0) $t4 = "";
		//$t0 = '<input type="checkbox" name="plg'.$i.'" id="plg'.$i.'" value="1" '.(($curval==0)?'':'checked="checked"').' />';
		$html .= "<tr>
			<td style='text-align: right; $sty $sty2'>".($t0)."</td>
			<th scope='row' style='$sty'>".($t1)."</th>
			<td style='$sty'>".($t2)."</td>
			<td style='$sty'>".($t3)."</td>
			<td style='$sty'>".($t4)."</td>
		</tr>
		";
//			<td style='$sty'>".($t5)."</td>
//			<td style='$sty'>".($xres)."</td>
	}
	?>
	<div class="xtra_box xtra_tabbes xtra_tabbeID_plugins <?php if ($xtra_seltab=="plugins" || $xtra_seltab==="*") echo "active"; ?>">
		<h2 class="icbefore dashicons-admin-plugins">Listing <?php echo $i; ?> Installed Plugins &nbsp;&nbsp;&nbsp;<small>(<?php echo $a; ?> active, <?php echo ($i-$a); ?> in-active)</small></h2>
		<form action="?page=xtra" method="post" id="pluginform">
		<table class="wp-list-table widefat striped xtra slimrow">
			<?php echo $html; ?>
		</table>
		<div class="butt-wrap">
		</div>
		<?php wp_nonce_field( 'mynonce' ); ?>
		</form>
	</div>
	<?php
//		<label for="xtra_submit_muteplugin_sure"><input type="checkbox" name="xtra_submit_muteplugin_sure" id="xtra_submit_muteplugin_sure" value="1" />I am sure</label><br/><br/>
//		<input type="submit" name="xtra_submit_muteplugin" value="Save All Mute-State" class="button button-primary button-hero mybigbutt" />
	}
	?>


	<?php
	//. Images
	if ( $show_images ) {
	?>
	<?php

	$html = '<tr class="xtra-thead">
		<td>Path</td>
		<td>Name</td>
		<td>W</td>
		<td>H</td>
		<td></td>
		<td>Size</td>
		<td>Mod</td>
		<td>Links</td>
		<td><input type="checkbox" onclick="jQuery(\'#imageform input:checkbox\').not(this).not(\'#xtra_submit_delimage_sure\').not(\'#xtra_ajax_sure\').not(\'#xtra_ajax_convGIF\').not(\'#xtra_ajax_convBMP\').not(\'#xtra_ajax_convPNG\').prop(\'checked\', this.checked);"</td>
	</tr>
	';
	$html .= xtra_get_images("html");
	global $xtra_get_images_count;
	global $xtra_get_images_size;
	?>
	<div class="xtra_box xtra_tabbes xtra_tabbeID_images <?php if ($xtra_seltab=="images" || $xtra_seltab==="*") echo "active"; ?>">
		<form action="?page=xtra" method="post" id="imageform">
		<h2 class="icbefore dashicons-format-gallery">Listing <?php echo $xtra_get_images_count; ?> Images <?php echo "(".round($xtra_get_images_size/1024/1024,0)." MB".")"; ?>
		<input type="submit" name="xtra_refresh_images" value="Refresh Image List" class="button button-primary bold float-right" />
		</h2>
		<div class="xtra h-350 clear">
		<table class="wp-list-table widefat striped xtra slimrow">
			<?php 
			echo $html; 
			?>
		</table>
		</div>
		<label for="xtra_ajax_compPRC">Compression quality:<input type="text" size="2" id="xtra_ajax_compPRC" value="<?php echo get_option('xtra_custom_jpeg_quality_num',82);?>" />%</label>
		&nbsp;&nbsp;&nbsp;
		<?php if (extension_loaded('gd') && function_exists('gd_info') ) { ?>
		<label for="xtra_ajax_compM1"><input type="radio" name="xtra_ajax_compM" id="xtra_ajax_compM1" value="1" onchange="jQuery('#xtra_ajax_maxW_span,#xtra_ajax_maxH_span').toggle(!this.checked);" />PHP GD method</label>
		&nbsp;&nbsp;&nbsp;
		<?php } ?>
		<label for="xtra_ajax_compM2"><input type="radio" name="xtra_ajax_compM" id="xtra_ajax_compM2" value="2" checked="checked" onchange="jQuery('#xtra_ajax_maxW_span,#xtra_ajax_maxH_span').toggle(this.checked);" />WP-Editor method</label>
		&nbsp;&nbsp;&nbsp;
		<span id="xtra_ajax_maxW_span">
		<label for="xtra_ajax_maxW">Max Width:<input type="text" size="3" id="xtra_ajax_maxW" value="1600" />px</label>
		&nbsp;&nbsp;&nbsp;
		</span>
		<span id="xtra_ajax_maxH_span">
		<label for="xtra_ajax_maxH">Max Height:<input type="text" size="3" id="xtra_ajax_maxH" value="1600" />px (use 0 for not resize)</label>
		</span>
		<br/><br/>
		<div class="butt-wrap">
			<div id="xtra_ajax_buttonsDiv">
				<strong>With Selected Images:</strong>
				&nbsp;&nbsp;&nbsp;
				<input type="checkbox" id="xtra_ajax_sure" value="1" /><label for="xtra_ajax_sure">I am sure:</label>
				&nbsp;&nbsp;&nbsp;
				<input type="button" id="xtra_ajax_compressButton" value="Compress Images" title="...with auto-backup" class="button button-primary bold" onclick="xtra_do_images('Compress');" />
				&nbsp;&nbsp;&nbsp;
				<input type="button" id="xtra_ajax_restoreButton" value="Restore from Backup" class="button button-primary bold" onclick="xtra_do_images('Restore');" />
				&nbsp;&nbsp;&nbsp;
				<input type="button" id="xtra_ajax_delbackupButton" value="Delete Backup File" class="button button-primary bold" onclick="xtra_do_images('Delete Backup');" />
			</div>
			<input type="button" id="xtra_ajax_stopButton" value="Stop Compress" style="display:none;" class="button button-primary button-hero mybigbutt" onclick="xtra_do_stop();" />
			<div id="xtra_ajax_resultsDiv" class="xtra_ajax_response"></div>
		</div>
		<?php wp_nonce_field( 'mynonce' ); ?>
		</form>
	</div>
	<?php
/*		<br/>
		<br/>
		<input type="checkbox" id="xtra_ajax_convGIF" value="1"><label for="xtra_ajax_convGIF">Convert GIF to JPG</label>&nbsp;&nbsp;&nbsp;
		<input type="checkbox" id="xtra_ajax_convBMP" value="1"><label for="xtra_ajax_convBMP">Convert BMP to JPG</label>&nbsp;&nbsp;&nbsp;
		<input type="checkbox" id="xtra_ajax_convPNG" value="1"><label for="xtra_ajax_convPNG">Convert PNG to JPG</label>
*/
//		<label for="xtra_submit_delimage_sure"><input type="checkbox" name="xtra_submit_delimage_sure" id="xtra_submit_delimage_sure" value="1" />I am sure</label><br/><br/>
//		<input type="submit" name="xtra_submit_delimage" value="Delete All Selected Images" class="button button-primary button-hero mybigbutt" />
	}
	?>


	<?php
	//. Images sizes
	if ( $show_images ) {
	?>
	<?php
	$all_image_sizes = xtra_get_image_sizes();
	$html = '<tr class="xtra-thead">
		<td></td>
		<th scope="row">Name</th>
		<td>Width</td>
		<td>Height</td>
		<td>Crop</td>
		<td><input type="checkbox" onclick="jQuery(\'#imgsizform input:checkbox\').not(this).not(\'#xtra_submit_imgsiz_sure\').prop(\'checked\', this.checked);"</td>
	</tr>
	';
	$i = 0;
	foreach ($all_image_sizes as $ims_name=>$ims_data) {
		$i++;
		$t1 = $t2 = $t3 = $t4 = $t5 = "";
		$t1 = $ims_name;
		$t2 = $ims_data['width'];
		$t3 = $ims_data['height'];
		$t4 = $ims_data['crop'];
		$t5 = '<input type="checkbox" name="imgsiz'.$i.'" id="imgsiz'.$i.'" value="1" />';

		$sty = "";
		$sty2 = ""; //"border-left: 4px solid #00a0d2;";
		$curval = 1;

		$html .= "<tr>
			<td style='text-align: right; $sty $sty2'>".($t0)."</td>
			<th scope='row' style='$sty'>".($t1)."</th>
			<td style='$sty'>".($t2)."</td>
			<td style='$sty'>".($t3)."</td>
			<td style='$sty'>".($t4)."</td>
			<td style='$sty'>".($t5)."</td>
		</tr>
		";
	}
	?>
	<br/>
	<br/>
	<div class="xtra_box xtra_tabbes xtra_tabbeID_images <?php if ($xtra_seltab=="images" || $xtra_seltab==="*") echo "active"; ?>">
		<h2 class="icbefore dashicons-format-gallery">Listing <?php echo $i; ?> Image Sizes</h2>
		<form action="?page=xtra" method="post" id="imgsizform">
		<table class="wp-list-table widefat striped xtra slimrow">
			<?php echo $html; ?>
		</table>
		<div class="butt-wrap">
		</div>
		<?php //wp_nonce_field( 'mynonce' ); ?>
		</form>
	</div>
	<?php
	}
	?>


	<?php
	//. Database Backup
	?>
	<div class="xtra_box xtra_tabbes xtra_tabbeID_database <?php if (($xtra_seltab=="database" || $xtra_seltab==="*") && @$_REQUEST['fetch']!=1) echo "active"; ?>">
		<h2 class="icbefore dashicons-download">Database Backup</h2>
		<table class="wp-list-table widefat fixed striped xtra">

	<form action="?page=xtra" method="post">
		<tr><th>Backup the whole WordPress database<br/><span>(Download in an .sql file)</span></th><td>
		<p class="submit"><input type="submit" name="xtra_database_backup" value="Backup" class="button button-primary" /></p>
		</td></tr>
		<?php wp_nonce_field( 'mynonce' ); ?>
		<input type="hidden" name="xtra_submit_last_seltab" id="xtra_submit_last_seltab" value="database" />
	</form>

	<?php
	//. Database Restore
	/*
	?>
	<form action="?page=xtra" method="post" enctype="multipart/form-data" onsubmit="return confirm('Do you really want to DELETE all Database & RESTORE from this file?');">
		<tr><th>Restore database<br/><span>(From exported .sql file)</span></th><td>
		<p>
		<label for="file">Select File To Upload:<br/><input type="file" id="file" name="file" value="" /></label>
		
		</p>
		<p class="submit"><input type="submit" name="xtra_database_restore" value="Restore Now" class="button button-primary" /></p>
		</td></tr>
		<?php wp_nonce_field( 'mynonce' ); ?>
		<input type="hidden" name="xtra_submit_last_seltab" id="xtra_submit_last_seltab" value="database" />
	</form>
	<?php
	*/
	?>
		</table>
    </div>


	<?php
	//. Database Cleanup
	$a1 = '<a href="admin.php?page=xtra&xfetch=';
	$ax = 1;
	$a2 = '">';
	$a3 = '</a>';
	$tnames = array(
		'Revisions',
		'Auto-Drafts',
		'Trash Posts',
		'Spam Comments',
		'Disapproved Comments',
		'Transient Options<br/><span>(site and feed)',
		'Expired Transients<br/><span>(more than 7 days)',
	);
	?>
    <div class="xtra_box xtra_tabbes xtra_tabbeID_database  <?php if (($xtra_seltab=="database" || $xtra_seltab==="*") && @$_REQUEST['fetch']!=1) echo "active"; ?>">
    <h2 class="icbefore dashicons-trash">Database Cleanup</h2>
    <table class="wp-list-table widefat fixed striped xtra">
	<?php
	$i = 0;
	foreach ($tnames as $tname) {
		echo '<tr><th>Delete '.$tname.'</th><td><form action="?page=xtra" method="post"><input type="hidden" name="hid_twsirem" value="'.++$i.'" />
			<input type="submit" name="xtra_rem_opt_submit" value="Clean Now" class="button button-primary" />
			&nbsp;&nbsp;&nbsp; current: '.$a1.$ax++.$a2.xtra_table_remove_count($i).$a3.'';
		wp_nonce_field( 'mynonce' );
		echo '<input type="hidden" name="xtra_submit_last_seltab" id="xtra_submit_last_seltab" value="database" />';
		echo '</form></td></tr>';
		if(@$_REQUEST['xfetch']==$i) echo "<tr><td colspan=2><textarea class='texta-350'>".xtra_table_remove_show($i)."</textarea></td></tr>";
	}
	?>
    </table>
    </div>



	<?php
	//. Optimize Database
	?>
	<div class="xtra_box xtra_tabbes xtra_tabbeID_database  <?php if ($xtra_seltab=="database" || $xtra_seltab==="*") echo "active"; ?>">
		<h2 class="icbefore dashicons-admin-generic">Optimize Database and Tables</h2>
		<?php if(@$_REQUEST['fetch']!=1){ ?>
			<table class="wp-list-table widefat fixed striped xtra">
			<tr><th>Show database tables, sizes and overload<br/><span>(Optimize button will be at the bottom of the table list)</span></th><td>
				<p class="submit"><a href="admin.php?page=xtra&fetch=1" class="button button-primary" />Show Tables</a></p>
			</td></tr>
			</table>
		<?php } else { ?>
			<form action="?page=xtra" method="post">
				<p class="submit"><input type="submit" name="xtra_submit_hide_tables" value="Hide Tables" class="button button-primary" /></p>
				<input type="hidden" name="xtra_submit_last_seltab" id="xtra_submit_last_seltab" value="database" />
				<?php wp_nonce_field( 'mynonce' ); ?>
			</form>
		<?php } ?>
		<?php
		if(@$_REQUEST['fetch']==1){

		$sql = "SELECT TABLE_NAME AS table_name, DATA_FREE as data_overload, (data_length + index_length) as data_size FROM information_schema.TABLES WHERE table_schema = '".DB_NAME."'";
		$results = $wpdb->get_results( $sql, OBJECT );
		$total_size = 0;
		$total_overload_size = 0;
		//print_r($results);
		?>
		<table class="wp-list-table widefat fixed striped xtra">
			<tr><th>Name</th><th>Size</th><th>Overload</th></tr>
			<?php
			$table_str = "";
			foreach($results as $result){
				$table_str .= $result->table_name.',';
				?>
				<tr>
				  <td><?php echo $result->table_name;?></td>
				  <td><?php $total_size += $result->data_size; echo xtra_getSizes($result->data_size);?></td>
				  <td class="xtra_box <?php if($result->data_overload>0){ echo 'xtra_error';} ?>"><?php $total_overload_size += $result->data_overload; echo xtra_getSizes($result->data_overload);?></td>
				</tr>
			<?php } //foreach ?>
			<tr><th>Total</th><th><?php echo xtra_getSizes($total_size); ?></th><th class="xtra_box <?php if($total_overload_size>0){ echo 'xtra_error';} ?>"><?php echo xtra_getSizes($total_overload_size); ?></th></tr>
		</table>
		<form action="?page=xtra" method="post">
			<input type="hidden" name="hid_tables" value="<?php echo rtrim($table_str,',');?>" />
			<p class="submit"><input type="submit" name="xtra_optimize_submit" value="Optimize Tables" class="button button-primary" /></p>
			<input type="hidden" name="xtra_submit_last_seltab" id="xtra_submit_last_seltab" value="database" />
			<?php wp_nonce_field( 'mynonce' ); ?>
		</form>
		<?php } //if(@$_REQUEST... ?>
    </div>





	</div><!--xtra-tab-wrapper-->
	</div><!--xtra_left-->







	<?php
	//---Right Column
	?>
    <div class="xtra_right">
	<?php
	//. Donation
	?>
    <div class="xtra_box">
		<h2>Enjoy? Consider a Donation</h2>
		<table class="wp-list-table widefat fixed striped xtra"><tr><td>
		<p>Your support is the appreciation for my efforts I devote to XTRA from my free time.</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="PWGY7HZXMJS5G">
<table>
<tr><td><input type="hidden" name="on0" value="Donate to support this free plugin">Donate to support this free plugin</td></tr><tr><td><select name="os0">
	<option value="Medium">Medium $45.00 USD</option>
	<option value="Small">Small $15.00 USD</option>
	<option value="Larger">Larger $145.00 USD</option>
</select> </td></tr>
</table>
<input type="hidden" name="currency_code" value="USD">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
	</td></tr></table>
	</div>
	<?php
	?>



	<?php
	$xtra_pageload_time = timer_stop(0);
	//. Site Info
	$site_info = array(
		'Page Load Time'			=> $xtra_pageload_time . " sec",
		'Database Queries'			=> get_num_queries(),
		'Memory Usage'				=> memory_get_usage(true)/1024/1024 . " MB",
		'Peak Memory'				=> memory_get_peak_usage(true)/1024/1024 . " MB",
		'Memory Limit'				=> ini_get('memory_limit'),
		'Max upload file size'		=> ini_get('upload_max_filesize'),
		'Max script exec time'		=> ini_get('max_execution_time') . " sec",
		'Error Reporting Level'		=> $xtra_error_level_string,
		'Web server'				=> $_SERVER["SERVER_SOFTWARE"],
		'PHP version'				=> phpversion(),
		'MySQL version'				=> $wpdb->db_version(),
		'------------------'		=> 'BLOG INFO',
		'name'						=> get_bloginfo('name'),
		'description'				=> get_option('blogdescription'),
		'admin_email'				=> get_option('admin_email'),
		'WP version'					=> get_bloginfo('version'),
		'charset'					=> (get_option('blog_charset')?get_option('blog_charset'):'UTF-8'),
		'html_type' 				=> get_option('html_type'),
		'Site URL'					=> site_url(),
		'Home URL'					=> home_url(),
		'ABSPATH'					=> ABSPATH,
		'CONTENT_DIR'				=> WP_CONTENT_DIR,
		'PLUGIN_DIR'				=> WP_PLUGIN_DIR,
		'stylesheet_directory'		=> get_stylesheet_directory_uri(),
		'stylesheet_url'			=> get_stylesheet_uri(),
		'template_directory'		=> get_stylesheet_directory_uri(),
		'template_url'				=> get_template_directory_uri(),
		'rss_url'					=> get_feed_link('rss'),
		'rss2_url'					=> get_feed_link('rss2'),
		'atom_url'					=> get_feed_link('atom'),
		'rdf_url'					=> get_feed_link('rdf'),
		'comments_rss2_url'			=> get_feed_link('comments_rss2'),
		'comments_atom_url'			=> get_feed_link('comments_atom'),
		'pingback_url'				=> site_url( 'xmlrpc.php' ),
	);
	$site_info_text = "";
	foreach ($site_info as $key=>$val) {
		$site_info_text .= "<tr><td class='padd-2-10'>".esc_html($key)."</td><td class='padd-2-10'>".esc_html($val)."</td></tr>";
	}

	?>
    <div class="xtra_box">
		<h2>Site Info</h2>
		<table class="wp-list-table widefat fixed striped xtra"><tr><td>
			<div class="max-h-150" onclick="if(this.style.maxHeight!='400px'){this.style.maxHeight='400px';}else{this.style.maxHeight='150px';}">
				<table class="wp-list-table widefat striped xtra">
					<?php echo $site_info_text;?>
				</table>
			</div>
		</td></tr></table>
    </div>

	
	
	
	
	
	<?php
	//. XTRA Plugin Settings
	?>
    <div class="xtra_box"><form action="?page=xtra" method="post">
		<h2>XTRA Plugin Settings</h2>
		<table class="wp-list-table widefat fixed striped xtra">
			<tr><th>Light view<br/></th><td>
				<input type="checkbox" name="xtra_opt_light" value="1" <?php if (get_option( 'xtra_opt_light', 0 )==1) echo 'checked="checked"'; ?> />
			</td></tr>
			<tr><th>Vertical Tabs<br/></th><td>
				<input type="checkbox" name="xtra_opt_vertical" value="1" <?php if (get_option( 'xtra_opt_vertical', 0 )==1) echo 'checked="checked"'; ?> />
			</td></tr>
			<tr><th>Show Plugins<br/></th><td>
				<input type="checkbox" name="xtra_opt_show_plugins" value="1" <?php if (get_option( 'xtra_opt_show_plugins', 1 )==1) echo 'checked="checked"'; ?> />
			</td></tr>
			<tr><th>Show Images<br/></th><td>
				<input type="checkbox" name="xtra_opt_show_images" value="1" <?php if (get_option( 'xtra_opt_show_images', 1 )==1) echo 'checked="checked"'; ?> />
			</td></tr>
			<tr><td></td><td><input type="submit" name="xtra_submit_options" value="Save Settingss" class="button button-primary" />
		</table>
		<?php wp_nonce_field( 'mynonce' ); ?>
	</form></div>
	




	<?php
	//. Current htaccess
	$xtra_htacc_sts = extract_from_markers( ABSPATH . '.htaccess', "XTRA Settings" );
	?>
    <div class="xtra_box"><form action="?page=xtra" method="post">
		<h2>Current XTRA insertions into .htaccess</h2>
		<table class="wp-list-table widefat fixed striped xtra">
			<tr><td><textarea class="texta-150" onfocus="this.style.height='400px';" onblur="this.style.height='150px';"><?php echo esc_html(implode("\n",$xtra_htacc_sts));?></textarea></td></tr>
		</table>
		<?php wp_nonce_field( 'mynonce' ); ?>
    </form></div>

	
	
	
	
	
	<?php
	//. Current wp-config
	$xtra_wpconfig_sts = xtra_extract_from_markers( ABSPATH . 'wp-config.php', "XTRA Settings" );
	?>
    <div class="xtra_box"><form action="?page=xtra" method="post">
		<h2>Current XTRA insertions into wp-config.php</h2>
		<table class="wp-list-table widefat fixed striped xtra">
			<tr><td><textarea class="texta-150" onfocus="this.style.height='400px';" onblur="this.style.height='150px';"><?php echo esc_html(implode("\n",$xtra_wpconfig_sts));?></textarea></td></tr>
		</table>
		<?php wp_nonce_field( 'mynonce' ); ?>
    </form></div>


	

	
	<?php
	//. Delete All Options
	?>
	<div class="xtra_box">
	<h2>Delete All XTRA Options</h2>
	<table class="wp-list-table widefat fixed striped xtra">
		<tr><th>Delete and reset all XTRA options to Wordpress defaults<br/><span>(Also delete all XTRA insertions from .htaccess and wp-config.php files)</span></th><td>
		<form action="?page=xtra" method="post" onsubmit="return confirm('Do you really want to DELETE & RESET all XTRA options?');">
			<input type="hidden" name="hid_reset_all" value="1" />
			<input type="submit" name="xtra_reset_opt_submit" value="Reset All Now" class="button button-primary" />
			<?php wp_nonce_field( 'mynonce' ); ?>
		</form></td></tr>
	</table></div>




	</div><!--xtra_right-->
    <div class="clear"></div>

</div><!--xtra_full_wr-->
</div><!--wrap-->

<?php
if(isset($_GET['do'])) {
	xtra_check_nonce();
	if ($_GET['do'] == 'maybe_auto_update') {
		do_action( 'wp_maybe_auto_update' );
	}
}
?>

