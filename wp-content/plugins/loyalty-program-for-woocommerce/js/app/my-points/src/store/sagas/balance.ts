// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";
import { put, call, takeEvery } from "redux-saga/effects";

// Actions
import {
    IReadStorePointsBalanceActionPayload,
    EPointsBalanceTypes,
    PointsBalanceActions,
    IRedeemPointsActionPayload,
} from "../actions/balance";
import { UserCouponsActions } from "../actions/coupons";
import { HistoryEntriesActions } from "../actions/history";

// Helpers
import axiosInstance from "../../helpers/axios";

// #endregion [Imports]

// #region [Sagas] =====================================================================================================

export function* readPointsBalanceSaga(action: {
    type: string;
    payload: IReadStorePointsBalanceActionPayload;
}): any {
    const { processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const response = yield call(() =>
            axiosInstance.get(`loyalty-program/v1/mypoints`)
        );

        if (response && response.data) {
            yield put(
                PointsBalanceActions.setPointsBalance({ data: response.data })
            );

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }
}

export function* redeemPointsSaga(action: {
    type: string;
    payload: IRedeemPointsActionPayload;
}): any {
    const { points, processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const response = yield call(() =>
            axiosInstance.post(`loyalty-program/v1/mypoints/redeem`, {
                points: points,
            })
        );

        if (response && response.data) {
            yield put(
                PointsBalanceActions.setPointsBalance({
                    data: response.data.balance,
                })
            );
            yield put(UserCouponsActions.readUserCoupons({ page: 1 }));
            yield put(HistoryEntriesActions.readHistoryEntries({ page: 1 }));

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }
}

// #endregion [Sagas]

// #region [Action Listeners] ==========================================================================================

export const actionListeners = [
    takeEvery(EPointsBalanceTypes.READ_POINTS_BALANCE, readPointsBalanceSaga),
    takeEvery(EPointsBalanceTypes.REDEEM_POINTS, redeemPointsSaga),
];

// #endregion [Action Listeners]
