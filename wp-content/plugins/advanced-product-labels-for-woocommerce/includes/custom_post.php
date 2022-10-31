<?php
class BeRocket_conditions_advanced_labels extends BeRocket_conditions {
    public static function get_conditions() {
        $conditions = parent::get_conditions();

        $conditions['condition_single_product'] = array(
            'func' => 'check_condition_single_product',
            'type' => 'single_product',
            'name' => __('Single Product', 'BeRocket_products_label_domain')
        );
        return $conditions;
    }
    public static function condition_single_product($html, $name, $options) {
        $html .= static::supcondition($name, $options);
        $html .= '<p>' . __('Single Product page on product itself', 'BeRocket_products_label_domain') . '</p>';
        return $html;
    }
    public static function check_condition_single_product($show, $condition, $additional) {
        $show = ( ( ! empty($additional['var_product']) && is_a($additional['var_product'], 'WC_Product_Variation') ) 
               || ( ! empty($additional['product']) && is_a($additional['product'], 'WC_Product') && $additional['product']->get_id() == get_queried_object_id() )
            );
        if( $condition['equal'] == 'not_equal' ) {
            $show = ! $show;
        }
        return $show;
    }
}

class BeRocket_advanced_labels_custom_post extends BeRocket_custom_post_class {
    public $hook_name = 'berocket_advanced_label_editor';
    public $conditions, $templates, $templates_hide, $templates_rotate = array();
    public static $base_color = '#f16543';
    protected static $instance;
    public $post_type_parameters = array(
        'sortable' => true,
        'can_be_disabled' => true
    );
    public $default_settings = array(
        'bottom_padding' => 0,
        'left_padding'   => 0,
        'right_padding'  => 0,
        'top_padding'    => 0,

        'bottom_margin'  => -10,
        'left_margin'    => -10,
        'right_margin'   => -10,
        'top_margin'     => -10,

        'padding_horizontal' => -10,
        'padding_top'        => -10,

        'better_position'        => 1,
        'border_radius'          => 3,
        'color_use'              => 1,
        'content_type'           => 'text',
        'data'                   => array(),
        'discount_minus'         => '',
        'font_color'             => '#ffffff',
        'font_size'              => 14,
        'image'                  => '',
        'image_height'           => '35',
        'image_width'            => '60',
        'img_title'              => '',
        'label_from_post'        => '',
        'line_height'            => 1.2,
        'line_height_units'      => 'em',
        'position'               => 'right',
        'text'                   => 'SALE',
        'text_after'             => '',
        'text_after_nl'          => '',
        'text_before'            => '',
        'text_before_nl'         => '',
        'template'               => '',
        'tooltip_close_delay'    => '0',
        'tooltip_close_on_click' => '0',
        'tooltip_content'        => '',
        'tooltip_max_width'      => '300',
        'tooltip_open_delay'     => '0',
        'tooltip_open_on'        => 'click',
        'tooltip_position'       => 'top',
        'tooltip_theme'          => 'dark',
        'tooltip_use_arrow'      => '0',
        'type'                   => 'image',
        'zindex'                 => '500',

        'border_radius_units'      => 'px',
        'font_size_units'          => 'px',
        'image_height_units'       => 'px',
        'image_width_units'        => 'px',
        'margin_units'             => 'px',
        'padding_units'            => 'px',
        'padding_top_units'        => 'px',
        'padding_horizontal_units' => 'px',
        'div_custom_class'  => '',
        'div_custom_css'    => '',
        'span_custom_class' => '',
        'span_custom_css'   => 'color: white; display: flex; display: -webkit-box;
display: -ms-flexbox; position: relative; right: 0;text-align: center;',
        'b_custom_class'    => '',
        'b_custom_css'      => '',
        'i1_custom_class'   => '',
        'i1_custom_css'     => 'display: block;height: 0;position: absolute;width: 0;',
        'i2_custom_class'   => '',
        'i2_custom_css'     => 'background-color: transparent;display: block;line-height: 30px;position: absolute;z-index: 99;',
        'i3_custom_class'   => '',
        'i3_custom_css'     => 'display: block;height: 0;position: absolute;width: 0;',
        'i4_custom_class'   => '',
        'i4_custom_css'     => 'display: block;height: 0;position: absolute;width: 0;',
    );
    function __construct() {
        $this->templates_hide = array(
            'css' => array(
                1 => array('img_title'),
                2 => array( 
                    'img_title',
                    'image_height',
                    'image_width',
                 ),
                3 => array('img_title'),
                4 => array('img_title'),
                5 => array( 
                    'img_title',
                    'image_height',
                    'image_width',
                ),
                19 => array( 
                    'img_title', 
                    'image_height',
                    'image_width',
                ),
                20 => array(
                    'img_title', 
                ),
                23 => array(
                    'border_color',
                    'border_radius',
                    'border_width',
                    'image_height',
                    'image_width',
                    'img_title', 
                ),
            ),
            'image' => array(
                1000 => array(
                    'border_width',
                    'border_color',
                    'border_radius',
                    'line_height',
                    'font_family',
                    'font_size',
                    'top_padding',
                    'color',
                    'color_use',
                    'font_color',
                    'content_type',
                    'text',
                    'image',
                    'text_before',
                    'text_after',
                    'discount_minus',
                    'attribute',
                    'attribute_values_all',
                    'first_attribute',
                    'attribute_type',
                ),
            ),
        );
        $this->templates = array(
            'css' => array(
                1 => array(
                    'border_radius' => '3',
                    'image_height'  => '35',
                    'image_width'   => '60',
                ),
                2 => array(
                    'border_radius' => '0',
                    'image_height'  => '50',
                    'image_width'   => '50',
                ),
                3 => array(
                    'border_radius' => '0',
                    'image_height'  => '35',
                    'image_width'   => '60',
                ),
                4 => array(
                    'border_radius'       => '50',
                    'border_radius_units' => '%',
                    'image_height'        => '35',
                    'image_width'         => '60',
                ),
                5 => array(
                    'border_radius'       => '50',
                    'border_radius_units' => '%',
                    'image_height'        => '50',
                    'image_width'         => '50',
                ),
                19 => array(
                    'border_color'        => self::$base_color,
                    'border_radius'       => '50',
                    'border_radius_units' => '%',
                    'border_width'        => '1',
                    'image_height'        => '58',
                    'image_width'         => '58',
                    'left_margin'         => '-15',
                    'top_margin'          => '-15',
                    'right_margin'        => '-15',
                    'padding_horizontal'  => '-15',
                    'padding_top'         => '-15',
                    'span_custom_css' => array(
                        'background' => 'transparent !important',
                    ),
                    'i4_custom_css' => array( //span i.template-i-before
                        'border-radius' => '50%',
                        'height' => '50px',
                        'width' => '50px',
                        'left' => '50%',
                        'top' => '50%',
                        'transform' => 'translateX(-50%) translateY(-50%)',
                        '-moz-transform' => 'translateX(-50%) translateY(-50%)',
                        '-ms-transform' => 'translateX(-50%) translateY(-50%)',
                        '-webkit-transform' => 'translateX(-50%) translateY(-50%)',
                        '-o-transform' => 'translateX(-50%) translateY(-50%)',
                    ),
                ),
                20 => array(
                    'border_color'        => '#FFFFFF',
                    'border_radius'       => '50',
                    'border_radius_units' => '%',
                    'border_width'        => '4',
                    'image_height'        => '70',
                    'image_width'         => '70',
                    'left_margin'         => '-15',
                    'right_margin'        => '-15',
                    'top_margin'          => '-15',
                    'padding_horizontal'  => '-15',
                    'padding_top'         => '-15',
                ),
                23 => array(
                    'image_height'       => '40',
                    'image_width'        => '120',
                    'left_margin'        => '10',
                    'right_margin'       => '10',
                    'top_margin'         => '10',
                    'padding_horizontal' => '10',
                    'padding_top'        => '10',
                    'text'               => 'FLASH SALE',
                    'span_custom_css' => array(
                        'background' => 'transparent !important',
                    ),
                    'i4_custom_css' => array( //span i.template-i-before
                        'clip-path' => 'polygon(100% 0%, 100% 100%, 15% 100%, 0% 50%, 15% 0%)',
                        '-webkit-clip-path' => 'polygon(100% 0%, 100% 100%, 15% 100%, 0% 50%, 15% 0%)',
                        'height'            => '100%',
                        'width'             => '100%',
                    ),
                    'b_custom_css' => array(
                        'padding-left'  => '10%',
                    ),
                ),
            ),
            "image" => array(
                1000 => array(
                    'border_radius'      => '0',
                    'image_height'       => '80',
                    'image_width'        => '80',
                    'line_height'        => '0',
                    'right_margin'       => '0',
                    'top_margin'         => '0',
                    'padding_horizontal' => '0',
                    'padding_top'        => '0',
                    'div_custom_class' => 'berocket-label-user-image',
                    'span_custom_css' => array(
                        'background-color' => 'transparent!important',
                    ),
                    'b_custom_css' => array(
                        'display' => 'none'
                    ),
                    'i1_custom_css' => array(
                        'display' => 'none'
                    ),
                    'i2_custom_css' => array(
                        'display' => 'none'
                    ),
                    'i3_custom_css' => array(
                        'display' => 'none'
                    ),
                    'i4_custom_css' => array(
                        'display' => 'none'
                    ),
                ),
            ),
        );

        $this->templates_rotate = array(
            // 'css-14' => array(
            //     'elements' => array(
            //         'span_custom_css' => array(
            //             'transform: scaleX(-1);',
            //             '-moz-transform: scaleX(-1);',
            //             '-ms-transform: scaleX(-1);',
            //             '-o-transform: scaleX(-1);',
            //             '-webkit-transform: scaleX(-1);',
            //         ),
            //         'b_custom_css' => array(
            //             'left: 10%;',
            //             'right: 0;',
            //             'top: 43%;',
            //             'width: auto;',
            //             'transform: translateY(-50%) scaleX(-1);',
            //             '-moz-transform: translateY(-50%) scaleX(-1);',
            //             '-ms-transform: translateY(-50%) scaleX(-1);',
            //             '-o-transform: translateY(-50%) scaleX(-1);',
            //             '-webkit-transform: translateY(-50%) scaleX(-1);',
            //         ),
            //     ),
            // ),
            'css-23' => array(
                'elements' => array(
                    'span_custom_css' => array(
                        'transform: scaleX(-1);',
                        '-moz-transform: scaleX(-1);',
                        '-ms-transform: scaleX(-1);',
                        '-o-transform: scaleX(-1);',
                        '-webkit-transform: scaleX(-1);',
                    ),
                    'b_custom_css' => array(
                        'padding-left: 0;',
                        'padding-right: 10%;',
                        'transform: scaleX(-1);',
                        '-moz-transform: scaleX(-1);',
                        '-ms-transform: scaleX(-1);',
                        '-o-transform: scaleX(-1);',
                        '-webkit-transform: scaleX(-1);',
                    ),
                ),
            ),
        );
        $this->templates_rotate = apply_filters( "berocket_labels_templates_rotate", $this->templates_rotate );

        add_action('products_label_framework_construct', array($this, 'init_conditions'));
        $this->post_name = 'br_labels';
        $this->post_settings = array(
            'label' => __( 'Advanced Label', 'BeRocket_products_label_domain' ),
            'labels' => array(
                'name'               => __( 'Advanced Labels', 'BeRocket_products_label_domain' ),
                'singular_name'      => __( 'Advanced Label', 'BeRocket_products_label_domain' ),
                'menu_name'          => _x( 'Advanced Labels', 'Admin menu name', 'BeRocket_products_label_domain' ),
                'add_new'            => __( 'Add Label', 'BeRocket_products_label_domain' ),
                'add_new_item'       => __( 'Add New Label', 'BeRocket_products_label_domain' ),
                'edit'               => __( 'Edit', 'BeRocket_products_label_domain' ),
                'edit_item'          => __( 'Edit Label', 'BeRocket_products_label_domain' ),
                'new_item'           => __( 'New Label', 'BeRocket_products_label_domain' ),
                'view'               => __( 'View Labels', 'BeRocket_products_label_domain' ),
                'view_item'          => __( 'View Label', 'BeRocket_products_label_domain' ),
                'search_items'       => __( 'Search Advanced Labels', 'BeRocket_products_label_domain' ),
                'not_found'          => __( 'No Advanced Labels found', 'BeRocket_products_label_domain' ),
                'not_found_in_trash' => __( 'No Advanced Labels found in trash', 'BeRocket_products_label_domain' ),
            ),
            'description'     => __( 'This is where you can add advanced labels.', 'BeRocket_products_label_domain' ),
            'public'          => true,
            'show_ui'         => true,
            'map_meta_cap'    => true,
            'capability_type' => 'product',
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'show_in_menu'        => 'berocket_account',
            'hierarchical'        => false,
            'rewrite'             => false,
            'query_var'           => false,
            'supports'            => array( 'title' ),
            'show_in_nav_menus'   => false,
            'show_in_nav_menus'   => false,
        );

        $this->add_meta_box('conditions', __( 'Conditions', 'BeRocket_products_label_domain' ));
        $this->add_meta_box('settings', __( 'Advanced Labels Settings', 'BeRocket_products_label_domain' ));
        $this->add_meta_box('description', __( 'Description', 'BeRocket_products_label_domain' ), false, 'side');
        $this->add_meta_box('preview', __( 'Preview', 'BeRocket_products_label_domain' ), false, 'side');
        
        add_filter('brfr_berocket_advanced_label_editor_custom_css_explanation', array(__CLASS__, 'section_custom_css_explanation'), 10, 4);
        add_filter('brfr_berocket_advanced_label_editor_templates', array($this, 'section_templates'), 10, 4);
        add_filter( 'berocket_label_adjust_options', array( $this, 'adjust_options' ), 1, 2 );
        add_filter( 'berocket_label_custom_get_options', array( $this, 'get_custom_options' ), 10, 2 );

        parent::__construct();
    }

    public function admin_init() {
        parent::admin_init();
        add_action( 'pre_get_posts', array($this, 'sortable_labels'), 999999 );
    }

    public function sortable_labels( $query ) {
        global $pagenow;
        $post_type = $query->get('post_type');
        if( 'edit.php' == $pagenow && $post_type == $this->post_name && ! empty($_GET['orderby']) && in_array($_GET['orderby'], array('date')) ){
            if( $_GET['orderby'] == 'date' ) {
                $query->set( 'orderby', 'date' );
            }
            $query->set( 'order', ( (empty($_GET['order']) || strtoupper($_GET['order']) == 'ASC') ? 'ASC' : 'DESC' ) );
        }
    }

    public function init() {
        $BeRocket_products_label = BeRocket_products_label::getInstance();
        $base_options = $BeRocket_products_label->get_option();
        $this->default_settings['color'] = self::$base_color;
        $this->default_settings['font_family'] = $base_options['font_family'];
        parent::init();
    }

    public function add_class( $templates, $element, $prefix ) {
        foreach ( $templates as $template_type => $template_group ) {
            foreach ( $template_group as $key => $template ) {
                $templates[$template_type][$key][$element] = "$prefix-$template_type $prefix-$template_type-$key";
            }
        }
        return $templates;
    }

    public function adjust_options( $br_label, $label_id = false ) {

        if ( $br_label['position'] != 'left' || empty( $br_label['template'] ) ) {
            return $br_label;
        }

        if ( empty( $this->templates_rotate[ $br_label['template'] ] ) ) {
            return $br_label;
        }
        $rotate_styles = $this->templates_rotate[ $br_label['template'] ];

        if ( !empty( $rotate_styles['styles'] ) ) {
            foreach( $rotate_styles['styles'] as $style => $value ) {
                $br_label[$style] = $value;
            }
        }

        if ( !empty( $rotate_styles['elements'] ) ) {
            foreach( $rotate_styles['elements'] as $element => $styles ) {
                $br_label[$element] .= implode( ' ', $styles );
            }            
        }
        $br_label['b_custom_class'] .= ' rotated'; 
        return $br_label;
    }

    public function init_conditions() {
        $this->conditions = new BeRocket_conditions_advanced_labels($this->post_name.'[data]', $this->hook_name, array(
            'condition_product',
            'condition_product_category',
            'condition_product_sale',
            'condition_product_bestsellers',
            'condition_product_price',
            'condition_product_stockstatus',
            'condition_product_totalsales',
            'condition_product_featured',
            'condition_product_age',
            'condition_product_type',
            'condition_product_rating',
        ));
    }
    public function conditions($post) {
        $options = $this->get_option( $post->ID );
        if( empty($options['data']) ) {
            $options['data'] = array();
        }
        echo $this->conditions->build($options['data']);
    }
    public function description($post) {
        ?>
        <p><?php _e('Label without any condition will be displayed on all products', 'BeRocket_products_label_domain'); ?></p>
        <p><?php _e('Connection between condition can be AND and OR', 'BeRocket_products_label_domain'); ?></p>
        <p>
            <strong><?php _e('AND', 'BeRocket_products_label_domain'); ?></strong> 
            <?php _e('uses between condition in one section', 'BeRocket_products_label_domain'); ?>
        </p>
        <p>
            <strong><?php _e('OR', 'BeRocket_products_label_domain'); ?></strong> 
            <?php _e('uses between different sections with conditions', 'BeRocket_products_label_domain'); ?>
        </p>
        <?php
    }
    public function preview($post) {
        wp_enqueue_style( 'berocket_framework_tippy' );
        wp_enqueue_script( 'berocket_framework_tippy' );
        wp_enqueue_style( 'berocket_tippy' );
        wp_enqueue_script( 'berocket_tippy');
        ?>
        <div class="berocket_label_preview_wrap">
            <div class="berocket_label_preview berocket_product_image_wrap">
                <img class="berocket_product_image" src="<?php echo plugin_dir_url(__FILE__).'../images/labels.png'; ?>">
            </div>
            <div class="berocket_product_label_wrap"></div>
            <h2 class="berocket_product_in_title_wrap">
                <span><?php _e( 'Product title', 'BeRocket_products_label_domain' ) ?></span>
            </h2>
        </div>
        <style>
            div.berocket_label_preview_wrap {
                display: inline-block;
                width: 240px;
                padding: 20px;
                background: white;
                position: relative;
                top: 0;
                margin-top: 0;
                min-height: 320px;
                right: 0;
                box-sizing: border-box;
            }
            .berocket_label_preview_wrap .berocket_label_preview {
                position: relative;
            }
            .berocket_label_preview_wrap .berocket_product_image {
                display: block;
                width: 200px;
            }
            #poststuff .berocket_label_preview_wrap h2.berocket_product_in_title_wrap {
                padding-left: 0;
                padding-right: 0;
                padding-top: 0;
                margin-top: 10px;
                line-height: 1.2;
            }
            .postbox#preview {
                overflow:hidden;
            }
        </style>
        <?php
    }

    public function settings($post) {
        include_once('libraries/googlefonts.php');
        $BeRocket_products_label = BeRocket_products_label::getInstance();
        $BeRocket_products_label->load_admin_edit_scripts();
        $options = $this->get_option( $post->ID );


        echo '<div class="br_framework_settings br_alabel_settings">';

        $BeRocket_products_label->display_admin_settings(
            array(
                'General' => array(
                    'icon' => 'cog',
                    'name' => __( 'General', 'BeRocket_products_label_domain' ),
                ),
                'Style'     => array(
                    'icon' => 'css3',
                    'name' => __( 'Style', 'BeRocket_products_label_domain' ),
                ),
                'Position'     => array(
                    'icon' => 'arrows',
                    'name' => __( 'Position', 'BeRocket_products_label_domain' ),
                ),
                'Tooltip'     => array(
                    'icon' => 'comment',
                    'name' => __( 'Tooltip', 'BeRocket_products_label_domain' ),
                ),
                'Custom CSS'   => array(
                    'icon' => 'css3',
                    'name' => __( 'Custom CSS', 'BeRocket_products_label_domain' ),
                ),
            ),
            array(
                'General' => array(
                    'templates' => array(
                        'section' => 'templates',
                        "label"    => __('Templates', 'BeRocket_products_label_domain'),
                        "name"     => "template",
                        "value"    => '',
                    ),
                    'content_type' => array(
                        "type"     => "selectbox",
                        "options"  => array(
                            array('value' => 'text', 'text' => __('Text', 'BeRocket_products_label_domain')),
                            array('value' => 'sale_p', 'text' => __('Discount percentage', 'BeRocket_products_label_domain')),
                            array('value' => 'price', 'text' => __('Price', 'BeRocket_products_label_domain')),
                            array('value' => 'stock_status', 'text' => __('Stock Status', 'BeRocket_products_label_domain')),
                        ),
                        "class"    => 'berocket_label_content_type',
                        "label"    => __('Content type', 'BeRocket_products_label_domain'),
                        "name"     => "content_type",
                        "value"    => $options['content_type'],
                    ),
                    'text' => array(
                        "type"     => "text",
                        "label"    => __('Text', 'BeRocket_products_label_domain'),
                        "class"    => 'berocket_label_ berocket_label_text',
                        "name"     => "text",
                        "value"    => $options['text'],
                    ),
                    'text_before' => array(
                        "label"    => __('Text Before', 'BeRocket_products_label_domain'),
                        "items"    => array(
                            'text_before' => array(
                                "type"     => "text",
                                "class"    => 'berocket_label_ berocket_label_sale_p berocket_label_price berocket_label_stock_status',
                                "label_be_for" => __('Text', 'BeRocket_products_label_domain'),
                                "name"     => "text_before",
                                "value"    => $options['text_before'],
                            ),
                            "text_before_nl" =>array(
                                "type"     => "checkbox",
                                "label_for" => __('New Line', 'BeRocket_products_label_domain'),
                                "name"     => "text_before_nl",
                                "value"    => "1",
                                "selected" => false
                            ),
                        )
                    ),
                    'text_after' => array(
                        "label"    => __('Text After', 'BeRocket_products_label_domain'),
                        "items"    => array(
                            'text_after' => array(
                                "type"     => "text",
                                "class"    => 'berocket_label_ berocket_label_sale_p berocket_label_price berocket_label_stock_status',
                                "label_be_for" => __('Text', 'BeRocket_products_label_domain'),
                                "name"     => "text_after",
                                "value"    => $options['text_after'],
                            ),
                            "text_before_nl" =>array(
                                "type"     => "checkbox",
                                "label_for" => __('New Line', 'BeRocket_products_label_domain'),
                                "name"     => "text_after_nl",
                                "value"    => "1",
                                "selected" => false
                            ),
                        )
                    ),
                    'discount_minus' => array(
                        "type"     => "checkbox",
                        "label"    => __('Use minus symbol', 'BeRocket_products_label_domain'),
                        "class"    => 'berocket_label_ berocket_label_sale_p',
                        "name"     => "discount_minus",
                        "value"    => "1",
                        "selected" => false
                    ),
                ),
                'Style' => array(
                    'color_use' => array(
                        "type"     => "checkbox",
                        "label"    => __('Use background color', 'BeRocket_products_label_domain'),
                        "class"    => 'br_label_backcolor_use br_js_change',
                        "name"     => "color_use",
                        "value"    => "1",
                        "extra"    => ' data-for=".br_alabel > span" data-forsvg="1" data-style="use:background-color" data-ext=""',
                        "selected" => false
                    ),
                    'color' => array(
                        "type"     => "color",
                        "label"    => __('Background color', 'BeRocket_products_label_domain'),
                        "name"     => "color",
                        "class"    => 'br_label_backcolor br_js_change',
                        "extra"    => " data-for='.br_alabel > span' data-forsvg='1' data-style='background-color' data-ext=''",
                        "value"    => $options['color'],
                    ),
                    'font_color' => array(
                        "type"     => "color",
                        "label"    => __('Font color', 'BeRocket_products_label_domain'),
                        "name"     => "font_color",
                        "class"    => 'berocket_label_ berocket_label_text berocket_label_sale_end berocket_label_sale_p br_js_change',
                        "extra"    => " data-for='.br_alabel > span' data-style='color' data-ext=''",
                        "value"    => $options['font_color'],
                    ),
                    'font' => berocket_labels_googlefonts::select_fonts(),
                    'font_size' => array(
                        "label"    => __('Font size', 'BeRocket_products_label_domain'),
                        "items" => array(
                            array(
                                "type"     => "number",
                                "name"     => "font_size",
                                "extra"    => ' min="0" max="500" data-for=".br_alabel>span" data-style="font-size" data-ext="px" data-default="' . $options['font_size'] . '"',
                                "class"    => 'br_js_change',
                                "value"    => $options['font_size'],
                            ),
                            brapl_select_units( 'font_size', $options['font_size_units'] ),
                        ),
                    ),
                    'line_height' => array(
                        "label"    => __('Line height', 'BeRocket_products_label_domain'),
                        "items" => array(
                            array(
                                "type"     => "number",
                                "name"     => "line_height",
                                "class"    => "br_js_change",
                                "extra"    => " min='0' max='400' step='0.1' data-for='.br_alabel > span' data-style='line-height' data-ext='{$options['line_height_units']}' data-default='{$options['line_height']}'",
                                "value"    => $options['line_height'],
                            ),
                            brapl_select_units( 'line_height', $options['line_height_units'] ),
                        ),
                    ),
                    'image_height' => array(
                        "label"    => __('Height', 'BeRocket_products_label_domain'),
                        "items" => array(
                            array(
                                "type"     => "number",
                                "name"     => "image_height",
                                "class"    => "br_js_change",
                                "extra"    => ' data-for=".br_alabel > span" data-style="height" data-ext="px" data-default="' . $options['image_height'] . '"',
                                "value"    => $options['image_height'],
                            ),
                            brapl_select_units( 'image_height', $options['image_height_units'] ),
                        ),
                    ),
                    'image_width' => array(
                        "label"    => __('Width', 'BeRocket_products_label_domain'),
                        "items" => array(
                            array(
                                "type"     => "number",
                                "name"     => "image_width",
                                "class"    => "br_js_change",
                                "extra"    => ' data-for=".br_alabel > span" data-style="width" data-ext="px" data-default="' . $options['image_width'] . '"',
                                "value"    => $options['image_width'],
                            ),
                            brapl_select_units( 'image_width', $options['image_width_units'] ),
                        ),
                    ),
                    'border_radius' => array(
                        "label"    => __('Border radius', 'BeRocket_products_label_domain'),
                        "items" => array(
                            array(
                                "type"     => "number",
                                "name"     => "border_radius",
                                "class"    => "br_js_change",
                                "extra"    => " data-for='.br_alabel > span' data-style='border-radius' data-ext='{$options['border_radius_units']}'",
                                "value"    => '10',
                            ),
                            brapl_select_units( 'border_radius', $options['border_radius_units'] ),
                        ),
                    ),
                ),
                'Position' => array(
                    'type' => array(
                        "type"     => "selectbox",
                        "options"  => array(
                            array('value' => 'label', 'text' => __('Label', 'BeRocket_products_label_domain')),
                            array('value' => 'image', 'text' => __('On image', 'BeRocket_products_label_domain')),
                        ),
                        "class"    => 'berocket_label_type_select',
                        "label"    => __('Type', 'BeRocket_products_label_domain'),
                        "name"     => "type",
                        "value"    => $options['type'],
                    ),
                    'padding_top' => array(
                        "label"    => __('Padding from top', 'BeRocket_products_label_domain'),
                        "items" => array(
                            'value' => array(
                                "type"  => "number",
                                "class" => 'berocket_label_type_ berocket_label_type_image br_js_change',
                                "name"  => "padding_top",
                                "extra" => " data-for='.br_alabel' data-style='top' data-ext='{$options['padding_top_units']}' data-default='{$options['padding_horizontal']}'",
                                "value" => $options['padding_top'],
                            ),
                            brapl_select_units( 'padding_top', $options['padding_top_units'] ),
                        ),
                    ),
                    'padding_horizontal' => array(
                        "label"    => '<span class="pos__ pos__left">' . __('Padding from left', 'BeRocket_products_label_domain') . '</span><span class="pos__ pos__right">' . __('Padding from right', 'BeRocket_products_label_domain') . '</span>',
                        "items" => array(
                            'value' => array(
                                "type"  => "number",
                                "class" => 'berocket_label_type_ berocket_label_type_image pos_label_ pos_label_right pos_label_left br_js_change',
                                "name"  => "padding_horizontal",
                                "extra" => " data-for='.br_alabel' data-from='.pos_label' data-ext='{$options['padding_horizontal_units']}' data-default='{$options['padding_horizontal']}'",
                                "value" => $options['padding_horizontal'],
                            ),
                            brapl_select_units( 'padding_horizontal', $options['padding_horizontal_units'] ),
                        ),
                    ),
                    'position' => array(
                        "type"     => "selectbox",
                        "options"  => array(
                            array('value' => 'left', 'text' => __('Left', 'BeRocket_products_label_domain')),
                            array('value' => 'center', 'text' => __('Center', 'BeRocket_products_label_domain')),
                            array('value' => 'right', 'text' => __('Right', 'BeRocket_products_label_domain')),
                        ),
                        "class"    => 'pos_label',
                        "label"    => __('Position', 'BeRocket_products_label_domain'),
                        "name"     => "position",
                        "value"    => $options['position'],
                    ),
                    'zindex' => array(
                        "type"     => "number",
                        "label"    => __('Z index', 'BeRocket_products_label_domain'),
                        "name"     => "zindex",
                        "extra"    => ' min="0" data-for=".br_alabel" data-style="z-index" data-ext=""',
                        "class"    => 'br_js_change',
                        "value"    => $options['zindex'],
                    ),
                ),
                'Tooltip'   => array(
                    'tooltip_content' => array(
                        'label'    => __('Content', 'BeRocket_products_label_domain'),
                        "type"     => "textarea",
                        "class"    => "berocket_html_tooltip_content",
                        "name"     => "tooltip_content",
                        "value"    => $options['tooltip_content'],
                    ),
                    'tooltip_theme' => array(
                        "type"     => "selectbox",
                        "options"  => array(
                            array('value' => 'dark', 'text' => __('Dark', 'BeRocket_products_label_domain')),
                            array('value' => 'light', 'text' => __('Light', 'BeRocket_products_label_domain')),
                            array('value' => 'translucent', 'text' => __('Translucent', 'BeRocket_products_label_domain')),
                        ),
                        "label"    => __('Style', 'BeRocket_products_label_domain'),
                        "name"     => "tooltip_theme",
                        "value"    => $options['tooltip_theme'],
                    ),
                    'tooltip_position' => array(
                        "type"     => "selectbox",
                        "options"  => array(
                            array('value' => 'top', 'text' => __('Top', 'BeRocket_products_label_domain')),
                            array('value' => 'bottom', 'text' => __('Bottom', 'BeRocket_products_label_domain')),
                            array('value' => 'left', 'text' => __('Left', 'BeRocket_products_label_domain')),
                            array('value' => 'right', 'text' => __('Right', 'BeRocket_products_label_domain')),
                        ),
                        "label"    => __('Position', 'BeRocket_products_label_domain'),
                        "name"     => "tooltip_position",
                        "value"    => $options['tooltip_position'],
                    ),
                    'tooltip_open_delay' => array(
                        "type"     => "number",
                        "label"    => __('Open delay', 'BeRocket_products_label_domain'),
                        "name"     => "tooltip_open_delay",
                        "extra"    => 'min="0"',
                        "value"    => $options['tooltip_open_delay'],
                        "label_for" => __('milliseconds', 'BeRocket_products_label_domain'),
                    ),
                    'tooltip_close_delay' => array(
                        "type"     => "number",
                        "label"    => __('Close delay', 'BeRocket_products_label_domain'),
                        "name"     => "tooltip_close_delay",
                        "extra"    => 'min="0"',
                        "value"    => $options['tooltip_close_delay'],
                        "label_for" => __('milliseconds', 'BeRocket_products_label_domain'),
                    ),
                    'tooltip_open_on' => array(
                        "type"     => "selectbox",
                        "options"  => array(
                            array('value' => 'mouseenter', 'text' => __('Hover', 'BeRocket_products_label_domain')),
                            array('value' => 'click', 'text' => __('Click', 'BeRocket_products_label_domain')),
                        ),
                        "label"     => __('Open on', 'BeRocket_products_label_domain'),
                        "label_for" => __('Warning: If the label has a link, tooltip will always open on hover', 'BeRocket_products_label_domain'),
                        "name"      => "tooltip_open_on",
                        "value"     => $options['tooltip_open_on'],
                    ),
                    'tooltip_close_on_click' => array(
                        "type"     => "checkbox",
                        "label"    => __('Close on click everywhere', 'BeRocket_products_label_domain'),
                        "name"     => "tooltip_close_on_click",
                        "value"    => '1',
                    ),
                    'tooltip_use_arrow' => array(
                        "type"     => "checkbox",
                        "label"    => __('Use arrow', 'BeRocket_products_label_domain'),
                        "name"     => "tooltip_use_arrow",
                        "value"    => '1',
                    ),
                    'tooltip_max_width' => array(
                        "type"     => "number",
                        "label"    => __('Max width', 'BeRocket_products_label_domain'),
                        "name"     => "tooltip_max_width",
                        "extra"    => 'min="0"',
                        "value"    => $options['tooltip_max_width'],
                        "label_for"=> "px"
                    ),
                ),
                'Custom CSS' => array(
                    'custom_css_explanation' => array(
                        "section" => "custom_css_explanation",
                    ),
                    'div_custom_class' => array(
                        "type"     => "text",
                        "label"    => __('&lt;div&gt; block custom class', 'BeRocket_products_label_domain'),
                        "name"     => "div_custom_class",
                        "value"    => $options['div_custom_class'],
                    ),
                    'div_custom_css' => array(
                        "type"     => "textarea",
                        "label"    => __('&lt;div&gt; block custom CSS', 'BeRocket_products_label_domain'),
                        "name"     => "div_custom_css",
                        "value"    => $options['div_custom_css'],
                    ),
                    'span_custom_class' => array(
                        "type"     => "text",
                        "label"    => __('&lt;span&gt; block custom class', 'BeRocket_products_label_domain'),
                        "name"     => "span_custom_class",
                        "value"    => $options['span_custom_class'],
                    ),
                    'span_custom_css' => array(
                        "type"     => "textarea",
                        "label"    => __('&lt;span&gt; block custom CSS', 'BeRocket_products_label_domain'),
                        "name"     => "span_custom_css",
                        "value"    => $options['span_custom_css'],
                    ),
                    'b_custom_class' => array(
                        "type"     => "text",
                        "label"    => __('&lt;b&gt; block custom class', 'BeRocket_products_label_domain'),
                        "name"     => "b_custom_class",
                        "value"    => $options['b_custom_class'],
                    ),
                    'b_custom_css' => array(
                        "type"     => "textarea",
                        "label"    => __('&lt;b&gt; block custom CSS', 'BeRocket_products_label_domain'),
                        "name"     => "b_custom_css",
                        "value"    => $options['b_custom_css'],
                    ),
                    'i1_custom_class' => array(
                        "type"     => "text",
                        "label"    => __('1) &lt;i&gt; block custom class', 'BeRocket_products_label_domain'),
                        "name"     => "i1_custom_class",
                        "value"    => $options['i1_custom_class'],
                    ),
                    'i1_custom_css' => array(
                        "type"     => "textarea",
                        "label"    => __('1) &lt;i&gt; block custom CSS', 'BeRocket_products_label_domain'),
                        "name"     => "i1_custom_css",
                        "value"    => $options['i1_custom_css'],
                    ),
                    'i2_custom_class' => array(
                        "type"     => "text",
                        "label"    => __('2) &lt;i&gt; block custom class', 'BeRocket_products_label_domain'),
                        "name"     => "i2_custom_class",
                        "value"    => $options['i2_custom_class'],
                    ),
                    'i2_custom_css' => array(
                        "type"     => "textarea",
                        "label"    => __('2) &lt;i&gt; block custom CSS', 'BeRocket_products_label_domain'),
                        "name"     => "i2_custom_css",
                        "value"    => $options['i2_custom_css'],
                    ),
                    'i3_custom_class' => array(
                        "type"     => "text",
                        "label"    => __('3) &lt;i&gt; block custom class', 'BeRocket_products_label_domain'),
                        "name"     => "i3_custom_class",
                        "value"    => $options['i3_custom_class'],
                    ),
                    'i3_custom_css' => array(
                        "type"     => "textarea",
                        "label"    => __('3) &lt;i&gt; block custom CSS', 'BeRocket_products_label_domain'),
                        "name"     => "i3_custom_css",
                        "value"    => $options['i3_custom_css'],
                    ),
                    'i4_custom_class' => array(
                        "type"     => "text",
                        "label"    => __('4) &lt;i&gt; block custom class', 'BeRocket_products_label_domain'),
                        "name"     => "i4_custom_class",
                        "value"    => $options['i4_custom_class'],
                    ),
                    'i4_custom_css' => array(
                        "type"     => "textarea",
                        "label"    => __('4) &lt;i&gt; block custom CSS', 'BeRocket_products_label_domain'),
                        "name"     => "i4_custom_css",
                        "value"    => $options['i4_custom_css'],
                    ),
                ),
            ),
            array(
                'name_for_filters' => $this->hook_name,
                'hide_header' => true,
                'hide_form' => true,
                'hide_additional_blocks' => true,
                'hide_save_button' => true,
                'settings_name' => $this->post_name,
                'options' => $options
            )
        );
        echo '</div>';
        ?>
        <style>
        .berocket-label-margin-paddings-block {
            width: 50px;
        }
        </style>
        <?php
    }
    public function section_templates( $html, $item, $options ) {
        $html = "<tr><th>
                    <div class='br_settings_vtab" . ( ( strpos( $options['template'], 'css' ) !== false || empty($options['template']) ) ? ' active' : '' ) . "' data-tab='css-templates'>" . __('CSS Templates', 'BeRocket_products_label_domain') . "</div>
                    <div class='br_settings_vtab" . ( ( strpos( $options['template'], 'image' ) !== false ) ? ' active' : '' ) . "' data-tab='image-templates'>" . __('Image Templates', 'BeRocket_products_label_domain') . "</div>
                    <div class='br_settings_vtab" . ( ( strpos( $options['template'], 'advanced' ) !== false ) ? ' active' : '' ) . "' data-tab='advanced-templates'>" . __('Advanced Templates', 'BeRocket_products_label_domain') . "</div>
                </th>
                <td class='br_label_css_templates'>";
        $html .= $this->get_templates_section_html( $options['template'] );
        $html .= "</div></td></tr>";

        return $html;
    }
    public static function section_custom_css_explanation($html, $item, $options, $name) {
        $html .= '<tr><td colspan="2">' . __( 'It is settings for advanced users. Please do not use it if you don\'t know how it work.<br/ > This options is provided for designer and programmers.<br />How labels looks in HTML<br />'. 'BeRocket_products_label_domain' ) . 
        '&lt;div&gt;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&lt;span&gt;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;i&gt;&lt;/i&gt;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;i&gt;&lt;/i&gt;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;i&gt;&lt;/i&gt;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;i&gt;&lt;/i&gt;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;b&gt;' 
        . __( 'TEXT OF LABEL', 'BeRocket_products_label_domain' ) . '&lt;/b&gt;<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&lt;/span&gt;<br>
        &lt;/div&gt;</td></tr>';
        return $html;
    }
    public function get_option( $post_id ) {
        $options_test = get_post_meta( $post_id, $this->post_name, true );
        if( empty($options_test) ) {
            $this->post_name = 'br_label';
        }

        $options = parent::get_option( $post_id );
        if( empty($options_test) ) {
            $this->post_name = 'br_labels';
            update_post_meta( $post_id, $this->post_name, $options );
        }

        return $options;
    }

    public function get_custom_options( $options, $post_id ) {
        return $options + $this->get_option( $post_id );
    }

    public function wc_save_check($post_id, $post) {
        if ( $this->post_name != $post->post_type && $post->post_type != 'product' ) {
            return false;
        }
        $current_settings = get_post_meta( $post_id, $this->post_name, true );

        if( empty($current_settings) ) {
            update_post_meta( $post_id, $this->post_name, $this->default_settings );
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return false;
        }

        if( empty($_REQUEST[$this->post_name.'_nonce']) || ! wp_verify_nonce($_REQUEST[$this->post_name.'_nonce'], $this->post_name.'_check') ) {
            return false;
        }

        return true;
    }
    public function wc_save_product( $post_id, $post ) {
        if( ! $this->wc_save_check($post_id, $post) ) {
            return;
        }
        if( ! isset($_POST['br_labels']['color_use']) ) {
            $_POST['br_labels']['color_use'] = 0;
        }
        $_POST['br_labels'] = apply_filters('berocket_apl_wc_save_product', $_POST['br_labels'], $post_id);
        parent::wc_save_product( $post_id, $post );
    }

    public function manage_edit_columns ( $columns ) {
        $date = $columns['date'];
        $columns = parent::manage_edit_columns($columns);
        $columns["products"] = __( "Label text", 'BeRocket_products_label_domain' );
        $columns["data"] = __( "Position", 'BeRocket_products_label_domain' );
        $columns["date"] = $date;
        return $columns;
    }

    public function columns_replace ( $column ) {
        parent::columns_replace($column);
        global $post;
        $label_type = $this->get_option($post->ID);
        switch ( $column ) {
            case "products":
                $text = '';
                if( isset($label_type['text']) ) {
                    $text = $label_type['text'];
                }
                if( $label_type['content_type'] == 'sale_p' ) {
                    $text = __('Discount percentage', 'BeRocket_products_label_domain');
                }
                $text = esc_html($text);
                echo apply_filters('berocket_labels_products_column_text', $text, $label_type);
                break;
            case "data":
                $position = array('left' => __('Left', 'BeRocket_products_label_domain'), 'center' => __('Center', 'BeRocket_products_label_domain'), 'right' => __('Right', 'BeRocket_products_label_domain'));
                $type = array(
                    'image' => __('On image', 'BeRocket_products_label_domain'), 
                    'label' => __('Label', 'BeRocket_products_label_domain'),
                    'in_title' => __('In title', 'BeRocket_products_label_domain'),
                );
                if( isset($label_type['position']) && isset($label_type['type']) ) {
                    echo $type[$label_type['type']].' ( '.$position[$label_type['position']].' )';
                }
                break;
        }
    }
    public function get_templates_section_html( $current_template = '' ) {
        global $post;
        $label_type = $this->get_option($post->ID);

        $i = 1;
        $html = '';
        $default_template_custom_css = array(
            'div_custom_css'  => array(),
            'span_custom_css' => array(
                'color'      => 'white',
                'display'    => 'flex',
                'position'   => 'relative',
                'right'      => '0',
                'text-align' => 'center',
            ),
            'b_custom_css' => array(
                // 'left'       => '0',
                // 'right'      => '0',
                // 'overflow'   => 'hidden',
                // 'position'   => 'absolute',
                'text-align' => 'center',
                'z-index'    => '100',
            ),
            'i1_custom_css' => array(
                'display'  => 'block',
                'height'   => '0',
                'position' => 'absolute',
                'width'    => '0',
            ),
            'i2_custom_css' => array(
                'background-color' => 'transparent',
                'display'          => 'block',
                'line-height'      => '30px',
                'position'         => 'absolute',
                'z-index'          => '99',
            ),
            'i3_custom_css' => array(
                'display'  => 'block',
                'height'   => '0',
                'position' => 'absolute',
                'width'    => '0',
            ),
            'i4_custom_css' => array(
                'display'  => 'block',
                'height'   => '0',
                'position' => 'absolute',
                'width'    => '0',
            )
        );

        $templates_hide  = apply_filters( "berocket_labels_templates_hide", $this->templates_hide );
        $templates = apply_filters( "berocket_labels_templates", $this->templates );
        $templates = $this->add_class( $templates, 'b_custom_class', 'br-labels' );

        foreach ( $templates as $type => $template ) {
            $k = 1;
            $html .= "<div class='br_settings_vtab-content" .
                     ( ( strpos( $current_template, $type ) !== false || (empty($current_template) && $type == 'css') ) ? ' active' : '' )
                     . " tab-{$type}-templates'>";
            if ( count( $template ) ) {
                $html .= "  <ul class='br_template_select'>";

                foreach ( $template as $template_value => $template_styles ) {

                    $template_hide = ( isset($templates_hide[$type][$template_value]) ? $templates_hide[$type][$template_value] : array() );
                    foreach($default_template_custom_css as $template_style_name => $template_style_value) {
                        if( isset($template_styles[$template_style_name]) ) {
                            $template_style_value = array_merge($template_style_value, $template_styles[$template_style_name]);
                        }
                        $template_style_css = '';
                        foreach($template_style_value as $style_name => $style_value) {
                            $template_style_css .= $style_name . ': ' . $style_value . ';';
                        }
                        $template_styles[$template_style_name] = $template_style_css;
                    }
                    $html .= "  <li>";
                    $html .= apply_filters( "berocket_labels_template_preview_start", '', $type, $template_value );
                    $html .= "  <input id='thumb_layout_{$i}' type='radio' name='br_labels[template]'";

                    foreach ( $template_styles as $template_style_name => $template_style_value ) {
                        if ( $template_value == 1000 and $template_style_name == 'span_custom_css' and ! empty( $label_type['custom_image'] ) ) {
                            $template_style_value .= "background: transparent url(" . $label_type['custom_image'] . ") no-repeat right top/contain;";
                        }
                        $html .= " data-" . $template_style_name . "='" . $template_style_value . "'";
                    }
                    $html .= " data-template_hide='" . json_encode($template_hide) . "'";

                    $rotate = empty( $this->templates_rotate["{$type}-{$template_value}"] ) ? 0 : 1;
                    $html .= " data-template_rotate='$rotate'";

                    if ( $type . '-' . $template_value == $current_template ) {
                        $html .= ' checked="checked"';
                    }
                    if ( $template_value == 1000 ) {
                        do_action('berocket_enqueue_media');
                        $html .= " class='br_not_change' value='{$type}-{$template_value}' />
                                <label class='template-preview-{$type} {$type}-{$template_value} " . ( ! empty( $label_type['custom_image'] ) ? 'has_custom_image' : '' ) . "' for='thumb_layout_{$i}'>
                                    <span class='berocket_selected_image'>" .
                                        ( ! empty( $label_type['custom_image'] ) ? "<img src='{$label_type['custom_image']}' alt=''>" : '' ) .
                                    "</span>
                                    <input type='hidden' class='br_not_change berocket_image_value template-preview-custom-image-input' name='br_labels[custom_image]' value='" . ( ! empty( $label_type['custom_image'] ) ? $label_type['custom_image'] : '' ) . "' />
                                    <input type='hidden' class='br_not_change berocket_image_value_size' name='br_labels[custom_image_size]' value='" . ( ! empty( $label_type['custom_image_size'] ) ? $label_type['custom_image_size'] : '' ) . "' />
                                    <span class='template-preview-custom-image berocket_upload_image' data-for='thumb_layout_{$i}'>
                                        <b>" . __( 'Custom<br />Image', 'BeRocket_products_label_domain' ) . "<br /></b>
                                        <input type='button' class='button tiny-button' value='Upload'/>
                                    </span>
                                </label>
                            </li>
                            ";
                    } else {
                        $html .= " class='br_not_change' value='{$type}-{$template_value}' />
                                <label class='template-preview-{$type} {$type}-{$template_value}' for='thumb_layout_{$i}'>
                                    <span>
                                        <span>
                                            <i></i>
                                            <b>SALE</b>
                                        </span>
                                    </span>
                                </label>
                            </li>
                            ";
                    }
                    $i++;
                    $k++;
                }

                $html .= "      <li class='clear'></li>
                        </ul>";
            } else {
                $html .= "<h3>" . __( 'No Templates Available Yet', 'BeRocket_products_label_domain' ) . "</h3>";
            }
            $html .= '</div>';
        }

        return $html;
    }
}
