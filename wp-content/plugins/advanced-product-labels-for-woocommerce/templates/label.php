<?php 
global $pagenow, $post;
$BeRocket_advanced_labels_custom_post = BeRocket_advanced_labels_custom_post::getInstance();
$label = array(
    'label_from_post'       => '',
);
if( ! in_array( $pagenow, array( 'post-new.php' ) ) ) {
    $label = $BeRocket_advanced_labels_custom_post->get_option($post->ID);
}
echo '<div class="panel wc-metaboxes-wrapper" id="br_alabel" style="display: none;">';
wp_nonce_field('br_labels_check', 'br_labels_nonce');
echo '<table style="width: 99%;"><tr><th style="width: 250px;">'.__('Label to display on this product', 'BeRocket_products_label_domain').'</th>
<td><div style="max-height:200px;margin:10px 0;overflow: auto;">';
foreach($posts_array as $post_id) {
    $post_title = get_the_title($post_id);
    echo '<p style="margin: 0 0 3px;"><label><input name="br_labels[label_from_post][]" type="checkbox" value="'.$post_id.'"'.(is_array($label['label_from_post']) && in_array($post_id, $label['label_from_post']) ? ' checked' : '').'>('.$post_id.') '.$post_title.'</label></p>';
}
echo '</div></td></tr></table>';
?>
</div>
