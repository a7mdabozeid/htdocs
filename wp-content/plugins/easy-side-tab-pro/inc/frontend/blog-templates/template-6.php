<div class="estp-recent-blog-single-wrap estp-blog-layout-6">

    <?php
    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ),array('150','150'));
	if ( ! empty( $large_image_url[0] ) ) 
	{  
	?>
	<div class="estp-blog-outer-wrapper">	
		<div class="estp-recent-blog-feat-img">
			<div class="estp-blog-read-more">
				<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>">
					<?php _e('Read More', ESTP_DOMAIN); ?>
				</a>
			</div>
			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
				<?php    echo "<img src='".esc_url( $large_image_url[0] )."' alt='".the_title_attribute( array( 'echo' => 0 ) )."'/>"; ?>
			</a>
		</div>

		<?php 
		}
		?>
		<div class="estp-blog-inner-wrapper">
			<div class="estp-blog-author">
				<?php the_author_posts_link(); ?>
			</div>

		 	<div class="estp-recent-blog-title">
			 	<h2>
			 		<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
			 			<?php //the_title(); ?>
			 			<?php echo strip_tags( substr(get_the_title(), 0, 15) ) .'....'; ?>
			 		</a>
			 	</h2>
		 	</div>
		 	
			<div class="estp-recent-blog-date-author">
				<small>
					<?php the_time( 'jS F, Y' ); ?> 
				</small>
			</div>
		</div>
	</div>

</div>