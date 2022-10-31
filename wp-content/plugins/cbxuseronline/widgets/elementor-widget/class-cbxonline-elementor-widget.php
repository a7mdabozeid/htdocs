<?php

namespace CBXOnlineElementorWidget\Widgets;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * CBX Latest Tweets Elementor Widget
 */
class CBXOnlineElementorWidget extends \Elementor\Widget_Base
{

    /**
     * Retrieve Online User widget name.
     *
     * @return string Widget name.
     * @since  1.0.9
     * @access public
     *
     */
    public function get_name()
    {
        return 'cbx_online_user';
    }

    /**
     * Retrieve Online User widget title.
     *
     * @return string Widget title.
     * @since  1.0.9
     * @access public
     *
     */
    public function get_title()
    {
        return esc_html__('Online user', 'cbxuseronline');
    }

    /**
     * Get widget icon.
     *
     * Retrieve heading widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.9
     * @access public
     *
     */
    public function get_icon()
    {
        return 'cbxonlineuser-icon';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the widget categories.
     *
     * @return array Widget categories.
     * @since  1.0.9
     * @access public
     *
     */
    public function get_categories()
    {
        return array('cbxuseronline');
    }


    /**
     * Register Online User widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.9
     * @access protected
     */
    protected function _register_controls()
    {


        $this->start_controls_section(
            'section_cbxuseronline',
            array(
                'label' => esc_html__('CBX Online User ', 'cbxuseronline'),
            )
        );

        $this->add_control(
            'cbxuseronline_memberlist',
            array(
                'label'        => esc_html__('Show Memberlist', 'plugin-domain'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );
        $this->add_control(
            'cbxuseronline_linkusername',
            array(
                'label'        => esc_html__('Link user to author page', 'cbxuseronline'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );
        $this->add_control(
            'cbxuseronline_count',
            array(
                'label'        => esc_html__('Show online count', 'cbxuseronline'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );
        $this->add_control(
            'cbxuseronline_count_individual',
            array(
                'label'        => esc_html__('Show individual count', 'cbxuseronline'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );
        $this->add_control(
            'cbxuseronline_member_count',
            array(
                'label'        => esc_html__('Show member count', 'cbxuseronline'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );
        $this->add_control(
            'cbxuseronline_guest_count',
            array(
                'label'        => esc_html__('Show guest count', 'cbxuseronline'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );
        $this->add_control(
            'cbxuseronline_bot_count',
            array(
                'label'        => esc_html__('Show bot count', 'cbxuseronline'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );
        $this->add_control(
            'cbxuseronline_page',
            array(
                'label'        => esc_html__('Show for current page', 'cbxuseronline'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );
        $this->add_control(
            'cbxuseronline_mostuseronline',
            array(
                'label'        => esc_html__('Show most user online', 'cbxuseronline'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );
        $this->add_control(
            'cbxuseronline_mobile',
            array(
                'label'        => esc_html__('Show mobile or desktop logged in status', 'cbxuseronline'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'cbxuseronline'),
                'label_off'    => esc_html__('Hide', 'cbxuseronline'),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );


        $this->end_controls_section();
    }

    /**
     * @param  int  $value
     *
     * @return int
     */
    private function yes_no_to_10($value = 0)
    {
        if ($value === 'yes') {
            return 1;
        }

        return 0;
    }

    /**
     * Render oEmbed widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.9
     * @access protected
     */
    protected function render()
    {

        $settings = $this->get_settings();
        $atts     = array();


        //render online user from custom attributes
        $memberlist       = $atts['memberlist'] = isset($settings['cbxuseronline_memberlist']) ? $this->yes_no_to_10($settings['cbxuseronline_memberlist']) : 0;
        $linkusername     = $atts['linkusername'] = isset($settings['cbxuseronline_linkusername']) ? $this->yes_no_to_10($settings['cbxuseronline_linkusername']) : 0;
        $count            = $atts['count'] = isset($settings['cbxuseronline_count']) ? $this->yes_no_to_10($settings['cbxuseronline_count']) : 0;
        $count_individual = $atts['count_individual'] = isset($settings['cbxuseronline_count_individual']) ? $this->yes_no_to_10($settings['cbxuseronline_count_individual']) : 0;
        $member_count     = $atts['member_count'] = isset($settings['cbxuseronline_member_count']) ? $this->yes_no_to_10($settings['cbxuseronline_member_count']) : 0;
        $guest_count      = $atts['guest_count'] = isset($settings['cbxuseronline_guest_count']) ? $this->yes_no_to_10($settings['cbxuseronline_guest_count']) : 0;
        $bot_count        = $atts['bot_count'] = isset($settings['cbxuseronline_bot_count']) ? $this->yes_no_to_10($settings['cbxuseronline_bot_count']) : 0;
        $page             = $atts['page'] = isset($settings['cbxuseronline_page']) ? $this->yes_no_to_10($settings['cbxuseronline_page']) : 0;
        $mostuseronline   = $atts['mostuseronline'] = isset($settings['cbxuseronline_mostuseronline']) ? $this->yes_no_to_10($settings['cbxuseronline_mostuseronline']) : 0;
        $mobile           = $atts['mobile'] = isset($settings['cbxuseronline_mobile']) ? $this->yes_no_to_10($settings['cbxuseronline_mobile']) : 0;


        $atts = apply_filters('cbxuseronline_shortcode_builder_elementor_attr', $atts, $settings);

        $attr_html = '';

        foreach ($atts as $key => $value) {
            $attr_html .= ' '.$key.'="'.$value.'" ';
        }

        echo do_shortcode('[cbxuseronline '.$attr_html.']');


    }//end method render

    protected function _content_template()
    {

    }//end method _content_template


}//end class CBXOnlineElementorWidget