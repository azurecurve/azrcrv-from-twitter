<div class="wrap arcrv-ft">

	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>

	<?php if( isset($_GET['settings-updated']) ) { ?>
		<div class="notice notice-success is-dismissible">
			<p><strong><?php esc_html_e('Settings have been saved.', 'from-twitter') ?></strong></p>
		</div>
	<?php } ?>
	
	<form method="post" action="admin-post.php">
		<input type="hidden" name="action" value="azrcrv_ft_save_options" />
		
		<?php wp_nonce_field('azrcrv-ft', 'azrcrv-ft-nonce'); ?>

		<input type="hidden" name="azrcrv_ft_data_update" value="yes" />

		<?php
		if (isset($options['api']['access-key']) AND strlen($options['api']['access-key']) > 0){
			$show_app_settings = false;
		}else{
			$show_app_settings = true;
		}
		?>

		<h2 class="nav-tab-wrapper nav-tab-wrapper-azrcrv-ft">
			<a class="nav-tab <?php if ($show_app_settings == true){ echo 'nav-tab-active'; } ?>" data-item=".tabs-1" href="#tabs-1"><?php _e('App Settings', 'from-twitter') ?></a>
			<a class="nav-tab <?php if ($show_app_settings == false){ echo 'nav-tab-active'; } ?>" data-item=".tabs-2" href="#tabs-2"><?php _e('Post Settings', 'from-twitter') ?></a>
			<a class="nav-tab" data-item=".tabs-3" href="#tabs-3"><?php _e('Tweet Settings', 'from-twitter') ?></a>
			<a class="nav-tab" data-item=".tabs-4" href="#tabs-4"><?php _e('Tweet Queries', 'from-twitter') ?></a>
			<a class="nav-tab" data-item=".tabs-5" href="#tabs-5"><?php _e('Cron Settings', 'from-twitter') ?></a>
			
			<input type="submit" style="float: left; margin: 6px; margin-bottom: 3px " value="<?php _e('Save Settings', 'from-twitter'); ?>" class="button-primary" id="submit" name="submit" />
		</h2>

		<div>
		
			<div class="azrcrv_ft_tabs <?php if ($show_app_settings == false){ echo 'invisible'; } ?> tabs-1">
				<p class="azrcrv_ft_horiz">
					
					<h3><?php _e('API Access', 'from-twitter'); ?></h3>
					
					<table class="form-table">
					
						<tr>
							<th scope="row">
								<?php _e('Consumer Key', 'from-twitter'); ?>
							</th>
							<td>
								<input type="text" name="access-key" class="regular-text" value="<?php echo $options['api']['access-key']; ?>">
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Consumer Secret', 'from-twitter'); ?>
							</th>
							<td>
								<input type="text" name="access-secret" class="regular-text" value="<?php echo $options['api']['access-secret']; ?>">
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Access Token', 'from-twitter'); ?>
							</th>
							<td>
								<input type="text" name="access-token" class="regular-text" value="<?php echo $options['api']['access-token']; ?>">
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Access Token Secret', 'from-twitter'); ?>
							</th>
							<td>
								<input type="text" name="access-token-secret" class="regular-text" value="<?php echo $options['api']['access-token-secret']; ?>">
							</td>
						</tr>
						
					</table>
					
					<h3><?php _e('Custom Post Type', 'from-twitter'); ?></h3>
					
					<table class="form-table">
						
						<tr>
							<th scope="row">
								<?php _e('Enable Custom Post Type', 'from-twitter'); ?>
							</th>
							<td>
								<input name="enable-cpt" type="checkbox" id="enable-cpt" value="1" <?php checked('1', $options['cpt']['enabled']); ?> />
								<label for="enable-cpt"><span class="description">
									<?php _e('Create custom post type for retrieved tweets.', 'from-twitter'); ?>
								</span></label
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Menu Position', 'from-twitter'); ?>
							</th>
							<td>
								<input type="number" min=25 max=100 step=1 name="menu-position" class="small-text" value="<?php echo $options['cpt']['menu-position']; ?>">
							</td>
						</tr>
						
					</table>
				</p>
			</div>
			
			<div class="azrcrv_ft_tabs <?php if ($show_app_settings == true){ echo 'invisible'; } ?> tabs-2">
				<p class="azrcrv_ft_horiz">
					
					<table class="form-table">
					
						<tr>
							<th scope="row">
								<?php _e('Post Type', 'from-twitter'); ?>
							</th>
							<td>
								<?php
								$post_types = get_post_types( '', 'objects' ); 
								
								if ($options['cpt']['enabled'] == 1){
									$disabled = 'disabled';
								}else{
									$disabled = '';
								}
								echo '<select name="post-type" '.$disabled.'>';
									 
								foreach ( $post_types as $post_type){
									if ($options['cpt']['enabled'] == 1){
										if ($post_type->name == 'tweet'){
											$selected = 'selected';
										}else{
											$selected = '';
										}
									}elseif ($post_type->name == $options['post']['type']){
										$selected = 'selected';
									}else{
										$selected = '';
									}
								
									echo '<option value="'.$post_type->name.'" '.$selected.'>'.$post_type->labels->singular_name.'</option>';
								}
								 
								echo '</select>';
								?>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Post Format', 'from-twitter'); ?>
							</th>
							<td>
								<?php
								$post_formats = get_theme_support('post-formats');
								
								echo '<select name="post-format" '.$disabled.'>';
								if ($options['post']['format'] == 'standard'){
									$selected = 'selected';
								}else{
									$selected = '';
								}
							
								echo '<option value="standard" '.$selected.'>Standard</option>';
									 
								foreach ($post_formats[0] as $key => $post_format){
									if ($post_format == $options['post']['format']){
										$selected = 'selected';
									}else{
										$selected = '';
									}
									
									echo '<option value="'.$post_format.'" '.$selected.'>'.ucwords($post_format).'</option>';
								}
								 
								echo '</select>';
								?>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Post Status', 'from-twitter'); ?>
							</th>
							<td>
								<?php
								
								echo '<select name="post-status" >';
								if ($options['post']['status'] == 'publish'){
									echo '<option value="publish" selected>Published</option>';
									echo '<option value="draft" >Draft</option>';
								}else{
									echo '<option value="publish" >Published</option>';
									echo '<option value="draft" selected>Draft</option>';
								}
								 
								echo '</select>';
								?>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Post Author', 'from-twitter'); ?>
							</th>
							<td>
								<?php
								
								echo '<select name="post-author" >';
								
								$users = get_users(['role__in' => ['administrator','author']]);
								
								foreach ($users as $user){
									if ($user->ID == $options['post']['author']){
										$selected = 'selected';
									}else{
										$selected = '';
									}
									
									echo '<option value="'.$user->ID.'" '.$selected.'>'.$user->display_name.' ('.$user->user_login.')</option>';
								}
								 
								echo '</select>';
								?>
							</td>
						</tr>
					</table>
				</p>
			</div>
			
			<div class="azrcrv_ft_tabs invisible tabs-3">
				<p class="azrcrv_ft_horiz">
					
					<table class="form-table">
						
						<tr>
							<th scope="row">
								<?php _e('Number of items', 'from-twitter'); ?>
							</th>
							<td>
								<input type="number" min=1 max=100 step=1 name="tweet-number" class="small-text" value="<?php echo $options['tweet']['number']; ?>">
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Exclude replies', 'from-twitter'); ?>
							</th>
							<td>
								<input name="tweet-exclude-replies" type="checkbox" id="tweet-exclude-replies" value="1" <?php checked('1', $options['tweet']['exclude-replies']); ?> />
								<label for="tweet-exclude-replies"><span class="description">
									<?php _e('Exclude replies in retrieved tweets.', 'from-twitter'); ?>
								</span></label
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Download images', 'from-twitter'); ?>
							</th>
							<td>
								<input name="tweet-download-images" type="checkbox" id="tweet-download-images" value="1" <?php checked('1', $options['tweet']['download-images']); ?> />
								<label for="tweet-download-images"><span class="description">
									<?php _e('Download images in tweets.', 'from-twitter'); ?>
								</span></label
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Image Download Method', 'from-twitter'); ?>
							</th>
							<td>
								<?php
								
								echo '<select name="tweet-download-method">';
								if ($options['tweet']['download-method'] == 'standard'){
									echo '<option value="standard" selected>Standard</option>';
									echo '<option value="curl" >Curl</option>';
								}else{
									echo '<option value="standard" >Standard</option>';
									echo '<option value="curl" selected>Curl</option>';
								}
								 
								echo '</select>';
								?>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Title', 'from-twitter'); ?>
							</th>
							<td>
								<input type="text" name="tweet-title" class="regular-text" value="<?php echo $options['tweet']['title']; ?>"><br />
								<span class="description">
								<span class="description">
									<?php printf(__('The following placeholders can be used in the title:
									<ul>
										<li>%s</li>
										<li>%s</li>
										<li>%s</li>
									</ul>', 'from-twitter'), '<strong>%id%</strong>',
															'<strong>%username%</strong>',
															'<strong>%screen_name%</strong>'
															); ?>
								</span>
								</span>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Content', 'from-twitter'); ?>
							</th>
							<td>
								<textarea name="tweet-content" rows="4" cols="50"><?php echo $options['tweet']['content']; ?></textarea><br />
								<span class="description">
									<?php printf(__('The following placeholders can be used in content:
									<ul>
										<li>%s</li>
										<li>%s</li>
										<li>%s</li>
										<li>%s</li>
										<li>%s</li>
										<li>%s</li>
									</ul>', 'from-twitter'), '<strong>%id%</strong>',
															'<strong>%tweet%</strong>',
															'<strong>%username%</strong>',
															'<strong>%screen_name%</strong>',
															'<strong>%profile%</strong>',
															'<strong>%url%</strong>'
															); ?>
								</span>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Store all tweet data', 'from-twitter'); ?>
							</th>
							<td>
								<input name="tweet-store-all-data" type="checkbox" id="tweet-store-all-data" value="1" <?php checked('1', $options['tweet']['store-all-data']); ?> />
								<label for="tweet-store-all-data"><span class="description">
									<?php _e('Store al tweet data (if unmarked, only the tweet id is recorded.', 'from-twitter'); ?>
								</span></label
							</td>
						</tr>
						
					</table>
				</p>
			</div>
			
			<div class="azrcrv_ft_tabs invisible tabs-4">
				<p class="azrcrv_ft_horiz">
					
					<table class="form-table">
					
					<table class="form table azrcrv-ft-queries">
						<tr>
							<th><?php _e('Query', 'from-twitter'); ?></th>
							<th><?php _e('Tags', 'from-twitter'); ?></th>
							<th><?php _e('Delete', 'from-twitter'); ?></th>
						</tr>
						<?php
						$query_count = 0;
						foreach ($options['queries'] as $query => $tags){
							echo '<tr>
								<td>
									<input type="text" name="queries['.$query_count.'][query]" class="regular-text" value="'.$query.'">
								</td>
								<td>
									<input type="text" name="queries['.$query_count.'][tags]" class="regular-text" value="'.$tags.'">
								</td>
								<td>
									<input type="checkbox" name="queries['.$query_count.'][delete]" class="regular-text" value="1">
								</td>
							</tr>';
							$query_count += 1;
						}
						for ($query_loop = $query_count; $query_loop <= $query_count + 4; $query_loop++){
							echo '<tr>
								<td>
									<input type="text" name="queries['.$query_loop.'][query]" class="regular-text" value="">
								</td>
								<td>
									<input type="text" name="queries['.$query_loop.'][tags]" class="regular-text" value="">
								</td>
								<td>
									&nbsp;
								</td>
							</tr>';
						}
						?>
					</table>
				</p>
			</div>
			
			<div class="azrcrv_ft_tabs invisible tabs-5">
				<p class="azrcrv_ft_horiz">
					
					<table class="form-table">
						
						<tr>
							<th scope="row">
								<?php _e('Frequency', 'from-twitter'); ?>
							</th>
							<td>
								<?php
								
								echo '<select name="cron-frequency">';
								if ($options['cron']['frequency'] == 'daily'){
									echo '<option value="daily" selected>Daily</option>';
									echo '<option value="twicedaily" >Twice Daily</option>';
									echo '<option value="hourly" >Hourly</option>';
								}elseif ($options['cron']['frequency'] == 'twicedaily'){
									echo '<option value="daily" >Daily</option>';
									echo '<option value="twicedaily" selected>Twice Daily</option>';
									echo '<option value="hourly" >Hourly</option>';
								}else{
									echo '<option value="daily" >Daily</option>';
									echo '<option value="twicedaily" >Twice Daily</option>';
									echo '<option value="hourly" selected>Hourly</option>';
								}
								 
								echo '</select>';
								?>
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Time', 'from-twitter'); ?>
							</th>
							<td>
								<input type="number" min=0 max=23 step=1 name="cron-time-hour" style="width: 50px; " value="<?php echo substr('0'.$options['cron']['time']['hour'], -2); ?>" onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;" />:<input type="number" min=0 max=59 step=1 name="cron-time-minute" style="width: 50px; " value="<?php echo substr('0'.$options['cron']['time']['minute'], -2); ?>" onchange="if(parseInt(this.value,10)<10)this.value='0'+this.value;" />
							</td>
						</tr>
						
						<tr>
							<th scope="row">
								<?php _e('Enable cron', 'from-twitter'); ?>
							</th>
							<td>
								<input name="cron-enabled" type="checkbox" id="cron-enabled" value="1" <?php checked('1', $options['cron']['enabled']); ?> />
								<label for="cron-enabled"><span class="description">
									<?php _e('Enable cron to retrieve tweets.', 'from-twitter'); ?>
								</span></label
							</td>
						</tr>
						
					</table>
				</p>
			</div>
			
			<div class="azrcrv_ft_tabs invisible tabs-5">
				<p class="azrcrv_ft_horiz">
				</p>
			</div>
			
		</div>
		
		<input type="submit" style="margin-top: 6px;" value="<?php _e('Save Settings', 'from-twitter'); ?>" class="button-primary" id="submit" name="submit" />
		
	</form>
	
</div>
