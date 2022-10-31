// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";

// Actions
import {
  IReadDashboardDataActionPayload,
  EDashboardDataActionTypes,
  DashboardDataActions,
  IReadDashboardHistoryDataActionPayload,
} from "../actions/dashboard";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;
const {
  axiosInstance,
  redux: { sagaEffects },
} = acfwpElements;

const { put, call, takeEvery } = sagaEffects;

// #endregion [Variables]

// #region [Sagas] =====================================================================================================

export function* readDashboardDataSaga(action: {
  type: string;
  payload: IReadDashboardDataActionPayload;
}): any {
  const { processingCB, successCB, failCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.get(`loyalty-program/v1/dashboard`)
    );

    if (response && response.data && put) {
      yield put(
        DashboardDataActions.setStoreDashboardData({
          data: response.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e });
  }
}

export function* readDashboardHistoryDataSaga(action: {
  type: string;
  payload: IReadDashboardHistoryDataActionPayload;
}): any {
  const {
    page,
    before_date,
    after_date,
    processingCB,
    successCB,
    failCB,
  } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.get(`loyalty-program/v1/dashboard/history`, {
        params: {
          page,
          before_date,
          after_date,
        },
      })
    );

    if (response && response.data && put) {
      yield put(
        DashboardDataActions.setStoreDashboardHistoryData({
          data: response.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e });
  }
}

// #endregion [Sagas]

// #region [Action Listeners] ==========================================================================================

export const actionListener = [
  takeEvery(
    EDashboardDataActionTypes.READ_DASHBOARD_DATA,
    readDashboardDataSaga
  ),
  takeEvery(
    EDashboardDataActionTypes.READ_DASHBOARD_HISTORY_DATA,
    readDashboardHistoryDataSaga
  ),
];

// #endregion [Action Listeners]
