<?php

if (!defined('ABSPATH')) {
    exit;
}

class WooQR {

    private $wooqr_options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'wooqr_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'wooqr_page_init' ) );
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_script'), 10);
        if ( ! get_option('wooqr_option_name') ) {
            $default = array('render' => 'image','size'=> '700','crisp'=> 'true','fill'=> '#333333','back'=> '#ffffff','minVersion'=> '1','ecLevel'=> 'H','quiet'=> '1','rounded'=> '100','mode'=> 'plain','mSize'=> '20','mPosX'=> '50','mPosY'=> '50','label'=> 'QR Code','fontname'=> 'Lato','fontcolor'=> '#ff9818');
            update_option( 'wooqr_option_name', $default );
        }

    }


    public function enqueue_admin_script()
    {
        global $WooCommerceQrCodes, $post_id;
        $screen = get_current_screen();
        $wcqrc_family = get_option('wooqr_option_name')['fontname'];
        if ($screen->id == 'toplevel_page_wooqr') {
            wp_enqueue_style('wcqrc-admin-panel-style', $WooCommerceQrCodes->plugin_url . 'assets/admin/css/wcqrc-admin-panel.css', array(), $WooCommerceQrCodes->version);
            wp_enqueue_script('wcqrc-kjua-js', $WooCommerceQrCodes->plugin_url . 'assets/common/js/kjua.js', array('jquery'), $WooCommerceQrCodes->version);
            wp_enqueue_script('wcqrc-kjua-scripts', $WooCommerceQrCodes->plugin_url . 'assets/admin/js/kjua-scripts.js', array('jquery'), $WooCommerceQrCodes->version);
            // wp_enqueue_script('wcqrc-webfontloader-scripts', 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js', array('jquery'), $WooCommerceQrCodes->version);
            ?>
            <link rel="preconnect" href="//fonts.gstatic.com">
            <?php
            if ( $wcqrc_family != '0' ) {
                wp_register_style( 'wcqrc-googleFonts', '//fonts.googleapis.com/css?family=' . $wcqrc_family );
                wp_enqueue_style( 'wcqrc-googleFonts' );
            }



        }
    }



    public function wooqr_add_plugin_page() {

        add_menu_page(
            'Woo QR',
            'Woo QR',
            'manage_options',
            'wooqr',
            array( $this, 'wooqr_create_admin_page' ),
            'dashicons-admin-generic',
            56
        );
        add_submenu_page( 'wooqr', 'Design', 'Design', 'manage_options', 'wooqr', array( $this, 'wooqr_create_admin_page' ) );


    }

    public function wooqr_create_admin_page() {
        $this->wooqr_options = get_option( 'wooqr_option_name' ); ?>

        <div class="wrap">
            <div class="fixed-holder">
                <h1 class="reorder-title"><?php esc_html_e('Woo QR Code - Design', 'wpr-reorder');?>
                    <small> Manage QR codes from single screen </small>
                </h1>
            </div>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <div class="wooqr-setting-wrapper">
                    <?php
                    settings_fields( 'wooqr_option_group' );
                    ?>
                    <div class="left-panel">
                        <input type="hidden" name="wooqr_option_name[render]" value="image" id="render">
                        <input type="hidden" name="wooqr_option_name[size]" value="700" id="size">
                        <input type="hidden" name="wooqr_option_name[text]" value="Woo QR" id="text">
                        <?php

                        do_settings_sections( 'wooqr-admin' );
                        submit_button();
                        ?>
                    </div>
                    <div id="qr-container" class="right-panel"></div>

                </div>
            </form>
        </div>
    <?php }

    public function wooqr_page_init() {
        register_setting(
            'wooqr_option_group', // option_group
            'wooqr_option_name', // option_name
            array( $this, 'wooqr_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'wooqr_setting_section', // id
            '', // title
            array( $this, 'wooqr_section_info' ), // callback
            'wooqr-admin' // page
        );
        add_settings_field(
            'mode', // id
            'Mode', // title
            array( $this, 'mode_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'render', // id
            array( $this, 'render_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'size', // id
            array( $this, 'size_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'crisp', // id
            'Crisp', // title
            array( $this, 'crisp_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'fill', // id
            'Fill', // title
            array( $this, 'fill_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'back', // id
            'Background', // title
            array( $this, 'back_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'minVersion', // id
            'Min Version', // title
            array( $this, 'minVersion_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'ecLevel', // id
            'Error Correction Level', // title
            array( $this, 'ecLevel_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'quiet', // id
            'Quite Zone', // title
            array( $this, 'quiet_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'rounded', // id
            'Rounded Corners', // title
            array( $this, 'rounded_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );



        add_settings_field(
            'mSize', // id
            'Size', // title
            array( $this, 'mSize_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'mPosX', // id
            'POS X', // title
            array( $this, 'mPosX_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'mPosY', // id
            'POS Y', // title
            array( $this, 'mPosY_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section' // section
        );

        add_settings_field(
            'label', // id
            'Label', // title
            array( $this, 'label_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section', // section
            ( (get_option( 'wooqr_option_name' )['mode'] == 'plain' ? array( 'class' => 'hidden' ) : get_option( 'wooqr_option_name' )['mode'] == 'image' ) ? array( 'class' => 'hidden' ) : '' )
        );

        add_settings_field(
            'fontname', // id
            'Font Name', // title
            array( $this, 'fontname_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section', // section
            ( (get_option( 'wooqr_option_name' )['mode'] == 'plain' ? array( 'class' => 'hidden' ) : get_option( 'wooqr_option_name' )['mode'] == 'image') ? array( 'class' => 'hidden' ) : '' )
        );

        add_settings_field(
            'fontcolor', // id
            'Font Color', // title
            array( $this, 'fontcolor_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section', // section
            ( (get_option( 'wooqr_option_name' )['mode'] == 'plain' ? array( 'class' => 'hidden' ) : get_option( 'wooqr_option_name' )['mode'] == 'image') ? array( 'class' => 'hidden' ) : '' )
        );

        add_settings_field(
            'image', // id
            'Image', // title
            array( $this, 'wooqr_upload_image_callback' ), // callback
            'wooqr-admin', // page
            'wooqr_setting_section', // section
            ( (get_option( 'wooqr_option_name' )['mode'] == 'plain' ? array( 'class' => 'hidden' ) : get_option( 'wooqr_option_name' )['mode'] == 'label') ? array( 'class' => 'hidden' ) : '' )
        );



    }

    public function wooqr_sanitize($input) {
        $sanitary_values = array();
        if ( isset( $input['render'] ) ) {
            $sanitary_values['render'] = sanitize_text_field( $input['render'] );
        }

        if ( isset( $input['size'] ) ) {
            $sanitary_values['size'] = sanitize_text_field( $input['size'] );
        }

        if ( isset( $input['crisp'] ) ) {
            $sanitary_values['crisp'] = $input['crisp'];
        }

        if ( isset( $input['fill'] ) ) {
            $sanitary_values['fill'] = sanitize_text_field( $input['fill'] );
        }

        if ( isset( $input['back'] ) ) {
            $sanitary_values['back'] = sanitize_text_field( $input['back'] );
        }

        if ( isset( $input['minVersion'] ) ) {
            $sanitary_values['minVersion'] = sanitize_text_field( $input['minVersion'] );
        }

        if ( isset( $input['ecLevel'] ) ) {
            $sanitary_values['ecLevel'] = $input['ecLevel'];
        }

        if ( isset( $input['quiet'] ) ) {
            $sanitary_values['quiet'] = sanitize_text_field( $input['quiet'] );
        }
        if ( isset( $input['rounded'] ) ) {
            $sanitary_values['rounded'] = sanitize_text_field( $input['rounded'] );
        }

        if ( isset( $input['mode'] ) ) {
            $sanitary_values['mode'] = $input['mode'];
        }

        if ( isset( $input['mSize'] ) ) {
            $sanitary_values['mSize'] = sanitize_text_field( $input['mSize'] );
        }

        if ( isset( $input['mPosX'] ) ) {
            $sanitary_values['mPosX'] = sanitize_text_field( $input['mPosX'] );
        }

        if ( isset( $input['mPosY'] ) ) {
            $sanitary_values['mPosY'] = sanitize_text_field( $input['mPosY'] );
        }

        if ( isset( $input['label'] ) ) {
            $sanitary_values['label'] = sanitize_text_field( $input['label'] );
        }

        if ( isset( $input['fontname'] ) ) {
            $sanitary_values['fontname'] = sanitize_text_field( $input['fontname'] );
        }

        if ( isset( $input['fontcolor'] ) ) {
            $sanitary_values['fontcolor'] = sanitize_text_field( $input['fontcolor'] );
        }

        if ( isset( $input['image'] ) ) {
            $sanitary_values['image'] = sanitize_text_field( $input['image'] );
        }

        return $sanitary_values;
    }

    public function wooqr_section_info() {

    }

    public function render_callback() {
        printf(
            '<input class="regular-text" type="hidden" name="wooqr_option_name[render]" id="render" value="%s">',
            isset( $this->wooqr_options['render'] ) ? esc_attr( $this->wooqr_options['render']) : 'image'
        );
    }

    public function size_callback() {
        printf(
            '<input class="regular-text" type="hidden" name="wooqr_option_name[size]" id="size" value="%s">',
            isset( $this->wooqr_options['size'] ) ? esc_attr( $this->wooqr_options['size']) : '700'
        );
    }

    public function crisp_callback() {
        ?> <select name="wooqr_option_name[crisp]" id="crisp">
            <?php $selected = (isset( $this->wooqr_options['crisp'] ) && $this->wooqr_options['crisp'] === 'True') ? 'selected' : '' ; ?>
            <option value="true" <?php echo $selected; ?>>True</option>
            <?php $selected = (isset( $this->wooqr_options['crisp'] ) && $this->wooqr_options['crisp'] === 'False') ? 'selected' : '' ; ?>
            <option value="false" <?php echo $selected; ?>>False</option>
        </select> <?php
    }
    public function fill_callback() {
        printf(
            '<input class="regular-text" type="color" name="wooqr_option_name[fill]" id="fill" value="%s">',
            isset( $this->wooqr_options['fill'] ) ? esc_attr( $this->wooqr_options['fill']) : '#333333'
        );
    }

    public function back_callback() {
        printf(
            '<input class="regular-text" type="color" name="wooqr_option_name[back]" id="back" value="%s">',
            isset( $this->wooqr_options['back'] ) ? esc_attr( $this->wooqr_options['back']) : '#ffffff'
        );
    }

    public function minVersion_callback($args) {
        $minversion = isset( $this->wooqr_options['minVersion'] ) ? esc_attr( $this->wooqr_options['minVersion']) : '1';
        printf(
            '<input class="regular-text" type="range" min="1" max="10" step="1" name="wooqr_option_name[minVersion]" id="minVersion" value="'.$minversion.'" oninput="minversionOutput.value = minVersion.value"> <output id="minversionOutput">'.$minversion.'</output>');
    }

    public function ecLevel_callback() {
        ?>
        <select name="wooqr_option_name[ecLevel]" id="ecLevel">
            <?php $selected = (isset( $this->wooqr_options['ecLevel'] ) && $this->wooqr_options['ecLevel'] === 'H') ? 'selected' : '' ; ?>
            <option value="H" <?php echo $selected; ?>>H - high (30%)</option>
            <?php $selected = (isset( $this->wooqr_options['ecLevel'] ) && $this->wooqr_options['ecLevel'] === 'Q') ? 'selected' : '' ; ?>
            <option value="Q" <?php echo $selected; ?>>Q - quartile (25%)</option>
            <?php $selected = (isset( $this->wooqr_options['ecLevel'] ) && $this->wooqr_options['ecLevel'] === 'M') ? 'selected' : '' ; ?>
            <option value="M" <?php echo $selected; ?>>M - medium (15%)</option>
            <?php $selected = (isset( $this->wooqr_options['ecLevel'] ) && $this->wooqr_options['ecLevel'] === 'L') ? 'selected' : '' ; ?>
            <option value="L" <?php echo $selected; ?>>L - low (7%)</option>
        </select>
        <?php
    }

    public function quiet_callback() {
        $quitzone = isset( $this->wooqr_options['quiet'] ) ? esc_attr( $this->wooqr_options['quiet']) : '1';
        printf(
            '<input class="regular-text" type="range" min="0" max="4" step="1" name="wooqr_option_name[quiet]" id="quiet" value="'.$quitzone.'" oninput="quitzoneOutput.value = quiet.value"> <output id="quitzoneOutput">'.$quitzone.'</output>');
    }

    public function rounded_callback() {
        $rc = isset( $this->wooqr_options['rounded'] ) ? esc_attr( $this->wooqr_options['rounded']) : '100';
        printf(
            '<input class="regular-text" type="range" min="0" max="100" step="10" name="wooqr_option_name[rounded]" id="rounded" value="'.$rc.'" oninput="rcOutput.value = rounded.value"> <output id="rcOutput">'.$rc.'</output>');
    }

    public function mode_callback() {
        ?> <select name="wooqr_option_name[mode]" id="mode">
            <?php $selected = (isset( $this->wooqr_options['mode'] ) && $this->wooqr_options['mode'] === 'plain') ? 'selected' : '' ; ?>
            <option value="plain" <?php echo $selected; ?>>Plain</option>
            <?php $selected = (isset( $this->wooqr_options['mode'] ) && $this->wooqr_options['mode'] === 'label') ? 'selected' : '' ; ?>
            <option value="label" <?php echo $selected; ?>>Label</option>
            <?php $selected = (isset( $this->wooqr_options['mode'] ) && $this->wooqr_options['mode'] === 'image') ? 'selected' : '' ; ?>
            <option value="image" <?php echo $selected; ?>>Image</option>
        </select> <?php
    }

    public function mSize_callback() {
        $msize = isset( $this->wooqr_options['mSize'] ) ? esc_attr( $this->wooqr_options['mSize']) : '20';
        printf(
            '<input class="regular-text" type="range" min="0" max="40" step="1" name="wooqr_option_name[mSize]" id="mSize" value="'.$msize.'" oninput="mSizeOutput.value = mSize.value"> <output id="mSizeOutput">'.$msize.'</output>');
    }

    public function mPosX_callback() {
        $mposx = isset( $this->wooqr_options['mPosX'] ) ? esc_attr( $this->wooqr_options['mPosX']) : '50';
        printf(
            '<input class="regular-text" type="range" min="0" max="100" step="1" name="wooqr_option_name[mPosX]" id="mPosX" value="'.$mposx.'" oninput="mposxOutput.value = mPosX.value"> <output id="mposxOutput">'.$mposx.'</output>');
    }

    public function mPosY_callback() {
        $mposy = isset( $this->wooqr_options['mPosY'] ) ? esc_attr( $this->wooqr_options['mPosY']) : '50';
        printf(
            '<input class="regular-text" type="range" min="0" max="100" step="1" name="wooqr_option_name[mPosY]" id="mPosY" value="'.$mposy.'" oninput="mposyOutput.value = mPosY.value"> <output id="mposyOutput">'.$mposy.'</output>');
    }

    public function label_callback() {
        printf(
            '<input class="regular-text" type="text" name="wooqr_option_name[label]" id="label" value="%s">',
            isset( $this->wooqr_options['label'] ) ? esc_attr( $this->wooqr_options['label']) : 'QR Code'
        );
    }

    public function fontname_callback() {
        /* printf(
            '<input class="regular-text" type="text" name="wooqr_option_name[fontname]" id="fontname" value="%s">',
            isset( $this->wooqr_options['fontname'] ) ? esc_attr( $this->wooqr_options['fontname']) : 'Ubuntu Mono'
        ); */
        $font = get_option('wooqr_option_name')['fontname'];
        $file = json_decode( maybe_unserialize( file_get_contents( 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyD3zPQHpiP7VcHtH3_3WjvYz3AShprKkIU' ) ) );

        $family         = array();
        // $family[ 0 ]     = 'Default';
        foreach ( $file->items as $k => $v ) {
            $family[ $v->family ] = $v->family;
        }
        ?>
        <select name="wooqr_option_name[fontname]" id="fontname">
            <?php
            foreach ( $family as $key => $value ) {

                echo "<option value='".$value."' ".(($value == $font)? 'selected':'').">".$value."</option>";
            }
            ?>

        </select>
        <input type="hidden" value="<?=$font?>" id="wooqr-fontname"/>
        <?php

    }

    public function fontcolor_callback() {
        printf(
            '<input class="regular-text" type="color" name="wooqr_option_name[fontcolor]" id="fontcolor" value="%s">',
            isset( $this->wooqr_options['fontcolor'] ) ? esc_attr( $this->wooqr_options['fontcolor']) : '#ff9818'
        );
    }

    public function wooqr_upload_image_callback() {
        global $WooCommerceQrCodes;
        //var_dump($this->wooqr_options);
        printf(
            '<input id="wooqr_upload_image" type="text" size="36" name="wooqr_option_name[image]" value="%s" />', isset( $this->wooqr_options['image'] ) ? esc_attr( $this->wooqr_options['image']) : $WooCommerceQrCodes->plugin_url . 'assets/admin/images/wooqr-icon.png'
        );

        printf(
            '<input id="wooqr_upload_button" class="button" type="button" value="Upload image" />'
        );
        printf(
            '<img id="wooqrimg-buffer" src="%s">',isset( $this->wooqr_options['image'] ) ? esc_attr( $this->wooqr_options['image']) : $WooCommerceQrCodes->plugin_url . 'assets/admin/images/wooqr-icon.png');



    }

}
if ( is_admin() )
    $wooqr = new WooQR();

/*
    * Retrieve this value with:
    * $wooqr_options = get_option( 'wooqr_option_name' ); // Array of All Options
    * $wooqr_crisp = $wooqr_options['wooqr_crisp']; // Crisp
    * $fill = $wooqr_options['fill']; // Fill
    * $back = $wooqr_options['back']; // Background
    * $minVersion = $wooqr_options['minVersion']; // Min Version
    * $ecLevel = $wooqr_options['ecLevel']; // Error Correction level
    * $quiet = $wooqr_options['quiet']; // Quite Zone
    * $rounded = $wooqr_options['rounded']; // Rounded Corners
    * $mode = $wooqr_options['mode']; // Mode
    * $mSize = $wooqr_options['mSize']; // Size
    * $mPosX = $wooqr_options['mPosX']; // POS X
    * $mPosY = $wooqr_options['mPosY']; // POS Y
    * $label = $wooqr_options['label']; // Label
    * $fontname = $wooqr_options['fontname']; // Font
    * $fontcolor = $wooqr_options['fontcolor']; // Font Color
    * $image = $wooqr_options['wooqr_upload_image']; // Image
*/