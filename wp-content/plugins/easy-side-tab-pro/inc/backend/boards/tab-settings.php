<div class="content estp-content postbox" id="estp-tab-settings">   

    <?php 
        $random_value = ESTP_Class :: generateRandomIndex();
        $random_val = isset($estp_settings['tab']['random_value']) && !empty($estp_settings['tab']['random_value'])?esc_attr($estp_settings['tab']['random_value']): $random_value;
    ?>

    <input type="hidden" name="tab[random_value]" value="<?php echo isset($estp_settings['tab']['random_value']) && !empty($estp_settings['tab']['random_value']) ? esc_attr($estp_settings['tab']['random_value']) : $random_val; ?>">

    <div class="estp-field-wrap" id="estp-tab-title">
        <label><?php _e('Tab Title', 'easy-side-tab-pro'); ?></label>
        <input type="text" value="<?php ( $estp_settings['tab']['tab_settings']['tab_name'] )?esc_attr_e( $estp_settings['tab']['tab_settings']['tab_name'] ):''; ?>" class="regular-text estp-input-text" name="tab[tab_settings][tab_name]" />
    </div>      

    <?php if(isset($_GET['id'])) { ?>
    <div class="estp-field-wrap" id="estp-shortcode">    
        <label><?php _e('Shortcode', ESTP_DOMAIN);?></label>

        <input type='text' class='estp-shortcode-value' readonly='' value='[estp tab_id="<?php echo $_GET['id']; ?>"]' onclick='select()' />
        <span class="estp-copied-info" style="display: none;"><?php _e('Shortcode copied to your clipboard.', ESTP_DOMAIN); ?></span>
        <p class="description">
            <?php _e('Copy ',ESTP_DOMAIN);?>&amp;<?php _e(' paste the shortcode directly into any WordPress post or page.',ESTP_DOMAIN);?>
        </p>
    </div>

    <div class="estp-field-wrap">
        <label><?php _e('Template Include', ESTP_DOMAIN);?></label>
        
        <textarea cols="37" rows="3" class='estp-shortcode-value' readonly='readonly'>&lt;?php echo do_shortcode("[estp tab_id=<?php echo $_GET['id']; ?>]") ?&gt;</textarea>
        <span class="estp-copied-info" style="display: none;"><?php _e('Shortcode copied to your clipboard.', ESTP_DOMAIN); ?></span>   
        <p class="description">
            <?php _e('Copy ', ESTP_DOMAIN);?>&amp;<?php _e(' paste this code into a template file to include the Easy Side Tab on your theme to display this specific page tab.',ESTP_DOMAIN);?>
        </p>
    </div>
    <?php } ?>
        

    <div class="estp-tab-items">
        <h3 class="postbox estp-tab-items-drop"> <?php _e('Tab Items', ESTP_DOMAIN); ?> 
            <span id="drop-toggle"><i class="fa fa-caret-down"></i></span>
        </h3>

        <div class="estp-add-item-wrap postbox" style="display: none;">
            <input type="button" class="button button-primary estp-add-button" name="" value="<?php _e('Add Tab Item', ESTP_DOMAIN); ?>" data-action="add_item">
            <span class="estp-loader-image" style="display: none;">
                <img src="">
            </span>
            <div class="estp-add-new-item">

                <?php 
                if(isset($estp_settings) && !empty( $estp_settings) )
                {
                    $tabs_counts = count($estp_settings);
                    if($tabs_counts != 0)
                    {
                        $count = 1;
                        $tab_item = isset($estp_settings['tab']['tab_settings']['tab_items'])?$estp_settings['tab']['tab_settings']['tab_items']:array();
                        if(!empty($tab_item))
                        {
                            foreach ($tab_item as $key => $item) 
                            {
                                include(ESTP_PLUGIN_ROOT_DIR. 'inc/backend/boards/metaboxes/item.php');
                            } 
                        }
                    }
                }
                else
                {
                    $key = ESTP_Class :: generateRandomIndex();
                    $counter = 1;
                    include ESTP_PLUGIN_ROOT_DIR.'inc/backend/boards/metaboxes/item.php'; 
                }    
                ?>
            </div>
        </div>
    </div>	
    		
</div> <!-- content -->