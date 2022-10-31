// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";
import { put, call, takeEvery } from "redux-saga/effects";

// Actions
import {
  IReadStoreCreditsCustomersPayload,
  IReadStoreCreditsCustomerStatusPayload,
  IReadStoreCreditsCustomerEntriesPayload,
  EStoreCreditsCustomersActionTypes,
  StoreCreditsCustomersActions,
  IAdjustCustomerStoreCreditsPayload,
} from "../actions/storeCreditsCustomers";

// Helpers
import axiosInstance, { getCancelToken } from "../../helpers/axios";

// #endregion [Imports]

// #region [Sagas] =====================================================================================================

export function* readStoreCreditsCustomersSaga(action: {
  type: string;
  payload: IReadStoreCreditsCustomersPayload;
}): any {
  const { params, processingCB, successCB, failCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response = yield call(() =>
      axiosInstance.get(`store-credits/v1/customers`, {
        params,
        cancelToken: getCancelToken("customer_search"),
      })
    );

    if (response && response.data) {
      yield put(
        StoreCreditsCustomersActions.setStoreCreditsCustomers({
          data: response.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (error) {
    if (typeof failCB === "function") failCB({ error });
  }
}

export function* readStoreCreditsCustomerStatusSaga(action: {
  type: string;
  payload: IReadStoreCreditsCustomerStatusPayload;
}): any {
  const { id, processingCB, successCB, failCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    if (!id) throw new Error("Can't fetch data. No customer ID was provided.");

    const response = yield call(() =>
      axiosInstance.get(`store-credits/v1/customers/${id}`)
    );

    if (response && response.data) {
      yield put(
        StoreCreditsCustomersActions.setStoreCreditsCustomerStatus({
          id,
          status: response.data.status,
          sources: response.data.sources,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (error) {
    if (typeof failCB === "function") failCB({ error });
  }
}

export function* readStoreCreditsCustomerEntriesSaga(action: {
  type: string;
  payload: IReadStoreCreditsCustomerEntriesPayload;
}): any {
  const { id, page, processingCB, successCB, failCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    if (!id) throw new Error("Can't fetch data. No customer ID was provided.");

    const response = yield call(() =>
      axiosInstance.get(`store-credits/v1/entries`, {
        params: {
          user_id: id,
          page: page ?? 1,
          is_admin: true,
        },
      })
    );

    if (response && response.data) {
      if (typeof successCB === "function") successCB(response);
    }
  } catch (error) {
    if (typeof failCB === "function") failCB({ error });
  }
}

export function* adjustCustomerStoreCreditsSaga(action: {
  type: string;
  payload: IAdjustCustomerStoreCreditsPayload;
}): any {
  const { id, type, amount, processingCB, successCB, failCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    if (!id)
      throw new Error(
        "Can't adjust store credits. No customer ID was provided."
      );

    const response = yield call(() =>
      axiosInstance.post(`store-credits/v1/entries`, {
        user_id: id,
        type,
        amount,
        action: `admin_${type}`,
      })
    );

    if (response && response.data) {
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
    EStoreCreditsCustomersActionTypes.READ_STORE_CREDITS_CUSTOMERS,
    readStoreCreditsCustomersSaga
  ),
  takeEvery(
    EStoreCreditsCustomersActionTypes.READ_STORE_CREDITS_CUSTOMER_STATUS,
    readStoreCreditsCustomerStatusSaga
  ),
  takeEvery(
    EStoreCreditsCustomersActionTypes.READ_STORE_CREDITS_CUSTOMER_ENTRIES,
    readStoreCreditsCustomerEntriesSaga
  ),
  takeEvery(
    EStoreCreditsCustomersActionTypes.ADJUST_CUSTOMER_STORE_CREDITS,
    adjustCustomerStoreCreditsSaga
  ),
];

// #endregion [Action Listeners]
