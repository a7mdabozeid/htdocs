<?php defined('ABSPATH') or die('No scripts for you') ?>
<?php
	$checked_id_array = array();
	if (isset($_POST['checked_state'])) {
		$checked_id_array = explode(',', $_POST['checked_state']);
	}
	if (isset($_POST['key_val_pair'])) {
		$temp_array = $_POST['key_val_pair'];
		$args = array(
				   'public'   => true,
				);
				$output = 'names'; // names or objects, note names is the default
				$operator = 'and'; // 'and' or 'or'
				$post_types = get_post_types( $args, $output, $operator );
				unset($post_types['attachment']); //Attachment is excluded
				$i = 1;
				foreach ($post_types as $post_type_name) {

					$paged = "paged".intval($i);
					$indexid = 1;
					foreach ($temp_array as $index => $key_val_pair) {
						$key_val_pair_array = explode('=', esc_attr($key_val_pair)) ;
						$key = esc_attr($key_val_pair_array[0]);
						$value = intval($key_val_pair_array[1]);
						if ($key == $paged) {
							$indexid = $value;
						}
					}
		?>
					<div class="estp_post_type_container">
						<div>
							<label><?php echo ucwords(esc_attr($post_type_name)); ?></label>
						</div>
						<?php
							?>
							<?php
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
								if (is_array($checked_id_array)) {
									foreach ($checked_id_array as $index => $value) {
										if ($act_value->ID == $value) {
											$matched_value = $value;
										}
									}
								}
								?>
								<div class="estp_individual_term"><input type="checkbox" class="estp_post_type_term" value="<?php echo $act_value->ID; ?>" <?php checked($matched_value,$act_value->ID) ?>><?php echo (!empty($act_value->post_title))?esc_attr($act_value->post_title):"#" . (intval($act_value->ID)); ?></div>
								<?php
							endforeach; //End of Posts of certain post type
							endif;
							?>
							</div>
					</div>
					<div class="estp_pagination_links" data-paged="<?php echo esc_attr($paged) . '=' . intval($indexid) ?>">
						<?php
							// $big = 999999999;
							$pargs = array(
									'current'		=> $indexid,
									'format'		=> '?'.$paged.'=%#%',
									'prev_next'		=> true,
									'prev_text'		=> esc_attr__('« Previous',WPCUIP_DOMAIN),
									'next_text'		=> esc_attr__('Next »',WPCUIP_DOMAIN),
									'type'			=> 'plain',
									'add_args'		=> false,
									'base'			=> str_replace('%_%', (1 == $indexid) ? '' : "?$".$paged."=%#%", "?".$paged."=%#%&estp-admin"),
                  					// 'base'			=> str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
									'total'			=> $post_type_object->max_num_pages
								);
								echo paginate_links($pargs);
								$i++;
						?>
					</div>
				<?php
			} //End of Post Type Loops
		}
?>