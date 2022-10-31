(function ($) {
    "use strict";

    $('.owl-carousel').owlCarousel({
        loop:true,
        margin:30,
        responsiveClass:true,
	    items: 2,
        responsive:{
            0:{
                items:1,
                nav:true,
	            slideBy: 1
            },
            600:{
                items:2,
                nav:false,
	            slideBy: 2
            },
            1000:{
                items:2,
                nav:true,
	            slideBy: 2
            }
        }
    });

})
(jQuery);
