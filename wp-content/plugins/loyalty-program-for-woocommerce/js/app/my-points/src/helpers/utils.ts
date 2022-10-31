// #region [Variables] =================================================================================================

declare var lpfwMyPoints: any;
declare var location: any;

// #endregion [Variables]

export const getPathPrefix = function () {
    return lpfwMyPoints.page_url.replace(location.origin, "");
};
