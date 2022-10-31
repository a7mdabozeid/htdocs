/* eslint-disable func-names,no-var,prefer-reflect,prefer-arrow-callback */
(function () {
    var win = window; // eslint-disable-line no-undef
    var FR = win.FileReader;
    var doc = win.document;
    var kjua = win.kjua;

    var gui_val_pairs = [
        ['size', 'px'],
        ['minversion', ''],
        ['quiet', ' modules'],
        ['rounded', '%'],
        ['msize', '%'],
        ['mposx', '%'],
        ['mposy', '%']
    ];

    function el_by_id(id) {
        return doc.getElementById(id);
    }

    function val_by_id(id) {
        var el = el_by_id(id);
        return el && el.value;
    }

    function int_by_id(id) {
        return parseInt(val_by_id(id), 10);
    }

    function on_event(el, type, fn) {
        el.addEventListener(type, fn);
    }

    function on_ready(fn) {
        on_event(doc, 'DOMContentLoaded', fn);
    }

    function for_each(list, fn) {
        Array.prototype.forEach.call(list, fn);
    }

    function all(query, fn) {
        var els = doc.querySelectorAll(query);
        if (fn) {
            for_each(els, fn);
        }
        return els;
    }

    function update_gui() {
        gui_val_pairs.forEach(function (pair) {
            var label = all('label[for="' + pair[0] + '"]')[0];
            var text = label.innerHTML;
            label.innerHTML = text.replace(/:.*$/, ': ' + val_by_id(pair[0]) + pair[1]);
        });
    }

    function update_qrcode() {
        var options = {
            render: val_by_id('render'),
            size: int_by_id('size'),
            text: val_by_id('text'),

            crisp: val_by_id('crisp') === 'true',
            ecLevel: val_by_id('ecLevel'),
            minVersion: int_by_id('minVersion'),

            fill: val_by_id('fill'),
            back: val_by_id('back'),

            rounded: int_by_id('rounded'),
            quiet: int_by_id('quiet'),

            mode: val_by_id('mode'),

            mSize: int_by_id('mSize'),
            mPosX: int_by_id('mPosX'),
            mPosY: int_by_id('mPosY'),

            label: val_by_id('label').trim(),
            fontname: val_by_id('wooqr-fontname'),
            // fontname: 'Montserrat',
            // fontname: 'Calligraffitti',
            fontcolor: val_by_id('fontcolor'),

            image: el_by_id('wooqrimg-buffer')
        };
        //console.log(val_by_id('fontname'));
        var container = el_by_id('qr-container');
        var qrcode = kjua(options);
        for_each(container.childNodes, function (child) {
            container.removeChild(child);
        });
        if (qrcode) {
            container.appendChild(qrcode);
        }
    }

    function update() {
        // update_gui();
        update_qrcode();
    }

    function on_img_input() {
        var input = el_by_id('wooqr_upload_image');
        //console.log(input.value);
        if (input.value) {
            setTimeout(update, 250);
        }
    }

    jQuery(document).ready(function () {

        jQuery(document).on("change input","#wooqr_upload_image", function(){
            //console.log(1);
            var input = el_by_id('wooqr_upload_image');
            console.log(input.value);
            setTimeout(update, 250);
        });


    })
    function on_font_select() {
        var fontname = el_by_id('fontname');
        var text=fontname.options[fontname.selectedIndex].text;
        console.log("text: "+text);
        el_by_id('wcqrc-googleFonts-css').remove();

        var link = document.createElement('link');

        // set the attributes for link element
        link.rel = 'stylesheet';

        link.type = 'text/css';

        link.id = 'wcqrc-googleFonts-css';

        link.href = 'http://fonts.googleapis.com/css?family='+text;

        // Append link element to HTML head
        document.head.appendChild(link);

        el_by_id("wooqr-fontname").value = text;

        setTimeout(update, 250);


    }
    on_ready(function () {
        on_event(el_by_id('wooqr_upload_image'), 'change', function(){

        });
        on_event(el_by_id('fontname'), 'change', on_font_select);

        all('input, textarea, select', function (el) {
            on_event(el, 'input', update);
            on_event(el, 'change', update);
        });
        on_event(win, 'load', update);
        update();
    });



}(jQuery));

/* eslint-enable */
jQuery(document).ready(function($){

    $('#mode').change(function() {
        if (this.value === 'plain') {
            $('#label, #fontname, #fontcolor, #wooqr_upload_image').parents('tr').hide();
        } else if (this.value === 'label') {
            $('#wooqr_upload_image').parents('tr').hide();
            $('#label, #fontname, #fontcolor').parents('tr').show();
        } else if (this.value === 'image') {
            $('#label, #fontname, #fontcolor').parents('tr').hide();
            $('#wooqr_upload_image').parents('tr').show();
        }
    });


    $('#fontname').change(function() {
        setTimeout(function(){

            el = document.getElementById('wooqr-fontname');
            ev = document.createEvent('Event');
            ev.initEvent('change', true, false);
            el.dispatchEvent(ev);

        }, 500);

    });

});

