<?php
/**
 * The admin settings page specific functionality of the plugin.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/admin
 */
if(!defined('WPINC')) {
    die;
}

if(!class_exists('THMAF_Admin_Settings')) :

    /**
     * Admin settings class.
    */
    abstract class THMAF_Admin_Settings {
        protected $page_id = '';
        public static $section_id = '';
        protected $tabs = '';
        protected $sections = '';

        /**
         * Constructor.
         *
         * @param string $page the page id
         * @param array $section the section information
         */
        public function __construct($page, $section = '') {
            $this->page_id = $page;
            $this->tabs = array(
                'general_settings' => esc_html__('General Settings', 'themehigh-multiple-addresses'),
                'pro' => esc_html__('Premium Features', 'themehigh-multiple-addresses'),
            );
        }

        /**
         * Get tabs.
         *
         * @return array
         */
        public function get_tabs() {
            return $this->tabs;
        }

        /**
         * Get current tabs.
         *
         * @return array
         */
        public function get_current_tab() {
            return $this->page_id;
        }

        /**
         * Get sections.
         *
         * @return array
         */
        public function get_sections() {
            return $this->sections;
        }

        /**
         * Get current sections.
         *
         * @return array
         */
        public function get_current_section() {
            return isset($_GET['section']) ? sanitize_key($_GET['section']) : self::$section_id;
        }

        /**
         * Set current sections.
         *
         * @param intiger $section_id the section id
         *
         * @return void
         */
        public static function set_current_section($section_id) {
            if($section_id) {
                self::$section_id = $section_id;
            }
        }

        /**
         * Set first section as current.
         *
         * @return void
         */
        public static function set_first_section_as_current() {
            $sections = THMAF_Admin_Utils::get_sections();
            if($sections && is_array($sections)) {
                $array_keys = array_keys($sections);
                if($array_keys && is_array($array_keys) && isset($array_keys[0])) {
                    self::set_current_section($array_keys[0]);
                }
            }
        }

        /**
         * Render tabs.
         *
         * @return void
         */
        public function render_tabs() {
            $current_tab = $this->get_current_tab();
            $tabs = $this->get_tabs();
            if(empty($tabs)) {
                return;
            }

            echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
            if(!empty($tabs) && is_array($tabs)) {
                foreach($tabs as $id => $label) {
                    $active = ($current_tab == $id) ? 'nav-tab-active' : '';
                    $label = THMAF_i18n::__t($label);
                    echo '<a class="nav-tab '.esc_attr($active).'" href="'. esc_url($this->get_admin_url($id)) .'">'.esc_html($label).'</a>';
                }
            }
            echo '</h2>';
        }

        /**
         * Render sections.
         *
         * @return void
         */
        public function render_sections() {
            $current_section = $this->get_current_section();
            $sections = $this->get_sections();

            if(empty($sections)) {
                return;
            }

            $array_keys = array_keys($sections);
            $section_html = '';
            if(!empty($sections) && is_array($sections)) {
                foreach($sections as $id => $label) {
                    $label = THMAF_i18n::__t($label);
                    $url   = $this->get_admin_url($this->page_id, sanitize_title($id));
                    $section_html .= '<li><a href="'. esc_url($url) .'" class="'.($current_section == $id ? 'current' : '').'">'.$label.'</a> '.(end($array_keys) == $id ? '' : '|').' </li>';
                }
            }

            if($section_html) {
                echo '<ul class="thpladmin-sections">';
                echo esc_html($section_html);
                echo '</ul>';
            }
        }

        /**
         * Get admin Url.
         *
         * @param string $tab the current tab name
         * @param string $section the current section name
         *
         * @return admin url
         */
        public function get_admin_url($tab = false, $section = false) {
            $url = 'admin.php?&page=th_multiple_addresses_free';
            if($tab && !empty($tab)) {
                $url .= '&tab='. $tab;
            }
            if($section && !empty($section)) {
                $url .= '&section='. $section;
            }
            return admin_url($url);
        }

        /**
         * Render form field element.
         *
         * @param array $field the field datas
         * @param array $atts field style array attribute
         * @param array $render_cell render cell information
         *
         * @return array
         */
        public function render_form_field_element($field, $atts = array(), $render_cell = true) {
            if($field && is_array($field)) {
                $args = shortcode_atts(array(
                    'label_cell_props' => '',
                    'input_cell_props' => '',
                    'label_cell_colspan' => '',
                    'input_cell_colspan' => '',
                ), $atts);

                $ftype     = isset($field['type']) ? $field['type'] : 'text';
                $flabel    = isset($field['label']) && !empty($field['label']) ? THMAF_i18n::__t($field['label']) : '';
                $sub_label = isset($field['sub_label']) && !empty($field['sub_label']) ? THMAF_i18n::__t($field['sub_label']) : '';
                $tooltip   = isset($field['hint_text']) && !empty($field['hint_text']) ? THMAF_i18n::__t($field['hint_text']) : '';

                $field_html = '';

                if($ftype == 'text') {
                    $field_html = $this->_render_form_field_element_inputtext($field, $atts);
                }
                else if($ftype == 'number') {
                    $field_html = $this->_render_form_field_element_inputnumber($field, $atts);

                }else if($ftype == 'textarea') {
                    $field_html = $this->_render_form_field_element_textarea($field, $atts);

                }else if($ftype == 'select') {
                    $field_html = $this->_render_form_field_element_select($field, $atts);

                }else if($ftype == 'multiselect') {
                    $field_html = $this->_render_form_field_element_multiselect($field, $atts);

                }else if($ftype == 'colorpicker') {
                    $field_html = $this->_render_form_field_element_colorpicker($field, $atts);

                }else if($ftype == 'checkbox') {
                    $field_html = $this->_render_form_field_element_checkbox($field, $atts, $render_cell);
                    $flabel     = '&nbsp;';
                }


                if($render_cell) {
        					// $required_html = isset($field['required']) && $field['required'] ? '<abbr class="required" title="required">*</abbr>' : '';
        					$label_cell_props = !empty($args['label_cell_props']) ? $args['label_cell_props'] : '';
        					$input_cell_props = !empty($args['input_cell_props']) ? $args['input_cell_props'] : '';
        					if($flabel) { ?>
        						<td <?php if($label_cell_props) {echo $label_cell_props;} ?> >
        							<?php echo $flabel; //echo $required_html;
        							if($sub_label) { ?>
        								<br/><span class="thpladmin-subtitle"><?php echo $sub_label; ?></span>
        							<?php } ?>
        						</td>
        						<?php }
        						$this->render_form_fragment_tooltip($tooltip); ?>
        						<td <?php if($field_html) { echo $input_cell_props;} ?> ><?php echo $field_html; ?></td>
        					<?php } else {
        						echo $field_html;
        				}
            }
        }

        /**
         * Function prepare field props.
         *
         * @param array $field the field data
         * @param array $atts the input info
         *
         * @return array
         */

        private function _prepare_form_field_props($field, $atts = array()) {
            $field_props = '';
            $args = shortcode_atts(array(
                'input_width' => '',
                'input_name_prefix' => 'i_',
                'input_name_suffix' => '',
            ), $atts);
            $ftype = isset($field['type']) ? $field['type'] : 'text';
            if($ftype == 'multiselect') {
                $args['input_name_suffix'] = $args['input_name_suffix'].'[]';
            }
            $fname  = $args['input_name_prefix'].$field['name'].$args['input_name_suffix'];
            $fvalue = isset($field['value']) ? $field['value'] : '';
            if(!is_array($fvalue)){
                $fvalue = htmlspecialchars($fvalue);
            } else {
                if(($field['name'] == 'billing_address_limit')||($field['name'] == 'shipping_address_limit')){
                    $fvalue = '20';
                } else {
                    $fvalue = '';
                }
            }

            $input_width  = $args['input_width'] ? 'width:'.$args['input_width'].';' : '';
            $fid = isset($field['id']) ? $field['id'] : '';
            $frequired = isset($field['required']) ? 'required' : '';
            $field_props  = 'name="'. $fname .'" value="'. $fvalue .'" style="'. $input_width .'"id="'.$fid.'"'.$frequired.' ';
            $field_props .= (isset($field['placeholder']) && !empty($field['placeholder'])) ? ' placeholder="'.$field['placeholder'].'"' : '';
            $field_props .= (isset($field['onchange']) && !empty($field['onchange'])) ? ' onchange="'.$field['onchange'].'"' : '';
            if($ftype == 'number') {
                $fmin=isset($field['min']) ? $field['min'] : '';
                $fmax=isset($field['max']) ? $field['max'] : '';
                $field_props .= 'min="'. $fmin .'"max="'.$fmax.'"';
            }
            return $field_props;
        }

        /**
         * Render form field for text input element.
         *
         * @param string $field the field data
         * @param string $atts the attribute information
         *
         * @return void
         */
        private function _render_form_field_element_inputtext($field, $atts = array()) {
            $field_html = '';
            if($field && is_array($field)) {
                $field_props = $this->_prepare_form_field_props($field, $atts);
                $field_html = '<input type="text" '. $field_props .' />';
            }
            return $field_html;
        }

        /**
         * Render form field for number input element.
         *
         * @param string $field the field data
         * @param string $atts the attribute information
         *
         * @return void
         */
        private function _render_form_field_element_inputnumber($field, $atts = array()) {
            $field_html = '';
            if($field && is_array($field)) {
                $args = shortcode_atts(array(
                    'label_props' => '',
                    'cell_props'  => 3,
                    'render_input_cell' => false,
                ), $atts);
                $field_props = $this->_prepare_form_field_props($field, $atts);
                $field_html .= '<input type="number" '. $field_props .' />';
            }
            return $field_html;
        }

        /**
         * Render form field for textarea element.
         *
         * @param string $field the field data
         * @param string $atts the attribute information
         *
         * @return void
         */
        private function _render_form_field_element_textarea($field, $atts = array()) {
            $field_html = '';
            if($field && is_array($field)) {
                $args = shortcode_atts(array(
                    'rows' => '5',
                    'cols' => '100',
                ), $atts);

                $fvalue = isset($field['value']) ? $field['value'] : '';
                $field_props = $this->_prepare_form_field_props($field, $atts);
                $field_html = '<textarea '. $field_props .' rows="'.esc_attr($args['rows']).'" cols="'.esc_attr($args['cols']).'" >'.esc_attr($fvalue).'</textarea>';
            }
            return $field_html;
        }

        /**
         * Render form field for select element.
         *
         * @param string $field the field data
         * @param string $atts the attribute information
         *
         * @return void
         */
        private function _render_form_field_element_select($field, $atts = array()) {
            $field_html = '';
            if($field && is_array($field)) {
                $fvalue = isset($field['value']) ? $field['value'] : '';
                $field_props = $this->_prepare_form_field_props($field, $atts);

                $field_html = '<select '. $field_props .' >';
                if(!empty($field['options']) && is_array($field['options'])){
                    foreach($field['options'] as $value => $label){
                        $selected = $value === $fvalue ? 'selected' : '';
                        $field_html .= '<option value="'. trim($value) .'" '.esc_attr($selected).'>'. THMAF_i18n::__t($label) .'</option>';
                    }
                }
                $field_html .= '</select>';
            }
            return $field_html;
        }

        /**
         * Render form field for multi select element.
         *
         * @param string $field the field data
         * @param string $atts the attribute information
         *
         * @return void
         */
        private function _render_form_field_element_multiselect($field, $atts = array()) {
            $field_html = '';
            if($field && is_array($field)){
                $field_props = $this->_prepare_form_field_props($field, $atts);

                $field_html = '<select multiple="multiple" '. $field_props .' class="thpladmin-enhanced-multi-select" >';
                if(!empty($field['options']) && is_array($field['options'])){
                    foreach($field['options'] as $value => $label){
                        $field_html .= '<option value="'. trim($value) .'" >'. THMAF_i18n::__t($label) .'</option>';
                    }
                }
                $field_html .= '</select>';
            }
            return $field_html;
        }

        /**
         * Render form field for input radio element.
         *
         * @param string $field the field data
         * @param string $atts the attribute information
         *
         * @return void
         */
        private function _render_form_field_element_radio($field, $atts = array()) {
            $field_html = '';
            if($field && is_array($field)) {
                $field_props = $this->_prepare_form_field_props($field, $atts);

                $field_html = '<select '. $field_props .' >';
                if(!empty($field['options']) && is_array($field['options'])) {
                    foreach($field['options'] as $value => $label){
                        $selected = $value === $fvalue ? 'selected' : '';
                        $field_html .= '<option value="'. trim($value) .'" '.esc_attr($selected).'>'. THMAF_i18n::__t($label) .'</option>';
                    }
                }
                $field_html .= '</select>';
            }
            return $field_html;
        }

        /**
         * Render form field for input checkbox element.
         *
         * @param string $field the field data
         * @param string $atts the attribute information
         *
         * @return void
         */
        private function _render_form_field_element_checkbox($field, $atts = array(), $render_cell = true) {
          $args = shortcode_atts(array('cell_props'  => '', 'input_props' => '', 'label_props' => '', 'name_prefix' => 'i_', 'id_prefix' => 'a_f'), $atts);
          $fid    = $args['id_prefix'].$field['name'];
          $fname  = $args['name_prefix'].$field['name'];
          $fvalue = isset($field['value']) ? $field['value'] : '';
          $flabel = $field['label'];

          $field_props  = 'id="'. $fid .'" name="'. $fname .'"';
          $field_props .= !empty($fvalue) ? ' value="'. $fvalue .'"' : '';
          $field_props .= $field['checked'] ? ' checked' : '';
          $field_props .= $args['input_props'];
          $field_props .= isset($field['onchange']) && !empty($field['onchange']) ? ' onchange="'.$field['onchange'].'"' : '';

          $field_html  = '<input type="checkbox" '. $field_props .' />';
          $field_html .= '<label for="'. $fid .'" '. $args['label_props'] .' > '. esc_html__($flabel, 'woocommerce-multiple-addresses-pro') .'</label>';

          if($render_cell) { ?>
            <td <?php echo $args['cell_props']; ?> ><?php echo $field_html; ?></td>
          <?php } else { ?>
            <?php echo $field_html; ?>
          <?php }
        }

        /**
         * Render form field for colorpicker element.
         *
         * @param string $field the field data
         * @param string $atts the attribute information
         *
         * @return void
         */
        private function _render_form_field_element_colorpicker($field, $atts = array()) {
            $field_html = '';
            if($field && is_array($field)) {
                $field_props = $this->_prepare_form_field_props($field, $atts);

                $field_html  = '<span class="thpladmin-colorpickpreview '.esc_attr($field['name']).'_preview" style=""></span>';
                $field_html .= '<input type="text" '. $field_props .' class="thpladmin-colorpick"/>';
            }
            return $field_html;
        }

        /**
         * Render form field for tooltip element.
         *
         * @param string $field the field data
         * @param string $atts the attribute information
         *
         * @return void
         */
        public function render_form_fragment_tooltip($tooltip = false) {
            if($tooltip){ ?>
                <td style="width: 26px; padding:0px;">
                    <a href="javascript:void(0)" title="<?php echo esc_attr__($tooltip,'themehigh-multiple-addresses'); ?>" class="thpladmin-tooltip"><img src="<?php echo THMAF_ASSETS_URL_ADMIN; ?>/css/help.png" title=""/></a>
                </td>
            <?php }else{ ?>
                <td style="width: 26px; padding:0px;"></td>
            <?php }
        }

        /**
         * Render form fragment separator.
         *
         * @param string $atts the attribute information
         *
         * @return void
         */
        public function render_form_fragment_h_separator($atts = array()) {
            $args = shortcode_atts(array(
                'colspan'      => 6,
                'padding-top'  => '5px',
                'border-style' => 'dashed',
                'border-width' => '1px',
                'border-color' => '#e6e6e6',
                'content'      => '',
            ), $atts);

            $style  = $args['padding-top'] ? 'padding-top:'.$args['padding-top'].';' : '';
            $style .= $args['border-style'] ? ' border-bottom:'.$args['border-width'].' '.$args['border-style'].' '.$args['border-color'].';' : ''; ?>
            <tr><td colspan="<?php echo esc_html($args['colspan']); ?>" style="<?php echo esc_html($style); ?>"><?php echo $args['content']; ?></td></tr>
        <?php }

        /**
         * Render form fragment spacing.
         *
         * @param string $padding the padding measurement
         *
         * @return void
         */
        public function render_field_form_fragment_h_spacing($padding = 5) {
            $style = $padding ? 'padding-top:'.$padding.'px;' : ''; ?>
            <tr><td colspan="6" style="<?php echo esc_html($style) ?>"></td></tr>
        <?php }

        /**
         * Render form field blank.
         *
         * @param string $colspan the colspan measurement
         *
         * @return void
         */
        public function render_form_field_blank($colspan = 3) { ?>
            <td colspan="<?php echo esc_html($colspan); ?>">&nbsp;</td>
        <?php }

        /**
         * Render form section separator.
         *
         * @param array $props the measurement details
         * @param array $atts the attribute info
         *
         * @return void
         */
        public function render_form_section_separator($props, $atts=array()) { ?>
            <tr valign="top"><td colspan="<?php echo esc_html($props['colspan']); ?>" style="height:10px;"></td></tr>
            <tr valign="top"><td colspan="<?php echo esc_html($props['colspan']); ?>" class="thpladmin-form-section-title" ><?php echo esc_html($props['title']); ?></td></tr>
            <tr valign="top"><td colspan="<?php echo esc_html($props['colspan']); ?>" style="height:0px;"></td></tr>
        <?php }

        /**
         * Render form note.
         *
         * @param array $props the measurement details
         * @param array $atts the attribute info
         *
         * @return void
         */
        public function render_form_note($props, $atts=array()) {
            ?>
            <tr valign="top"><td colspan="<?php echo esc_html($props['colspan']); ?>" class="thpladmin-form-note"><p><?php  echo esc_html('The maximum allowed multi shipping locations using the free plugin is 3. To allow multi shipping to unlimited number of locations,');?> <a href="<?php echo esc_url('https://www.themehigh.com/product/woocommerce-multiple-addresses-pro/'); ?>" target="_blank"><?php echo 'upgrade'; ?> </a> <?php echo esc_html('to the premium plugin.');?></p></td></tr>
        <?php }
    }
endif;
