<?php

namespace FRFreeVendor;

/**
 * @var array $field
 */
$name = $field['id'] ?? '';
if (!$name) {
    return '';
}
$disabled = $field['should_disable'] ?? \false;
$periods = ['days' => \esc_html__('Day(s)', 'flexible-refund-and-return-order-for-woocommerce'), 'weeks' => \esc_html__('Week(s)', 'flexible-refund-and-return-order-for-woocommerce'), 'months' => \esc_html__('Month(s)', 'flexible-refund-and-return-order-for-woocommerce'), 'years' => \esc_html__('Year(s)', 'flexible-refund-and-return-order-for-woocommerce')];
$time_value = $field['value']['time_value'] ?? 1;
$time_period = $field['value']['time_period'] ?? 'days';
?>
<tr valign="top" id="auto-hide-row">
	<td></td>
	<td>
		<span><?php 
\esc_html_e('Hide the time button after', 'flexible-refund-and-return-order-for-woocommerce');
?></span>
		<span>
		<input <?php 
echo $disabled ? 'disabled="disabled"' : '';
?>
			size="5"
			type="number"
			min="1"
			max="10000"
			name="<?php 
echo \esc_attr($name);
?>[time_value]"
			placeholder="1"

			value="<?php 
echo (int) $time_value;
?>"
		/>
		</span>
		<span>
		<select <?php 
echo $disabled ? 'disabled="disabled"' : '';
?> name="<?php 
echo \esc_attr($name);
?>[time_period]" style="width: 200px !important; line-height: 2;">
			<?php 
foreach ($periods as $period_value => $period_label) {
    echo '<option value="' . \esc_attr($period_value) . '" ' . \selected($time_period, $period_value, \false) . '>' . \esc_html($period_label) . '</option>';
}
?>
		</select>
		</span>
		<span>
		<?php 
\esc_html_e('after placing the order.', 'flexible-refund-and-return-order-for-woocommerce');
?>
		</span>
	</td>
</tr>
<?php 
