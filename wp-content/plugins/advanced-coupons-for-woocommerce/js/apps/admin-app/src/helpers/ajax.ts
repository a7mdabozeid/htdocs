// #region [Variables] =================================================================================================

declare var ajaxurl: string;
declare var jQuery: any;
declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface ILicenseData {
    action: string;
    key: string;
    email: string;
}

interface IGenericAjaxData {
    action: string;
}

// #region [Functions]=================================================================================================

/**
 * Generic WP AJAX jQuery promise.
 * 
 * @param data IGenericAjaxData
 */
export const wpAjax: any = ( data: IGenericAjaxData ) => {

    return jQuery.ajax({
        method: "post",
        url: ajaxurl,
        data: data,
        dataType: "json"
    });
};

/**
 * WP AJAX jQuery promise to fetch license data.
 * 
 * @param data any
 */
export const getLicense: any = () => {

    return jQuery.ajax({
        url: ajaxurl,
        data: { action: "acfw_get_license_details" },
        dataType: "json"
    });

};

/**
 * WP AJAX jQuery promise to activate license data.
 * 
 * @param data ILicenseData
 */
export const activateLicense: any = ( data: ILicenseData ) => {

    const { license_page: {
        _formNonce
    } } = acfwAdminApp;

    data.action = "acfw_activate_license";

    return jQuery.ajax({
        method: "post",
        url: ajaxurl,
        data: {
            action: "acfw_activate_license",
            'activation-email' : data.email,
            'license-key': data.key,
            'ajax-nonce': _formNonce
        },
        dataType: "json"
    });
};

// #endregion [Functions]