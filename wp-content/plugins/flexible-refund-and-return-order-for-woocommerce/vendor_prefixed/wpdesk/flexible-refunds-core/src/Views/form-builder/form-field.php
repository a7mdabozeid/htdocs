<?php

namespace FRFreeVendor;

/**
 * @var \WPDesk\Forms\Field            $field
 * @var \WPDesk\View\Renderer\Renderer $renderer
 * @var string                                      $name_prefix
 * @var string                                      $value
 * @var string                                      $template_name Real field template.
 */
?>
<p class="field-row field-type-<?php 
echo \esc_attr($field->get_type());
?>">
	<label>
		<?php 
echo \esc_html($field->get_label());
?>
		<?php 
echo \esc_html($field->get_type());
?>
	</label>
	<?php 
$renderer->output_render($template_name, ['field' => $field, 'renderer' => $renderer, 'name_prefix' => $name_prefix, 'value' => $value]);
?>
</p>
<?php 
