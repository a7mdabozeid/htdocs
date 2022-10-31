<?php defined('ABSPATH') or die("No script kiddies please!"); ?>


<div class="wrap estp-wrap">
    <div class="estp-header-wrap">
        <h3><span class="estp-admin-title"><?php esc_attr_e( 'Subscribers', ESTP_DOMAIN ); ?></span></h3>
        <div class="logo">
            <img src="<?php echo ESTP_IMAGE_DIR . '/logo.png'; ?>" alt="<?php esc_attr_e('AccessPress Social Icons', ESTP_DOMAIN); ?>">
        </div>
    </div>
    
   <?php
    if ( isset($_POST[ 'remove_subs' ]) ) {
        global $wpdb;
        $checked_id = array_map('intval', $_POST[ 'rem' ]);
        $table_name = $wpdb->prefix . 'estp_subscribers';
        if ( !$checked_id == '' ) {
            foreach ( $checked_id as $id ) {
                $wpdb->delete($table_name, array( 'subscriber_id' => $id ), array( '%d' ));
            }
        }
    }
    ?>

    <form method="post" action="">
        <div class="estp-panel-body">
            <div class="estp-subscribe-actions">
                <input type="submit" class="button-secondary" name="remove_subs" id="remove-sub" value="<?php _e('Remove Subscribers', ESTP_DOMAIN); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete?', ESTP_DOMAIN); ?>')" />

                <a class="button" href="<?php echo admin_url('admin-post.php?action=estp_export_subscriber&_wpnonce=' . wp_create_nonce('estp_export_nonce')); ?>">
                	<?php _e('Export as CSV', ESTP_DOMAIN) ?>
                </a>
            </div>
            <table class=" widefat ">
                <thead>
                    <tr>
                        <th>
                            <label><input type="checkbox" name="checkall_sub" value="1" id="estp-checkall" /></label>
                        </th>
                        <th>
                            <span><?php _e('S.N',ESTP_DOMAIN); ?></span>
                        </th>
                        <th>
                            <span><?php _e('Name', ESTP_DOMAIN); ?></span>
                        </th>
                        <th>
                            <span><?php _e('Email', ESTP_DOMAIN); ?></span>
                        </th>
                    </tr>
                </thead>
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'estp_subscribers';
                $user_sets = $wpdb->get_results("SELECT * FROM $table_name");
                ?>
                <tbody>
                    <?php
                    if ( count($user_sets) > 0 ) 
                    {
                        $counter = 1;
                        foreach ( $user_sets as $user_set ) 
                        {
                            ?>

                            <tr class="<?php echo ($counter % 2 !== 0)?'alternate':NULL; ?>">
                                <td><input type="checkbox" name="rem[]" class="estp-select-subs" value="<?php echo esc_js(esc_html($user_set->subscriber_id)); ?>"></td>
                                <td><?php echo $counter;$counter++; ?></td>
                                <td><?php echo $user_set->subscriber_name; ?></td>
                                <td><?php echo $user_set->email; ?></td>
                            </tr>

                    <?php
                        }
                    } 
                    else
                    {
                        ?>
                        	<tr>
                        		<td colspan="2">
                        			<div class="estp-noresult"><?php _e('No Subscribers Found.', ESTP_DOMAIN); ?></div>
                        		</td>
                        	</tr>
                    <?php } ?>

            </table>
        </div>
    </form>
</div>