<?php

namespace FRFreeVendor\RRWProVendor;

use FRFreeVendor\WPDesk\Forms\Field;
/**
 * @var Field  $field
 * @var string $name_prefix
 * @var string $value
 */
if ($field->get_type() === 'checkbox' && $field->has_sublabel()) {
    ?>
	<label><?php 
}
?>
	<input name="<?php 
echo \esc_attr($name_prefix) . '[upload_name]';
?>" value="<?php 
echo \esc_attr($field->get_name());
?>" type="hidden"/>
	<input
		type="<?php 
echo \esc_attr($field->get_type());
?>"
		name="<?php 
echo \esc_attr($field->get_name());
?>"

		<?php 
if ($field->has_classes()) {
    ?>
			class="<?php 
    echo \esc_attr($field->get_classes());
    ?>"
			<?php 
}
?>

		<?php 
foreach ($field->get_attributes() as $key => $atr_val) {
    echo \esc_attr($key) . '="' . \esc_attr($atr_val) . '"';
    ?>
			<?php 
}
?>

		<?php 
if (\in_array($field->get_type(), ['number', 'text', 'hidden'], \true)) {
    ?>
			value="<?php 
    echo \esc_html($value);
    ?>"
			<?php 
} else {
    ?>
			value="yes"
			<?php 
    if ($value === 'yes') {
        ?>
				checked="checked"
				<?php 
    }
    ?>
			<?php 
}
?>
	/>

	<?php 
if ($field->get_type() === 'checkbox' && $field->has_sublabel()) {
    ?>
	<?php 
    echo \esc_html($field->get_sublabel());
    ?></label>
	<?php 
}
