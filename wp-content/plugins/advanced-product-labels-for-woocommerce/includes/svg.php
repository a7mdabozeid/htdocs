<?php
if( ! class_exists('BRAPL_svg_styles') ) {
    class BRAPL_svg_styles {
        private $svg_gradients = '';
        private $svg_files = array();
        private $gradient_id = 1;
        function __construct() {
            foreach (glob(__DIR__ . "/svg/*.php") as $filename) {
                $this->svg_files[] = basename($filename);
            }
            add_filter('berocket_apl_show_label_on_product_html', array($this, 'svg_icon'), 5, 3 );
            add_filter('brapl_svg_predefined', array($this, 'svg_predefined'), 10, 2 );
            add_filter('berocket_apl_label_sanitize_data', array($this, 'border_remove'), 10, 1);
        }
        function svg_icon($html, $br_label, $product) {
            if( ! empty($br_label['template']) && in_array($br_label['template'].'.php', $this->svg_files) ) {
                unset($html['template-span-before']);
                unset($html['template-i-before']);
                unset($html['template-i-after']);
                unset($html['template-i']);
                $svg_gradient = $this->generate_gradient($br_label);
                if( empty($svg_gradient) ) {
                    $brlabel_svg = array(
                        'color' => (  empty($br_label['color_use']) ? 'transparent' : $br_label['color'] )
                    );
                } else {
                    $brlabel_svg = array(
                        'color' => 'url(#'.$svg_gradient.')',
                        'gradient' => $this->get_gradients()
                    );
                    
                }
                $svg_attributes = '';
                $svg_attributes .= $this->svg_border($br_label);
                ob_start();
                include (__DIR__ . "/svg/" . $br_label['template'].'.php');
                $html_svg = ob_get_clean();
                $html = berocket_insert_to_array($html, 'template-b', array(
                    'template_svg' => $html_svg
                ));
            }
            return $html;
        }
        function generate_gradient($label) {
            $svg = '';
            $svg_id = '';
            if(! empty($label['gradient_use']) ) {
                $svg_id = 'brsvg_'.$this->gradient_id;
                if( $label['gradient_orientation'] == 'radial' ) {
                    $svg = $this->radial_gradient($label);
                } elseif( $label['gradient_orientation'] == 'elliptical' ) {
                    $svg = $this->elliptical_gradient($label);
                } else {
                    $svg = $this->linear_gradient($label);
                }
                $this->svg_gradients .= $svg;
            }
            return $svg_id;
        }
        function radial_gradient($label) {
            $start_position = intval($label['gradient_start_position_num']);
            $end_position = intval($label['gradient_end_position_num']);
            $angle = deg2rad(intval($label['gradient_angle']));
            $position = $label['gradient_position'];
            $gradient_size = ! empty($label['gradient_size']);
            switch ($position) {
                case 'at top left':
                    $gradient_data = array('cx' => '0%', 'cy' => '0%', 'r' => ($gradient_size ? '%maxline%' : '%diag%'));
                    break;
                case 'at top center':
                    $gradient_data = array('cx' => '50%', 'cy' => '0%', 'r' => ($gradient_size ? '%maxtb%' : '%diagw%'));
                    break;
                case 'at top right':
                    $gradient_data = array('cx' => '100%', 'cy' => '0%', 'r' => ($gradient_size ? '%maxline%' : '%diag%'));
                    break;
                case 'at left center':
                    $gradient_data = array('cx' => '0%', 'cy' => '50%', 'r' => ($gradient_size ? '%maxlr%' : '%diagh%'));
                    break;
                case 'at right center':
                    $gradient_data = array('cx' => '100%', 'cy' => '50%', 'r' => ($gradient_size ? '%maxlr%' : '%diagh%'));
                    break;
                case 'at bottom left':
                    $gradient_data = array('cx' => '0%', 'cy' => '100%', 'r' => ($gradient_size ? '%maxline%' : '%diag%'));
                    break;
                case 'at bottom center':
                    $gradient_data = array('cx' => '50%', 'cy' => '100%', 'r' => ($gradient_size ? '%maxtb%' : '%diagw%'));
                    break;
                case 'at bottom right':
                    $gradient_data = array('cx' => '100%', 'cy' => '100%', 'r' => ($gradient_size ? '%maxline%' : '%diag%'));
                    break;
                default:
                    $gradient_data = array('cx' => '50%', 'cy' => '50%', 'r' => ($gradient_size ? '%maxhalf%' : '%halfdiag%'));
            }
            $svg = '<defs><radialGradient id="brsvg_'.$this->gradient_id.'" gradientUnits="userSpaceOnUse" cx="'.$gradient_data['cx'].'" cy="'.$gradient_data['cy'].'" r="'.$gradient_data['r'].'">';
            $svg .= '<stop offset="0%" stop-color="'.$label['gradient_start_color'].'"/>';
            $svg .= '<stop offset="'.$start_position.'%" stop-color="'.$label['gradient_start_color'].'"/>';
            $svg .= '<stop offset="'.$end_position.'%" stop-color="'.$label['gradient_end_color'].'"/>';
            $svg .= '<stop offset="100%" stop-color="'.$label['gradient_end_color'].'"/>';
            $svg .= '</linearGradient></defs>';
            return $svg;
        }
        function elliptical_gradient($label) {
            $start_position = intval($label['gradient_start_position_num']);
            $end_position = intval($label['gradient_end_position_num']);
            $angle = deg2rad(intval($label['gradient_angle']));
            $position = $label['gradient_position'];
            $gradient_size = ! empty($label['gradient_size']);
            switch ($position) {
                case 'at top left':
                    $gradient_data = array('cx' => ($gradient_size ? '0' : '1'), 'cy' => ($gradient_size ? '0' : '1'));
                    $gradient_data['gradient_add'] = ($gradient_size ?
                        ' gradientUnits="userSpaceOnUse" gradientTransform="scale(%width% %height%)'
                        : ' gradientTransform="translate(-1, -1)"');
                    break;
                case 'at top center':
                    $gradient_data = array('cx' => ($gradient_size ? '0.5' : '1'), 'cy' => ($gradient_size ? '0' : '1'));
                    $gradient_data['gradient_add'] = ($gradient_size ?
                        ' gradientUnits="userSpaceOnUse" gradientTransform="scale(%width% %height%)'
                        : ' gradientTransform="translate(-0.5, -1)"');
                    break;
                case 'at top right':
                    $gradient_data = array('cx' => '1', 'cy' => ($gradient_size ? '0' : '1'));
                    $gradient_data['gradient_add'] = ($gradient_size ?
                        ' gradientUnits="userSpaceOnUse" gradientTransform="scale(%width% %height%)'
                        : ' gradientTransform="translate(0, -1)"');
                    break;
                case 'at left center':
                    $gradient_data = array('cx' => ($gradient_size ? '0' : '1'), 'cy' => ($gradient_size ? '0.5' : '1'));
                    $gradient_data['gradient_add'] = ($gradient_size ?
                        ' gradientUnits="userSpaceOnUse" gradientTransform="scale(%width% %height%)'
                        : ' gradientTransform="translate(-1, -0.5)"');
                    break;
                case 'at right center':
                    $gradient_data = array('cx' => '1', 'cy' => ($gradient_size ? '0.5' : '1'));
                    $gradient_data['gradient_add'] = ($gradient_size ?
                        ' gradientUnits="userSpaceOnUse" gradientTransform="scale(%width% %height%)'
                        : ' gradientTransform="translate(0, -0.5)"');
                    break;
                case 'at bottom left':
                    $gradient_data = array('cx' => ($gradient_size ? '0' : '1'), 'cy' => '1');
                    $gradient_data['gradient_add'] = ($gradient_size ?
                        ' gradientUnits="userSpaceOnUse" gradientTransform="scale(%width% %height%)'
                        : ' gradientTransform="translate(-1, 0)"');
                    break;
                case 'at bottom center':
                    $gradient_data = array('cx' => ($gradient_size ? '0.5' : '1'), 'cy' => '1');
                    $gradient_data['gradient_add'] = ($gradient_size ?
                        ' gradientUnits="userSpaceOnUse" gradientTransform="scale(%width% %height%)'
                        : ' gradientTransform="translate(-0.5, 0)"');
                    break;
                case 'at bottom right':
                    $gradient_data = array('cx' => '1', 'cy' => '1');
                    $gradient_data['gradient_add'] = ($gradient_size ?
                        ' gradientUnits="userSpaceOnUse" gradientTransform="scale(%width% %height%)'
                        : ' gradientTransform="translate(0, 0)"');
                    break;
                default:
                    $gradient_data = array('cx' => '1', 'cy' => '1');
                    $gradient_data['gradient_add'] = ($gradient_size ?
                        ' gradientUnits="userSpaceOnUse" gradientTransform="scale(%halfwidth% %halfheight%)'
                        : ' gradientTransform="translate(-0.5, -0.5)"');
            }
            $svg = '<defs><radialGradient id="brsvg_'.$this->gradient_id.'" cx="'.$gradient_data['cx'].'" cy="'.$gradient_data['cy'].
            '" r="1"'.$gradient_data['gradient_add'].'">';
            $svg .= '<stop offset="0%" stop-color="'.$label['gradient_start_color'].'"/>';
            $svg .= '<stop offset="'.$start_position.'%" stop-color="'.$label['gradient_start_color'].'"/>';
            $svg .= '<stop offset="'.$end_position.'%" stop-color="'.$label['gradient_end_color'].'"/>';
            $svg .= '<stop offset="100%" stop-color="'.$label['gradient_end_color'].'"/>';
            $svg .= '</linearGradient></defs>';
            return $svg;
        }
        function linear_gradient($label) {
            $start_position = intval($label['gradient_start_position_num']);
            $end_position = intval($label['gradient_end_position_num']);
            $angle = deg2rad(intval($label['gradient_angle']));
            $x1 = 0;
            $y1 = 0;
            $x2 = cos($angle);
            $y2 = sin($angle);
            if($x2 < 0 ) {
                $x2 = $x2 + 1;
                $x1 = 1;
            }
            if($y2 < 0 ) {
                $y2 = $y2 + 1;
                $y1 = 1;
            }
            $svg = '<defs><linearGradient x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" id="brsvg_'.$this->gradient_id.'">';
            $svg .= '<stop offset="0%" stop-color="'.$label['gradient_start_color'].'"/>';
            $svg .= '<stop offset="'.$start_position.'%" stop-color="'.$label['gradient_start_color'].'"/>';
            $svg .= '<stop offset="'.$end_position.'%" stop-color="'.$label['gradient_end_color'].'"/>';
            $svg .= '<stop offset="100%" stop-color="'.$label['gradient_end_color'].'"/>';
            $svg .= '</linearGradient></defs>';
            return $svg;
        }
        function get_gradients() {
            $svg = '';
            if( ! empty($this->svg_gradients) ) {
                $svg = $this->svg_gradients;
                $this->svg_gradients = '';
            }
            return $svg;
        }
        function svg_predefined($predefines, $args) {
            $width  = intval($args['width']);
            $height = intval($args['height']);
            $halfwidth = intval($width/2);
            $halfheight = intval($height/2);
            $diag = sqrt(($width*$width+$height*$height));
            $halfdiag = intval($diag/2);
            $diagw = sqrt(($halfwidth*$halfwidth+$height*$height));
            $diagh = sqrt(($width*$width+$halfheight*$halfheight));
            $predefines['%width%']      = $width;
            $predefines['%halfwidth%']  = $halfwidth;
            $predefines['%height%']     = $height;
            $predefines['%halfheight%'] = $halfheight;
            $predefines['%diag%']       = intval($diag);
            $predefines['%diagw%']      = intval($diagw);
            $predefines['%diagh%']      = intval($diagh);
            $predefines['%halfdiag%']   = $halfdiag;
            $predefines['%maxline%']    = max($width, $height);
            $predefines['%maxhalf%']    = max($halfwidth, $halfheight);
            $predefines['%maxlr%']      = max($width, $halfheight);
            $predefines['%maxtb%']      = max($halfwidth, $height);
            return $predefines;
        }
        function svg_border($br_label) {
            $attr = '';
            if( ! empty($br_label['svg_border_width']) ) {
                //$attr = ' stroke-linecap="square" stroke-linejoin="miter" stroke="blue"'
                $attr = ' stroke="'.$br_label['svg_border_color'].'" stroke-width="'.$br_label['svg_border_width'].'"';
            }
            return $attr;
        }
        function border_remove($br_label) {
            if( ! empty($br_label['template']) && in_array($br_label['template'].'.php', $this->svg_files) ) {
                if( ! empty($br_label['border_width']) ) {
                    $br_label['svg_border_width'] = $br_label['border_width'];
                    unset($br_label['border_width']);
                }
                if( ! empty($br_label['border_color']) ) {
                    $br_label['svg_border_color'] = $br_label['border_color'];
                    unset($br_label['border_color']);
                } else {
                    $br_label['svg_border_color'] = '#000000';
                }
            }
            return $br_label;
        }
    }
    new BRAPL_svg_styles;
}