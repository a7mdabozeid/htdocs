<div class="estp-recent-blog-single-wrap estp-blog-layout-1">
    <?php
    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ),array('150','150'));
	if ( ! empty( $large_image_url[0] ) ) {  ?>
		<div class="estp-recent-blog-feat-img">
			<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
				<?php    echo "<img src='".esc_url( $large_image_url[0] )."' alt='".the_title_attribute( array( 'echo' => 0 ) )."'/>"; ?>
			</a>
		</div>
	<?php 
	}
	?>
		<div class="estp-recent-blog-date-author">
			<small><?php the_time( 'jS F, Y' ); ?> / <?php the_author_posts_link(); ?></small>
		</div>
	<!-- Display the Title as a link to the Post's permalink. -->
 	<h2>
 		<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
 		<?php //the_title(); ?>
 		<?php echo strip_tags( substr(get_the_title(), 0, 15) ) .'....'; ?>
 		</a>
 	</h2>
	
</div>