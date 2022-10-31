<?php

add_action( 'admin_menu', 'woo_bulk_qr_codes', 9999 );

function woo_bulk_qr_codes() {
    add_submenu_page( 'wooqr', 'Bulk QR Codes', 'Bulk QR Codes', 'manage_options', 'woo_bulk_qr_codes', 'woo_bulk_qr_codes_callback' );
}

function woo_bulk_qr_codes_callback() {
    global $WooCommerceQrCodes;?>

    <div class="wrap">
    <div class="fixed-holder">
        <h1 class="reorder-title"><?php esc_html_e('Woo QR Code - Bulk Generator', 'wpr-reorder');?>
            <small> Manage QR codes from single screen </small>
        </h1>
    </div>
    <div id="wooqr-api">
        <div id="wpr-action-bar">

            <div id="wooqr_loader">Fetching Products...</div>
            <div class="wooqr-bulk-generate">

            </div>
            <div class="wooqr-search-wrap">
                <input type="text" onkeyup="search_wooqr_list()" name="find-wooqr-pro" id="search-wooqr-pro" placeholder="Search Product..">
            </div>


        </div>



        <div class="product-grid-container" id="wooqr_pro_grid">


            <div id="wooqr-status"></div>
            <ul id="wooqr-data"></ul>

        </div>
    <?php

}