<?php
global $wpdb;
$table_name = $wpdb->prefix . 'est_settings';
//get all the row from the database
$estp_lists = $wpdb->get_results("SELECT * FROM $table_name ORDER BY ID ASC");
?>

<div class="wrap estp-wrap">
  <div class="estp-header-wrap">
      <h3><span class="estp-admin-title"><?php esc_attr_e( 'All Tabs', ESTP_DOMAIN ); ?></span></h3>
      <div class="logo">
          <img src="<?php echo ESTP_IMAGE_DIR; ?>/logo.png" alt="<?php esc_attr_e('AccessPress Social Icons', ESTP_DOMAIN); ?>">
      </div>
  </div>

  <?php if (isset($_GET['message']) && $_GET['message'] == 1): ?>
    <div class="notice notice-success is-dismissible"><p>Deleted Successfully</p></div>
  <?php elseif(isset($_GET['message']) && $_GET['message'] == 0): ?>
    <div class="notice notice-error is-dismissible"><p>Failed to delete</p></div>
  <?php endif ?>

  <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">

  <?php wp_nonce_field('delete_chosen_settings_nonce', 'delete_chosen_settings_nonce_field'); ?>
  <input type="hidden" name="action" value="estp_delete_chosen_settings"/>  

  <a class="estp-add-tab-btn button-primary" href="<?php echo admin_url().'admin.php?page=estp-admin&action=estp-add-tab'; ?>"><?php _e('Add Tab',ESTP_DOMAIN); ?></a> &nbsp;&nbsp;

  <input type="submit" class="button-secondary" name="remove_tabs" id="remove-tabs" value="<?php _e('Remove', ESTP_DOMAIN); ?>"  />

  <table class="widefat estp-tab-list-table" style="margin-top:12px;">
    <thead>
    <tr>
      <th>
          <label><input type="checkbox" name="checkall_tab" value="1" id="estp-checkall-tab" /></label>
      </th>
      <th><?php _e( 'S.N', ESTP_DOMAIN ); ?></th>
      <th class="row-title"><?php esc_attr_e( 'Side Tab Name', ESTP_DOMAIN ); ?></th>
      <th><?php esc_attr_e('Shortcode', ESTP_DOMAIN); ?></th>
      <th><?php esc_attr_e('Template Include', ESTP_DOMAIN); ?></th>
      <th><?php esc_attr_e( 'Template Name', ESTP_DOMAIN ); ?></th>
      <th><?php esc_attr_e( 'Action', ESTP_DOMAIN ); ?></th>
    </tr>
    </thead>

    <tbody>
    <?php $count = 1; ?>
    <?php 
    foreach ($estp_lists as $estp_list) { 
      $estp_settings = maybe_unserialize( $estp_list->plugin_settings, ARRAY_A);
    ?>
    <tr class="<?php echo ($count % 2 !== 0)?'alternate':NULL; ?>">
      <td><input type="checkbox" name="tabchk[]" class="estp-select-tab" value="<?php echo esc_js(esc_html($estp_list->id)); ?>"></td>
      <td><?php echo $count; ?></td>
      <td class="row-title">
        <label for="tablecell">
          <a href="<?php echo admin_url().'admin.php?page=estp-admin&action=edit-tab&id='.$estp_list->id; ?>">
          <?php esc_attr_e($estp_list->name); ?> 
          </a>
        </label>
      </td>
      <td>
        <input type='text' class='estp-shortcode-value' readonly='' value='[estp tab_id="<?php echo $estp_list->id; ?>"]' />
        <span class="estp-copied-info" style="display: none;"><?php _e('Shortcode copied to your clipboard.', ESTP_DOMAIN); ?></span>
      </td>
      <td>
        <textarea cols="37" rows="3" class='estp-shortcode-value' readonly='readonly'>&lt;?php echo do_shortcode("[estp tab_id=<?php echo $estp_list->id; ?>]") ?&gt;</textarea>
        <span class="estp-copied-info" style="display: none;"><?php _e('Shortcode copied to your clipboard.', ESTP_DOMAIN); ?></span> 
      </td>
      <td><?php esc_attr_e( $estp_settings['tab']['layout_settings']['template'], ESTP_DOMAIN ); ?></td>
      <td>
          <div class="estp-action-btn-wrap">
              <a href="<?php echo admin_url().'admin.php?page=estp-admin&action=edit-tab&id='.$estp_list->id; ?>" class="button-secondary estp-button-secondary" title="Edit Tab"></a> &nbsp;&nbsp;

              <a href="javascript:void(0)" data-tab-id="<?php echo $estp_settings['tab']['tab_id']; ?>" title="<?php _e('Copy Tab', ESTP_DOMAIN); ?>" class="button estp-tab-copy estp-tab-preview"></a> &nbsp;&nbsp;

              <a onclick="return confirm('Do you really want to delete ?')" href="<?php echo admin_url().'admin-post.php?action=delete_chosen_setting&_wpnonce='.wp_create_nonce('estp_delete_tab').'&id='.$estp_list->id; ?>" class="button estp-tab-delete"></a> &nbsp;&nbsp;

              <a href="<?php echo get_home_url(); ?>?estp_preview=<?php echo intval($estp_list->id) ?>" target="_blank" class="button estp-tab-preview" ></a>

          </div> 
      </td>
    </tr>
    <?php $count++; } ?>
    </tbody>
    <tfoot>
    <tr>
      <th></th>
      <th><?php _e('S.N',ESTP_DOMAIN); ?></th>
      <th class="row-title"><?php esc_attr_e( 'Side Tab Name', ESTP_DOMAIN ); ?></th>
      <th><?php esc_attr_e('Shortcode', ESTP_DOMAIN); ?></th>
      <th><?php esc_attr_e('Template Include', ESTP_DOMAIN); ?></th>
      <th><?php esc_attr_e( 'Template Name', ESTP_DOMAIN ); ?></th>
      <th><?php esc_attr_e( 'Action', ESTP_DOMAIN ); ?></th>
    </tr>
    </tfoot>
  </table>
  </form>

  <div class="estp-notice-head" style="display: none;"> </div>
</div>
