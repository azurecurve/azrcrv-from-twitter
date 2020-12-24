<?php

$tweet_id_str = get_post_meta($post->ID, '_azrcrv-ft-id_str', true);
echo '<fieldset>
	<div>
		<table style="width: 100%; border-collapse: collapse; text-align: left; ">
			<colgroup>
				<col style="width: 180px; >
				<col style="">
			</colgroup>
			<tr>
				<th>
					'.__('Tweet ID', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="tweet-id-str" id="tweet-id-str" class="large-text" value="'.esc_attr__($tweet_id_str).'" />
				<td>
			</tr>';
			
if ($options['tweet']['store-all-data'] == 1){
	$tweet = get_post_meta($post->ID, '_azrcrv-ft-tweet', true);
	
	$tweet_query = $tweet['query'];
	$tweet_url = $tweet['url'];
	$tweet_created_at = $tweet['created_at'];
	$tweet_user_id_str = $tweet['user_id_str'];
	$tweet_user_name = $tweet['user_name'];
	$tweet_user_screen_name = $tweet['user_screen_name'];
	$tweet_user_profile_image_url = $tweet['user_profile_image_url'];
	
	echo	'<tr>
				<th>
					'.__('Query String', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="tweet-query" id="tweet-query" class="large-text" value="'.esc_attr__($tweet_query).'" />
				<td>
			</tr>
			<tr>
				<th>
					'.__('URL', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="tweet-url" id="tweet-url" class="large-text" value="'.esc_url($tweet_url).'" />
				<td>
			</tr>
			<tr>
				<th>
					'.__('Created At', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="tweet-created-at" id="tweet-created-at" class="large-text" value="'.esc_attr__($tweet_created_at).'" />
				<td>
			</tr>
			<tr>
				<th>
					'.__('User ID', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="tweet-user-id-str" id="tweet-user-id-str" class="large-text" value="'.esc_attr__($tweet_user_id_str).'" />
				<td>
			</tr>
			<tr>
				<th>
					'.__('Username', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="tweet-user-name" id="tweet-user-name" class="large-text" value="'.esc_attr__($tweet_user_name).'" />
				<td>
			</tr>
			<tr>
				<th>
					'.__('Screen Name', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="tweet-user-screen-name" id="tweet-user-screen-name" class="large-text" value="'.esc_attr__($tweet_user_screen_name).'" />
				<td>
			</tr>
			<tr>
				<th>
					'.__('Profile Image URL', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="tweet-user-profile-image-url" id="tweet-user-profile-image-url" class="large-text" value="'.esc_url($tweet_user_profile_image_url).'" />
				<td>
			</tr>';
	
	if (isset($tweet['in_reply_to_status_id_str'])){
		$tweet_in_reply_to_status_id_str = $tweet['tweet_in_reply_to_status_id_str'];
		$tweet_in_reply_to_user_id_str = $tweet['in_reply_to_user_id_str'];
		$tweet_in_reply_to_user_screen_name = $tweet['in_reply_to_user_screen_name'];
		$tweet_quoted_status_id_str = $tweet['quoted_status_id_str'];
		
		echo	'<tr>
					<th>
						'.__('In Reply to Tweet', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="in-reply-to-status-id-str" id="in-reply-to-status-id-str" class="large-text" value="'.esc_attr__($tweet_in_reply_to_status_id_str).'" />
					<td>
				</tr>
				<tr>
					<th>
						'.__('In Reply to User ID', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="in-reply-to-user-id-str" id="in-reply-to-user-id-str" class="large-text" value="'.esc_attr__($tweet_in_reply_to_user_id_str).'" />
					<td>
				</tr>
				<tr>
					<th>
						'.__('In Reply to Screen Name', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="in-reply-to-user-screen-name" id="in-reply-to-user-screen-name" class="large-text" value="'.esc_attr__($tweet_in_reply_to_user_screen_name).'" />
					<td>
				</tr>
				<tr>
					<th>
						'.__('Quoted Tweet ID', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="quoted-status-id-str" id="quoted-status-id-str" class="large-text" value="'.esc_attr__($tweet_quoted_status_id_str).'" />
					<td>
				</tr>';
	}
	
	if (isset($tweet['retweeted_id_str'])){
		$retweeted_id_str = $tweet['retweeted_id_str'];
		$retweeted_created_at = $tweet['retweeted_created_at'];
		$retweeted_user_id_str = $tweet['retweeted_user_id_str'];
		$retweeted_user_name = $tweet['retweeted_user_name'];
		$retweeted_user_screen_name = $tweet['retweeted_user_screen_name'];
		$retweeted_user_profile_image_url = $tweet['retweeted_user_profile_image_url'];
		
		echo	'<tr>
					<th>
						'.__('Retweeted Tweet ID', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="retweeted-id-str" id="retweeted-id-str" class="large-text" value="'.esc_attr__($retweeted_id_str).'" />
					<td>
				</tr>
				<tr>
					<th>
						'.__('Retweet Created At', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="retweeted-created-at" id="retweeted-created-at" class="large-text" value="'.esc_attr__($retweeted_created_at).'" />
					<td>
				</tr>
				<tr>
					<th>
						'.__('Retweet User ID', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="retweeted-user-id-str" id="retweeted-user-id-str" class="large-text" value="'.esc_attr__($retweeted_user_id_str).'" />
					<td>
				</tr>
				<tr>
					<th>
						'.__('Retweet User Name', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="retweeted-user-name" id="retweeted-user-name" class="large-text" value="'.esc_attr__($retweeted_user_name).'" />
					<td>
				</tr>
				<tr>
					<th>
						'.__('Retweet User Screen Name', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="retweeted-user-screen-name" id="retweeted-user-screen-name" class="large-text" value="'.esc_attr__($retweeted_user_screen_name).'" />
					<td>
				</tr>
				<tr>
					<th>
						'.__('Retweet Profile Image URL', 'from-twitter').'
					</th>
					<td>
						<input type="text" name="tweet-user-profile-image-url" id="tweet-user-profile-image-url" class="large-text" value="'.esc_url($retweeted_user_profile_image_url).'" />
					<td>
				</tr>';
	}
	
	if (isset($tweet['media-1'])){
		$tweet_media_1 = $tweet['media-1'];
		
		echo '<tr>
				<th>
					'.__('Media 1 URL', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="media-1" id="media-1" class="large-text" value="'.esc_url($tweet_media_1).'" />
				<td>
			</tr>';
	}
	
	if (isset($tweet['media-2'])){
		$tweet_media_2 = $tweet['media-2'];
		
		echo '<tr>
				<th>
					'.__('Media 1 URL', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="media-2" id="media-2" class="large-text" value="'.esc_url($tweet_media_2).'" />
				<td>
			</tr>';
	}
	
	if (isset($tweet['media-3'])){
		$tweet_media_3 = $tweet['media-3'];
		
		echo '<tr>
				<th>
					'.__('Media 1 URL', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="media-3" id="media-3" class="large-text" value="'.esc_url($tweet_media_3).'" />
				<td>
			</tr>';
	}
	
	if (isset($tweet['media-4'])){
		$tweet_media_4 = $tweet['media-4'];
		
		echo '<tr>
				<th>
					'.__('Media 1 URL', 'from-twitter').'
				</th>
				<td>
					<input type="text" name="media-4" id="media-4" class="large-text" value="'.esc_url($tweet_media_4).'" />
				<td>
			</tr>';
	}
}

echo '</table>
	</div>
</fieldset>';

wp_nonce_field('azrcrv_ft_form_tweet_details_metabox_nonce', 'azrcrv_ft_form_tweet_details_metabox_process');