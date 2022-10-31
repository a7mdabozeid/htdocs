// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";
import { put, call, takeEvery } from "redux-saga/effects";

// Actions
import {
  IReadAdminNoticesData,
  EAdminNoticesActionTypes,
  AdminNoticesActions,
} from "../actions/adminNotices";

// Helpers
import axiosInstance from "../../helpers/axios";

// #endregion [Imports]

// #region [Sagas] =====================================================================================================

export function* readAdminNoticesDataSaga(action: {
  type: string;
  payload: IReadAdminNoticesData;
}): any {
  const { processingCB, successCB, failCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.get(`coupons/v1/reports/notices`)
    );

    if (response && response.data) {
      yield put(
        AdminNoticesActions.setAdminNotices({
          notices: response.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (error) {
    if (typeof failCB === "function") failCB({ error });
  }
}

// #endregion [Sagas]

// #region [Action Listeners] ==========================================================================================

export const actionListener = [
  takeEvery(
    EAdminNoticesActionTypes.READ_ADMIN_NOTICES,
    readAdminNoticesDataSaga
  ),
];

// #endregion [Action Listeners]
