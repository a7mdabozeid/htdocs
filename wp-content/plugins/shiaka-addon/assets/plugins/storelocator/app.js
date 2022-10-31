(function ($) {
  $(document).ready(function () {
    Handlebars.registerHelper("ifEquals", function (arg1, arg2, options) {
      return arg1 == arg2 ? options.fn(this) : options.inverse(this);
    });

    $("#bh-sl-map-container").storeLocator({
      dataType: "json",
      dataLocation: store_locator_data.areas,
      infowindowTemplatePath: store_locator_data.path+"infowindow-description.html",
      listTemplatePath: store_locator_data.path+"location-list-description.html",
      exclusiveFiltering: true,
      //exclusiveTax: ["مكة المكرمة"],
      fullMapStart: true,
      autoComplete: false,
      loading:true,
      //openNearest:true,
      inlineDirections:true,
      slideMap:false,
     //
      defaultLoc:false

    });
  });
})(jQuery);
