// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";
import { put, call, takeEvery } from "redux-saga/effects";

// Actions
import {
  IReadDashboardWidgetsData,
  EDashboardWidgetsActionTypes,
  DashboardWidgetsActions,
} from "../actions/dashboardWidgets";

// Helpers
import axiosInstance, { getCancelToken } from "../../helpers/axios";

// #endregion [Imports]

// #region [Sagas] =====================================================================================================

export function* readDashboardWidgetsDataSaga(action: {
  type: string;
  payload: IReadDashboardWidgetsData;
}): any {
  const { startPeriod, endPeriod, processingCB, successCB, failCB } =
    action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.get(`coupons/v1/reports`, {
        params: { startPeriod, endPeriod },
        cancelToken: getCancelToken("dashboardwidgets"),
      })
    );

    if (response && response.data) {
      yield put(
        DashboardWidgetsActions.setDashboardWidgetsData({
          widgets: response.data,
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
    EDashboardWidgetsActionTypes.READ_DASHBOARD_WIDGETS_DATA,
    readDashboardWidgetsDataSaga
  ),
];

// #endregion [Action Listeners]
