<?php

namespace AGCFW\Objects;

/**
 * Model that houses the data model of an advanced gift card email.
 *
 * @since 1.0
 */
class Email extends \WC_Email
{
    /**
     * Class constructor.
     *
     * @since 1.0
     * @access public
     */
    public function __construct()
    {
        $this->id             = 'advanced_gift_card';
        $this->customer_email = true;
        $this->title          = __('Advanced Gift Card', 'advanced-gift-cards-for-woocommerce');
        $this->description    = __('Advanced gift card emails are sent to the set recipient after the order has been paid.', 'advanced-gift-cards-for-woocommerce');
        $this->template_html  = 'emails/email-advanced-gift-card.php';
        $this->template_plain = 'emails/plain/email-advanced-gift-card.php';
        $this->placeholders   = array(
            '{gift_card_code}'   => '',
            '{gift_card_value}'  => '',
            '{gift_card_expire}' => '',
        );

        add_action('agcfw_after_create_gift_card_for_order', array($this, 'trigger'), 10, 3);

        parent::__construct();
    }

    /**
     * Get email subject.
     *
     * @since 1.0
     * @access public
     *
     * @return string
     */
    public function get_default_subject()
    {
        return __('[{site_title}]: You have received a gift card', 'advanced-gift-cards-for-woocommerce');
    }

    /**
     * Get email subject.
     *
     * @since 1.0
     * @access public
     *
     * @return string
     */
    public function get_default_heading()
    {
        return "{site_title}";
    }

    /**
     * Default content to show below main email content.
     *
     * @since 1.0
     * @access public
     *
     * @return string
     */
    public function get_default_additional_content()
    {
        return '';
    }

    /**
     * Set gift card instance.
     *
     * @since 1.0
     * @access public
     *
     * @param Advanced_Gift_card $gift_card Gift card object.
     */
    public function set_gift_card(Advanced_Gift_card $gift_card)
    {
        $this->gift_card = $gift_card;

        $datetime_format = get_option('date_format', 'F j, Y') . ' ' . get_option('time_format', 'g:i a');
        $date_expire     = $gift_card->get_date('date_expire');

        // update placeholders.
        $this->placeholders['{gift_card_code}']   = $gift_card->get_code();
        $this->placeholders['{gift_card_value}']  = \ACFWF()->Helper_Functions->api_wc_price($gift_card->get_value());
        $this->placeholders['{gift_card_expire}'] = $date_expire ? $gift_card->get_date('date_expire')->format($datetime_format) : '';
    }

    /**
     * Set design image.
     *
     * @since 1.0
     * @access public
     *
     * @param string $image_src Image src.
     */
    public function set_design_image($image_src)
    {
        $this->design_image = $image_src;
    }

    /**
     * Set message.
     *
     * @since 1.0
     * @access public
     *
     * @param string $message Message text.
     */
    public function set_message($message)
    {
        $this->message = $message;
    }

    /**
     * Trigger the sending of this email.
     *
     * @since 1.0
     * @access public
     *
     * @param Advanced_Gift_Card    $gift_card Gift card object.
     * @param WC_Order_Item_Product $item      Product order item object.
     * @param WC_Order              $order     Order object.
     */
    public function trigger($gift_card, $item, $order)
    {
        do_action('agcfw_before_send_gift_card_email', $gift_card, $item, $order);

        $this->setup_locale();
        $this->set_gift_card($gift_card);

        $this->object       = $order;
        $this->design_image = $item->get_product()->get_gift_card_design_image_src();
        $this->message      = '';

        $email_already_sent = $item->get_meta(\AGCFW()->Plugin_Constants->EMAIL_ALREADY_SENT_META);

        /**
         * Controls if gift card emails can be resend multiple times.
         */
        // if ('yes' === $email_already_sent && !apply_filters('agcfw_allow_resend_gift_card_email', true)) {
        //     return;
        // }

        // set recipient to friend's email if the gift card purchased was giftable.
        if ('friend' === $item->get_meta(\AGCfW()->Plugin_Constants->GIFT_CARD_SEND_TO_META)) {
            $recipient     = $item->get_meta(\AGCfW()->Plugin_Constants->GIFT_CARD_RECIPIENT_EMAIL_META);
            $this->message = $item->get_meta(\AGCfW()->Plugin_Constants->GIFT_CARD_SHORT_MESSAGE_META);
        } else {
            $recipient = false;
        }

        // default to customer's billing email if recipient email is not available.
        $this->recipient = $recipient ? $recipient : $order->get_billing_email();

        if ($this->is_enabled() && $this->get_recipient()) {

            $this->send(
                $this->get_recipient(),
                $this->get_subject() . sprintf('(%s)', $gift_card->get_code()),
                $this->get_content(),
                $this->get_headers(),
                $this->get_attachments()
            );

            $item->update_meta_data(\AGCFW()->Plugin_Constants->EMAIL_ALREADY_SENT_META, 'yes');
            $item->save();
        }

        $this->restore_locale();

        do_action('agcfw_after_send_gift_card_email', $gift_card, $item, $order);
    }

    /**
     * Override setup locale function to remove customer email check.
     *
     * @since 1.0
     * @access public
     */
    public function setup_locale()
    {
        if (apply_filters('woocommerce_email_setup_locale', true)) {
            wc_switch_to_site_locale();
        }
    }

    /**
     * Override restore locale function to remove customer email check.
     *
     * @since 1.0
     * @access public
     */
    public function restore_locale()
    {
        if (apply_filters('woocommerce_email_restore_locale', true)) {
            wc_restore_locale();
        }
    }

    /**
     * Get content html.
     *
     * @since 1.0
     * @access public
     */
    public function get_content_html()
    {
        ob_start();
        \AGCFW()->Helper_Functions->load_template(
            $this->template_html,
            array(
                'gift_card'          => $this->gift_card,
                'design_image'       => $this->design_image,
                'message'            => $this->message,
                'order'              => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'email'              => $this,
            )
        );
        return ob_get_clean();
    }

    /**
     * Get content plain
     *
     * @since 1.0
     * @access public
     */
    public function get_content_plain()
    {
        ob_start();
        \AGCFW()->Helper_Functions->load_template(
            $this->template_plain,
            array(
                'gift_card'          => $this->gift_card,
                'design_image'       => $this->design_image,
                'message'            => $this->message,
                'order'              => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'email'              => $this,
            )
        );
        return ob_get_clean();
    }

    /**
     * Initialize settings form fields.
     *
     * @since 1.0
     * @access public
     */
    public function init_form_fields()
    {
        $placeholder_text  = sprintf(__('Available placeholders: %s', 'woocommerce'), '<code>' . implode('</code>, <code>', array_keys($this->placeholders)) . '</code>');
        $this->form_fields = array(
            'enabled'            => array(
                'title'   => __('Enable/Disable', 'advanced-gift-cards-for-woocommerce'),
                'type'    => 'checkbox',
                'label'   => __('Enable this email notification', 'advanced-gift-cards-for-woocommerce'),
                'default' => 'yes',
            ),
            'subject'            => array(
                'title'       => __('Subject', 'advanced-gift-cards-for-woocommerce'),
                'type'        => 'text',
                'desc_tip'    => true,
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_subject(),
                'default'     => '',
            ),
            'heading'            => array(
                'title'       => __('Email heading', 'advanced-gift-cards-for-woocommerce'),
                'type'        => 'text',
                'desc_tip'    => true,
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_heading(),
                'default'     => '',
            ),
            'additional_content' => array(
                'title'       => __('Additional content', 'advanced-gift-cards-for-woocommerce'),
                'description' => __('Text to appear below the main email content.', 'advanced-gift-cards-for-woocommerce') . ' ' . $placeholder_text,
                'css'         => 'width:400px; height: 75px;',
                'placeholder' => __('N/A', 'advanced-gift-cards-for-woocommerce'),
                'type'        => 'textarea',
                'default'     => $this->get_default_additional_content(),
                'desc_tip'    => true,
            ),
            'email_type'         => array(
                'title'       => __('Email type', 'advanced-gift-cards-for-woocommerce'),
                'type'        => 'select',
                'description' => __('Choose which format of email to send.', 'advanced-gift-cards-for-woocommerce'),
                'default'     => 'html',
                'class'       => 'email_type wc-enhanced-select',
                'options'     => $this->get_email_type_options(),
                'desc_tip'    => true,
            ),
        );
    }
}
