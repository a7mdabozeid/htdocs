<?php
defined("ABSPATH") or die();

$the_page_id = get_the_ID();

if(class_exists('WooCommerce')){
  if(is_shop())
  {
    $the_page_id = wc_get_page_id( 'shop' );
  }
}

$selected_tab_id =  isset($atts['tab_id']) && !empty($atts['tab_id'])?$atts['tab_id']:NULL;

if( $estp_general_settings['general_settings'][$atts['position']]['display_page'] == 'all_pages' || $estp_general_settings['general_settings'][$atts['position']]['display_page'] == 'homepage' )
{
  if( (isset($selected_tab_id) && !empty($selected_tab_id))  )
  {
    $estp_settings_db = $wpdb->get_results("SELECT * FROM $table_name WHERE ID=$selected_tab_id");
    if(empty($estp_settings_db))
    {
      return false;
    }
    $estp_settings_pos_1 = maybe_unserialize( $estp_settings_db[0]->plugin_settings, ARRAY_A);
    $estp_settings_pos = $estp_settings_pos_1['tab']['tab_settings']['tab_items'];
    $estp_layout_settings = $estp_settings_pos_1['tab']['layout_settings'];
    $estp_random_value = isset($estp_settings_pos_1['tab']['random_value']) && !empty($estp_settings_pos_1['tab']['random_value']) ? esc_attr($estp_settings_pos_1['tab']['random_value']):'';


    // get color if customize enabled
    if( isset($estp_layout_settings['enable_customize']) )
    {
      if( isset($estp_layout_settings['customize_settings']['background_color']) && !empty($estp_layout_settings['customize_settings']['background_color']) )
      {
        $background_color = esc_attr($estp_layout_settings['customize_settings']['background_color']);
      }
      if ( isset($estp_layout_settings['customize_settings']['text_color']) && !empty($estp_layout_settings['customize_settings']['text_color']) )
      {
        $text_color = esc_attr($estp_layout_settings['customize_settings']['text_color']);
      }
      if( isset($estp_layout_settings['customize_settings']['background_hover_color']) && !empty($estp_layout_settings['customize_settings']['background_hover_color']) )
      {
        $background_hover_color = esc_attr($estp_layout_settings['customize_settings']['background_hover_color']);
      }
      if( isset($estp_layout_settings['customize_settings']['text_hover_color']) && !empty($estp_layout_settings['customize_settings']['text_hover_color']) )
      {
        $text_hover_color = esc_attr($estp_layout_settings['customize_settings']['text_hover_color']);
      }
    }

    //For display Position
    if(isset($estp_layout_settings['display_position']))
    {
      if($position_class == 'estp-lft-side-tab' || $position_class == 'estp-rht-side-tab')
      {
        $display_position = ( $estp_layout_settings['display_position'] == 'fixed')?'estp-fixed':( ($estp_layout_settings['display_position'] == 'absolute')?'estp-absolute':'' );
      }
      else
      {
        $display_position = 'estp-fixed';
      }
    }

    //selected template
    if(isset($estp_layout_settings['template']))
    {
      $tab_template = $estp_layout_settings['template'];
      // $selected_template =  ( esc_attr($estp_layout_settings['template']) == 'Template 1')?'template-1':( ( esc_attr($estp_layout_settings['template']) == 'Template 2')?'template-2' : '') ;
      // $selected_template = 'estp-'.$selected_template;
      $selected_template = 'estp-'.$estp_layout_settings['template'];
    }else{
      $tab_template = '';
      $selected_template = '';
    }

    if( isset($selected_template) && !empty($selected_template) )
    {
      if(($selected_template == 'estp-template-2'))
      {
        if($atts['position'] == 'left_middle')
          $animate_style = 'estp-animated estp-bounceOutRight';

        else if($atts['position'] == 'right_middle')
          $animate_style = 'estp-animated estp-bounceOutLeft';

        else if($atts['position'] == 'bottom_left' || $atts['position'] == 'bottom_right')
          $animate_style = 'estp-animated estp-bounceOutUp';
      }

      else if($selected_template == 'estp-template-1')
      {
      }

      else if(($selected_template == 'estp-template-3'))
      {
        $animate_style = 'estp-animated estp-fadeIn';
      }

      else if($selected_template == 'estp-template-4' )
      {
        $animate_style = 'estp-animated estp-shake';

        if($atts['position'] == 'bottom_left' || $atts['position'] == 'bottom_right')
          $animate_style = 'estp-animated estp-shake-bottom';
      }

      else if($selected_template == 'estp-template-5')
      {
        $animate_style = 'estp-animated estp-shake';
      }

      else if($selected_template == 'estp-template-6')
      {
      }

      else if($selected_template == 'estp-template-7')
      {
      }

      else if($selected_template == 'estp-template-8')
      {
        if($atts['position'] == 'left_middle')
          $animate_style = 'estp-animated estp-zoomInLeft';

        else if($atts['position'] == 'right_middle')
          $animate_style = 'estp-animated estp-zoomInRight';

        else if($atts['position'] == 'bottom_left' || $atts['position'] == 'bottom_right')
          $animate_style = 'estp-animated estp-zoomInUp';
      }

      else if($selected_template == 'estp-template-9')
      {
        if( ($atts['position'] == 'left_middle') || ($atts['position'] == 'right_middle') )
          // $animate_style = 'estp-animated estp-flipInY';
          $animate_style = 'estp-animate-swing';

        else if($atts['position'] == 'bottom_left' || $atts['position'] == 'bottom_right')
          // $animate_style = 'estp-animated estp-flipInX';
          $animate_style = 'estp-btm-animate-swing';
      }

      else if($selected_template == 'estp-template-10')
      {
        if($atts['position'] == 'left_middle')
          $animate_style = 'estp-animated estp-bounceInLeft';

        else if($atts['position'] == 'right_middle')
          $animate_style = 'estp-animated estp-bounceInRight';

        else if($atts['position'] == 'bottom_left')
          $animate_style = 'estp-animated estp-bounceInUp';
      }

      else if(($selected_template == 'estp-template-11') || ($selected_template == 'estp-template-12') || ($selected_template == 'estp-template-13') || ($selected_template == 'estp-template-14') || ($selected_template == 'estp-template-15'))
      {
        $animate_style = 'estp-animated estp-fadeIn';
      }

    }
?>

    <?php
    if(isset($estp_layout_settings['enable_customize'])){
      if(($selected_template == 'estp-template-1') || ($selected_template == 'estp-template-10'))
      {
    ?>
    <style>
      .estp-tab-wrapper.<?php echo $position_class; ?> .estp-main-tab-wrap .estp-inner-tab-wrapper .estp-tab-element{
        background-color: <?php echo isset($background_color)?$background_color . ' !important':''; ?>;
      }
      .estp-tab-wrapper.<?php echo $position_class; ?> .estp-main-tab-wrap .estp-inner-tab-wrapper .estp-tab-element:hover{
        background-color: <?php echo isset($background_hover_color)?$background_hover_color . ' !important':''; ?>;
      }
      .estp-tab-wrapper.<?php echo $selected_template; ?>.<?php echo $position_class; ?> .estp-main-tab-wrap .estp-inner-tab-wrapper .estp-tab-element{
        color: <?php echo isset($text_color)?$text_color . ' !important':''; ?>;
      }
      .estp-tab-wrapper.<?php echo $selected_template; ?>.<?php echo $position_class; ?> .estp-main-tab-wrap .estp-inner-tab-wrapper .estp-tab-element:hover{
        color: <?php echo isset($text_hover_color)?$text_hover_color . ' !important':''; ?>;
      }
    </style>
    <?php
      }
      else{
    ?>
        <style>
          .estp-tab-wrapper.<?php echo $selected_template; ?>.<?php echo $position_class; ?> .estp-inner-tab-wrapper .estp-tab-element{
            background-color: <?php echo isset($background_color)?$background_color . ' !important':''; ?>;
            color: <?php echo isset($text_color)?$text_color . ' !important':''; ?>;
          }
          .estp-tab-wrapper.<?php echo $selected_template; ?>.<?php echo $position_class; ?> .estp-inner-tab-wrapper .estp-tab-element:hover{
            background-color: <?php echo isset($background_hover_color)?$background_hover_color . ' !important':''; ?>;
            color: <?php echo isset($text_hover_color)?$text_hover_color . ' !important':''; ?>;
          }
        </style>
    <?php
      }
    }
    ?>

    <?php
    if($position_class == 'estp-lft-side-tab')
    {
      if( isset($estp_general_settings['general_settings']['left_middle']['enable_offset']) && isset($estp_general_settings['general_settings']['left_middle']['position_from_top']) )
      {
        $tab_offset_pos = isset($estp_general_settings['general_settings']['left_middle']['position_from_top'])?$estp_general_settings['general_settings']['left_middle']['position_from_top'] : NULL;
    ?>
    <style>
      .estp-lft-side-tab.estp-tab-wrapper.<?php echo $position_class; ?>.<?php echo $selected_template; ?>
      {
        top: <?php echo $tab_offset_pos.'px'; ?>;
        transform: translateY(<?php echo $tab_offset_pos.'px'; ?>);
      }
    </style>
    <?php
      }

    }
    else if($position_class == 'estp-rht-side-tab')
    {
      if( isset($estp_general_settings['general_settings']['right_middle']['enable_offset']) && isset($estp_general_settings['general_settings']['right_middle']['position_from_top']) )
      {
        $tab_offset_pos = isset($estp_general_settings['general_settings']['right_middle']['position_from_top'])? $estp_general_settings['general_settings']['right_middle']['position_from_top']: NULL;
    ?>
    <style>
      .estp-rht-side-tab.estp-tab-wrapper.<?php echo $position_class; ?>.<?php echo $selected_template; ?>
      {
        top: <?php echo $tab_offset_pos.'px'; ?>;
        transform: translateY(<?php echo $tab_offset_pos.'px'; ?>);
      }
    </style>
    <?php

      }
    }
    else if($position_class == 'estp-btm-lft-side-tab')
    {
      if( isset($estp_general_settings['general_settings']['bottom_left']['enable_offset']) && isset($estp_general_settings['general_settings']['bottom_left']['position_from_left']) )
      {
        $tab_offset_pos = isset($estp_general_settings['general_settings']['bottom_left']['position_from_left'])? $estp_general_settings['general_settings']['bottom_left']['position_from_left'] : NULL;
    ?>
    <style>
      .estp-btm-lft-side-tab.estp-tab-wrapper.<?php echo $position_class; ?>.<?php echo $selected_template; ?>
      {
        left: <?php echo $tab_offset_pos.'px'; ?>;
      }
    </style>
    <?php

      }
    }
    else if($position_class == 'estp-btm-rht-side-tab')
    {
      if( isset($estp_general_settings['general_settings']['bottom_right']['enable_offset']) && isset($estp_general_settings['general_settings']['bottom_right']['position_from_right']) )
      {
        $tab_offset_pos = isset($estp_general_settings['general_settings']['bottom_right']['position_from_right'])? $estp_general_settings['general_settings']['bottom_right']['position_from_right'] : NULL;
    ?>
    <style>
      .estp-btm-rht-side-tab.estp-tab-wrapper.<?php echo $position_class; ?>.<?php echo $selected_template; ?>
      {
        right: <?php echo $tab_offset_pos.'px'; ?>;
      }
    </style>
    <?php

      }
    }

    if(isset($tab_template))
    {
      if($tab_template == 'template-1')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-1.php';

      else if($tab_template == 'template-2')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-2.php';

      else if($tab_template == 'template-3')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-3.php';

      else if($tab_template == 'template-4')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-4.php';

      else if($tab_template == 'template-5')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-5.php';

      else if($tab_template == 'template-6')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-6.php';

      else if($tab_template == 'template-7')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-7.php';

      else if($tab_template == 'template-8')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-8.php';

      else if($tab_template == 'template-9')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-9.php';

      else if($tab_template == 'template-10')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-10.php';

      else if($tab_template == 'template-11')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-11.php';

      else if($tab_template == 'template-12')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-12.php';

      else if($tab_template == 'template-13')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-13.php';

      else if($tab_template == 'template-14')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-14.php';

      else if($tab_template == 'template-15')
        include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/tab-templates/template-15.php';
    }

}// if condition for disable
} // if condition for display page
