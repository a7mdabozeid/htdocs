<?php defined('ABSPATH') or die("No script kiddies please!");?>

<?php 

$post_type = $pos_tab_settings['tab_content']['content_slider']['recent_blog']['post_type'];
$post_taxonomy = $pos_tab_settings['tab_content']['content_slider']['recent_blog']['taxonomy'];
$post_term = $pos_tab_settings['tab_content']['content_slider']['recent_blog']['term'];
$blog_tab_title = (isset($pos_tab_settings['tab_content']['content_slider']['recent_blog']['title_text']) && !empty($pos_tab_settings['tab_content']['content_slider']['recent_blog']['title_text']))?esc_attr($pos_tab_settings['tab_content']['content_slider']['recent_blog']['title_text']):'';

if ( isset( $pos_tab_settings['tab_content']['content_slider']['recent_blog']['number_of_post'] ) ) 
{
    $post_number = $pos_tab_settings['tab_content']['content_slider']['recent_blog']['number_of_post'];
} else {
    $post_number = 4;
}

if ( isset( $pos_tab_settings['tab_content']['content_slider']['recent_blog']['order'] ) ) 
{
    $order = $pos_tab_settings['tab_content']['content_slider']['recent_blog']['order'];
} else {
    $order = 'DESC';
}

if ( isset( $pos_tab_settings['tab_content']['content_slider']['recent_blog']['order_by'] ) ) 
{
    $order_by = $pos_tab_settings['tab_content']['content_slider']['recent_blog']['order_by'];
} else {
    $order_by = 'date';
}

$post_args = array( 'posts_per_page' => $post_number, 'post_status' => 'publish', 'post_type' => $post_type, 'order' => $order, 'orderby' => $order_by );

if ( !empty($post_taxonomy) && !empty($post_term) ) 
{
    $post_args[ 'tax_query' ] = array( array( 'taxonomy' => $post_taxonomy, 'field' => 'term_id', 'terms' => $post_term ) );
}


$post_query = new WP_Query($post_args);		 
?>

<div class="estp-field-wrap estp-front-recent-blogs-wrap">
	<div class="estp-recent-blog-outer-wrapper">
		<div class="estp-front-tab-title">
			<h3><?php _e($blog_tab_title, ESTP_DOMAIN); ?></h3>
		</div>
		<?php 
			if ( $post_query->have_posts() ) 
			{
		        while ( $post_query->have_posts() ) 
		        {
		            $post_query->the_post();
		            if(isset($pos_tab_settings['tab_content']['content_slider']['recent_blog']['layout']))
					{
						if($pos_tab_settings['tab_content']['content_slider']['recent_blog']['layout'] == 'blog-layout-1')
						{
							include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/blog-templates/template-1.php');
		        		}
		        		else if($pos_tab_settings['tab_content']['content_slider']['recent_blog']['layout'] == 'blog-layout-2')
						{
							include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/blog-templates/template-2.php');
		        		}
		        		else if($pos_tab_settings['tab_content']['content_slider']['recent_blog']['layout'] == 'blog-layout-3')
		        		{
		        			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/blog-templates/template-3.php');
		        		}
		        		else if($pos_tab_settings['tab_content']['content_slider']['recent_blog']['layout'] == 'blog-layout-4')
		        		{
		        			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/blog-templates/template-4.php');	
		        		}
		        		else if($pos_tab_settings['tab_content']['content_slider']['recent_blog']['layout'] == 'blog-layout-5')
		        		{
		        			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/blog-templates/template-5.php');	
		        		}
		        		else if($pos_tab_settings['tab_content']['content_slider']['recent_blog']['layout'] == 'blog-layout-6')
		        		{
		        			include(ESTP_PLUGIN_ROOT_DIR.'inc/frontend/blog-templates/template-6.php');	
		        		}
		    		}
		        }
		    }
		?>
	</div>
</div>