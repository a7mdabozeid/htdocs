<?php
defined('ABSPATH') or die('No Scirpt Kiddies Please');

if (isset($key)) { $key = $key; } else { $key = ESTP_Class:: generateRandomIndex(); }
// $this->print_array($item);
?>

<div class='estp-item-wrap'>
	<input type="hidden" class='estp_key_unique' name="estp-unique-key" value="<?php echo $key; ?>" />

	<div class='estp-item-wrap-inner' >
		<div class="estp-item-header estp-clearfix">
			<div class='estp-item-header-title' data-count="<?php echo $count;?>"> 
				<span class="estp_title_text_disp">
					<?php
					if( isset($item['tab_title']) && !empty($item['tab_title']) )
					{
						esc_attr_e( $item['tab_title'], ESTP_DOMAIN );
					} 
					else{
						_e('Your Title Here', ESTP_DOMAIN); 
					}
					?>
				</span>

			</div>
			<div class="item function">
                <span class="item_sort" style="cursor:move"> <i class="fa fa-arrows-alt"></i></span>
                <span data-confirm="return confirm('Do you really want to delete ?')" class="item_delete"><i class="fa fa-trash"></i></span>
                <span class='estp-item-hide-show'><i class="fa fa-caret-down"></i></span>
            </div>	
		</div>	

		<div class='estp-item-body estp-clearfix' >
			
			<div class="estp-field-wrap">
				<label><?php _e('Text For Tab', ESTP_DOMAIN); ?></label>
				<input type="text" value="<?php echo isset($item['tab_title'])?esc_attr($item['tab_title']):''; ?>" class="regular-text estp-input-text estp-tab-text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_title]" />
			</div>

			<div class="estp-field-wrap">
				<label><?php _e('Tool Tip', ESTP_DOMAIN); ?></label>
				<input type="text" value="<?php echo isset($item['tooltip'])?esc_attr($item['tooltip']):''; ?>" class="" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tooltip]"/>
			</div>

			<div class="estp-field-wrap">
                <label for="estp-icon-choose-<?php echo $key ?>"><?php _e('Choose Icon Type',ESTP_DOMAIN);?></label>
                 
                <select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_icon_type]" class="estp-tab_icon-type" id="estp-icon-choose-<?php echo $key; ?>">
                    <option value="none"><?php _e('None', ESTP_DOMAIN);?></option>
                    <option value="available_icon" <?php echo (isset($item['tab_icon_type']) && $item['tab_icon_type'] == "available_icon")?'selected':''; ?>><?php _e('Available Icon',ESTP_DOMAIN);?></option>
                    <option value="upload_own" <?php echo (isset($item['tab_icon_type']) && $item['tab_icon_type'] == "upload_own")?'selected':''; ?>><?php _e('Upload Own Icon',ESTP_DOMAIN);?></option>
                </select>
			</div>

			<div class="estp_selection_icontype_wrapper">
                <div class="estp_available_icon" style="<?php echo (isset($item['tab_icon_type']) && $item['tab_icon_type'] == "available_icon")?'display: block':'display: none'; ?>"> <!-- Choose Icon -->
                    <div class="estp-field-wrap">
                        <label for="estp-icon_<?php echo $key; ?>"><?php _e('Available Icon',ESTP_DOMAIN);?></label>
                        
                        <input class="estp-icon-picker" type="hidden" id="estp-icon_<?php echo $key; ?>" name='tab[tab_settings][tab_items][<?php echo $key; ?>][available_icon_code]' value='<?php echo isset($item['available_icon_code'])?$item['available_icon_code']:''; ?>' />

                        <div data-target="#estp-icon_<?php echo $key; ?>" class="estp-icon-pick-button icon-picker  <?php if ($item['available_icon_code'] !='' && isset($item['available_icon_code'])) { $v = explode('|', $item['available_icon_code']); echo $v[0] . ' ' . $v[1]; }else{ echo '';} ?>">
                       		<?php _e( 'Select Icon', ESTP_DOMAIN); ?>
                        </div>
                    </div>
                </div>

                <div class="estp_upload_own_icon" style="<?php echo (isset($item['tab_icon_type']) && $item['tab_icon_type'] == "upload_own")?'display: block':'display: none'; ?>"> <!-- upload own image icon -->
                	<div class="estp-field-wrap">
                		<label for="estp-upload_icon_<?php echo $key; ?>"><?php _e('Upload Own Icon',ESTP_DOMAIN);?></label>
                		
                		<input type="text" id='estp-image-url_<?php echo $key; ?>' name='tab[tab_settings][tab_items][<?php echo $key; ?>][own_icon][icon_img_url]' class='estp-image-url estp-tab-text' value='<?php if( isset($item['own_icon']['icon_img_url'])  ){ echo esc_url($item['own_icon']['icon_img_url']); } ?>' />
                		
			            <input type="button" class='estp-upload-icon-btn' onclick="own_icon_upld(this)" value='<?php _e('Upload Icon', ESTP_DOMAIN); ?>' />
			            
			            <div class='estp-iconpreview'>    
			            	<?php if( isset($item['own_icon']['icon_img_url']) && !empty($item['own_icon']['icon_img_url'])){
	                              $iconurl = $item['own_icon']['icon_img_url'];
	                          }else{
	                          
	                              $iconurl =  ESTP_IMAGE_DIR.'/thumbnail-default.jpg';
	                        }?>
                        	<img src='<?php echo esc_url($iconurl); ?>' height="125px" width="125px"/>
                        </div>

                	</div>
                    
                    <div class="estp-field-wrap">
                       <label for="estp-icon-width_<?php echo $key; ?>"><?php _e( 'Width/Height (px) ', ESTP_DOMAIN ); ?></label>
		                   
                      	<input type="number" id='estp-icon-image-width_<?php echo $key; ?>' name='tab[tab_settings][tab_items][<?php echo $key; ?>][own_icon][icon_width]' placeholder='<?php _e('Width', ESTP_DOMAIN); ?>' value='<?php echo isset($item['own_icon']['icon_width'])?esc_attr($item['own_icon']['icon_width']):''; ?>' min="0" />
	                    <input type="number" id='estp-icon-image-height_<?php echo $key; ?>' name='tab[tab_settings][tab_items][<?php echo $key; ?>][own_icon][icon_height]' placeholder='<?php _e('Height', ESTP_DOMAIN); ?>' value='<?php echo isset($item['own_icon']['icon_height'])?esc_attr($item['own_icon']['icon_height']):''; ?>' min="0"/>
                    </div>
                </div>
            </div>

			<div class="estp-field-wrap">	
				<label><?php _e('Tab Type', ESTP_DOMAIN); ?></label>
				<div class="estp-tab-type-wrap">
					<label>
						<input type="radio" class="estp-link-type" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][type]" value="internal" <?php echo (isset( $item['tab_content']['type'] ) && $item['tab_content']['type']=='internal')?'checked':''; ?> /><?php _e('Internal',ESTP_DOMAIN); ?>
					</label>

					<label>
						<input type="radio" class="estp-link-type" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][type]" value="external" <?php echo (isset($item['tab_content']['type']) && $item['tab_content']['type']=='external')?'checked':''; ?> /> <?php _e('External',ESTP_DOMAIN); ?>
					</label>

					<label>
						<input type="radio" class="estp-link-type" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][type]" value="content_slider" <?php echo (isset($item['tab_content']['type']) && $item['tab_content']['type']=='content_slider')?'checked':''; ?> /><?php _e('Content Slider',ESTP_DOMAIN); ?>
					</label>

					<label>
						<input type="radio" class="estp-link-type" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][type]" value="scroll_navigation" <?php echo isset($item['tab_content']['type']) && $item['tab_content']['type'] == 'scroll_navigation' ? 'checked' : ''; ?>> <?php _e('On Page Navigation', ESTP_DOMAIN); ?>
					</label>
				</div>
			</div>

			<div class="estp-field-wrap estp-page-scroll-nav" style="<?php if( $item['tab_content']['type'] == 'scroll_navigation' ) { echo 'display: block;'; } else { echo 'display: none;'; } ?>">
				<div class="estp-field-wrap estp-inner-field-wrap">
					<label><?php _e('Scroll Type', ESTP_DOMAIN); ?></label>
					<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][scroll_navigation][scroll_type]" class="estp-scroll-type">
						<option value=""><?php _e('Select Scroll Type', ESTP_DOMAIN ); ?></option>
						<option value="scroll_to_top" <?php isset($item['tab_content']['scroll_navigation']['scroll_type'])?selected($item['tab_content']['scroll_navigation']['scroll_type'], 'scroll_to_top'):NULL; ?>><?php _e('Scroll To Top', ESTP_DOMAIN); ?></option>
						<option value="custom_element" <?php isset($item['tab_content']['scroll_navigation']['scroll_type'])?selected($item['tab_content']['scroll_navigation']['scroll_type'], 'custom_element'):NULL; ?>><?php _e('Scroll To Custom Element', ESTP_DOMAIN); ?></option>
					</select>
				</div>

				<div class="estp-field-wrap estp-inner-field-wrap">
					<label><?php _e('Scroll Speed', ESTP_DOMAIN); ?></label>
					<input type="number" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][scroll_navigation][scroll_speed]" value="<?php echo (isset($item['tab_content']['scroll_navigation']['scroll_speed']) && !empty($item['tab_content']['scroll_navigation']['scroll_speed'])) ? esc_attr($item['tab_content']['scroll_navigation']['scroll_speed']) : ""; ?>">
				</div>

				<div class="estp-custom-element-id" style="<?php if( $item['tab_content']['scroll_navigation']['scroll_type'] == 'custom_element' ) { echo 'display: block;'; } else { echo 'display: none;'; } ?>">
					<div class="estp-field-wrap estp-inner-field-wrap" >
						<label><?php _e('Custom Element ID', ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][scroll_navigation][scroll_element_id]" value="<?php echo (isset($item['tab_content']['scroll_navigation']['scroll_element_id']) && !empty($item['tab_content']['scroll_navigation']['scroll_element_id'])) ? esc_attr($item['tab_content']['scroll_navigation']['scroll_element_id']) : ""; ?>">
					</div>

					<div class="estp-field-wrap estp-inner-field-wrap estp-specific-page-nav-disp">
						<div class="estp_checked_posts_div">
							<input type="hidden" class="estp_checked_cma" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][scroll_navigation][scroll_nav_page]" value="<?php echo isset($item['tab_content']['scroll_navigation']['scroll_nav_page'])?esc_attr($item['tab_content']['scroll_navigation']['scroll_nav_page']):'' ?>">
						</div>

						<label><?php esc_attr_e('Choose From Specific'); ?></label>
						<?php
							$p_nonce = wp_create_nonce('estp_pagination_nonce');
						?>
						<input type="hidden" class="pagination_nonce" value="<?php echo $p_nonce; ?>">
						<div class="estp_post_types_div">
						<?php
							$args = array(
									   'public'   => true,
									);
							$output = 'names'; // names or objects, note names is the default
							$operator = 'and'; // 'and' or 'or'
							$post_types = get_post_types( $args, $output, $operator );
							unset($post_types['attachment']); //Attachment is excluded
							$i = 1;

							foreach ($post_types as $post_type_name) 
							{
							?>
							<div class="estp_post_type_container">
								<div>
									<label><?php echo ucwords(esc_attr($post_type_name)); ?></label>
								</div>
								<?php
									$paged = "paged".intval($i);
									$indexid = 1;
									$posts_per_page = 8; 
									$args = array(
										'posts_per_page'	=> $posts_per_page,
										'post_type'			=> $post_type_name,
										'paged'				=> $indexid,
									);
									$post_type_object = new WP_Query($args);
									$posts_array = $post_type_object->posts;
								?>
								<div class="estp_post_type_field">
								<?php
								if (isset($posts_array)) :
								foreach ($posts_array as $index => $act_value) :
									$matched_value = '';
									if (isset($item['tab_content']['scroll_navigation']['scroll_nav_page']) && !empty($item['tab_content']['scroll_navigation']['scroll_nav_page'])) {
										$specific_page = explode(',', $item['tab_content']['scroll_navigation']['scroll_nav_page']);
										foreach ($specific_page as $index => $value) {
											if ($act_value->ID == $value) {
												$matched_value = $value;
											}
										}
									}
									?>
									<div class="estp_individual_term">
										<input type="checkbox" class="estp_post_type_term" value="<?php echo $act_value->ID; ?>" <?php checked($matched_value,$act_value->ID) ?>><?php echo (!empty($act_value->post_title))?esc_attr($act_value->post_title):"#" . (intval($act_value->ID)); ?>
									</div>
									<?php
								endforeach; //End of Posts of certain post type
								endif;
								?>
								</div>
							</div>
							<div class="estp_pagination_links" data-paged="<?php echo esc_attr($paged) . '=' . intval($indexid) ?>">
							<?php
								$pargs = array(
										'current'		=> $indexid,
										'format'		=> '?'.$paged.'=%#%',
										'prev_next'		=> true,
										'prev_text'		=> esc_attr__('« Previous'),
										'next_text'		=> esc_attr__('Next »'),
										'type'			=> 'plain',
										'add_args'		=> false,
										'base'			=> str_replace('%_%', (1 == $indexid) ? '' : "?".$paged."=%#%", "?".$paged."=%#%&page=estp-admin"),
										'total'			=> $post_type_object->max_num_pages
									);
								echo paginate_links($pargs);
								$i++;
							?>
							</div>
							<?php
							} //End of Post Type Loops
							?>
						</div> <!--  End of estp_post_types_div -->
					</div> <!-- End of estp-specific-page-nav-disp -->
				</div>	<!-- Wrapper of Custom Element id and specific page -->

			</div>

			<div class="estp-field-wrap estp-external-tab" style="<?php if( $item['tab_content']['type'] == 'external'){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>" >
				<div class="estp-field-wrap estp-inner-field-wrap">
					<label><?php _e('Target', ESTP_DOMAIN); ?></label>
					
					<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][external][target]">
						<option value=""><?php _e('Select Target',ESTP_DOMAIN); ?></option>
						<option value="_self" <?php echo ( ((esc_attr( $item['tab_content']['external']['target'] ))== '_self') ) ? 'selected="selected"' : ''; ?>>
							<?php _e('_self',ESTP_DOMAIN); ?>
						</option>
						<option value="_blank" <?php echo ( ((esc_attr( $item['tab_content']['external']['target'] ))== '_blank') ) ? 'selected="selected"' : ''; ?>>
							<?php _e('_blank',ESTP_DOMAIN); ?>
						</option>
					</select><br>
				</div>

				<div class="estp-field-wrap estp-inner-field-wrap">
					<label><?php _e('Tab URL', ESTP_DOMAIN); ?></label>
					<input type="text" value="<?php echo isset($item['tab_content']['external']['url']) ? esc_attr( $item['tab_content']['external']['url'] ) : ''; ?>" class="regular-text estp-input-text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][external][url]"/><br>
				</div>
			</div>

			<div class="estp-field-wrap estp-internal-tab" style="<?php if( $item['tab_content']['type'] == 'internal'){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>" >		

				<div class="estp-field-wrap estp-inner-field-wrap">	
					<label><?php _e('Target', ESTP_DOMAIN); ?></label>
					<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][internal][target]">
						<option value=""><?php _e('Select Target', ESTP_DOMAIN); ?></option>
						<option value="_self" <?php echo ( $item['tab_content']['internal']['target'] == '_self')  ? 'selected="selected"' : ''; ?>>
							<?php _e('_self', ESTP_DOMAIN); ?>
						</option>
						<option value="_blank" <?php echo ( $item['tab_content']['internal']['target'] == '_blank') ? 'selected="selected"' : ''; ?>>
							<?php _e('_blank', ESTP_DOMAIN); ?>
						</option>
					</select><br>
				</div>

				<div class="estp-field-wrap estp-inner-field-wrap">
					<label><?php _e('Redirect Page', ESTP_DOMAIN); ?></label>
					<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][internal][page]">
						<option value=""><?php _e('Select Page',ESTP_DOMAIN); ?></option>
						<?php
						//For Loading all the internal pages
						$my_wp_query = new WP_Query();
						$all_wp_pages = $my_wp_query->query(array('post_type' => 'page', 'posts_per_page' => '-1')); 
						foreach ($all_wp_pages as $keys => $pages) {
						?>
						<option value="<?php echo $pages->ID; ?>" <?php echo ( $item['tab_content']['internal']['page'] == $pages->ID)?'selected="selected"' : ''; ?>><?php echo $pages->post_title; ?>
						</option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="estp-field-wrap estp-contentSlider-type" style="<?php if( $item['tab_content']['type'] == 'content_slider'){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>" >

				<div class="estp-field-wrap estp-inner-field-wrap">
					<label><?php _e('Content Type',ESTP_DOMAIN); ?></label>
					<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][content_type]" class="estp-content-type-select">
						<option value="social_icons" <?php selected($item['tab_content']['content_slider']['content_type'], 'social_icons'); ?>><?php _e('Social Icons',ESTP_DOMAIN); ?></option>
						<option value="twitter_feed" <?php selected($item['tab_content']['content_slider']['content_type'], 'twitter_feed'); ?>><?php _e('Twitter Feed',ESTP_DOMAIN); ?></option>
						<option value="custom_shortcode" <?php selected($item['tab_content']['content_slider']['content_type'], 'custom_shortcode'); ?>><?php _e('Custom Shortcode',ESTP_DOMAIN); ?></option>
						<option value="subscription_form" <?php selected($item['tab_content']['content_slider']['content_type'], 'subscription_form'); ?>><?php _e('Subscription Form',ESTP_DOMAIN); ?></option>
						<option value="recent_blogs" <?php selected($item['tab_content']['content_slider']['content_type'], 'recent_blogs'); ?>><?php _e('Recent Blogs',ESTP_DOMAIN); ?></option>

						<?php  if(class_exists('WooCommerce')){  ?>
						<option value="woocommerce_product" <?php selected($item['tab_content']['content_slider']['content_type'], 'woocommerce_product'); ?>><?php _e('WooCommerce Product',ESTP_DOMAIN); ?></option>
						<?php } ?>

						<option value="html_content" <?php selected($item['tab_content']['content_slider']['content_type'], 'html_content'); ?>><?php _e('HTML Content',ESTP_DOMAIN); ?></option>
					</select>
				</div>

				<div class="estp-field-wrap estp-content-type estp-twitter_feed estp-twitter-feed" style="<?php if( $item['tab_content']['content_slider']['content_type'] == 'twitter_feed'){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
					
					<h3><?php _e('Twitter Feed Settings',ESTP_DOMAIN);?></h3>

					<div class="estp-field-wrap">
						<label><?php _e('Title Text', ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][twitter_feed][title_text]" value="<?php echo isset($item['tab_content']['content_slider']['twitter_feed']['title_text'])?esc_attr($item['tab_content']['content_slider']['twitter_feed']['title_text']):''; ?>" placeholder="Your Title">
					</div>

					<div class="estp-field-wrap">
						<label><?php _e('Twitter Username',ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][twitter_feed][twitter_username]" value="<?php echo isset($item['tab_content']['content_slider']['twitter_feed']['twitter_username'])?esc_attr($item['tab_content']['content_slider']['twitter_feed']['twitter_username']):''; ?>">
                        <div class="estp-tab-option-note">
                            <?php _e('Please enter the username of twitter account from which the feeds need to be fetched.For example:@apthemes', ESTP_DOMAIN); ?>
                        </div>
					</div>

                    <div class="estp-field-wrap">
                        <label><?php _e('Total no of Feed',ESTP_DOMAIN); ?></label>
                        <input type="number" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][twitter_feed][total_twitter_feed]" value="<?php echo isset($item['tab_content']['content_slider']['twitter_feed']['total_twitter_feed'])?esc_attr($item['tab_content']['content_slider']['twitter_feed']['total_twitter_feed']):''; ?>" min="0">
                        <div class="estp-tab-option-note">
                            <?php _e('Please enter the number of feeds to be fetched.Default number of feeds is 5.And please don\'t forget to delete cache once you change the number of tweets using delete cache button below.', ESTP_DOMAIN); ?>
                        </div>
                    </div>

                    <div class="estp-field-wrap">
						<label><?php _e('Display Twitter Follow Button',ESTP_DOMAIN); ?></label>
						<input type="checkbox" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][twitter_feed][display_twitter_followbtn]" value="1" <?php (isset($item['tab_content']['content_slider']['twitter_feed']['display_twitter_followbtn']))?checked($item['tab_content']['content_slider']['twitter_feed']['display_twitter_followbtn'], '1'):NULL; ?>>
                        <div class="estp-tab-option-note">
                            <?php _e('Check if you want to display twitter follow button at the end of the feeds', ESTP_DOMAIN); ?>
                        </div>
					</div>

					<div class="estp-field-wrap">
						<label><?php _e('Time Format', ESTP_DOMAIN); ?></label>

						<label><input type="radio" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][twitter_feed][time_format]" value="full_date" <?php isset($item['tab_content']['content_slider']['twitter_feed']['time_format'])?checked($item['tab_content']['content_slider']['twitter_feed']['time_format'],'full_date'):NULL; ?>>
							<?php _e('Full Date and Time: e.g Feb 10, 2018, 5:16 pm', ESTP_DOMAIN); ?>
						</label>

						<label><input type="radio" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][twitter_feed][time_format]" value="date_only" <?php isset($item['tab_content']['content_slider']['twitter_feed']['time_format'])?checked($item['tab_content']['content_slider']['twitter_feed']['time_format'],'date_only'):NULL; ?>>
							<?php _e('Full Date: e.g May 12, 2018',ESTP_DOMAIN); ?>
						</label>

						<label>
							<input type="radio" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][twitter_feed][time_format]" value="elapsed_time" <?php isset($item['tab_content']['content_slider']['twitter_feed']['time_format'])?checked($item['tab_content']['content_slider']['twitter_feed']['time_format'],'elapsed_time'):NULL; ?>>
							<?php _e('Elapsed Time: e.g 12 hours ago',ESTP_DOMAIN); ?>
						</label>
					</div>

					
					<div class="estp-field-wrap" class="estp-twitter-layout-wrap">
						<label><?php esc_attr_e( 'Twitter Layout', ESTP_DOMAIN ); ?></label>
						<div class="estp-select-img-wrap">
							<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][twitter_feed][layout]" class="estp-image-selector">
								<?php 
									global $estp_variables;
									$img_url = ESTP_IMAGE_DIR . "/twitter-layout/layout1.png"; 
									foreach($estp_variables['twitter_layout'] as $tweet_key=>$value){
								    //echo "<pre>";print_r($value);echo "</pre>";
									if(isset($item['tab_content']['content_slider']['twitter_feed']['layout']) && $item['tab_content']['content_slider']['twitter_feed']['layout'] == $value['value']){
										
										$selected = 'selected="selected"';
										$img_url = $value['img'];	
									}else{
										$selected = '';
									}
									
								?>
								<option value="<?php echo esc_attr($value['value']); ?>" <?php if(isset($item['tab_content']['content_slider']['twitter_feed']['layout']) && $item['tab_content']['content_slider']['twitter_feed']['layout'] == $value['value']){ ?> selected="selected"<?php } ?>  data-img="<?php echo esc_url($value['img']); ?>">
									<?php echo esc_attr($value['name']); ?>
								</option>
								<?php } ?>
							</select>
							<div class="estp-image-preview-wrap">
								<div class="estp-twitter-layout-template-image">
									<img src="<?php echo esc_url($img_url); ?>" height="200" width="200" alt="template image">
								</div>
							</div>
						</div>
					</div>
					

				</div>

				<div class="estp-field-wrap estp-content-type estp-subscription_form estp-subscription-form" style="<?php if( $item['tab_content']['content_slider']['content_type'] == 'subscription_form'){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
					<h3><?php _e('Subscription Form',ESTP_DOMAIN);?></h3>


					<div class="estp-field-wrap">
						<label><?php _e('Name Label',ESTP_DOMAIN) ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][name]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['name'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['name']):''; ?>" placeholder="Your Name">
					</div>

					<div class="estp-field-wrap">
						<label><?php _e('Email Label',ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][email]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['email'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['email']):''; ?>" placeholder="Your Email">
					</div>

					<div class="estp-field-wrap">
						<label><?php _e('Submit Label',ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][submit_label]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['submit_label'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['submit_label']):''; ?>" placeholder="Your Submit Label">
					</div>

					<div class="estp-field-wrap">
						<label><?php _e('Error Message',ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][error_msg]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['error_msg'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['error_msg']):''; ?>" placeholder="Your Error Message">
					</div>

					<div class="estp-field-wrap">
						<label><?php _e('Success Message',ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][success_msg]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['success_msg'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['success_msg']):''; ?>" placeholder="Your Success Message">
					</div>

					<div class="estp-field-wrap">
						<label><?php _e('Email already available message',ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][email_available]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['email_available'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['email_available']):''; ?>" placeholder="Email Available Message">
					</div>

					<div class="estp-field-wrap" class="estp-subscribe-layout-wrap">
						<label><?php esc_attr_e( 'Subscription Form Layout', ESTP_DOMAIN ); ?></label>
						<div class="estp-select-img-wrap">
							<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][layout]" class="estp-subscribe-image-selector">
								<?php 
									global $estp_variables;
									$img_url = ESTP_IMAGE_DIR . "/subscribe-form-layout/layout1.png"; 
									foreach($estp_variables['subscribe_layout'] as $subscribe_key=>$subscribe_value){
								    //echo "<pre>";print_r($value);echo "</pre>";
									if(isset($item['tab_content']['content_slider']['subscription_form']['layout']) && $item['tab_content']['content_slider']['subscription_form']['layout'] == $subscribe_value['value']){
										
										$selected = 'selected="selected"';
										$img_url = $subscribe_value['img'];	
									}else{
										$selected = '';
									}
									
								?>
								<option value="<?php echo esc_attr($subscribe_value['value']); ?>" <?php if(isset($item['tab_content']['content_slider']['subscription_form']['layout']) && $item['tab_content']['content_slider']['subscription_form']['layout'] == $subscribe_value['value']){ ?> selected="selected"<?php } ?>  data-img="<?php echo esc_url($subscribe_value['img']); ?>">
									<?php echo esc_attr($subscribe_value['name']); ?>
								</option>
								<?php } ?>
							</select>
							<div class="estp-image-preview-wrap">
								<div class="estp-subscribe-layout-template-image">
									<img src="<?php echo esc_url($img_url); ?>" height="200" width="200" alt="template image">
								</div>
							</div>
						</div>
					</div>
					
					<div class="estp-field-wrap estp-subscription-title" style="display: none;">
						<label><?php _e('Subscription Title',ESTP_DOMAIN) ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][subscription_title]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['subscription_title'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['subscription_title']):''; ?>" placeholder="Subscription Title">
					</div>	

					<div class="estp-field-wrap estp-subscription-description">
						<label><?php _e('Subscription Description', ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][description]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['description'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['description']):''; ?>" placeholder="Subscription Description">
					</div>				

					<?php /* ?><div class="estp_upload_subscribe_img">
						<div class="estp-field-wrap">
							<label for="estp_upload_subscribe_image"><?php _e('Title Image',ESTP_DOMAIN); ?></label>

							<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][top_image]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['top_image'])?esc_url($item['tab_content']['content_slider']['subscription_form']['top_image']):''; ?>" class="estp-subscribe-image-url estp-subscribe-tab-text">
							<input type="button" class="estp-upload-subscribe-img-btn" onclick="subscription_upld(this)" value="<?php _e('Upload',ESTP_DOMAIN); ?>">

							<div class="estp-subscribe-img-preview">
								<?php 
								if(isset($item['tab_content']['content_slider']['subscription_form']['top_image']))
								{
									$subscribe_img_url = $item['tab_content']['content_slider']['subscription_form']['top_image'];
								}
								else
								{
									$subscribe_img_url = ESTP_IMAGE_DIR.'/thumbnail-default.jpg';
								}
								?>
								<img src="<?php echo esc_url( $subscribe_img_url ); ?>" height="125px" width="125px">
							</div>
						</div>	
					</div> <?php */ ?>

					<div class="estp_subscribe_btn_icon" > 
	                    <div class="estp-field-wrap">
	                        <label for="estp-subscribe-icon_<?php echo $key; ?>">
	                        	<?php _e('Subscribe Icon',ESTP_DOMAIN);?>
	                        </label>
	                        
	                        <input class="estp-subscribe-icon-picker" type="hidden" id="estp-subscribe-icon_<?php echo $key; ?>" name='tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][btn_icon]' value='<?php echo isset($item['tab_content']['content_slider']['subscription_form']['btn_icon'])?$item['tab_content']['content_slider']['subscription_form']['btn_icon']:''; ?>' />

	                        <div data-target="#estp-subscribe-icon_<?php echo $key; ?>" class="estp-icon-pick-button icon-picker  <?php if ($item['tab_content']['content_slider']['subscription_form']['btn_icon'] !='' && isset($item['tab_content']['content_slider']['subscription_form']['btn_icon'])) { $x = explode('|', $item['tab_content']['content_slider']['subscription_form']['btn_icon']); echo $x[0] . ' ' . $x[1]; }else{ echo '';} ?>">
	                       		<?php _e( 'Select Icon', ESTP_DOMAIN); ?>
	                        </div>
	                    </div>
	                </div>

	                <div class="estp-field-wrap">
	                	<label><?php _e('Mail From Name', ESTP_DOMAIN); ?></label>
	                	<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][mail_from_name]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['mail_from_name'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['mail_from_name']):''; ?>" placeholder="Mail From Name">
	                </div>

	                <div class="estp-field-wrap">
	                	<label><?php _e('Mail from address', ESTP_DOMAIN); ?></label>
	                	<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][mail_from_address]" value="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['mail_from_address'])?esc_attr($item['tab_content']['content_slider']['subscription_form']['mail_from_address']):'' ?>" placeholder="Mail From Address">
	                </div>

	                <div class="estp-field-wrap">
	                	<label><?php _e('Subscription Type', ESTP_DOMAIN); ?></label>
	                	<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][subscription_type]" id="estp-form-subscription-type">
	                		<option><?php _e('Select Subscription Type', ESTP_DOMAIN); ?></option>
	                		<option value="builtin_form" <?php echo isset($item['tab_content']['content_slider']['subscription_form']['subscription_type']) && ($item['tab_content']['content_slider']['subscription_form']['subscription_type'] == 'builtin_form') ? "selected" : ""; ?>><?php _e('Built-in Form', ESTP_DOMAIN); ?></option>
	                		<option value="mailchimp_subscription" <?php echo isset($item['tab_content']['content_slider']['subscription_form']['subscription_type']) && ($item['tab_content']['content_slider']['subscription_form']['subscription_type'] == 'mailchimp_subscription' ) ? "selected" : ''; ?>><?php _e('MailChimp Subscription', ESTP_DOMAIN); ?></option>
	                	</select>
	                </div>

	                <div class="estp-field-wrap" id="estp-mailchimp-lists-show" style="<?php echo isset($item['tab_content']['content_slider']['subscription_form']['subscription_type']) && ($item['tab_content']['content_slider']['subscription_form']['subscription_type'] == 'mailchimp_subscription') ? 'display: block;' : 'display: none;';  ?>">
	                	<label><?php _e('Mailchimp Lists', ESTP_DOMAIN); ?></label>
	                	<?php  
		                	$connected = $this->estp_mc_get_api()->is_connected();
		                	if($connected) {
		                		$mailchimp_lists = $this->mailchimp->get_lists();
		                		foreach ($mailchimp_lists as $list) {
		                			if(!empty($list)) {
		                				if( isset($item['tab_content']['content_slider']['subscription_form']['mailchimp_lists']) ) {
		                					if( in_array( $list->id, $item['tab_content']['content_slider']['subscription_form']['mailchimp_lists'] ) ) {
		                						$check = 'checked';
		                					} else {
		                						$check = '';
		                					}
		                				} else {
		                					$check = '';
		                				}
		                			}
		                			?>
		                			<label for="estp-mailchimp-list-<?php echo $list->id; ?>">
		                				<input type="checkbox" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][mailchimp_lists][<?php esc_attr_e($list->id); ?>]" id="estp-mailchimp-list-<?php echo $list->id; ?>" value="<?php esc_attr_e($list->id); ?>" <?php echo $check; ?>> 
		                				<?php esc_html_e($list->name); ?>
		                			</label>
		                			<?php
		                		}
		                	}
	                	?>
	                </div>

	                <div class="estp-field-wrap">
						<label for="estp_mail_notification_<?php echo $key;?>" class="estp-field-label"><?php _e('Enable New Subscriber Mail Notification',ESTP_DOMAIN);?></label>

					    <label class="estp-field-content">
						    <input type="checkbox" id="estp_mail_notification_<?php echo $key;?>" class="et_show_atc_btn" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][subscription_form][subscription_mail_notification]" value="1" <?php echo (isset($item['tab_content']['content_slider']['subscription_form']['subscription_mail_notification']) && $item['tab_content']['content_slider']['subscription_form']['subscription_mail_notification'] == 1)?'checked':NULL; ?>/> 
						    <div class="estp-checkbox-style"></div>
					   	</label>
					</div>
				</div>


				<div class="estp-field-wrap estp-content-type estp-social_icons estp-social-icons" style="<?php if( !isset($item['tab_content']['content_slider']['content_type']) || $item['tab_content']['content_slider']['content_type'] == 'social_icons' ){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
					<?php 
					$estp_icons = array();
					$estp_icons = array(
									'dribble'=>'https://www.dribble.com',
									'facebook'=>'https://www.facebook.com',
									'flickr'=>'https://www.flickr.com',
									'foursquare'=>'https://www.foursquare.com',
									'github'=>'https://www.github.com',
									'googleplus'=>'https://www.googleplus.com',
									'instagram'=>'https://www.instagram.com',
									'linkedin'=>'https://www.linkedin.com',
									'meetup'=>'https://www.meetup.com',
									'pinterest'=>'https://www.pinterest.com',
									'reddit'=>'https://www.reddit.com',
									'skype'=>'https://www.skype.com',
									'spotify'=>'https://www.spotify.com',
									'soundcloud'=>'https://www.soundcloud.com',
									'steam'=>'https://www.steam.com',		
									'stumbleupon'=>'https://www.stumbleupon.com',
									'tumblr'=>'https://www.tumblr.com',
									'twitch'=>'https://www.twitch.com',
									'twitter'=>'https://www.twitter.com',
									'vimeo'=>'https://www.vimeo.com',
									'vine'=>'https://www.vine.com',
									'vk'=>'https://www.vk.com',
									'wordpress'=>'https://www.wordpress.com',
									'yelp'=>'https://www.yelp.com',
									'youtube'=>'https://www.youtube.com',
									); 
					?>
					<h3 class="estp-socialicons-header-title"><?php _e('Social Icons',ESTP_DOMAIN);?></h3>

					<div class="estp-field-wrap">
					    <label><?php _e('Title Text', ESTP_DOMAIN); ?></label>
					    <input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][social_icon][title_text]" value="<?php echo isset($item['tab_content']['content_slider']['social_icon']['title_text'])?esc_attr($item['tab_content']['content_slider']['social_icon']['title_text']):''; ?>" placeholder="Your Title">
					</div>

					<div class="estp-field-wrap estp-socialicons-layout-wrap">
						<label><?php esc_attr_e( 'Social icons Layout', ESTP_DOMAIN ); ?></label>
						<div class="estp-select-img-wrap">
							<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][social_icon][layout]" class="estp-socialicons-image-selector">
								<?php 
									global $estp_variables;
									$img_url = ESTP_IMAGE_DIR . "/social-icons-layout/layout1.png"; 
									foreach($estp_variables['socialicons_layout'] as $socialicons_key=>$socialicons_value){
								    //echo "<pre>";print_r($value);echo "</pre>";
									if(isset($item['tab_content']['content_slider']['social_icon']['layout']) && $item['tab_content']['content_slider']['social_icon']['layout'] == $socialicons_value['value']){
										
										$selected = 'selected="selected"';
										$img_url = $socialicons_value['img'];	
									}else{
										$selected = '';
									}
									
								?>
								<option value="<?php echo esc_attr($socialicons_value['value']); ?>" <?php if(isset($item['tab_content']['content_slider']['social_icon']['layout']) && $item['tab_content']['content_slider']['social_icon']['layout'] == $socialicons_value['value']){ ?> selected="selected"<?php } ?>  data-img="<?php echo esc_url($socialicons_value['img']); ?>">
									<?php echo esc_attr($socialicons_value['name']); ?>
								</option>
								<?php } ?>
							</select>
							<div class="estp-image-preview-wrap">
								<div class="estp-socialicons-layout-template-image">
									<img src="<?php echo esc_url($img_url); ?>" height="200" width="200" alt="template image">
								</div>
							</div>
						</div>
					</div>
					<div class="estp-sort-social-icons">
					<?php foreach($estp_icons as $social_icon => $link){ ?>
					<div class="estp-field-wrap estp-social-icon-field-wrap">
						<span class="estp-icon-handle"><i class="fa fa-arrows-alt"></i></span>
						<label><?php _e(ucwords($social_icon),ESTP_DOMAIN); ?></label>
						<input type="url" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][social_icon][<?php echo $social_icon; ?>][link]"  placeholder="<?php echo $link; ?>" value="<?php echo isset($item['tab_content']['content_slider']['social_icon'][$social_icon]['link'])?esc_attr($item['tab_content']['content_slider']['social_icon'][$social_icon]['link']):''; ?>">
						
						<label><?php _e('Tooltip Text', ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][social_icon][<?php echo $social_icon; ?>][tooltip]" placeholder="<?php echo $social_icon; ?>" value="<?php echo isset($item['tab_content']['content_slider']['social_icon'][$social_icon]['tooltip'])?esc_attr($item['tab_content']['content_slider']['social_icon'][$social_icon]['tooltip']):''; ?>">
					</div>
					<?php } ?>
					</div>
				</div>

				<div class="est-field-wrap estp-content-type estp-custom_shortcode estp-custom-shortcode" style="<?php if( $item['tab_content']['content_slider']['content_type'] == 'custom_shortcode'){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">

					<div class="estp-field-wrap">
						<label><?php _e('Title Text', ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][custom_shortcode][title_text]" value="<?php echo isset($item['tab_content']['content_slider']['custom_shortcode']['title_text'])?esc_attr($item['tab_content']['content_slider']['custom_shortcode']['title_text']):''; ?>" placeholder="Your Title">
					</div>

					<div class="estp-field-wrap">
						<label><?php _e('Custom Shortcode',ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key ?>][tab_content][content_slider][custom_shortcode][shortcode]" value="<?php echo isset($item['tab_content']['content_slider']['custom_shortcode']['shortcode'])?esc_attr($item['tab_content']['content_slider']['custom_shortcode']['shortcode']):''; ?>" placeholder="Enter Your Shortcode"  class="estp-shortcode-value" autocomplete="off">
                        <span class="estp-copied-info" style="display: none;"><?php _e('Shortcode copied to your clipboard.', ESTP_DOMAIN); ?></span>
					</div>
				</div>

				<div class="estp-field-wrap estp-content-type estp-recent_blogs estp-recent-blog" style="<?php if( $item['tab_content']['content_slider']['content_type'] == 'recent_blogs'){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
                    
                    <h3><?php _e('Blog Posts',ESTP_DOMAIN);?></h3>

                    <div class="estp-field-wrap">
						<label><?php _e('Title Text', ESTP_DOMAIN); ?></label>
						<input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][recent_blog][title_text]" value="<?php echo isset($item['tab_content']['content_slider']['recent_blog']['title_text'])?esc_attr($item['tab_content']['content_slider']['recent_blog']['title_text']):''; ?>" placeholder="Your Title">
					</div>


					<div class="estp-field-wrap">
			            <label><?php _e('Post Type', ESTP_DOMAIN); ?></label>
			                <?php
			                $post_types = $this->get_registered_post_types();
			                ?>
			                <select class="estp-post-type-trigger" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][recent_blog][post_type]">
			                    <option value=""><?php _e('Choose Post Type', ESTP_DOMAIN); ?></option>
			                    <?php
			                    if ( !empty($post_types) ) {
			                        foreach ( $post_types as $post_type ) {
			                            ?>
			                            <option value="<?php echo $post_type; ?>" <?php isset($item['tab_content']['content_slider']['recent_blog']['post_type'])?selected($item['tab_content']['content_slider']['recent_blog']['post_type'],$post_type):NULL; ?>><?php echo ucwords(str_replace('_', ' ', $post_type)); ?></option>
			                            <?php
			                        }
			                    }
			                    ?>
			                </select>
			                <img src="<?php echo ESTP_IMAGE_DIR . '/ajax-loader.gif' ?>" class="estp-ajax-loader" style="display: none;"/>
			        </div>

			        <div class="estp-field-wrap">
			            <label><?php _e('Post Taxonomies', ESTP_DOMAIN); ?></label>
			            
		                <select class="estp-post-taxonomy-trigger" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][recent_blog][taxonomy]">
		                    <option value=""><?php _e('Choose Taxonomy', ESTP_DOMAIN); ?></option>
		                	<?php  
		                		$post_type = $item['tab_content']['content_slider']['recent_blog']['post_type'];
		                		if( isset($post_type) )
		                		{
		                			$taxonomies = get_object_taxonomies($post_type, 'objects');
					                unset($taxonomies[ 'post_format' ]);
					                if(!empty($taxonomies))
					                {
					                	foreach($taxonomies as $taxonomy => $taxonomy_object)
					                	{
					                	?>
					                	<option value="<?php echo $taxonomy; ?>" <?php isset($item['tab_content']['content_slider']['recent_blog']['taxonomy'])?selected($item['tab_content']['content_slider']['recent_blog']['taxonomy'],$taxonomy):NULL; ?>>
					                		<?php echo $taxonomy_object->label; ?>
					                	</option>
					                	<?php
					                	}
					                }
		                		}
		                	?>
		                </select>
		                <img src="<?php echo ESTP_IMAGE_DIR . '/ajax-loader.gif' ?>" class="estp-ajax-loader" style="display: none;"/>           
			        </div>

			        <div class="estp-field-wrap">
			            <label><?php _e('Post Terms', ESTP_DOMAIN); ?></label>
			           
		                <select class="estp-post-terms-trigger" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][recent_blog][term]">
		                    <option value=""><?php _e('Choose Terms', ESTP_DOMAIN); ?></option>
		                    <?php
		                    $taxonomy = isset($item['tab_content']['content_slider']['recent_blog']['taxonomy'])?$item['tab_content']['content_slider']['recent_blog']['taxonomy']:NULL;
		                    if(isset( $taxonomy ) && !empty($taxonomy))
		                    {

		                    	$terms = get_terms($taxonomy, array( 'hide_empty' => false, 'orderby' => 'name', 'order' => 'asc' ));
		                    	if(!empty($terms))
		                    	{
		                    		foreach($terms as $term)
		                    		{
		                    		?>
		                    		<option value="<?php echo $term->term_id; ?>" <?php isset($item['tab_content']['content_slider']['recent_blog']['term'])?selected($item['tab_content']['content_slider']['recent_blog']['term'],$term->term_id):NULL; ?>><?php echo $term->name; ?></option>
		                    		<?php
		                    		}
		                    	}
		                    }
		                    ?>
		                </select>
			        </div>

                    <div class="estp-field-wrap">
                    	<label for="number_of_blogpost_<?php echo $key; ?>"><?php _e('Number of posts',ESTP_DOMAIN) ?></label>
                    	<input type="number" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][recent_blog][number_of_post]" value="<?php echo isset($item['tab_content']['content_slider']['recent_blog']['number_of_post'])?esc_attr($item['tab_content']['content_slider']['recent_blog']['number_of_post']):''; ?>" id="number_of_blogpost_<?php echo $key; ?>" min="0">
                    </div>

                    <div class="estp-field-wrap">
                    	<label><?php _e('Order By',ESTP_DOMAIN); ?></label>
                    	<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][recent_blog][order_by]">
                    		<option value="none"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order_by'] ) && $item['tab_content']['content_slider']['recent_blog']['order_by'] == 'none' ) echo 'selected'; ?>>
                    			<?php _e( 'None', ESTP_DOMAIN ); ?>
                    		</option>

		                    <option value="ID"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order_by'] ) && $item['tab_content']['content_slider']['recent_blog']['order_by'] == 'ID' ) echo 'selected'; ?>>
		                    	<?php _e( 'ID', ESTP_DOMAIN ); ?>
		                    </option>

		                    <option value="author"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order_by'] ) && $item['tab_content']['content_slider']['recent_blog']['order_by'] == 'author' ) echo 'selected'; ?>>
		                    	<?php _e( 'Author', ESTP_DOMAIN ); ?>
		                    </option>

		                    <option value="title"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order_by'] ) && $item['tab_content']['content_slider']['recent_blog']['order_by'] == 'title' ) echo 'selected'; ?>>
		                    	<?php _e( 'Title', ESTP_DOMAIN ); ?>
		                    </option>

		                    <option value="name"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order_by'] ) && $item['tab_content']['content_slider']['recent_blog']['order_by'] == 'name' ) echo 'selected'; ?>>
		                    	<?php _e( 'Post Name', ESTP_DOMAIN ); ?>
		                    </option>

		                    <option value="type"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order_by'] ) && $item['tab_content']['content_slider']['recent_blog']['order_by'] == 'type' ) echo 'selected'; ?>>
		                    	<?php _e( 'Post Type', ESTP_DOMAIN ); ?>
		                    </option>

		                    <option value="date"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order_by'] ) && $item['tab_content']['content_slider']['recent_blog']['order_by'] == 'date' ) echo 'selected'; ?>>
		                    	<?php _e( 'Date', ESTP_DOMAIN ); ?>
		                    </option>

		                    <option value="modified"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order_by'] ) && $item['tab_content']['content_slider']['recent_blog']['order_by'] == 'modified' ) echo 'selected'; ?>>
		                    	<?php _e( 'Last Modified Date', ESTP_DOMAIN ); ?>
		                    </option>
                    	</select>
                    </div>

                    <div class="estp-field-wrap">
                    	<label><?php _e('Order',ESTP_DOMAIN); ?></label>

                    	<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][recent_blog][order]">
                    		<option value=""><?php _e('None',ESTP_DOMAIN); ?></option>
                    		<option value="ASC"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order'] ) && $item['tab_content']['content_slider']['recent_blog']['order'] == 'ASC' ) echo 'selected=="selected"'; ?>>
                    			<?php _e( 'Ascending', ESTP_DOMAIN ) ?>
                    		</option>
                    		<option value="DESC"  <?php if ( isset( $item['tab_content']['content_slider']['recent_blog']['order'] ) && $item['tab_content']['content_slider']['recent_blog']['order'] == 'DESC' ) echo 'selected=="selected"'; ?>>
                    			<?php _e( 'Descending', ESTP_DOMAIN ) ?>
                    		</option>
                    	</select>
                    </div>

                    <div class="estp-field-wrap" class="estp-blogs-layout-wrap">
						<label><?php esc_attr_e( 'Blogs Layout', ESTP_DOMAIN ); ?></label>
						<div class="estp-select-img-wrap">
							<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][recent_blog][layout]" class="estp-blogs-image-selector">
								<?php 
									global $estp_variables;
									$img_url = ESTP_IMAGE_DIR . "/blog-layout/layout1.png"; 
									foreach($estp_variables['recent_blogs_layout'] as $blog_key=>$blog_value){
								    //echo "<pre>";print_r($value);echo "</pre>";
									if(isset($item['tab_content']['content_slider']['recent_blog']['layout']) && $item['tab_content']['content_slider']['recent_blog']['layout'] == $blog_value['value'])
									{
										$selected = 'selected="selected"';
										$img_url = $blog_value['img'];	
									}else{
										$selected = '';
									}
									
								?>
								<option value="<?php echo esc_attr($blog_value['value']); ?>" <?php if(isset($item['tab_content']['content_slider']['recent_blog']['layout']) && $item['tab_content']['content_slider']['recent_blog']['layout'] == $blog_value['value']){ ?> selected="selected"<?php } ?>  data-img="<?php echo esc_url($blog_value['img']); ?>">
									<?php echo esc_attr($blog_value['name']); ?>
								</option>
								<?php } ?>
							</select>
							<div class="estp-image-preview-wrap">
								<div class="estp-blogs-layout-template-image">
									<img src="<?php echo esc_url($img_url); ?>" height="200" width="200" alt="template image">
								</div>
							</div>
						</div>
					</div>

				</div>

				<div class="estp-field-wrap estp-content-type estp-woocommerce_product estp-woocommerce-product" style="<?php if( $item['tab_content']['content_slider']['content_type'] == 'woocommerce_product'){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
					
					<?php include(ESTP_PLUGIN_ROOT_DIR.'inc/backend/boards/metaboxes/includes/wooproducts.php'); ?>

					<div class="estp-field-wrap" class="estp-woocommerce-layout-wrap">
						<label><?php esc_attr_e( 'Woocommerce Layout', ESTP_DOMAIN ); ?></label>
						<div class="estp-select-img-wrap">
							<select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][woocommerce_product][layout]" class="estp-woocommerce-image-selector">
								<?php 
									global $estp_variables;
									$img_url = ESTP_IMAGE_DIR . "/woocommerce-layout/layout1.png"; 
									foreach($estp_variables['woocommerce_layout'] as $woo_key=>$woo_value){
								    //echo "<pre>";print_r($value);echo "</pre>";
									if(isset($item['tab_content']['content_slider']['woocommerce_product']['layout']) && $item['tab_content']['content_slider']['woocommerce_product']['layout'] == $woo_value['value']){
										
										$selected = 'selected="selected"';
										$img_url = $woo_value['img'];	
									}else{
										$selected = '';
									}
									
								?>
								<option value="<?php echo esc_attr($woo_value['value']); ?>" <?php if(isset($item['tab_content']['content_slider']['woocommerce_product']['layout']) && $item['tab_content']['content_slider']['woocommerce_product']['layout'] == $woo_value['value']){ ?> selected="selected"<?php } ?>  data-img="<?php echo esc_url($woo_value['img']); ?>">
									<?php echo esc_attr($woo_value['name']); ?>
								</option>
								<?php } ?>
							</select>
							<div class="estp-image-preview-wrap">
								<div class="estp-woocommerce-layout-template-image">
									<img src="<?php echo esc_url($img_url); ?>" height="200" width="200" alt="template image">
								</div>
							</div>
						</div>
					</div>

					<div class="estp_woocommerce_btn_icon" > 
	                    <div class="estp-field-wrap">
	                        <label for="estp-woocommerce-icon_<?php echo $key; ?>">
	                        	<?php _e('Woocommerce Icon',ESTP_DOMAIN);?>
	                        </label>
	                        
	                        <input class="estp-woocommerce-icon-picker" type="hidden" id="estp-woocommerce-icon_<?php echo $key; ?>" name='tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][woocommerce_product][btn_icon]' value='<?php echo isset($item['tab_content']['content_slider']['woocommerce_product']['btn_icon'])?$item['tab_content']['content_slider']['woocommerce_product']['btn_icon']:''; ?>' />

	                        <div data-target="#estp-woocommerce-icon_<?php echo $key; ?>" class="estp-icon-pick-button icon-picker  <?php if ($item['tab_content']['content_slider']['woocommerce_product']['btn_icon'] !='' && isset($item['tab_content']['content_slider']['woocommerce_product']['btn_icon'])) { $y = explode('|', $item['tab_content']['content_slider']['woocommerce_product']['btn_icon']); echo $y[0] . ' ' . $y[1]; }else{ echo '';} ?>">
	                       		<?php _e( 'Select Icon', ESTP_DOMAIN); ?>
	                        </div>
	                    </div>
	                </div>
					
				</div>
			
				<div class="estp-field-wrap estp-content-type estp-html_content  estp-inner-field-wrap estp-html-content" id="estp-tab-content" style="<?php if( $item['tab_content']['content_slider']['content_type'] == 'html_content'){ echo 'display:block;'; }else{ echo 'display:none;'; } ?>">
					<label><?php esc_attr_e( 'Content', ESTP_DOMAIN ); ?></label>
					<?php
			            if ( isset($item['tab_content']['content_slider']['html_content']) ) 
			            {
			                $content_editor = ($item['tab_content']['content_slider']['html_content']);
			            }
			            else{
			            	$content_editor = "";
			            }
			            $settings = array(
			            			'media_buttons' => false, 
			            			'textarea_name' => 'tab[tab_settings][tab_items][' .$key. '][tab_content][content_slider][html_content]', 
			            			'quicktags' => array('buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close'),
			            			'editor_class' => 'estp_slider_content_'.$key,
			            			);

			            $editor_id = 'estp_content_'.$key; //id for this editor
			            wp_editor($content_editor, $editor_id, $settings);
			        ?>
				</div>
			</div>	

		</div>

	</div>
</div>

