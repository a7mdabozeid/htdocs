<?php

namespace FRFreeVendor;

/**
 * @template form-builder-fields.php
 */
$field_name = $field_name ?? '';
$label = $label ?? '';
$html = $html ?? '';
$type = $type ?? '';
$description = $description ?? '';
$name = $name ?? '';
$enable = $enable ?? '';
$required = $required ?? '';
$minlength = $minlength ?? '';
$maxlength = $maxlength ?? '';
$placeholder = $placeholder ?? '';
$css = $css ?? '';
?>
<div class="fr-fb-field fb-field-wrapper">
	<div class="fr-fb-header">
		<div class="sortable icon"></div>
		<div class="label"><?php 
echo \esc_html(\wp_strip_all_tags($label));
?></div>
		<div class="type"><?php 
echo \esc_html($type);
?></div>
		<div class="remove fr-fb-remove-field icon"></div>
		<div class="collapse icon"></div>
	</div>
	<div class="fr-fb-body">
		<?php 
if ($type === 'html') {
    ?>
			<div class="fr-fb-body-tab general-tab">
				<p>
					<label>
						<span><?php 
    \esc_html_e('Label', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span>
						<input type="text" class="fr-fb-field-label regular-text" value="<?php 
    echo \sanitize_text_field(\strip_tags($label));
    ?>" name="<?php 
    echo \esc_attr($field_name);
    ?>[label]"/>
					</label>
				</p>
				<p>
					<label>
						<span><?php 
    \esc_html_e('HTML', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span>
						<textarea class="regular-text" style="width: 100%; min-height: 300px;" name="<?php 
    echo \esc_attr($field_name);
    ?>[html]"><?php 
    echo $html;
    ?></textarea>
					</label>
					<input type="hidden" value="<?php 
    echo \sanitize_text_field($type);
    ?>" name="<?php 
    echo \esc_attr($field_name);
    ?>[type]"/>
				</p>
				<p>
					<label>
						<input <?php 
    \checked((int) $enable, 1);
    ?> type="checkbox" value="1" name="<?php 
    echo \esc_attr(\esc_attr($field_name));
    ?>[enable]"/> <?php 
    \esc_html_e('Enable', 'flexible-refund-and-return-order-for-woocommerce');
    ?>
					</label>
				</p>
			</div>
		<?php 
} else {
    ?>
			<ul class="fr-fb-body-menu">
				<li><span data-tab="general"><?php 
    \esc_html_e('General', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span></li>
				<li><span data-tab="advanced"><?php 
    \esc_html_e('Advanced', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span></li>
				<li><span data-tab="appearance"><?php 
    \esc_html_e('Appearance', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span></li>
			</ul>
			<div class="fr-fb-body-tab general-tab">
				<p>
					<label>
						<span><?php 
    \esc_html_e('Label', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span>
						<input type="text" class="fr-fb-field-label regular-text" value="<?php 
    echo \sanitize_text_field($label);
    ?>" name="<?php 
    echo \esc_attr($field_name);
    ?>[label]"/>
					</label>
				</p>
				<p>
					<label>
						<span><?php 
    \esc_html_e('Name', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span>
						<input type="text" disabled="disabled" value="<?php 
    echo \sanitize_text_field($name);
    ?>" class="regular-text" name="<?php 
    echo \esc_attr($field_name);
    ?>[name]"/>
						<input type="hidden" value="<?php 
    echo \sanitize_text_field($type);
    ?>" name="<?php 
    echo \esc_attr($field_name);
    ?>[type]"/>
					</label>
				</p>
				<p>
					<label>
						<input <?php 
    \checked((int) $enable, 1);
    ?> type="checkbox" value="1" name="<?php 
    echo \esc_attr($field_name);
    ?>[enable]"/> <?php 
    \esc_html_e('Enable', 'flexible-refund-and-return-order-for-woocommerce');
    ?>
					</label>
				</p>
				<p>
					<label>
						<input <?php 
    \checked((int) $required, 1);
    ?> type="checkbox" value="1" name="<?php 
    echo \esc_attr($field_name);
    ?>[required]"/> <?php 
    \esc_html_e('Required', 'flexible-refund-and-return-order-for-woocommerce');
    ?>
					</label>
				</p>
				<?php 
    if ($type === 'multiselect' || $type === 'select' || $type === 'radio' || $type === 'checkbox') {
        ?>
					<div class="tab-wrapper tab_options">
						<div><label><?php 
        \esc_html_e('Options', 'flexible-refund-and-return-order-for-woocommerce');
        ?></label></div>
						<div class="option-wrapper">
							<?php 
        if (empty($options)) {
            $options[] = '';
        }
        ?>
							<?php 
        foreach ($options as $option) {
            ?>
								<p class="option-field">
									<label class="option-label">
										<?php 
            \esc_html_e('Value', 'flexible-refund-and-return-order-for-woocommerce');
            ?>
										<input class="option-value" type="text" name="<?php 
            echo \esc_attr($field_name);
            ?>[options][]" value="<?php 
            echo \esc_attr($option);
            ?>" size="16"/>
										<a class="add_row" href="#" data-name="<?php 
            echo \esc_attr($name);
            ?>"><span class="dashicons dashicons-insert"></span></a>
										<a class="remove_row" href="#"><span class="dashicons dashicons-remove"></span></a>
									</label>
								</p>
							<?php 
        }
        ?>
						</div>
					</div>
				<?php 
    }
    ?>
			</div><!-- end general-tab -->
			<div class="fr-fb-body-tab advanced-tab">
				<p>
					<label>
						<span><?php 
    \esc_html_e('Description', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span>
						<textarea class="fr-fb-field-description regular-text" name="<?php 
    echo \esc_attr($field_name);
    ?>[description]"><?php 
    echo \sanitize_textarea_field($description);
    ?></textarea>
					</label>
				</p>
				<p>
					<label>
						<span><?php 
    \esc_html_e('Min length', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span>
						<input type="number" min="0" class="fr-fb-field-minlength regular-text" value="<?php 
    echo \sanitize_text_field($minlength);
    ?>" name="<?php 
    echo \esc_attr($field_name);
    ?>[minlength]"/>
					</label>
				</p>
				<p>
					<label>
						<span><?php 
    \esc_html_e('Max length', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span>
						<input type="number" min="0" class="fr-fb-field-maxlength regular-text" value="<?php 
    echo \sanitize_text_field($maxlength);
    ?>" name="<?php 
    echo \esc_attr($field_name);
    ?>[maxlength]"/>
					</label>
				</p>

			</div><!-- end appearance-tab -->
			<div class="fr-fb-body-tab appearance-tab">
				<p>
					<label>
						<span><?php 
    \esc_html_e('Placeholder', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span>
						<input type="text" class="fr-fb-field-placeholder regular-text" value="<?php 
    echo \sanitize_text_field($placeholder);
    ?>" name="<?php 
    echo \esc_attr($field_name);
    ?>[placeholder]"/>
					</label>
				</p>
				<p>
					<label>
						<span><?php 
    \esc_html_e('CSS class', 'flexible-refund-and-return-order-for-woocommerce');
    ?></span>
						<input type="text" class="fr-fb-field-css regular-text" value="<?php 
    echo \sanitize_text_field($css);
    ?>" name="<?php 
    echo \esc_attr($field_name);
    ?>[css]"/>
					</label>
				</p>
			</div><!-- end appearance-tab -->
		<?php 
}
?>
	</div><!-- end fr-fb-body -->

</div><!-- end fr-fb-field -->
<?php 
