<?php

namespace FRFreeVendor;

/**
 * @template form-builder.php
 */
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\FormBuilder;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Plugin;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\FormBuilderTab;
?>
<table class="form-builder-table">
	<tbody>
	<tr valign="top">
		<td style="width: 30%">
			<div class="form-builder-metabox" id="form-builder-field-selector">
				<header><h3><?php 
\esc_html_e('Add new field', 'flexible-refund-and-return-order-for-woocommerce');
?></h3></header>
				<section class="form-builder-field-selector-wrapper">
					<div class="default-fields">
						<div class="field-row">
							<input type="hidden" id="fb-field-type"/>
							<label for="fb-field-name"><?php 
\esc_html_e('Field type', 'flexible-refund-and-return-order-for-woocommerce');
?> <span class="req">*</span><span
									class="error_msg error_type"><?php 
\esc_html_e('&darr; select field type &darr;', 'flexible-refund-and-return-order-for-woocommerce');
?></span></label>
							<ul class="form-builder-field-items">
								<?php 
foreach (\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\FormBuilder::buttons_field() as $button_key => $button) {
    ?>
									<?php 
    $is_need = !$button['free'] ? 'yes' : 'no';
    ?>
									<li>
										<a data-type="<?php 
    echo \esc_attr($button_key);
    ?>" data-pro="<?php 
    echo \esc_attr($is_need);
    ?>" href="#" class="field-<?php 
    echo \esc_attr($button_key);
    ?>">
											<span class="icon"></span>
											<span class="label"><?php 
    echo \esc_html($button['label']);
    ?></span>
											<?php 
    if ($is_need === 'yes') {
        ?>
												<span class="pro-button">PRO</span>
											<?php 
    }
    ?>
										</a>
									</li>
								<?php 
}
?>
							</ul>
						</div>
						<div style="display:none;" class="fb-field-pro">
							<?php 
\esc_html_e('This field is available in the PRO version.', 'flexible-refund-and-return-order-for-woocommerce');
?><br>
							<a href="<?php 
echo \esc_url(\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Plugin::get_url_to_pro());
?>" target="_blank">
								<?php 
\esc_html_e('Upgrade to PRO', 'flexible-refund-and-return-order-for-woocommerce');
?>
							</a>
						</div>
						<div class="fb-field-wrapper">
							<div class="field-row">
								<label for="fb-field-label"><?php 
\esc_html_e('Label', 'flexible-refund-and-return-order-for-woocommerce');
?> <span class="req">*</span><span
										class="error_msg error_label"><?php 
\esc_html_e('&darr; fill the field &darr;', 'flexible-refund-and-return-order-for-woocommerce');
?></span></label>
								<div class="field"><input type="text" class="regular-text" id="fb-field-label" placeholder="<?php 
\esc_attr_e('Customer Name', 'flexible-refund-and-return-order-for-woocommerce');
?>"/></div>
							</div>
							<div class="field-row">
								<label for="fb-field-name"><?php 
\esc_html_e('Name', 'flexible-refund-and-return-order-for-woocommerce');
?> <span class="req">*</span><span
										class="error_msg error_name"><?php 
\esc_html_e('&darr; fill the field &darr;', 'flexible-refund-and-return-order-for-woocommerce');
?></span></label>
								<div class="field"><input type="text" class="regular-text" id="fb-field-name" placeholder="customer_name"/></div>
							</div>
							<ul class="validation-msg" style="display: none;"></ul>
							<div class="field-row">
								<button type="button" class="button-primary" name="fr-fb-add-field"><?php 
\esc_attr_e('Add field', 'flexible-refund-and-return-order-for-woocommerce');
?></button>
							</div>
						</div>

					</div>
				</section>
			</div>
		</td>
		<td style="padding-left: 2em;" valign="top">
			<div class="form-builder-metabox" id="form_builder_selected_fields">
				<header><h3><?php 
\esc_html_e('Edit form', 'flexible-refund-and-return-order-for-woocommerce');
?></h3></header>
				<section>
					<div class="form-builder-selected-fields-wrapper">
						<input type="hidden" value="" name="<?php 
echo \esc_attr(\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\FormBuilderTab::SETTING_PREFIX . 'form_builder');
?>"/>
						<?php 
$fields = $field['value'] ?? [];
if (!empty($fields)) {
    foreach ($fields as $name => $field) {
        $label = $field['label'] ?? '';
        $html = $field['html'] ?? '';
        $enable = $field['enable'] ?? 0;
        $required = $field['required'] ?? 0;
        $type = $field['type'] ?? 'text';
        $options = $field['options'] ?? [];
        $placeholder = $field['placeholder'] ?? '';
        $css = $field['css'] ?? '';
        $description = $field['description'] ?? '';
        $maxlength = $field['maxlength'] ?? '';
        $minlength = $field['minlength'] ?? '';
        $field_name = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Settings\Tabs\FormBuilderTab::SETTING_PREFIX . 'form_builder[' . $name . ']';
        require __DIR__ . '/form-builder-field.php';
    }
}
?>
					</div>
					<div class="field-row" id="field-row-button"></div>
				</section>
			</div>

		</td>
	</tr>
	</tbody>
</table>

<?php 
