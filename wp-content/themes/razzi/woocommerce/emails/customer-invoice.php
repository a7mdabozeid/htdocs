<?php
/**
 * @todo siitze phone to not inlcuede ppx
 * Customer invoice email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-invoice.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$shiaka = get_option('shiaka__settings');


/**
 * Executes the e-mail header.
 *
 * @hooked WC_Emails::email_header() Output the email header
 */
//do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
    <p><?php printf(esc_html__('Hi %s,', 'woocommerce'), esc_html($order->get_billing_first_name())); ?></p>

<?php if ($order->needs_payment()) { ?>
    <p>
        <?php
        printf(
            wp_kses(
            /* translators: %1$s Site title, %2$s Order pay link */
                __('An order has been created for you on %1$s. Your invoice is below, with a link to make payment when you’re ready: %2$s', 'woocommerce'),
                array(
                    'a' => array(
                        'href' => array(),
                    ),
                )
            ),
            esc_html(get_bloginfo('name', 'display')),
            '<a href="' . esc_url($order->get_checkout_payment_url()) . '">' . esc_html__('Pay for this order', 'woocommerce') . '</a>'
        );
        ?>
    </p>

<?php } else { ?>
    <p>
        <?php
        /* translators: %s Order date */
        printf(esc_html__('Here are the details of your order placed on %s:', 'woocommerce'), esc_html(wc_format_datetime($order->get_date_created())));
        ?>
    </p>
    <?php
}

$vat = get_option('shiaka__settings');
#Frist do translation
#Second echo data and validated it before sending it
#define what are the errors needed in order to validate the data before echoing it
#Inslahh DOne
#QRCode
#define what payment method was taken and place its image you can do this by storing image paymenst links as options by payment method id
#After pluign loaded and woocommece is active get all active payment methods and there ids then store each one image link with option and option key will be payment_ID
?>

    <div class="container" style="width: 750px; margin: 0 auto ; background-color: #eee;padding: 19px">
        <div class="inner-wrapper">
            <table>
                <tbody>
                <tr>
                    <td style="text-align: center ; width: 193px">
                        <img class="img-responsive" src="<?= get_theme_mod('logo') ?> " alt="<?= bloginfo('name') ?>">
                    </td>
                    <td style="text-align: center">
                        <img src="<?php do_action('qr_code_customer_invoice', $order->get_id()); ?>" alt="">
                    </td>
                    <td style="text-align: center">
                        <h4><?= __('VAT Nummber') ?></h4>
                        <p><?= $vat['shiaka__text_field_0'] ?></p>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="h" style="text-align: center">
                <tbody>
                <tr class="border">
                    <td><?= __('Invoice Nummber', 'shiaka-addon') ?></td>
                    <td><?= $order->get_order_number() ?></td>
                    <td><?= __('Invoice Date') ?></td>
                    <td><?= $order->get_date_created()->date('Y/m/d') ?></td>
                </tr>
                </tbody>
            </table>
            <table>
                <tbody>
                <tr>
                    <th><?= __('Client Information', 'shiaka-emails') ?></th>
                    <th><?= __('Company information', 'shiaka-emails') ?></th>
                </tr>
                <tr>

                    <td class="border">
                        <table>
                            <tbody>
                            <tr>
                                <td><?= __('Name', 'shiaka-email') ?>></td>
                                <td><?= $order->get_billing_first_name() //to check if user has account latter     ?></td>

                            </tr>
                            <tr>
                                <td><?= __('Cell phone', 'shiaka-emails') ?></td>
                                <td><?= $order->get_billing_phone() ?></td>
                            </tr>
                            <tr>
                                <td><?= __('Email', 'shiaka-emails') ?></td>
                                <td><?= $order->get_billing_email() ?></td>
                            </tr>
                            <tr>
                                <td><?= __('Country', 'shiaka-emails') ?></td>
                                <td><?= $order->get_billing_country() ?></td>
                            </tr>
                            <tr>
                                <td><?= __('City', 'shiaka-emails') ?></td>
                                <td><?= $order->get_billing_city() ?></td>
                            </tr>
                            <tr>
                                <td><?= __('Province', 'shiaka-emails') ?></td>
                                <td><?= $order->get_billing_address_1() ?>></td>
                            </tr>
                            <tr>
                                <td><?= __('Address', 'shiaka-emails') ?></td>
                                <td><?= $order->get_billing_address_2() ?></td>
                            </tr>
                            <tr>
                                <td><?= __('Order nummber', 'shiaka-emails') ?></td>
                                <td><?= $order->get_order_number() ?>></td>
                            </tr>
                            <tr>
                                <td><?= __('Date created', 'shiaka-emails') ?></td>
                                <td><?= $order->get_date_created()->date('Y/m/d') ?></td>
                            <tr>
                                <td><?= __('Order status', 'shiaka-emails') ?></td>
                                <td><?= $order->get_status() ?></td>
                            </tr>

                            </tbody>

                        </table>
                    </td>
                    <td class="border" style="padding-right: 0 !important;">
                        <table style="margin-bottom: 0 ; margin-top: 13px">

                            <tr class="block-row-td">
                                <td><?= $shiaka['sh_text_field_company_name'] ?></td>
                                <td><?= $shiaka['sh_text_field_company_country'] ?></td>
                                <td><?= $shiaka['sh_text_field_company_city'] ?></td>
                                <td><?= $shiaka['sh_text_field_company_customer_care_email'] ?></td>
                                <td style="margin-bottom: 11px"><a
                                            href="<?= bloginfo('url') ?>"><?= bloginfo('name') ?></a></td>
                            </tr>

                            <tr style="background-color: #eee">

                                <td style="padding-right: 0 !important; padding-top: 11px ; background-color: #eee">
                                    <table>

                                        <tr>
                                            <td>
                                                <?= __('Payment method', 'shiaka-emails') ?>
                                            </td>
                                            <td>
                                                <?= $order->get_payment_method_title() ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= __('Shipping method', 'shiaka-emails') ?>
                                            </td>
                                            <td>
                                                <?php
                                                echo $order->get_shipping_method();
                                                ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
                <!-- Header start -->

                <tbody>


                <tr>
                    <td>
                        <table class="table-border h">
                            <tr>
                                <th><?= __('Total', 'shiaka-emails') ?></th>
                                <th><?= __('Tax', 'shiaka-emails') ?></th>
                                <th><?= __('Product price', 'shiaka-emails') ?></th>
                                <th><?= __('Quantity', 'shiaka-emails') ?></th>
                                <th><?= __('Product', 'shiaka-emails') ?></th>
                                <th><?= __('SKU', 'shiaka-emails') ?></th>
                            </tr>
                            <?php foreach ($order->get_items() as $item_id => $item) {

                                ?>
                                <tr>
                                    <td><?= $item->get_total() ?></td>
                                    <td><?= $item->get_total_tax() ?></td>
                                    <td><?= $item->get_product()->get_price() ?></td>
                                    <td><?= $item->get_quantity() ?></td>
                                    <td><?= $item->get_product()->get_name() ?></td>
                                    <td><?= $item->get_product()->get_sku() ?></td>
                                </tr>
                                <?php
                            } ?>

                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table class="table-border h">

                            <?php
                            foreach ($order->get_order_item_totals() as $key => $total) {
                                ?>
                                <tr>
                                    <th scope="row"><?php echo esc_html($total['label']); ?></th>
                                    <td><?php echo ('payment_method' === $key) ? esc_html($total['value']) : wp_kses_post($total['value']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
                                </tr>
                                <?php
                            }
                            ?>

                        </table>
                    </td>
                </tr>

                </tbody>

                <!-- Header ends -->
                <!-- Content Body start -->
                <!-- Conent Body Ends -->
                <!-- Footer start -->
                <!-- Footer Ends -->
            </table>
        </div>
    </div>

<?php
