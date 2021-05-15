<?php
/** 
 * ------------------------------------------------------------------------------
 * Plugin Name: From Twitter
 * Description: Automate the rerieval of tweets from Twitter to your ClassicPress site.
 * Version: 1.1.0
 * Author: azurecurve
 * Author URI: https://development.azurecurve.co.uk/classicpress-plugins/
 * Plugin URI: https://development.azurecurve.co.uk/classicpress-plugins/from-twitter/
 * Text Domain: from-twitter
 * Domain Path: /languages
 * ------------------------------------------------------------------------------
 * This is free sottware released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.html.
 * ------------------------------------------------------------------------------
 */

// Prevent direct access.
if (!defined('ABSPATH')){
	die();
}

// include plugin menu
require_once(dirname(__FILE__).'/pluginmenu/menu.php');
add_action('admin_init', 'azrcrv_create_plugin_menu_ft');

// include update client
require_once(dirname(__FILE__).'/libraries/updateclient/UpdateClient.class.php');

// include twitteroauth
require "libraries/twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Setup registration activation hook, actions, filters and shortcodes.
 *
 * @since 1.0.0
 *
 */
// register activation hook
register_activation_hook(__FILE__, 'azrcrv_ft_schedule_cron');

// add actions
add_action( 'init', 'azrcrv_ft_create_tweet_cpt' );
add_action( 'init', 'azrcrv_ft_create_tag_taxonomy', 0 );
add_action('admin_menu', 'azrcrv_ft_create_admin_menu');
add_action('add_meta_boxes', 'azrcrv_ft_create_tweet_metaboxes');
add_action('save_post', 'azrcrv_ft_save_tweet_details_metabox', 11, 2);
add_action('admin_post_azrcrv_ft_save_options', 'azrcrv_ft_save_options');
add_action('plugins_loaded', 'azrcrv_ft_load_languages');
add_action('azrcrv_ft_scheduled_cron', 'azrcrv_ft_scheduled_cron_perform');

// add filters
add_filter('plugin_action_links', 'azrcrv_ft_add_plugin_action_link', 10, 2);
add_filter('codepotent_update_manager_image_path', 'azrcrv_ft_custom_image_path');
add_filter('codepotent_update_manager_image_url', 'azrcrv_ft_custom_image_url');

/**
 * Load language files.
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_load_languages() {
    $plugin_rel_path = basename(dirname(__FILE__)).'/languages';
    load_plugin_textdomain('from-twitter', false, $plugin_rel_path);
}

/**
 * Custom plugin image path.
 *
 * @since 1.12.0
 *
 */
function azrcrv_ft_custom_image_path($path){
    if (strpos($path, 'azrcrv-from-twitter') !== false){
        $path = plugin_dir_path(__FILE__).'assets/pluginimages';
    }
    return $path;
}

/**
 * Custom plugin image url.
 *
 * @since 1.12.0
 *
 */
function azrcrv_ft_custom_image_url($url){
    if (strpos($url, 'azrcrv-from-twitter') !== false){
        $url = plugin_dir_url(__FILE__).'assets/pluginimages';
    }
    return $url;
}

/**
 * Get options including defaults.
 *
 * @since 1.12.0
 *
 */
function azrcrv_ft_get_option($option_name){
 
	$defaults = array(
						'api' => array(
											'access-key' => '',
											'access-secret' => '',
											'access-token' => '',
											'access-token-secret' => '',
										),
						'cpt' => array(
											'enabled' => 0,
										),
						'post' => array(
											'type' => 'post',
											'format' => 'standard',
											'status' => 'publish',
											'author' => '',
										),
						'tweet' => array(
											'number' => 25,
											'exclude-replies' => 0,
											'download-images' => 1,
											'download-method' => 'curl',
											'title' => 'Tweet ID: %id%',
											'content' => '%tweet%',
											'store-all-data' => 0,
										),
						'cron' => array(
											'enabled' => 0,
											'frequency' => 'daily',
											'time' => array(
																'hour' => 6,
																'minute' => 1,
															),
										),
						'queries' => array(
										),
					);

	$options = get_option($option_name, $defaults);

	$options = azrcrv_ft_recursive_parse_args($options, $defaults);

	return $options;

}

/**
 * Recursively parse options to merge with defaults.
 *
 * @since 1.14.0
 *
 */
function azrcrv_ft_recursive_parse_args( $args, $defaults ) {
	$new_args = (array) $defaults;

	foreach ( $args as $key => $value ) {
		if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
			$new_args[ $key ] = azrcrv_ft_recursive_parse_args( $value, $new_args[ $key ] );
		}
		else {
			$new_args[ $key ] = $value;
		}
	}

	return $new_args;
}

/*
 * Tweet Custom Post Type
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_create_tweet_cpt() {
	
	$options = azrcrv_ft_get_option('azrcrv-ft');
   
	if ($options['cpt']['enabled'] == 1){
		register_post_type( 
								'tweet',
								array(
										'labels' => array(
															'name' => esc_html__('From Twitter', 'from-twitter'),
															'singular_name' => esc_html__('Tweet', 'from-twitter'),
															'all_items' => esc_html__('All Tweets', 'from-twitter'),
															'add_new' => esc_html__('Add New', 'from-twitter'),
															'add_new_item' => esc_html__('Add New Tweet', 'from-twitter'),
															'edit' => esc_html__('Edit', 'from-twitter'),
															'edit_item' => esc_html__('Edit Tweet', 'from-twitter'),
															'new_item' => esc_html__('New Tweet', 'from-twitter'),
															'view' => esc_html__('View', 'from-twitter'),
															'view_item' => esc_html__('View Tweet', 'from-twitter'),
															'search_items' => esc_html__('Search Tweets', 'from-twitter'),
															'not_found' => esc_html__('No Tweets found', 'from-twitter'),
															'not_found_in_trash' => esc_html__('No Tweets found in Trash', 'from-twitter'),
															'parent' => esc_html__('Parent Tweet', 'from-twitter')
														),
										'public' => true,
										'menu_position' => 50,
										'supports' => array('title', 'comments', 'editor', 'custom-fields'),
										'taxonomies' => array(''),
										'menu_icon' => 'dashicons-twitter',
										'has_archive' => true,
									)
							);
	}
}

/**
 * Add To Twitter action link on plugins page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_create_tag_taxonomy() 
{
  // Add new taxonomy, NOT hierarchical (like tags)
  $labels = array(
					'name' => esc_html__('Tags', 'from-twitter'),
					'singular_name' => esc_html__('Tag', 'from-twitter'),
					'search_items' =>  esc_html__('Search Tags', 'from-twitter'),
					'popular_items' => esc_html__('Popular Tags', 'from-twitter'),
					'all_items' => esc_html__('All Tags', 'from-twitter'),
					'parent_item' => null,
					'parent_item_colon' => null,
					'edit_item' => esc_html__('Edit Tag', 'from-twitter'), 
					'update_item' => esc_html__('Update Tag', 'from-twitter'),
					'add_new_item' => esc_html__('Add New Tag', 'from-twitter'),
					'new_item_name' => esc_html__('New Tag Name', 'from-twitter'),
					'separate_items_with_commas' => esc_html__('Separate tags with commas', 'from-twitter'),
					'add_or_remove_items' => esc_html__('Add or remove tags', 'from-twitter'),
					'choose_from_most_used' => esc_html__('Choose from the most used tags', 'from-twitter'),
					'menu_name' => esc_html__('Tags', 'from-twitter'),
				); 

  register_taxonomy(
						'tag',
						'tweet',
						array(
								'hierarchical' => false,
								'labels' => $labels,
								'show_ui' => true,
								'update_count_callback' => '_update_post_term_count',
								'query_var' => true,
								'rewrite' => array('slug' => 'tag'),
							)
					);
}

/**
 * Add To Twitter action link on plugins page.
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_add_plugin_action_link($links, $file){
	static $this_plugin;

	if (!$this_plugin){
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin){
		$settings_link = '<a href="'.admin_url('admin.php?page=azrcrv-ft').'"><img src="'.plugins_url('/pluginmenu/images/logo.svg', __FILE__).'" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />'.esc_html__('Settings' ,'from-twitter').'</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_create_admin_menu(){
	
	// add settings to from twitter submenu
	$options = azrcrv_ft_get_option('azrcrv-ft');
   
	if ($options['cpt']['enabled'] == 1){
		add_submenu_page(
							'edit.php?post_type=tweet'
							,esc_html__('From Twitter Settings', 'from-twitter')
							,esc_html__('Settings', 'from-twitter')
							,'manage_options'
							,'azrcrv-ft'
							,'azrcrv_ft_display_options'
						);
	}
	
	add_submenu_page("azrcrv-plugin-menu"
						,esc_html__("From Twitter", "from-twitter")
						,esc_html__("From Twitter", "from-twitter")
						,'manage_options'
						,'azrcrv-ft'
						,'azrcrv_ft_display_options');
	
    wp_enqueue_script("from-twitter-js", plugins_url('assets/jquery/jquery.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-tabs'));
    wp_enqueue_style("from-twitter-css", plugins_url('assets/css/styles.css', __FILE__), array('colors-fresh'), '1.7.0');
    wp_enqueue_style("from-twitter-css-ui", plugins_url('assets/css/styles-ui.css', __FILE__), array('from-twitter-css'), '1.7.0');
}

/**
 * Create the post tweet metabox
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_create_tweet_metaboxes() {
	
	$options = azrcrv_ft_get_option('azrcrv-ft');
	
	add_meta_box(
					'azrcrv-ft-tweet-details-metabox', // Metabox ID
					esc_html__('Tweet Data', 'from-twitter'), // Title to display
					'azrcrv_ft_render_tweet_details_metabox', // Function to call that contains the metabox content
					array($options['post']['type']), // Post type to display metabox on
					'normal', // Where to put it (normal = main colum, side = sidebar, etc.)
					'default' // Priority relative to other metaboxes
				);
	
	add_meta_box(
					'azrcrv-ft-tweet-images-metabox',
					esc_html__('Tweet Images', 'from-twitter'),
					'azrcrv_ft_render_tweet_images_metabox',
					array($options['post']['type']),
					'normal',
					'default'
				);

}

/**
 * Render the post tweet metabox
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_render_tweet_details_metabox() {
	// Variables
	global $post; // Get the current post data
	
	$options = azrcrv_ft_get_option('azrcrv-ft');
	
    require_once('includes/tweet_detail_metabox.php');
	
}

/**
 * Save the post tweet metabox
 * @param  Number $post_id The post ID
 * @param  Array  $post    The post data
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_save_tweet_details_metabox($post_id, $post){

	// Verify that our security field exists. If not, bail.
	if ( !isset( $_POST['azrcrv_ft_form_tweet_details_metabox_process'] ) ) return;

	// Verify data came from edit/dashboard screen
	if ( !wp_verify_nonce( $_POST['azrcrv_ft_form_tweet_details_metabox_process'], 'azrcrv_ft_form_tweet_details_metabox_nonce' ) ) {
		return $post->ID;
	}

	// Verify user has permission to edit post
	if ( !current_user_can( 'edit_post', $post->ID )) {
		return $post->ID;
	}
	
	$options = azrcrv_ft_get_option('azrcrv-ft');
	
	// save tweet id
	update_post_meta($post->ID, '_azrcrv-ft-id_str', sanitize_text_field($_POST['tweet-id-str']));
	
	if ($options['tweet']['store-all-data'] == 1){
		
		$tweet = array(
							'query' => sanitize_text_field($_POST['tweet-query']),
							'url' => sanitize_url($_POST['tweet-url']),
							'created_at' => sanitize_text_field($_POST['tweet-created-at']),
							'user_id_str' => sanitize_text_field($_POST['tweet-user-id-str']),
							'user_name' => sanitize_text_field($_POST['tweet-user-name']),
							'user_screen_name' => sanitize_text_field($_POST['tweet-user-screen-name']),
							'user_profile_image_url' => sanitize_url($_POST['tweet-user-profile-image-url']),
						);
		if (isset($_POST['in-reply-to-status-id-str'])){
			$tweet['tweet_in_reply_to_status_id_str'] = sanitize_text_field($_POST['in-reply-to-status-id-str']);
			$tweet['tweet_in_reply_to_user_id_str'] = sanitize_text_field($_POST['in-reply-to-user-id-str']);
			$tweet['tweet_in_reply_to_user_screen_name'] = sanitize_text_field($_POST['in-reply-to-user-screen-name']);
			$tweet['tweet_quoted_status_id_str'] = sanitize_text_field($_POST['quoted-status-id-str']);
		}
		if (isset($_POST['retweeted-id-str'])){
			$tweet['retweeted_id_str'] = sanitize_text_field($_POST['retweeted-id-str']);
			$tweet['retweeted_created_at'] = sanitize_text_field($_POST['retweeted-created-at']);
			$tweet['retweeted_user_id_str'] = sanitize_text_field($_POST['retweeted-user-id-str']);
			$tweet['retweeted_user_name'] = sanitize_text_field($_POST['retweeted-user-name']);
			$tweet['retweeted_user_screen_name'] = sanitize_text_field($_POST['retweeted-user-screen-name']);
			$tweet['retweeted_user_profile_image_url'] = sanitize_url($_POST['tweet-user-profile-image-url']);
		}
		if (isset($_POST['media-1'])){
			$tweet['media-1'] = sanitize_url($_POST['media-1']);
		}
		if (isset($_POST['media-2'])){
			$tweet['media-2'] = sanitize_url($_POST['media-2']);
		}
		if (isset($_POST['media-3'])){
			$tweet['media-3'] = sanitize_url($_POST['media-3']);
		}
		if (isset($_POST['media-4'])){
			$tweet['media-4'] = sanitize_url($_POST['media-4']);
		}
		
		update_post_meta($post->ID, '_azrcrv-ft-tweet', $tweet);
	}
	
}


/**
 * Render the post tweet images metabox
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_render_tweet_images_metabox(){
	
	global $post;
	
	$arguments = array(
							'numberposts' => -1,
							'post_type' => 'attachment',
							'post_mime_type' => 'image',
							'post_parent' => $post->ID,
							'post_status' => null,
							'exclude' => get_post_thumbnail_id() ,
							'orderby' => 'menu_order',
							'order' => 'ASC'
						);
	$post_attachments = get_posts($arguments);
	
	$count = 0;
	foreach ($post_attachments as $attachment) {
		$count += 1;
		$preview = wp_get_attachment_image_src($attachment->ID, array('150','150'), true);
		echo '<img src="' . $preview[0] . '">';
	}
	if ($count == 0){
		_e('No images were attached to the tweet', 'from-twitter');
	}
}

/*
 * Display admin page for this plugin
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_display_options(){

	if (!current_user_can('manage_options')) {
		wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'from-twitter'));
	}
	
	$options = azrcrv_ft_get_option('azrcrv-ft');

    require_once('includes/admin_page.php');
}

function azrcrv_ft_save_options(){

	// Check that user has proper security level
	if (!current_user_can('manage_options')){
		wp_die(esc_html__('You do not have permissions to perform this action', 'from-twitter'));
	}
	
	// Check that nonce field created in configuration form is present
	if (! empty($_POST) && check_admin_referer('azrcrv-ft', 'azrcrv-ft-nonce')){
		
		if ($options['tweet']['download-method'] == 'curl'){
			if (!function_exists('curl_init')) {
				error_log('The From Twitter plugin requires CURL libraries');
				return;
			}
		}
		
		$options = azrcrv_ft_get_option('azrcrv-ft');
		
		if (!empty($options['api']['access-key']) && !empty($options['api']['access-secret']) && !empty($options['api']['access-token']) && !empty($options['api']['access-token-secret'])) {
			$connection = new TwitterOAuth($options['api']['access-key'], $options['api']['access-secret'], $options['api']['access-token'], $options['api']['access-token-secret']);
		}else{
			$tokens_error = true;
		}		

		
		/*
		* UPDATE API ACCESS
		*/
		$options['api']['access-key'] = sanitize_text_field($_POST['access-key']);
		$options['api']['access-secret'] = sanitize_text_field($_POST['access-secret']);
		$options['api']['access-token'] = sanitize_text_field($_POST['access-token']);
		$options['api']['access-token-secret'] = sanitize_text_field($_POST['access-token-secret']);
		
		/*
		* UPDATE CPT
		*/
		$option_name = 'enable-cpt';
		if (isset($_POST[$option_name])){
			$options['cpt']['enabled'] = 1;
		}else{
			$options['cpt']['enabled'] = 0;
		}
		
		/*
		* UPDATE POST TYPE AND FORMAT
		*/
		$option_name = 'post-type';
		if ($options['cpt']['enabled'] == 1){ // if cpt enabled force post type to tweet
			$options['post']['type'] = 'tweet';
		}else{
			if ($_POST[$option_name] == ''){
				$options['post']['type'] = 'post';
			}else{
				$options['post']['type'] = sanitize_text_field($_POST[$option_name]);
			}
		}
		
		$option_name = 'post-format';
		if ($options['cpt']['enabled'] == 1){ // if cpt enabled force post type to tweet
			$options['post']['format'] = 'standard';
		}else{
			if ($_POST[$option_name] == ''){
				$options['post']['format'] = 'standard';
			}else{
				$options['post']['format'] = sanitize_text_field($_POST[$option_name]);
			}
		}
		
		$option_name = 'post-status';
		$options['post']['status'] = sanitize_text_field($_POST[$option_name]);
		
		$option_name = 'post-author';
		$options['post']['author'] = sanitize_text_field(intval($_POST[$option_name]));
		
		$option_name = 'tweet-number';
		$options['tweet']['number'] = sanitize_text_field(intval($_POST[$option_name]));
		
		$option_name = 'tweet-exclude-replies';
		if (isset($_POST[$option_name])){
			$options['tweet']['exclude-replies'] = 1;
		}else{
			$options['tweet']['exclude-replies'] = 0;
		}
		
		$option_name = 'tweet-download-images';
		if (isset($_POST[$option_name])){
			$options['tweet']['download-images'] = 1;
		}else{
			$options['tweet']['download-images'] = 0;
		}
		
		$option_name = 'tweet-download-method';
		$options['tweet']['download-method'] = sanitize_text_field($_POST[$option_name]);
		
		$options['tweet']['title'] = sanitize_text_field($_POST['tweet-title']);
		
		$allowed_tags = azrcrv_ft_wp_kses_allowed_html();
		
		$options['tweet']['content'] = wp_kses($_POST['tweet-content'], $allowed_tags);
		
		$option_name = 'tweet-store-all-data';
		if (isset($_POST[$option_name])){
			$options['tweet']['store-all-data'] = 1;
		}else{
			$options['tweet']['store-all-data'] = 0;
		}
		
		$option_name = 'cron-enabled';
		if (isset($_POST[$option_name])){
			$options['cron']['enabled'] = 1;
		}else{
			$options['cron']['enabled'] = 0;
		}
		
		$options['cron']['frequency'] = sanitize_text_field($_POST['cron-frequency']);
		$options['cron']['time']['hour'] = sanitize_text_field(intval($_POST['cron-time-hour']));
		$options['cron']['time']['minute'] = sanitize_text_field(intval($_POST['cron-time-minute']));
		
		/*
		* process queries
		*/
		$queries = $_POST['queries'];
		$updated_queries = array();
		foreach ($queries as $key => $query){
			if (!isset($query['delete']) AND strlen(trim($query['query'])) > 0){
				$updated_queries[sanitize_text_field($query['query'])] = sanitize_text_field($query['tags']);
			}
		}
		$options['queries'] = $updated_queries;
		
		/*
		* Update options
		*/
		update_option('azrcrv-ft', $options);
		
		/*
		* Remove scheduled cron events
		*/
		wp_clear_scheduled_hook("azrcrv_ft_scheduled_cron");
		
		/*
		* Add scheduled cron event
		*/
		if ($options['cron']['enabled'] == 1){
			azrcrv_ft_schedule_cron();
		}
		
		// Redirect the page to the configuration form that was processed
		wp_redirect(add_query_arg('page', 'azrcrv-ft&settings-updated', admin_url('admin.php')));
		exit;
	}
}

/**
 * Schedule cron.
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_wp_kses_allowed_html(){
	
    $allowed_tags = wp_kses_allowed_html('post');
	
    $allowed_tags['p'] = array('class' => 1, 'style' => 1, 'alt' => 1, 'name' => 1);
    $allowed_tags['p'] = array('class' => 1, 'style' => 1);
    $allowed_tags['em'] = array('class' => 1, 'style' => 1);
    $allowed_tags['strong'] = array('class' => 1, 'style' => 1);
    $allowed_tags['i'] = array('class' => 1, 'style' => 1);
    $allowed_tags['b'] = array('class' => 1, 'style' => 1);
    $allowed_tags['p'] = array('class' => 1, 'style' => 1);
    $allowed_tags['ol'] = array('class' => 1, 'style' => 1);
    $allowed_tags['ul'] = array('class' => 1, 'style' => 1);
    $allowed_tags['li'] = array('class' => 1, 'style' => 1);
    $allowed_tags['quote'] = array('class' => 1, 'style' => 1);
    $allowed_tags['blockquote'] = array('class' => 1, 'style' => 1);
	
	return $allowed_tags;
	
}

/**
 * Schedule cron.
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_schedule_cron(){
	
	$options = azrcrv_ft_get_option('azrcrv-ft');
	
	if ($options['cron']['enabled'] == 1){
		wp_schedule_event(strtotime(substr('0'.$options['cron']['time']['hour'], -2).':'.substr('0'.$options['cron']['time']['minute'], -2).':00'), $options['cron']['frequency'], 'azrcrv_ft_scheduled_cron');
	}
	
}

/**
 * Scheduled cron perform.
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_scheduled_cron_perform(){
	
	$options = azrcrv_ft_get_option('azrcrv-ft');
	
	define('CONSUMER_KEY', $options['api']['access-key']);
	define('CONSUMER_SECRET', $options['api']['access-secret']);
	define('ACCESS_TOKEN', $options['api']['access-token']);
	define('ACCESS_TOKEN_SECRET', $options['api']['access-token_secret']);
	
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
	
	foreach ($options['queries'] as $query => $tags){
		$parameters = array(
							'q' => $query,
							'result_type' => 'recent',
							'tweet_mode' => 'extended',
							'include_entities' => "true",
							'count'=> $options['tweet']['number'],
							//'exclude_replies' => $options['tweet']['exclude-replies'] == 1 ? "false" : "true",
						);
		$tweets_object = $connection->get("search/tweets", $parameters);
		
		$tweets = $tweets_object->statuses;
		
		foreach($tweets as $tweet){
			// build query to see if tweet already loaded
			$args = array(
							'fields' => 'ids',
							'post_type'=> 'tweet',
							'meta_query'  => array(
														array(
																'key' => '_azrcrv-ft-id_str',
																'value' => $tweet->id_str,
															)
													)
						);
			$wp_query = new WP_Query( $args );
			
			if (empty($wp_query->have_posts())){
				
				$new_post_id = azrcrv_ft_create_post($options, $query, $tweet);
				
			}
		}
	}

}

/*
 * Create post from tweet
 *
 * @since 1.0.0
 *
 */
 function azrcrv_ft_create_post($options, $query, $tweet){
				
	$media_urls = array();
	foreach ($tweet->extended_entities->media as $media){
		if ($media->type == 'photo'){
			$media_urls[] = $media->media_url_https;
		}
	}

//1339651650324148225	/	1339651641218351104
	// construct array to create new post
	
	$tweet_url = sanitize_url('https://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str);
	$profile_url = sanitize_url('https://twitter.com/'.$tweet->user->screen_name);
	
	$tweet_title = str_replace('%id%', sanitize_text_field($tweet->id_str), $options['tweet']['title']);
	$tweet_title = str_replace('%username%', sanitize_text_field($tweet->user->name), $tweet_title);
	$tweet_title = str_replace('%screen_name%', sanitize_text_field($tweet->user->screen_name), $tweet_title);
	
	$tweet_content = str_replace('%id%', sanitize_text_field($tweet->full_text), $options['tweet']['content']);
	$tweet_content = str_replace('%tweet%', sanitize_text_field($tweet->full_text), $tweet_content);
	$tweet_content = str_replace('%username%', sanitize_text_field($tweet->user->name), $tweet_content);
	$tweet_content = str_replace('%screen_name%', sanitize_text_field($tweet->user->screen_name), $tweet_content);
	$tweet_content = str_replace('%url%', $tweet_url, $tweet_content);
	$tweet_content = str_replace('%profile%', $profile_url, $tweet_content);
	
	$meta_input = array(
							'_azrcrv-ft-id_str' => sanitize_text_field($tweet->id_str),
						);
	if ($options['tweet']['store-all-data'] == 1){
		$meta_input['_azrcrv-ft-tweet'] = array(
													'query' => sanitize_text_field($query),
													'tweet' => sanitize_text_field($tweet->full_text),
													'created_at' => sanitize_text_field($tweet->created_at),
													'user_id_str' => sanitize_text_field($tweet->user->id_str),
													'user_name' => sanitize_text_field($tweet->user->name),
													'user_screen_name' => sanitize_text_field($tweet->user->screen_name),
													'user_profile_image_url' => sanitize_text_field($tweet->user->profile_image_url),
													'url' => $tweet_url,
												);
		if (strlen($tweet->in_reply_to_status_id_str) > 0){
			$meta_input['_azrcrv-ft-tweet']['in_reply_to_status_id_str'] = sanitize_text_field($tweet->in_reply_to_status_id_str);
			$meta_input['_azrcrv-ft-tweet']['in_reply_to_user_id_str'] = sanitize_text_field($tweet->in_reply_to_user_id_str);
			$meta_input['_azrcrv-ft-tweet']['in_reply_to_user_screen_name'] = sanitize_text_field($tweet->in_reply_to_user_screen_name);
			$meta_input['_azrcrv-ft-tweet']['quoted_status_id_str'] = sanitize_text_field($tweet->quoted_status_id_str);
		}
		if (isset($tweet->retweeted_status->id_str)){
			$meta_input['_azrcrv-ft-tweet']['retweeted_id_str'] = sanitize_text_field($tweet->retweeted_status->id_str);
			$meta_input['_azrcrv-ft-tweet']['retweeted_tweet'] = sanitize_text_field($tweet->retweeted_status->full_text);
			$meta_input['_azrcrv-ft-tweet']['retweeted_created_at'] = sanitize_text_field($tweet->retweeted_status->created_at);
			$meta_input['_azrcrv-ft-tweet']['retweeted_user_id_str'] = sanitize_text_field($tweet->retweeted_status->user->id_str);
			$meta_input['_azrcrv-ft-tweet']['retweeted_user_name'] = sanitize_text_field($tweet->retweeted_status->user->name);
			$meta_input['_azrcrv-ft-tweet']['retweeted_user_screen_name'] = sanitize_text_field($tweet->retweeted_status->user->screen_name);
			$meta_input['_azrcrv-ft-tweet']['retweeted_user_profile_image_url'] = sanitize_text_field($tweet->retweeted_status->user->profile_image_url);
		}
		if (isset($media_urls[0])){
			$meta_input['_azrcrv-ft-tweet']['media-1'] = sanitize_url($media_urls[0]);
		}
		if (isset($media_urls[1])){
			$meta_input['_azrcrv-ft-tweet']['media-2'] = sanitize_url($media_urls[1]);
		}
		if (isset($media_urls[2])){
			$meta_input['_azrcrv-ft-tweet']['media-3'] = sanitize_url($media_urls[2]);
		}
		if (isset($media_urls[3])){
			$meta_input['_azrcrv-ft-tweet']['media-4'] = sanitize_url($media_urls[3]);
		}
	}
	
	$post_arr = array(
						'post_title'=> $tweet_title,
						'post_date'=> date('Y-m-d H:i:s', strtotime($tweet->created_at)),
						'post_content' => $tweet_content,
						'post_type'  => sanitize_text_field($options['post']['type']),
						'post_status'  => sanitize_text_field($options['post']['status']),
						'post_author'  => sanitize_text_field($options['post']['author']),
						'meta_input'=> $meta_input,
					);

	$new_post_id = wp_insert_post($post_arr);
	
	// download media images from tweet
	if ($options['tweet']['download-images'] == 1){
		$count = 0;
		foreach ($media_urls as $media_url){
			$count+= 1;
			azrcrv_ft_import_tweet_images($new_post_id, $media_url);
		}
	}
	
	// add tags
	if (strlen(trim($tags) ) > 0){
		$tags = $tags;
		$taxonomy = 'tag';
		wp_set_post_terms($new_post_id, $tags, $taxonomy);
	}
	
	// add post format (only required if not standard
	if ($options['post-format'] != 'standard'){
		$tag = 'post-format-'.$options['post']['format'];
		$taxonomy = 'post_format';
		wp_set_post_terms($new_post_id, $tag, $taxonomy);
	}
	
	return $new_post_id;
	
 }

/*
 * Import tweet images
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_import_tweet_images($post_id, $media_url){
	
	$upload_dir = wp_upload_dir();
	
	$media_url_large = $media_url.':large';
	
	$image_data = azrcrv_ft_file_get_contents($media_url_large);

	$filename = strtolower(pathinfo($media_url, PATHINFO_FILENAME)).".".strtolower(pathinfo($media_url, PATHINFO_EXTENSION));
	
	if (wp_mkdir_p($upload_dir['path'])){
		$file = $upload_dir['path'].'/'.$filename;
	}else{
		$file = $upload_dir['basedir'].'/'.$filename;
	}
	
	file_put_contents($file, $image_data);
	
	$wp_filetype = wp_check_filetype($filename, null);
	
	$attachment = array(
							'post_mime_type' => $wp_filetype['type'],
							'post_title' => sanitize_file_name($filename),
							'post_content' => '',
							'post_status' => 'inherit'
						);
						
	$attach_id = wp_insert_attachment($attachment, $file, $post_id);
	
	$attaches[] = $attach_id;
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	
	$attach_data = wp_generate_attachment_metadata($attach_id, $file);
	wp_update_attachment_metadata($attach_id, $attach_data);
	
}

/*
 * Get file contents
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_file_get_contents($media_url)
{
	$options = azrcrv_ft_get_option('azrcrv-ft');

	if ($options['tweet-download-method'] == 'curl'){
		return azrcrv_ft_curl_file_get_contents($media_url);
	}else{
		return file_get_contents($media_url);
	}
}

/*
 * Get curl content (json)
 *
 * @since 1.0.0
 *
 */
function azrcrv_ft_curl_file_get_contents($media_url)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $media_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 12);

    $contents = curl_exec($curl);
    curl_close($curl);
	
    return $contents;
}