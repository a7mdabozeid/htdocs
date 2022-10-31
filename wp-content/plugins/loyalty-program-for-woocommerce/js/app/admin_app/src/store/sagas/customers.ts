// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";

// Actions
import {
  IReadCustomerStatusActionPayload,
  IReadCustomerHistoryActionPayload,
  IAdjustCustomerPointsActionPayload,
  ECustomerDataActionTypes,
  CustomerDataActions,
  IReadCustomersActionPayload,
  IReadSingleCustomerActionPayload,
} from "../actions/customers";

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

export function* readCustomersSaga(action: {
  type: string;
  payload: IReadCustomersActionPayload;
}): any {
  const { params, processingCB, successCB, failCB, alwaysCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.get(`loyalty-program/v1/customers`, {
        params,
      })
    );

    if (response && response.data) {
      yield put(
        CustomerDataActions.setStoreCustomers({
          data: response.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* readSingleCustomerSaga(action: {
  type: string;
  payload: IReadSingleCustomerActionPayload;
}): any {
  const { id, processingCB, successCB, failCB, alwaysCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.get(`loyalty-program/v1/customers/${id}`)
    );

    if (response && response.data) {
      yield put(
        CustomerDataActions.setStoreSingleCustomer({
          data: response.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* readCustomerStatusSaga(action: {
  type: string;
  payload: IReadCustomerStatusActionPayload;
}): any {
  const { id, processingCB, successCB, failCB, alwaysCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.get(`loyalty-program/v1/customers/status/${id}`)
    );

    if (response && response.data && put) {
      yield put(
        CustomerDataActions.setStoreCustomerStatus({
          id,
          data: response.data.status,
        })
      );
      yield put(
        CustomerDataActions.setStoreCustomerSources({
          id,
          data: response.data.sources,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* readCustomerHistorySaga(action: {
  type: string;
  payload: IReadCustomerHistoryActionPayload;
}): any {
  const { id, page, processingCB, successCB, failCB, alwaysCB } =
    action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.get(`loyalty-program/v1/customers/history/${id}`, {
        params: {
          page: page,
        },
      })
    );

    if (response && response.data && put) {
      yield put(
        CustomerDataActions.setStoreHIstoryEntries({
          id,
          data: response.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* adjustCustomerPointsSaga(action: {
  type: string;
  payload: IAdjustCustomerPointsActionPayload;
}): any {
  const { id, type, points, processingCB, successCB, failCB, alwaysCB } =
    action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.post(`loyalty-program/v1/customers/points/${id}`, {
        id,
        type,
        points,
      })
    );

    if (response && response.data && put) {
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
  takeEvery(ECustomerDataActionTypes.READ_CUSTOMERS, readCustomersSaga),
  takeEvery(
    ECustomerDataActionTypes.READ_SINGLE_CUSTOMER,
    readSingleCustomerSaga
  ),
  takeEvery(
    ECustomerDataActionTypes.READ_CUSTOMER_STATUS,
    readCustomerStatusSaga
  ),
  takeEvery(
    ECustomerDataActionTypes.READ_CUSTOMER_HISTORY,
    readCustomerHistorySaga
  ),
  takeEvery(
    ECustomerDataActionTypes.ADJUST_CUSTOMER_POINTS,
    adjustCustomerPointsSaga
  ),
];

// #endregion [Action Listeners]
