// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";

// Actions
import {
    IReadLicenseActionPayload,
    IActivateLicenseActionPayload,
    ELicenseActionTypes,
    LicenseActions,
} from "../actions/license";

// Types
import IAxiosResponse from "../../types/axios";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;
declare var ajaxurl: string;

const {
    axiosInstance,
    redux: { sagaEffects },
} = acfwpElements;
const { put, call, takeEvery } = sagaEffects;

// #endregion [Variables]

// #region [Sagas] =====================================================================================================

export function* readLicenseSaga(action: {
    type: string;
    payload: IReadLicenseActionPayload;
}): any {
    const { processingCB, successCB, failCB, alwaysCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const response: IAxiosResponse = yield call(() =>
            axiosInstance({
                url: ajaxurl,
                baseURL: window.location.origin.toString(),
                params: { action: "lpfw_get_license_details" },
            })
        );

        if (response && response.data && put) {
            yield put(
                LicenseActions.setLicenseData({
                    data: {
                        key: response.data.license_key ?? "",
                        email: response.data.email ?? "",
                        is_active: response.data.is_active,
                    },
                })
            );

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }

    if (typeof alwaysCB === "function") alwaysCB();
}

export function* activateLicenseSaga(action: {
    type: string;
    payload: IActivateLicenseActionPayload;
}): any {
    const { license_key, email, processingCB, successCB, failCB, alwaysCB } =
        action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const _formNonce = acfwAdminApp.loyalty_program.license._formNonce;
        const data = new FormData();

        data.append("action", "lpfw_activate_license");
        data.append("activation-email", email);
        data.append("license-key", license_key);
        data.append("ajax-nonce", _formNonce);

        const response: IAxiosResponse = yield call(() =>
            axiosInstance({
                url: ajaxurl,
                method: "POST",
                baseURL: window.location.origin.toString(),
                data: data,
            })
        );

        if (response && response.data && put) {
            yield put(
                LicenseActions.setLicenseData({
                    data: {
                        key: license_key,
                        email: email,
                        is_active:
                            response.data.status === "success" ? "yes" : "no",
                    },
                })
            );

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }

    if (typeof alwaysCB === "function") alwaysCB();
}

// #endregion [Sagas]

// #region [Action Listeners] ==========================================================================================

export const actionListener = [
    takeEvery(ELicenseActionTypes.READ_LICENSE, readLicenseSaga),
    takeEvery(ELicenseActionTypes.ACTIVATE_LICENSE, activateLicenseSaga),
];

// #endregion [Action Listeners]
