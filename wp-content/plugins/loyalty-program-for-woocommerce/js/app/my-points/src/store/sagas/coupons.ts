// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";
import { put, call, takeEvery } from "redux-saga/effects";

// Actions
import {
    IReadStoreUserCouponsActionPayload,
    EUserCouponsTypes,
    UserCouponsActions,
} from "../actions/coupons";

// Helpers
import axiosInstance from "../../helpers/axios";

// #endregion [Imports]

// #region [Sagas] =====================================================================================================

export function* readUserCouponsSaga(action: {
    type: string;
    payload: IReadStoreUserCouponsActionPayload;
}): any {
    const { page, processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const response = yield call(() =>
            axiosInstance.get(`loyalty-program/v1/mypoints/coupons`, {
                params: {
                    page: page,
                },
            })
        );

        if (response && response.data) {
            yield put(
                UserCouponsActions.setUserCoupons({ data: response.data })
            );

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }
}

// #endregion [Sagas]

// #region [Action Listeners] ==========================================================================================

export const actionListeners = [
    takeEvery(EUserCouponsTypes.READ_USER_COUPONS, readUserCouponsSaga),
];

// #endregion [Action Listeners]
