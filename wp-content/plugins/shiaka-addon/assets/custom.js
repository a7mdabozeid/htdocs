(function ($) {

    $( document ).ready(function() {


        const customScript = {};

        customScript.init = function () {
            this.marquee();
            this.wsl();
            // this.productSingleCarousal()
            this.getStates()
            //this.storeLcoaroe();
            this._translate()

        }

        customScript.storeLcoaroe = function ()
        {


        }

        customScript.marquee = function () {
            var container = $("#campaign-bar");
            var marquee = container.attr("data-marquee")
            if (!marquee || marquee === "undefined" || marquee === null || marquee !== 'true') return;
            if (!container) return;

            container.css(
                {
                    'overflow': 'hidden'
                }
            )

            container.find("#campaign-bar__campaigns").addClass("item-wrap")
            container.find(".razzi-promotion").addClass("item");
            container.grouploop({

                // animation speed

                velocity: 0.5,

                // false = from left to right
                forward: $('html').attr('dir') !== 'rtl',

                // default selectors
                childNode: ".item",
                childWrapper: ".item-wrap",

                // enable pause on hover
                pauseOnHover: true,

                // stick the first item
                stickFirstItem: false,

                // callback
                complete: null

            });
        }

        customScript.wsl = function () {
            if (!$('.header-wishlist a.wishlist-icon').length) {
                return;
            }

            let wslE = $('.header-wishlist a.wishlist-icon');
            wslE.attr('data-toggle', 'modal')
            wslE.attr('data-target', 'wslModal')

        }



        customScript.getStates = function (){
            // get select state elelemnt
            // $('#billing_address_1').on('change' , function (e) {
            //     self = this;
            //     $('#billing_city').css({
            //         'background-color':'rgb(19 48 80 / 35%)',
            //         'color':'white',
            //         'opacity':'.5'
            //     })
            //     $.post(customData.ajax_url , {
            //         action:"get_cities_of_state_ajax",
            //         state_code:self.value,
            //         lang:$('html').attr('lang'),
            //     }).success(function (data){
            //         console.log(data.data)
            //         var options = [];
            //         data.data.forEach(function (item){
            //             options.push($(`<option value="${item}">${item}</option>`));
            //         });

            //         $('#billing_city')
            //             .empty()
            //             .append(options)
            //             .attr('style' , '');
            //     });
            // });

        }

        customScript._translate = function(){

            const ar = {
                "products" : " اخر المنتجات",
                "support" : "راسلنا",
                "finder":"عناوين المعارض",
                contentToolTip:{
                    'finder' : "Store finder" ,
                    'support' : "Client support ",
                    'products' : 'Recent products'
                }
            }

            if($('html').attr('lang') == 'ar') {
                $('footer .copyright').html('الشياكة 2020، جميع الحقوق محفوظة ©') ;
                if($('.razzi-map')) {
                    var intrval = setInterval(function(){
                        if($('.razzi-map .mapboxgl-ctrl-geocoder input').attr('placeholder') !== undefined ) {
                            $('.razzi-map .mapboxgl-ctrl-geocoder input').attr('placeholder' , 'بحث');
                            $('.razzi-map .geocoder-icon-search').css({'right':'initial' , 'left':'20px'});
                            clearInterval(intrval);
                        }
                    } ,200)
                    if($('.nsl-button-label-container')){
                        var socialButtons = setInterval(() => {
                            if($('.nsl-button-label-container')) {
                                $('.nsl-button-label-container').each(function(index) {
                                    var bold = $(this).find('b').text();
                                    var innerText = $(this).text();
                                    $(this).html(`سجل الدخول بواسطة <b> ${bold} </b>`)
                                    console.log([bold , innerText])
                                    clearInterval(socialButtons)
                                })
                            }
                        }, 200);
                    }

                    var calcBtn = setInterval(() => {
                        if($('.shipping-calculator-button')) {
                            $('.shipping-calculator-button').html('حساب تكلفة الشحن')
                            clearInterval(calcBtn);
                        }
                    } , 200);

                    var toolTipTrans = setInterval(() => {

                        if($('.estp-tab-wrapper')) {

                            Object.keys(ar.contentToolTip).forEach(string => {
                                const el =  $(`.estp-tab-wrapper span:contains(${string})`);
                                $.trim(el.text()) === $.trim(ar.contentToolTip[string]) && el.html(ar[string]) ;
                            });

                            clearInterval(toolTipTrans)
                        }
                    }, 200);

                }
            }

            if($('html').attr('lang') === 'en-US') {


                var shippingCosat = setInterval(() => {
                    if($('label[for="shipping_method_0_flat_rate3"]')) {
                        $('.shipping-calculator-button').html('حساب تكلفة الشحن')
                        $('label[for="shipping_method_0_flat_rate3"]')
                            .html($('label[for="shipping_method_0_flat_rate3"]')
                                .text()
                                .replace("رسوم الشحن" , "Shipping cost"));

                        clearInterval(shippingCosat);
                    }
                } , 200);

                var calcBtn = setInterval(() => {
                    if($('.shipping-calculator-button')) {
                        $('.shipping-calculator-button').html('Calculate Shipping')
                        clearInterval(calcBtn);
                    }
                } , 200);



            }
        }

        customScript.init();
    } )
})(jQuery);
