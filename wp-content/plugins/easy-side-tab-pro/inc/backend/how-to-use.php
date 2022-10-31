<?php defined('ABSPATH') or die("No script kiddies please!"); ?>

<div id="wpbody" role="main">

    <div id="wpbody-content" aria-label="Main content" tabindex="0">
        <div class="wrap estp-wrap">
            <div class="estp-header-wrap">
                <h3><span class="estp-admin-title"><?php esc_html_e('How to use','easy-side-tab-pro');?></span></h3>
                <div class="logo">
                    <img src="<?php echo ESTP_IMAGE_DIR; ?>/logo.png" alt="<?php esc_attr_e('AccessPress Social Icons', 'easy-side-tab-pro'); ?>">
                </div>
            </div>
            <div class="estp-form-wrap">
                <div class="estp-content-wrap">
                    <div class="estp-content-section">
                        <h5 class="description"><?php esc_html_e('For detailed documentation, please visit ','easy-side-tab-pro');?><a href="https://accesspressthemes.com/documentation/easy-side-tab-pro/" target="_blank">here</a>.</h5>

                        <h4 class="estp-content-title"><?php esc_html_e('Tab Settings','easy-side-tab-pro');?></h4>
                        <p><?php esc_html_e('In this section you can change the settings of the tab such as:','easy-side-tab-pro');?></p>
                        <ul>
                            <li><strong><?php esc_html_e('Tab Title','easy-side-tab-pro');?></strong><?php esc_html_e(' -Here,  you should assign  the name for your tab.','easy-side-tab-pro');?></li>
                            <li><strong><?php esc_html_e('Tab Text','easy-side-tab-pro');?></strong><?php esc_html_e(' - In this section you should input the name which is to be displayed on the tab.','easy-side-tab-pro');?></li>
                            <li><strong><?php esc_html_e('Choose Icon Type','easy-side-tab-pro');?></strong><?php esc_html_e(' - Choose icon as Available Fonts (Font Awesome/Dashicons/Genericons) or Upload your own custom icon here.','easy-side-tab-pro');?></li>
                            <li><strong><?php esc_html_e('Tab Type','easy-side-tab-pro');?></strong><?php esc_html_e(' - This field determines the type of tab that you are going to display on your    website which are as:','easy-side-tab-pro');?>
                                <ul>
                                    <li><strong><?php esc_html_e('Internal','easy-side-tab-pro');?></strong><?php esc_html_e(' -This field comes with a option to redirect to the internal page of your website.','easy-side-tab-pro');?></li>
                                    <li><strong><?php esc_html_e('External ','easy-side-tab-pro');?></strong><?php esc_html_e(' -This field has a URL field where you should give the external link url (eg:https://www.google.com).','easy-side-tab-pro');?></li>
                                    <li><strong><?php esc_html_e('Content Slider','easy-side-tab-pro');?></strong><?php esc_html_e(' -Under this field there is a field where you write content so that content will be displayed on tab click.','easy-side-tab-pro');?></li>
                                </ul>
                            </li>
                            <li><strong><?php esc_html_e('Content Type','easy-side-tab-pro');?></strong><?php esc_html_e(' - This options includes altogether 6 advanced components to fill on tab content which are mentioned below:','easy-side-tab-pro');?>
                                <ul>
                                    <li><strong><?php esc_html_e('Html Content : ','easy-side-tab-pro');?></strong><?php esc_html_e('Fill any html content here.','easy-side-tab-pro');?></li>
                                    <li><strong><?php esc_html_e('Recent Blogs : ','easy-side-tab-pro');?></strong><?php esc_html_e('Display recent post or products as tab content.','easy-side-tab-pro');?></li>
                                    <li><strong><?php esc_html_e('Twitter Feed : ','easy-side-tab-pro');?></strong><?php esc_html_e('Settings to show your tweets as tab content.','easy-side-tab-pro');?></li>
                                    <li><strong><?php esc_html_e('Custom Shortcode : ','easy-side-tab-pro');?></strong><?php esc_html_e('Fill any external shortcode.','easy-side-tab-pro');?></li>
                                    <li><strong><?php esc_html_e('Woocommerce Product : ','easy-side-tab-pro');?></strong><?php esc_html_e('Option to display your woocommerce products by type and category.','easy-side-tab-pro');?></li>
                                    <li><strong><?php esc_html_e('Subscription Form : ','easy-side-tab-pro');?></strong><?php esc_html_e('Fill your subscription form details which will be displayed in the tab.','easy-side-tab-pro');?></li>
                                    <li><strong><?php esc_html_e('Social Icons : ','easy-side-tab-pro');?></strong><?php esc_html_e('Add Social media icons which are connected to your profiles.','easy-side-tab-pro');?></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="estp-content-section">
                        <h4 class="estp-content-title"><?php esc_html_e('Layout Settings','easy-side-tab-pro');?></h4>
                        <h5><?php esc_html_e('Tab Layout','easy-side-tab-pro');?></h5>
                        <p><?php esc_html_e('With this option you can choose the template layout designs as you desire.','easy-side-tab-pro');?></p>

                        <h5><?php esc_html_e('Display Position','easy-side-tab-pro');?></h5>
                        <p><?php esc_html_e('This section determines whether you want the tab position to be fixed when the page is scrolled or absolute(ie. move when the page is scrolled) on your website.','easy-side-tab-pro');?></p>

                        <h5><?php esc_html_e('Customize Setting','easy-side-tab-pro');?></h5>
                        <p><?php esc_html_e('With this option selected you can choose the desired colors for your tab such as background color, text color, background hover color, text hover color, slider content background and text color.','easy-side-tab-pro');?></p><br>
                    </div>
                    <div class="estp-content-section">
                        <h4 class="estp-content-title"><?php esc_html_e('Side Tab General Settings','easy-side-tab-pro');?></h4>
                        <p><?php esc_html_e('This is the main control of our plugin which is common for all of your tab settings. Here you get the settings option for 4 different tab positions ie left middle, right middle, bottom left & bottom right which are as :','easy-side-tab-pro');?></p>
                        <ul>
                            <li><strong><?php esc_html_e('Enable Side Tab','easy-side-tab-pro');?></strong><?php esc_html_e(' -Switching the Tab on or off on your website.','easy-side-tab-pro');?></li>
                            <li><strong><?php esc_html_e('Enable On Mobile Devices','easy-side-tab-pro');?></strong><?php esc_html_e(' -Enable or disable the plugin for mobile device.','easy-side-tab-pro');?></li>
                            <li><strong><?php esc_html_e('Enable Offset','easy-side-tab-pro');?></strong><?php esc_html_e(' -Give offset to your tab from top(for left middle and right middle tabs), bottom left(for bottom left tab) and from bottom right(for bottom right tab).','easy-side-tab-pro');?></li>
                            <li><strong><?php esc_html_e('Display Pages','easy-side-tab-pro');?></strong><?php esc_html_e(' -Option whether to display tab on homepage or all pages.','easy-side-tab-pro');?></li>
                            <li><strong><?php esc_html_e('Selected Tab','easy-side-tab-pro');?></strong><?php esc_html_e(' -Select tab that you want to display for that specific position.','easy-side-tab-pro');?></li>
                        </ul>
                    </div>

                    <div class="estp-content-section">
                        <h4 class="estp-content-title"><?php esc_html_e('Twitter Feed Settings','easy-side-tab-pro');?></h4>
                        <p><?php esc_html_e('This is the main setting page for your twitter account which is common for all of your tab settings. Here you are required to fill your twitter consumer key, consumer secret ,access token and access token secret for displaying twitter feeds on specific tab content as per setup. :','easy-side-tab-pro');?></p>
                        <h5><?php esc_html_e('Cache Setting','easy-side-tab-pro');?></h5>
                        <p><?php esc_html_e('The plugin has inbuilt caching method to prevent the frequent API calls due to which site won’t get slow. So in this tab you can set up the cache period on how often the latest twitter feeds should be fetched from API.','easy-side-tab-pro');?></p>
                        <ul>
                            <li><strong><?php esc_html_e('Cache Period','easy-side-tab-pro');?></strong><?php esc_html_e(' - Enter the time period in minutes in which the feeds should be fetched.Default is 60 Minutes.','easy-side-tab-pro');?></li>
                            <li><strong><?php esc_html_e('Disable Cache','easy-side-tab-pro');?></strong><?php esc_html_e(' - Check if you want to disable caching of tweets and want to fetch new tweets every time your site loads.','easy-side-tab-pro');?></li>
                        </ul>
                    </div>

                    <div class="estp-content-section">
                        <h4 class="estp-content-title"><?php esc_html_e('Subscribers','easy-side-tab-pro');?></h4>
                        
                        <p><?php esc_html_e('In this section you can view the lists of peoples who are subscribe to your channel with their email. There is also the option to export the subscribers information into the CSV format file.','easy-side-tab-pro');?></p>
                    </div>

                    <div class="estp-content-section">
                        <h4 class="estp-content-title"><?php esc_html_e('Import / Export','easy-side-tab-pro');?></h4>
                        
                        <p><?php esc_html_e('Here you can backup and secure your tab settings by selecting the tab you want to export and then click on export. Then you can import the tab settings whenever you want.','easy-side-tab-pro');?></p>
                    </div>
                    <div class="estp-content-section">
                        <h4 class="estp-content-title"><?php esc_html_e('Shortcode Settings','easy-side-tab-pro');?></h4>
                        <p><?php esc_html_e('In order to use shortcode to specific page, make sure you specify your desired position for side tab.','easy-side-tab-pro');?></p><p><?php esc_html_e('For example [estp tab_id="1" position="left_middle"].','easy-side-tab-pro');?></p>
                        <ul>
                            <li><?php esc_html_e('Left Middle:position="left_middle"','easy-side-tab-pro');?></li>
                            <li><?php esc_html_e('Right Middle:position="right_middle"','easy-side-tab-pro');?></li>
                            <li><?php esc_html_e('Bottom Left:position="bottom_left"','easy-side-tab-pro');?></li>
                            <li><?php esc_html_e('Bottom Right:position="bottom_right"','easy-side-tab-pro');?></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <div class="clear"></div>
    </div><!-- wpbody-content -->
    
    <div class="clear"></div>
</div>