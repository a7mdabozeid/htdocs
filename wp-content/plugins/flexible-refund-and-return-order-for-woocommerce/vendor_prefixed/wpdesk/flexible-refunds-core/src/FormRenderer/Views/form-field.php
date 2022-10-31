<?php

namespace FRFreeVendor;

/**
 * @var \WPDesk\Forms\Field            $field
 * @var \WPDesk\View\Renderer\Renderer $renderer
 * @var string                         $name_prefix
 * @var string                         $value
 * @var string                         $template_name Real field template.
 */
if ($field->get_type() !== 'html') {
    $required = $field->is_attribute_set('data-required') && $field->get_attribute('data-required') === 'required';
    ?>
	<div class="field-row field-type-<?php 
    echo \esc_attr($field->get_type());
    ?> <?php 
    echo $required ? 'field-required' : '';
    ?>">
		<label>
			<?php 
    echo \esc_html($field->get_label());
    ?>
			<?php 
    if ($required) {
        ?>
				<span class="fr-required-field required">*</span>
			<?php 
    }
    ?>
		</label>
		<div class="fr-required-field-notice"></div>
		<?php 
    $renderer->output_render($template_name, ['field' => $field, 'renderer' => $renderer, 'name_prefix' => $name_prefix, 'value' => $value]);
    ?>
		<?php 
    if ($field->get_description()) {
        ?>
			<span style="display: block; color: #999;" class="fr-field-description"><?php 
        echo \wp_kses_post($field->get_description());
        ?></span>
		<?php 
    }
    ?>
	</div>
<?php 
} else {
    ?>
	<?php 
    $renderer->output_render($template_name, ['field' => $field, 'renderer' => $renderer, 'name_prefix' => $name_prefix, 'value' => $value]);
}
