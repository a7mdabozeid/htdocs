<?php

namespace FRFreeVendor;

/**
 * @var array $field
 */
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\FieldHelper;
$field = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\FieldHelper::parse_args($field);
?>
<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php 
echo \esc_attr($field['id']);
?>"><?php 
echo \esc_html($field['title']);
echo $field['tooltip_html'];
// WPCS: XSS ok.
?></label>
	</th>
	<td class="forminp forminp-<?php 
echo \esc_attr(\sanitize_title($field['type']));
?>">
		<select
			name="<?php 
echo \esc_attr($field['id']);
echo 'multiple' === $field['multiple'] ? '[]' : '';
?>"
			id="<?php 
echo \esc_attr($field['id']);
?>"
			style="<?php 
echo \esc_attr($field['css']);
?>"
			class="<?php 
echo \esc_attr($field['class']);
?>"
			<?php 
echo \implode(' ', $field['custom_attributes']);
// WPCS: XSS ok.
?>
			<?php 
echo 'multiple' === $field['multiple'] ? 'multiple="multiple"' : '';
?>
		>
			<?php 
foreach ($field['options'] as $key => $val) {
    ?>
				<option value="<?php 
    echo \esc_attr($key);
    ?>"
					<?php 
    if ($key === 'should_disable') {
        echo ' disabled="disabled" ';
    }
    ?>
					<?php 
    if (\is_array($field['value'])) {
        \selected(\in_array((string) $key, $field['value'], \true));
    } else {
        \selected($field['value'], (string) $key);
    }
    ?>
				><?php 
    echo \esc_html($val);
    ?></option>
				<?php 
}
?>
		</select> <?php 
echo $field['desc'];
// WPCS: XSS ok.
?>
	</td>
</tr>
<?php 
