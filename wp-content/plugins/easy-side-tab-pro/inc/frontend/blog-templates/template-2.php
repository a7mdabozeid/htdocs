<div class="estp-recent-blog-single-wrap estp-blog-layout-2">
	
    <?php
    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ),array('150','150'));
	if ( ! empty( $large_image_url[0] ) ) {  ?>
	<div class="estp-recent-blog-feat-img">
		<?php    echo "<img src='".esc_url( $large_image_url[0] )."' alt='".the_title_attribute( array( 'echo' => 0 ) )."'/>"; ?>
	</div>
	<?php 
	}
	?>
	<div class="estp-blog-author-title-date-wrap">
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
				<?php the_time( 'F jS, Y' ); ?> 
			</small>
		</div>
	</div>
	
</div>