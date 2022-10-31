<?php

namespace Shiaka;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Settings
{


    protected static $instance = null;

    public $plugin_page = 'shiaka_plugin_page';

    public static function instance(): ?Settings
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        return self::$instance = new self();
    }

    public function __construct()
    {
        add_action('admin_menu', [$this, 'shiaka__add_admin_menu']);
        add_action('admin_init', [$this, 'shiaka__settings_init']);
    }

    function shiaka__add_admin_menu()
    {

        add_menu_page('Shiaka Addon', 'Shiaka Addon', 'manage_options', 'shiaka_addon', [$this, 'shiaka__options_page']);

    }


    public function shiaka__settings_init()
    {

        register_setting('shiaka_plugin_page', 'shiaka__settings');

        $this->add_sections();

        $this->setting_fields_personal_information();
        $this->shiaka_social_media_fields();
        $this->api_section_fields();

    }

    protected function setting_fields_personal_information()
    {
        add_settings_field('sh_text_field_company_name',
            __('Company name', 'shiaka-addon'),
            [$this, 'sh_text_company_name'],
            $this->plugin_page ,'shiaka_personal_information');
        add_settings_field('sh_text_company_country', __('Country', 'shiaka-addon'), [$this, 'sh_text_company_country'], $this->plugin_page , 'shiaka_personal_information');

        add_settings_field('sh_text_company_city', __('City', 'shiaka-addon'), [$this, 'sh_text_company_city'], $this->plugin_page , 'shiaka_personal_information');



        add_settings_field('sh_order_terms_en', __('Order Term English', 'shiaka-addon'),
            [$this, 'sh_order_terms_en'], $this->plugin_page , 'shiaka_personal_information');
            
        add_settings_field('sh_order_terms_ar', __('Order Term Arabic', 'shiaka-addon'),
            [$this, 'sh_order_terms_ar'], $this->plugin_page , 'shiaka_personal_information');
            

            
        add_settings_field('sh_contactus_mobile_email', __('Receiver Email Contact Form on Mobile', 'shiaka-addon'),
            [$this, 'sh_contactus_mobile_email'], $this->plugin_page , 'shiaka_personal_information');
            
            
                        
        add_settings_field('sh_slider_id_apis', __('Mobile Slider ID', 'shiaka-addon'),
            [$this, 'sh_slider_id_apis'], $this->plugin_page , 'shiaka_mobile_app');
            
            
        add_settings_field('sh_banner_image_apis', __('Mobile App Banner Image', 'shiaka-addon'),
            [$this, 'sh_banner_image_apis'], $this->plugin_page , 'shiaka_mobile_app');
        
        
        add_settings_field('sh_banner_link_apis', __('Mobile App Banner Link', 'shiaka-addon'),
            [$this, 'sh_banner_link_apis'], $this->plugin_page , 'shiaka_mobile_app');
            
        
            
        // add_settings_field(
        //     'sh_banner_image_url',
        //     __('Mobile App Banner Link', 'shiaka'),
        //     [$this, 'sh_banner_image_link_apis_render'],
        //     'shiaka_plugin_page',
        //     'shiaka_personal_information'
        // );
        
            
            
        add_settings_field(
            'shiaka__text_field_0',
            __('Vat Nummber', 'shiaka'),
            [$this, 'shiaka__text_field_0_render'],
            'shiaka_plugin_page',
            'shiaka_personal_information'
        );
        
        

        add_settings_field('sh_text_company_customer_care_email', __('Customer care email', 'shiaka-addon'),
            [$this, 'sh_text_company_customer_care_email'], $this->plugin_page , 'shiaka_personal_information');

        
    }
    
    function shiaka_social_media_fields() {
        
        add_settings_field(
            'shiaka_facebook',
            __('Facebook', 'shiaka'),
            [$this, 'shiaka_facebook_render'],
            'shiaka_plugin_page',
            'shiaka_social_media'
        );
        
        add_settings_field(
            'shiaka_instagram',
            __('Instagram', 'shiaka'),
            [$this, 'shiaka_instagram_render'],
            'shiaka_plugin_page',
            'shiaka_social_media'
        );
        
        add_settings_field(
            'shiaka_twitter',
            __('Twitter', 'shiaka'),
            [$this, 'shiaka_twitter_render'],
            'shiaka_plugin_page',
            'shiaka_social_media'
        );

        
        add_settings_field(
            'shiaka_youtube',
            __('Youtube', 'shiaka'),
            [$this, 'shiaka_youtube_render'],
            'shiaka_plugin_page',
            'shiaka_social_media'
        );
    }

    protected function api_section_fields()
    {
        add_settings_field('sh_text_google_apish_text_google_api', __('Google Maps API Key', 'shiaka-addon'), [$this, 'sh_text_google_api'], 'shiaka_plugin_page',
            'shiaka_api_keys_section'
        );

    }

    protected function add_sections()
    {
        add_settings_section(
            'shiaka_personal_information',
            __('Shiaka Company personal information', 'shiaka'),
            [$this, 'shiaka__settings_section_callback'],
            'shiaka_plugin_page'
        );
        
        add_settings_section(
            'shiaka_mobile_app',
            __('Shiaka Mobile App Custom Settings', 'shiaka'),
            [$this, 'shiaka_mobile_app_callback'],
            'shiaka_plugin_page'
        );

        add_settings_section(
            'shiaka_social_media',
            __('Shiaka Social Media', 'shiaka'),
            [$this, 'shiaka__socialmedia_section_callback'],
            'shiaka_plugin_page'
        );
        
        add_settings_section('shiaka_api_keys_section',
            __('API Keys needed for Shiaka website to work', 'shiaka-addon'),
            [$this, 'shiaka_api_keys_setting_section_callback'],
            'shiaka_plugin_page');

    }

    public function shiaka_api_keys_setting_section_callback()
    {
        echo __('API Keys section', 'shiaka-addon');
    }


    public function shiaka_mobile_app_callback()
    {
        echo __('Mobile App Slider And Banner Section', 'shiaka-addon');
    }



    function sh_text_google_api()
    {
        $option = get_option('shiaka__settings');
        ?>
        <input type="text" name="shiaka__settings[sh_text_google_api]" value="<?= $option['sh_text_google_api'] ?>">
        <?php
    }

    
    public function sh_banner_image_link_apis_render()
    {

        $options = get_option('shiaka__settings');
        echo __('Open this link When the user click on the Banner image', 'shiaka-addon');
        echo $options['sh_banner_image_url'];
        ?>
        <br/>
        <input type='text' name='shiaka__settings[sh_banner_image_url]'
               value='<?php echo $options['sh_banner_image_url']; ?>'>
        <?php

    }


    public function shiaka__text_field_0_render()
    {

        $options = get_option('shiaka__settings');
        ?>
        <input type='text' name='shiaka__settings[shiaka__text_field_0]'
               value='<?php echo $options['shiaka__text_field_0']; ?>'>
        <?php

    }

    public function shiaka__socialmedia_section_callback()
    {

        echo __('Shiaka Social Media URLs', 'shiaka');

    }

    public function shiaka__settings_section_callback()
    {

        echo __('General setting for shiaka site such as API Keys and other stuff need to mange over a setting page', 'shiaka');

    }



    function sh_text_company_name()
    {
        $opt = get_option('shiaka__settings');
        ?>
        <input type="text" name="shiaka__settings[sh_text_field_company_name]"
               value="<?= $opt['sh_text_field_company_name'] ?>">
        <?php
    }

    function sh_text_company_country()
    {
        $opt = get_option('shiaka__settings');
        ?>
        <input type="text" name="shiaka__settings[sh_text_field_company_country]"
               value="<?= $opt['sh_text_field_company_country'] ?>">
        <?php
    }

    function sh_text_company_city()
    {
        $opt = get_option('shiaka__settings');

        ?>
        <input type="text" name="shiaka__settings[sh_text_field_company_city]"
               value="<?= $opt['sh_text_field_company_city'] ?>">
        <?php
    }



    function sh_order_terms_en()
    {
        $opt = get_option('shiaka__settings');
        ?>
        <textarea name="shiaka__settings[order_terms_en]"><?= $opt['order_terms_en'] ?></textarea>
        <?php
    }
    
    function sh_order_terms_ar()
    {
        $opt = get_option('shiaka__settings');
        ?>
        <textarea name="shiaka__settings[order_terms_ar]"><?= $opt['order_terms_ar'] ?></textarea>
        <?php
    }
    
    function sh_banner_link_apis() {
        $opt = get_option('shiaka__settings');
        echo __('Open this LINK when the user click on the banner image', 'shiaka');
        ?>
        <br/>
        <input type="text" name="shiaka__settings[sh_banner_link_apis]"
               value="<?= $opt['sh_banner_link_apis'] ?>">
        <?php
    }
    
    function sh_slider_id_apis()
    {
        $opt = get_option('shiaka__settings');
        echo __('Copy and Paste the smart slider ID to use it in the mobile application APIs ', 'shiaka');
        ?>
        <br/>
        <input type="number" name="shiaka__settings[sh_slider_id_apis]"
               value="<?= $opt['sh_slider_id_apis'] ?>">
        <?php
    }
    
    
    function sh_banner_image_apis() {
        $opt = get_option('shiaka__settings');
        wp_enqueue_media();
        
        // echo $opt['sh_banner_image_apis'];
        ?>
        <div class="banner-file">
            
            <input type="hidden" name="shiaka__settings[sh_banner_image_apis]" id="sh_banner_image_apis" value="<?= $opt['sh_banner_image_apis'] ?>" />

            <button class="button wpse-banner-upload">Upload</button>
            
            <div class="image-banner-container" style="width: 100px;height:100px;margin: 10px 0 20px 0;<?= empty($opt['sh_banner_image_apis']) ? 'display:none;' : ''; ?>">
                
                <button type="button" class="button wpse-banner-remove">Remove</button>
                <?php if(!empty($opt['sh_banner_image_apis'])): ?>
                    <img src="<?= $opt['sh_banner_image_apis']; ?>" style="max-width: 100%; max-height: 100%;" />
                <?php endif; ?>
            </div>
            

        </div>
  <script>
        jQuery(document).ready(function($){

            var custom_uploader
            //   , click_elem = jQuery('.wpse-banner-upload')
              , target = jQuery('#sh_banner_image_apis');

            jQuery('.wpse-banner-remove').on('click', function () {
                jQuery('#sh_banner_image_apis').val('');
                $('.image-banner-container').hide();
            });
            jQuery(document).on('click', '.wpse-banner-upload',function(e) {
                e.preventDefault();
                //If the uploader object has already been created, reopen the dialog
                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }
                //Extend the wp.media object
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });
                //When a file is selected, grab the URL and set it as the text field's value
                custom_uploader.on('select', function() {
                    attachment = custom_uploader.state().get('selection').first().toJSON();
                    // console.log(attachment);
                    var _in = jQuery('#sh_banner_image_apis'),
                    _p = $('.image-banner-container', _in.parent());
                    
                    
                    _in.val(attachment.url);
                    if( _in.val().length > 10 ) {
                        if( $('img', _p).length > 0 ) {
                            $('img', _p).attr('src', _in.val());
                        } else {
                            $('<img style="max-width: 100%;max-height: 100%;" src="'+_in.val()+'" />').appendTo(_p);
                        }
                        
                        $('.image-banner-container', _in.parent()).show();
                    }
                
                });
                //Open the uploader dialog
                custom_uploader.open();
            });      
        });
    </script>
        <!--<br/>-->
        <!--<input type="number" name="shiaka__settings[sh_banner_image_apis]"-->
        <!--       value="<?= $opt['sh_banner_image_apis'] ?>">-->
        <?php
    }
    

    function sh_contactus_mobile_email()
    {
        $opt = get_option('shiaka__settings');
        echo __('Mobile app contact us receiver email ', 'shiaka');
        ?>
        <br/>
        <input type="email" name="shiaka__settings[sh_contactus_mobile_email]"
               value="<?= $opt['sh_contactus_mobile_email'] ?>">
        <?php
    }
    


    function sh_text_company_customer_care_email()
    {
        $opt = get_option('shiaka__settings');
        ?>
        <input type="text" name="shiaka__settings[sh_text_field_company_customer_care_email]"
               value="<?= $opt['sh_text_field_company_customer_care_email'] ?>">
        <?php
    }

        // 
        // 
        // 
        // 
    public function shiaka_facebook_render()
    {

        $options = get_option('shiaka__settings');
        ?>
        <input type='text' name='shiaka__settings[shiaka_facebook]'
               value='<?php echo $options['shiaka_facebook']; ?>'>
        <?php

    }
    
    public function shiaka_instagram_render()
    {

        $options = get_option('shiaka__settings');
        ?>
        <input type='text' name='shiaka__settings[shiaka_instagram]'
               value='<?php echo $options['shiaka_instagram']; ?>'>
        <?php

    }
    
    public function shiaka_twitter_render()
    {

        $options = get_option('shiaka__settings');
        ?>
        <input type='text' name='shiaka__settings[shiaka_twitter]'
               value='<?php echo $options['shiaka_twitter']; ?>'>
        <?php

    }
    
    public function shiaka_youtube_render()
    {

        $options = get_option('shiaka__settings');
        ?>
        <input type='text' name='shiaka__settings[shiaka_youtube]'
               value='<?php echo $options['shiaka_youtube']; ?>'>
        <?php

    }
    
    public function shiaka__options_page()
    {

        ?>
        <form action='options.php' method='post'>

            <h2>Shiaka Addon</h2>

            <?php
            settings_fields('shiaka_plugin_page');
            do_settings_sections('shiaka_plugin_page');
            submit_button();
            ?>

        </form>
        <?php

    }


}

