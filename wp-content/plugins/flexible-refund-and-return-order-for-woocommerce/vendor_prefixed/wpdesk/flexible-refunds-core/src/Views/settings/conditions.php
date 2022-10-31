<?php

namespace FRFreeVendor;

/**
 * @template conditions.php
 */
/**
 * @var WPDesk\Library\FlexibleRefundsCore\Settings\ConditionSettingFactory $custom_fields
 */
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Plugin;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
$condition_types = $field['value']['condition_type'] ?? [];
?>
<tr valign="top">
	<th class="titledesc" scope="row">
		<?php 
$types_options = ['user_roles' => \esc_html__('User roles', 'flexible-refund-and-return-order-for-woocommerce'), 'order_statuses' => \esc_html__('Order statuses', 'flexible-refund-and-return-order-for-woocommerce'), 'product_cats' => \esc_html__('Product categories', 'flexible-refund-and-return-order-for-woocommerce'), 'products' => \esc_html__('Products', 'flexible-refund-and-return-order-for-woocommerce'), 'payment_methods' => \esc_html__('Payment methods', 'flexible-refund-and-return-order-for-woocommerce')];
$operator_options = ['is' => \esc_html__('is', 'flexible-refund-and-return-order-for-woocommerce'), 'is_not' => \esc_html__('is not', 'flexible-refund-and-return-order-for-woocommerce')];
?>
		<?php 
\esc_html_e('Button visibility', 'flexible-refund-and-return-order-for-woocommerce');
?>
	</th>
	<td>
		<table class="flexible-refund-conditions widefat" style="width: 860px">
			<thead>
			<tr>
				<td colspan="3"></td>
				<td colspan="2" class="condition-actions">
					<?php 
\esc_html_e('Add rule &rarr;', 'flexible-refund-and-return-order-for-woocommerce');
?>
					<a class="<?php 
echo \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Plugin::add_row_class();
?>" href="#"><span class="dashicons dashicons-insert"></span></span></a>
					<input type="hidden" name="fr_refund_refund_conditions_setting"/>
				</td>
			</tr>
			</thead>
			<tbody>
			<?php 
if (!\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()) {
    ?>
				<tr>
					<td colspan="5">
						<?php 
    $pro_url = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Plugin::get_url_to_pro();
    \printf(\esc_html__('To unlock this option, purchase the %1$sPRO%2$s version &rarr;', 'flexible-refund-and-return-order-for-woocommerce'), '<a href="' . \esc_url($pro_url) . '">', '</a>');
    ?>
					</td>
				</tr>
			<?php 
}
?>
			<?php 
if (\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::is_super()) {
    $condition_key = 0;
    if (!empty($condition_types)) {
        foreach ($condition_types as $condition_key => $condition_type) {
            $condition_operator = $field['value']['condition_operator'][$condition_key] ?? 'is';
            $condition_value = $field['value']['condition_values'][$condition_key][$condition_type] ?? [];
            ?>
						<tr data-index="<?php 
            echo $condition_key;
            ?>">
							<td class="label-col"><?php 
            \esc_html_e('Enable if', 'flexible-refund-and-return-order-for-woocommerce');
            ?></td>
							<td class="type-col">
								<select class="condition-type" name="fr_refund_refund_conditions_setting[condition_type][<?php 
            echo \esc_attr($condition_key);
            ?>]" style="width: 200px !important; line-height: 2;">
									<?php 
            foreach ($types_options as $type_key => $type_label) {
                ?>
										<option <?php 
                echo \selected($type_key, $condition_type);
                ?> value="<?php 
                echo \esc_attr($type_key);
                ?>"><?php 
                echo \esc_html($type_label);
                ?></option>
									<?php 
            }
            ?>
								</select>
							</td>
							<td class="condition-col">
								<select name="fr_refund_refund_conditions_setting[condition_operator][<?php 
            echo \esc_attr($condition_key);
            ?>]" style="width: 120px !important; line-height: 2;">
									<?php 
            foreach ($operator_options as $operator_key => $operator_label) {
                ?>
										<option <?php 
                echo \selected($operator_key, $condition_operator ?? '');
                ?>
											value="<?php 
                echo \esc_attr($operator_key);
                ?>"><?php 
                echo \esc_html($operator_label);
                ?></option>
									<?php 
            }
            ?>
								</select>
							</td>
							<td class="condition-type-select-wrapper">
								<?php 
            echo $custom_fields->get_field($condition_type, $condition_key, $condition_value);
            ?>
							</td>
							<td class="actions">
								<a class="remove_row" href="#"><span class="dashicons dashicons-remove"></span></a>
								<span class="and-row"><?php 
            \esc_html_e('and', 'flexible-refund-and-return-order-for-woocommerce');
            ?></span>
							</td>
						</tr>
						<?php 
        }
    }
}
?>
			</tbody>
		</table>
	</td>
</tr>
<script type="text/template" id="products_select">
	<?php 
echo $custom_fields->get_products_select(['index' => '__index__']);
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</script>
<script type="text/template" id="product_cats_select">
	<?php 
echo $custom_fields->get_product_cats_select(['index' => '__index__']);
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</script>
<script type="text/template" id="order_statuses_select">
	<?php 
echo $custom_fields->get_order_statuses_select(['index' => '__index__']);
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</script>
<script type="text/template" id="user_roles_select">
	<?php 
echo $custom_fields->get_user_roles_select(['index' => '__index__']);
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</script>
<script type="text/template" id="payment_methods_select">
	<?php 
echo $custom_fields->get_payment_methods_select(['index' => '__index__']);
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</script>

<script type="text/template" id="condition_row">
	<tr data-index="__index__">
		<td class="label-col"><?php 
\esc_html_e('Enable if', 'flexible-refund-and-return-order-for-woocommerce');
?></td>
		<td class="type-col">
			<select class="condition-type" name="fr_refund_refund_conditions_setting[condition_type][__index__]" style="width: 200px !important; line-height: 2;">
				<?php 
foreach ($types_options as $type_key => $type_label) {
    ?>
					<option <?php 
    echo \selected($type_key, 'order_statuses');
    ?> value="<?php 
    echo \esc_attr($type_key);
    ?>"><?php 
    echo \esc_html($type_label);
    ?></option>
				<?php 
}
?>
			</select>
		</td>
		<td class="condition-col">
			<select name="fr_refund_refund_conditions_setting[condition_operator][__index__]" style="width: 120px !important; line-height: 2;">
				<?php 
foreach ($operator_options as $operator_key => $operator_label) {
    ?>
					<option <?php 
    echo \selected($operator_key, 'is');
    ?> value="<?php 
    echo \esc_attr($operator_key);
    ?>"><?php 
    echo \esc_html($operator_label);
    ?></option>
				<?php 
}
?>
			</select>
		</td>
		<td class="condition-type-select-wrapper">
			<?php 
echo $custom_fields->get_field('order_statuses', '__index__', ['wc-completed']);
?>
		</td>
		<td class="actions">
			<a class="remove_row" href="#"><span class="dashicons dashicons-remove"></span></a>
			<span class="and-row"><?php 
\esc_html_e('and', 'flexible-refund-and-return-order-for-woocommerce');
?></span>
		</td>
	</tr>
</script>
<?php 
