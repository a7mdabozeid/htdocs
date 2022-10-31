// #region [Imports] ===================================================================================================

import axios from "axios";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var wpApiSettings: any;

// #endregion [Variables]

export default axios.create({
    baseURL: wpApiSettings.root,
    timeout: 30000,
    headers: { "X-WP-Nonce": wpApiSettings.nonce },
});
