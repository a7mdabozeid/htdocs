// #region [Imports] ===================================================================================================

import axios from "axios";

// #endregion [Imports]

interface IAxiosCancel {
    id: string;
    cancel: any;
}

// #region [Variables] =================================================================================================

declare var wpApiSettings: any;
const CancelToken = axios.CancelToken;

// #endregion [Variables]

// export axios instance
export default axios.create({
    baseURL: wpApiSettings.root,
    timeout: 30000,
    headers: { "X-WP-Nonce": wpApiSettings.nonce },
});

// variable to save all axios cancels.
const axiosCancelMap = new Map();

// export axios cancel method.
export const axiosCancel = (id: string) => {
    const cancel = axiosCancelMap.get(id);
    if (cancel) {
        cancel();
        axiosCancelMap.delete(id);
    }
};

// export cancel token.
export const getCancelToken = (id: string) =>
    new CancelToken((c) => axiosCancelMap.set(id, c));
