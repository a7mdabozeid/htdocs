'use strict';

// console.log(wooqr_options['qr_options']);
function genqrcode(permalink,id) {

    wooqr_options['qr_options'].text = permalink;
    var wooqr_img = document.createElement('img');
    wooqr_img.src =
        wooqr_options['qr_options']['image'];

    wooqr_options['qr_options']['image'] = wooqr_img;

    document.getElementById('product_qrcode_'+id).appendChild(kjua(wooqr_options['qr_options']));
}