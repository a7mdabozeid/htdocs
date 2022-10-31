
<?php

 if(!isset($order)) exit ;

$shiaka = get_option('shiaka__settings');

$vat = get_option('shiaka__settings');

?>

<!doctype html>
<html lang="<?= is_rtl() ? 'ar' : 'en' ?>" dir="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $order->get_id() ?></title>

    <style>
        .container {
            width: 750px;
            margin: 0 auto;
            background: #eee;
            padding: 21px;
            border-radius: 5px;

        }

        .inner-wrapper {
            padding: 19px;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 11px;
            font-size: 15px;
            text-align:right
        }

        .block-row-td td {
            display: block;
            padding-right: 5px;
        }


        .text-right {
            text-align: right;
        }

        img {
            max-width: 100%;
            height: auto;
            border: none;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            outline: none;
            text-decoration: none;
            text-transform: capitalize;
            vertical-align: middle;
        }

        .logo {
            width: 37px;
            height: 37px;
        }


        .info-table.border td {
            border: 0.6px solid #333;
        }

        .thead.border {
            border: 0.6px solid #333;
        }

        .table-border td, .table-border th {
            border: 1px solid;
        }

        .border {
            border: 1px solid #333;
        }
        .h tr {
            height: 47px;

        }
        td,th{
            padding-right: 5px;
        }
        td{color:black;}

    </style>
</head>
<body>
<div class="container" style="width: 750px; margin: 0 auto ; background-color: #eee;padding: 19px">
    <div class="inner-wrapper">
        <table>
            <tbody>
            <tr>
                <td style="text-align: center ; width: 193px">
                    <img class="img-responsive" src="<?= get_theme_mod('logo') ?> " alt="<?= bloginfo('name') ?>">
                </td>
                <td style="text-align: center">
                    <img src="<?= wp_upload_dir()['baseurl'] . '/order_qr/qr_order_' .$order->get_id().'.png'; ?>" alt="">
                </td>
                <td style="text-align: center">
                    <h4><?= __('VAT Nummber') ?></h4>
                    <p><?= $vat['shiaka__text_field_0'] ?></p>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="h" style="text-align: center" >
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
                            <td><?= $order->get_billing_first_name() //to check if user has account latter   ?></td>

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
                            <th><?= __('Product price after discount', 'shiaka-emails') ?></th>
                            <th><?= __('Discount', 'shiaka-emails') ?></th>
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
                                <td><?= "Dis" ?></td>
                                <td><?= "Dis" ?></td>
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
                        <tr>
                            <td><?= __('Order subtotak', 'shiaka-emails') ?></td>
                            <td><?= $order->get_subtotal() . " " . $order->get_currency() ?></td>
                        </tr>
                        <tr>
                            <td><?= __('Shipping price', 'shiaka-emails') ?></td>
                            <td><?= $order->get_shipping_total() . " " . $order->get_currency() ?></td>
                        </tr>
                        <tr>
                            <td><?= __('Vat amount', 'shiaka-emails') ?></td>
                            <td><?= $order->get_total_tax() . " " . $order->get_currency() ?></td>
                        </tr>
                        <tr>
                            <td><?= __('Total', 'shiala-emails') ?></td>
                            <td><?= $order->get_total() . " " . $order->get_currency() ?></td>
                        </tr>
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
</body>
</html>
