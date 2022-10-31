<?php 
extract($brlabel_svg); 
$replacements = apply_filters('brapl_svg_predefined', array(), array('width' => 40, 'height' => 61));
if(! empty($gradient) && is_string($gradient)) {
    $gradient = str_replace(array_keys($replacements), array_values($replacements), $gradient);
}
?><svg viewBox="0 0 40 61" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="<?php if( ! empty($color) ) echo $color; ?>"<?php 
if( ! empty($svg_attributes) ) echo $svg_attributes; ?>>
    <path d="M 7 36 L 0 61 L 20 46 L 40 61 L 33 36 a 20,20 0 1,0 -26,0 Z"/><?php 
    if(! empty($gradient) && is_string($gradient)) {
        echo $gradient;
    }
    ?>
</svg>