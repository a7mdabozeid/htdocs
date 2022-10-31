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
        console.log("QR Printed.");
        var parent_result_id = jQuery(this).parents('.result').attr('id');
        var img_url = jQuery("#"+parent_result_id+" .product-qr-code-img").attr("src");
        var product_id = $(this).data("product_id");
        // console.log(parent_result_id);
        var post_title = jQuery("#original_post_title").val();
        // console.log(post_title);
        if(post_title === undefined){
            post_title = jQuery("#"+parent_result_id+" .bulk_product-qr-code-title").html();
        }
        var post_type = $("#product-type option:selected").val()
        
        if(post_type === "variable") {
            let variable_title = $(this).closest(".woocommerce_variation").find(" h3 > select").find(":selected").text();
            //console.log(variable_title);
            if(variable_title !== "") {
                post_title = post_title + " - " + variable_title;
            }
        }
        
        toDataURL(img_url, function (dataUrl) {
            // console.log('RESULT:', dataUrl)
            var doc = new jsPDF();
            doc.setFontSize(25);
            doc.addImage(dataUrl, "JPEG", 15, 15, 180, 180);
            var lines = doc.splitTextToSize(post_title, 160);
            doc.text(25, 210, lines);
            doc.save(product_id + ".pdf");
        });
    });
    
    $(document).on("click", ".wcqrc-refresh", function (event) {
        //console.log("new link clicked!");
        var product_id = $(this).data("product_id");
        var data = {
            action: "regenerate_qr_code",
            product_id: product_id
        };
        $.post(wcqrc_product.ajax_url, data, function (response) {
            if (response) {
                console.log("QR code deleted");
                $(".product-qr-code-img").hide();
            }
        });
    });
    
    $(".wcqrc-refresh").on("click", function () {
        console.log("fire");
        var product_id = $(this).data("product_id");
        var data = {
            action: "regenerate_qr_code",
            product_id: product_id
        };
        $.post(wcqrc_product.ajax_url, data, function (response) {
            if (response) {
                console.log("QR code deleted");
                $(".product-qr-code-img").hide();
            }
        });
    });
    
    // variable product generate
    $(document).on("click", ".generate-btn", function () {
        //  console.log('fire 1');
        var varid = $(this).attr("data-product_id");
        
        $.ajax({
            url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: {
                action: "variableqrgen",
                varid: varid
            },
            success: function (data) {
                if($('.product-grid-item.'+varid).hasClass('has_var')){
                    $("#result_" + varid).html(data);
                    $('.product-grid-item.'+varid).addClass('1');
                    } else {
                    $("#output_" + varid).html(data);
                }
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    // variable product delete
    
    $(document).on("click", ".delete-btn", function () {
        var varid = $(this).attr("data-product_id");
        
        $.ajax({
            url: ajaxurl,
            data: {
                action: "variableqrdel",
                varid: varid
            },
            success: function (data) {
                if($('.product-grid-item.'+varid).hasClass('has_var')){
                    $("#result_" + varid).html(data);
                    if($('.product-grid-item.'+varid).hasClass('1')){
                        $('.product-grid-item.'+varid).removeClass('1');
                    }
                    } else {
                    $("#output_" + varid).html(data);
                }
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    
    //simple product qr generate
    
    $(document).on("click", ".simple-qr-gen", function () {
        var simid = $(this).attr("data-product_id");
        $.ajax({
            url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: {
                action: "simpleqrgen",
                simid: simid
            },
            success: function (data) {
                $("#result_" + simid).html(data);
                $('.product-grid-item.'+simid).addClass('1');
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    
    // simple product qr delete
    $(document).on("click", ".simple-qr-del", function () {
        var simid = $(this).attr("data-product_id");
        $.ajax({
            url: ajaxurl,
            data: {
                action: "simpleqrdel",
                simid: simid
            },
            success: function (data) {
                $("#result_" + simid).html(data);
				if($('.product-grid-item.'+simid).hasClass('1')){
                    $('.product-grid-item.'+simid).removeClass('1');
                }
                
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    // coupon qr delete
    $(document).on("click", ".delete-coupon", function () {
        var couid = $(this).attr("data-product_id");
        
        $.ajax({
            url: ajaxurl,
            data: {
                action: "delcoupon",
                couid: couid
            },
            success: function (data) {
                $("#coupon_" + couid).html(data);
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    
    //coupon qr generate
    $(document).on("click", ".generate-coupon", function () {
        //  console.log('fire 1');
        var couid = $(this).attr("data-product_id");
        
        $.ajax({
            url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: {
                action: "gencoupon",
                couid: couid
            },
            success: function (data) {
                $("#coupon_" + couid).html(data);
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    
    var timeouts = [];
    $(document).on("click", ".bulk-qr-g", function () {
        $('div[class^="product-grid-item"]:not(.1)').each(function(i) {
            var result_id = $( this ).children('.result').attr( "id" );
            
            var self = this;
            timeouts.push(setTimeout(function () {
                
                var varid = $( self ).find( ".button-primary" ).attr("data-product_id");
                if($(self).hasClass('simp_pro')){
                    var gen_action = "simpleqrgen";
                    $.ajax({
                        url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                        data: {
                            action: gen_action,
                            simid: varid
                        },
                        success: function (data) {
                            $("#result_" + varid).html(data);
                            $('.product-grid-item.'+varid).addClass('1');
                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    }); 
                    } else {
                    var gen_action = "variableqrgen";
                    $.ajax({
                        url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                        data: {
                            action: gen_action,
                            varid: varid
                        },
                        success: function (data) {
                            $("#result_" + varid).html(data);
                            if($('.product-grid-item.'+varid).hasClass('1')){
                                $('.product-grid-item.'+varid).removeClass('1');
                            }
                            
                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    }); 
                } 
                
                
                
                
            }, i * 2000));
            
            
        });
    });
    
    $('.cancel-bulk').click(function(){
        $.each(timeouts, function (_, id) {
            clearTimeout(id);
        });
        
        timeouts = [];
    });
    
    
    
});
