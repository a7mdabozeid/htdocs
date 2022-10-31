(function ($) {

    $(window).on('elementor/frontend/init', function () {

        //Time range condition cookie.
        var localTimeZone = new Date().toString().match(/([A-Z]+[\+-][0-9]+.*)/)[1],
            isSecured = (document.location.protocol === 'https:') ? 'secure' : '';

        document.cookie = "localTimeZone=" + localTimeZone + ";SameSite=Strict;" + isSecured;

        //Returning User condition cookie.
        if (PremiumSettings)
            document.cookie = "isReturningVisitor" + PremiumSettings.pageID + "=true;SameSite=Strict;" + isSecured;

    });

})(jQuery);
