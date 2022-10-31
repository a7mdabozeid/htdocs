<?php

namespace FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Emails;

use Exception;
use WC_Email;
use WC_Order;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\MyAccount;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
use FRFreeVendor\WPDesk\Persistence\Adapter\WordPress\WordpressOptionsContainer;
use FRFreeVendor\WPDesk\Persistence\PersistentContainer;
use FRFreeVendor\WPDesk\View\Renderer\Renderer;
use FRFreeVendor\WPDesk\View\Renderer\SimplePhpRenderer;
use FRFreeVendor\WPDesk\View\Resolver\ChainResolver;
use FRFreeVendor\WPDesk\View\Resolver\DirResolver;
abstract class AbstractRefundEmail extends \WC_Email
{
    const ID = 'unknown';
    public function __construct()
    {
        $this->id = static::ID;
        $this->customer_email = \true;
        $this->template_base = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::get_template_path();
        $this->template_html = 'emails/fr-refund.php';
        $this->template_plain = 'emails/plain/fr-refund.php';
        parent::__construct();
        $this->placeholders = ['{shop_title}' => '', '{shop_address}' => '', '{shop_url}' => '', '{shop_email}' => '', '{refund_url}' => '', '{refund_note}' => '', '{refund_order_table}' => '', '{customer_name}' => '', '{order_id}' => '', '{order_date}' => '', '{order_number}' => '', '{order_payment_method}' => '', '{coupon_code}' => ''];
        $this->append_wp_editor_to_fields();
    }
    /**
     * Set renderer.
     */
    protected function get_renderer() : \FRFreeVendor\WPDesk\View\Renderer\Renderer
    {
        $resolver = new \FRFreeVendor\WPDesk\View\Resolver\ChainResolver();
        $resolver->appendResolver(new \FRFreeVendor\WPDesk\View\Resolver\DirResolver(\get_stylesheet_directory() . '/flexible-refunds-pro/'));
        $resolver->appendResolver(new \FRFreeVendor\WPDesk\View\Resolver\DirResolver(\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::get_library_path() . 'src/Views'));
        return new \FRFreeVendor\WPDesk\View\Renderer\SimplePhpRenderer($resolver);
    }
    protected function get_settings() : \FRFreeVendor\WPDesk\Persistence\PersistentContainer
    {
        return new \FRFreeVendor\WPDesk\Persistence\Adapter\WordPress\WordpressOptionsContainer(\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration::SETTING_PREFIX);
    }
    /**
     * @param mixed $order_id Order ID is passed as string.
     *
     * @return string
     */
    public function get_refund_table($order_id) : string
    {
        $order = \wc_get_order($order_id);
        if ($order) {
            return $this->get_renderer()->render('myaccount/refund-table', ['show_shipping' => $this->get_settings()->get_fallback('refund_enable_shipment', 'no'), 'order' => $order, 'fields' => new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer()]);
        }
        return '';
    }
    public function trigger($order_id, $order = \false)
    {
        $this->setup_locale();
        if ($order_id && !\is_a($order, 'WC_Order')) {
            $order = \wc_get_order($order_id);
        }
        if (\is_a($order, 'WC_Order')) {
            $this->object = $order;
            $coupon_codes = $this->object->get_meta('fr_coupon_codes');
            $this->recipient = $this->object->get_billing_email();
            $this->placeholders['{shop_title}'] = $this->get_blogname();
            $this->placeholders['{shop_address}'] = \wp_parse_url(\home_url(), \PHP_URL_HOST);
            $this->placeholders['{shop_url}'] = \wp_parse_url(\home_url(), \PHP_URL_HOST);
            $this->placeholders['{shop_email}'] = \get_option('admin_email');
            $this->placeholders['{refund_url}'] = \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\MyAccount::get_refund_url($this->object);
            $this->placeholders['{refund_note}'] = $this->object->get_meta('fr_refund_request_note');
            $this->placeholders['{refund_order_table}'] = $this->get_refund_table($order_id);
            $this->placeholders['{customer_name}'] = $this->object->get_formatted_billing_full_name();
            $this->placeholders['{order_id}'] = $this->object->get_id();
            $this->placeholders['{order_date}'] = \wc_format_datetime($this->object->get_date_created());
            $this->placeholders['{order_number}'] = $this->object->get_order_number();
            $this->placeholders['{order_payment_method}'] = $this->object->get_payment_method_title();
            $this->placeholders['{coupon_code}'] = \is_array($coupon_codes) ? \implode(', ', $coupon_codes) : \esc_attr($coupon_codes);
        }
        if ($this->is_enabled() && $this->get_recipient()) {
            \error_log('email content');
            \error_log($this->get_content());
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
        $this->restore_locale();
    }
    public function get_content_html() : string
    {
        return \wc_get_template_html($this->template_html, ['order' => $this->object, 'email_heading' => $this->get_heading(), 'additional_content' => $this->get_additional_content(), 'sent_to_admin' => \false, 'plain_text' => \false, 'email' => $this], '', $this->template_base);
    }
    public function get_content_plain() : string
    {
        return \wc_get_template_html($this->template_plain, ['order' => $this->object, 'email_heading' => $this->get_heading(), 'additional_content' => $this->get_additional_content(), 'sent_to_admin' => \true, 'plain_text' => \true, 'email' => $this], '', $this->template_base);
    }
    private function append_wp_editor_to_fields()
    {
        $this->form_fields['additional_content']['type'] = 'wysiwyg';
        // Translators: %s placeholders.
        $this->form_fields['additional_content']['description'] = \sprintf(\esc_html__('Available placeholders: %s', 'flexible-refund-and-return-order-for-woocommerce'), \implode(', ', \array_keys($this->placeholders)));
        $this->form_fields['additional_content']['desc_tip'] = \false;
    }
    public function generate_wysiwyg_html($key, $data)
    {
        $field_key = $this->get_field_key($key);
        $defaults = ['title' => '', 'disabled' => \false, 'class' => '', 'css' => '', 'placeholder' => '', 'type' => 'text', 'desc_tip' => \false, 'description' => '', 'custom_attributes' => []];
        $data = \wp_parse_args($data, $defaults);
        \ob_start();
        ?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php 
        echo \esc_attr($field_key);
        ?>"><?php 
        echo \wp_kses_post($data['title']);
        echo $this->get_tooltip_html($data);
        // WPCS: XSS ok.
        ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php 
        echo \wp_kses_post($data['title']);
        ?></span></legend>
					<?php 
        $args = ['tinymce' => ['toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo', 'toolbar2' => '', 'toolbar3' => '']];
        \wp_editor(\wp_kses($this->get_option($key), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\EmailHelper::allowed_tags()), $field_key, $args);
        ?>
					<?php 
        echo $this->get_description_html($data);
        // WPCS: XSS ok.
        ?>
				</fieldset>
			</td>
		</tr>
		<?php 
        return \ob_get_clean();
    }
}
