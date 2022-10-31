/* global wcqrc_product */

jQuery(document).ready(function ($) {


    window.search_wooqr_list = function() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("search-wooqr-pro");
        filter = input.value.toUpperCase();
        ul = document.getElementById("wooqr_pro_grid");
        li = ul.getElementsByClassName("product-grid-item");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByClassName("bulk_product-qr-code-title")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
        return true
    }


    function toDataURL(src, callback, outputFormat) {
        var img = new Image();
        img.crossOrigin = "Anonymous";
        img.onload = function () {
            var canvas = document.createElement("CANVAS");
            var ctx = canvas.getContext("2d");
            var dataURL;
            canvas.height = this.height;
            canvas.width = this.width;
            ctx.drawImage(this, 0, 0);
            dataURL = canvas.toDataURL(outputFormat);
            callback(dataURL);
        };
        img.src = src;
        if (img.complete || img.complete === undefined) {
            img.src =
                "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
            img.src = src;
        }
    }

    $(document).on("click", ".print-qr", function (event) {
        var qr_img_src = jQuery(this).closest('.product_qrcode_content').find("img").attr("src");

        console.log(qr_img_src);

        var product_id = $(this).data("product_id");
        // console.log(parent_result_id);
        var post_title = jQuery("#original_post_title").val();
        // console.log(post_title);
        if(post_title === undefined){
            post_title = jQuery("#result_"+product_id+" .bulk_product-qr-code-title").html();
        }
        var post_type = $("#product-type option:selected").val()

        if(post_type === "variable") {
            let variable_title = $(this).closest(".woocommerce_variation").find(" h3 > select").find(":selected").text();
            //console.log(variable_title);
            if(variable_title !== "") {
                post_title = post_title + " - " + variable_title;
            }
        }

        var doc = new jsPDF();
        doc.setFontSize(25);
        doc.addImage(qr_img_src, "JPEG", 15, 15, 180, 180);
        var lines = doc.splitTextToSize(post_title, 160);
        doc.text(25, 210, lines);
        doc.save(product_id + ".pdf");
        console.log("QR Printed.");

    });


    $(document).on("click", ".copyshortcode", function (event) {
        var id = $(this).data('id');
        var copyText = document.getElementById("qrshortcode_"+id);
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        $(this).text("copied").css('color','green');

        setTimeout(function() {
            $("span[data-id="+id+"]").text("Copy Shortcode")
        }, 2000);


    });

    var wooqr_uploader;
    $('#wooqr_upload_button').click(function(e) {
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (wooqr_uploader) {
            wooqr_uploader.open();
            return;
        }
        //Extend the wp.media object
        wooqr_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        wooqr_uploader.on('select', function() {
            attachment = wooqr_uploader.state().get('selection').first().toJSON();
            $('#wooqr_upload_image').val(attachment.url);
            $('#wooqr_upload_image').attr("value", attachment.url);
            $('#wooqr_upload_image').trigger('change');

            //$("#wooqrimg-buffer").attr("src", attachment.url);
            toDataURL(attachment.url, function(base64Img){
                $("#wooqrimg-buffer").attr("src", base64Img);


            });
        });
        //Open the uploader dialog
        wooqr_uploader.open();
    });


});