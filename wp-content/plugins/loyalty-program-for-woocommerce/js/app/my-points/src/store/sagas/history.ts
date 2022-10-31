// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";
import { put, call, takeEvery } from "redux-saga/effects";

// Actions
import {
    IReadStoreHistoryEntriesActionPayload,
    EHistoryEntriesTypes,
    HistoryEntriesActions,
} from "../actions/history";

// Helpers
import axiosInstance from "../../helpers/axios";

// #endregion [Imports]

// #region [Sagas] =====================================================================================================

export function* readHistoryEntriesSaga(action: {
    type: string;
    payload: IReadStoreHistoryEntriesActionPayload;
}): any {
    const { page, processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const response = yield call(() =>
            axiosInstance.get(`loyalty-program/v1/mypoints/history`, {
                params: {
                    page: page,
                },
            })
        );

        if (response && response.data) {
            yield put(
                HistoryEntriesActions.setHistoryEntries({ data: response.data })
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
    takeEvery(
        EHistoryEntriesTypes.READ_HISTORY_ENTRIES,
        readHistoryEntriesSaga
    ),
];

// #endregion [Action Listeners]
