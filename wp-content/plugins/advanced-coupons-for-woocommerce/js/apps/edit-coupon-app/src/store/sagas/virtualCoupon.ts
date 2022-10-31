// #region [Imports] ===================================================================================================

// Libraries
import { put, call, takeEvery } from "redux-saga/effects";

// Actions
import {
  ICreateVirtualCouponPayload,
  IReadVirtualCouponPayload,
  IUpdateVirtualCouponPayload,
  IDeleteVirtualCouponPayload,
  IBulkCreateVirtualCouponsPayload,
  IRehydrateStoreVirtualCouponsPayload,
  EVirtualCouponActionTypes,
  VirtualCouponActions,
  IFetchVirtualCouponCodesPayload,
  IBulkDeleteVirtualCouponsPayload,
} from "../actions/virtualCoupon";

// Helpers
import axiosInstance, { getCancelToken } from "../../helpers/axios";

// Types
import { IVirtualCoupon } from "../../types/virtualCoupon";
import { IGenericResponse, IBulkResponse } from "../../types/store";

// #endregion [Imports]

// #region [Sagas] =====================================================================================================

export function* createVirtualCouponSaga(action: {
  type: string;
  payload: ICreateVirtualCouponPayload;
}) {
  const { data, processingCB, successCB, failCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response: IGenericResponse = yield call(() =>
      axiosInstance.post(`coupons/v1/virtualcoupons`, data, {
        cancelToken: getCancelToken(`create-virtual-coupon`),
      })
    );

    if (response && response.data) {
      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e, payload: data });
  }
}

export function* readVirtualCouponSaga(action: {
  type: string;
  payload: IReadVirtualCouponPayload;
}) {
  const { id, processingCB, successCB, failCB, alwaysCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response: { data: IVirtualCoupon } = yield call(() =>
      axiosInstance.get(`coupons/v1/virtualcoupons/${id}`, {
        cancelToken: getCancelToken(`create-virtual-coupon`),
      })
    );

    if (response && response.data.id) {
      yield put(
        VirtualCouponActions.setStoreVirtualCoupon({
          virtualCoupon: response.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e, payload: { id } });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* updateVirtualCouponSaga(action: {
  type: string;
  payload: IUpdateVirtualCouponPayload;
}) {
  const { data, processingCB, successCB, failCB, alwaysCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response: IGenericResponse = yield call(() => {
      const { id, ...updateData } = data;
      return axiosInstance.post(
        `coupons/v1/virtualcoupons/${id}`,
        {
          ...updateData,
          date_format: "F j, Y g:i a",
        },
        {
          cancelToken: getCancelToken(`update-virtual-coupon-${id}`),
        }
      );
    });

    if (response && response.data) {
      yield put(
        VirtualCouponActions.setStoreVirtualCoupon({
          virtualCoupon: response.data.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e, payload: data });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* deleteVirtualCouponSaga(action: {
  type: string;
  payload: IDeleteVirtualCouponPayload;
}) {
  const { id, processingCB, successCB, failCB, alwaysCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response: IGenericResponse = yield call(() => {
      return axiosInstance.delete(`coupons/v1/virtualcoupons/${id}`, {
        cancelToken: getCancelToken(`delete-virtual-coupon-${id}`),
      });
    });

    if (response && response.data) {
      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e, payload: { id } });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* bulkCreateVirtualCouponsSaga(action: {
  type: string;
  payload: IBulkCreateVirtualCouponsPayload;
}) {
  const { coupon_id, count, processingCB, successCB, failCB, alwaysCB } =
    action.payload;
  const data = {
    coupon_id,
    count,
  };

  try {
    if (typeof processingCB === "function") processingCB();

    const response: IBulkResponse = yield call(() => {
      return axiosInstance.post(`coupons/v1/bulk/virtualcoupons`, data, {
        cancelToken: getCancelToken(`bulk-create-virtual-coupon`),
      });
    });

    if (typeof successCB === "function") successCB(response);
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e, payload: data });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* bulkDeleteVirtualCouponsSaga(action: {
  type: string;
  payload: IBulkDeleteVirtualCouponsPayload;
}) {
  const { coupon_id, ids, processingCB, successCB, failCB, alwaysCB } =
    action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response: IBulkResponse = yield call(() => {
      return axiosInstance.delete(`coupons/v1/bulk/virtualcoupons`, {
        data: { coupon_id, ids },
        cancelToken: getCancelToken(`bulk-delete-virtual-coupon`),
      });
    });

    if (typeof successCB === "function") successCB(response);
  } catch (e) {
    if (typeof failCB === "function")
      failCB({ error: e, payload: { coupon_id, ids } });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* rehydrateStoreVirtualCouponsSaga(action: {
  type: string;
  payload: IRehydrateStoreVirtualCouponsPayload;
}) {
  const { query, processingCB, successCB, failCB, alwaysCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response: { data: IVirtualCoupon[] } = yield call(() => {
      return axiosInstance.get(`coupons/v1/virtualcoupons/`, {
        params: query,
        cancelToken: getCancelToken(`rehydrate-virtual-coupons`),
      });
    });

    if (Array.isArray(response.data)) {
      yield put(
        VirtualCouponActions.setStoreVirtualCoupons({
          virtualCoupons: response.data,
        })
      );

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e, payload: query });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

export function* fetchVirtualCouponCodesSaga(action: {
  type: string;
  payload: IFetchVirtualCouponCodesPayload;
}) {
  const { query, processingCB, successCB, failCB, alwaysCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response: { data: IVirtualCoupon[] } = yield call(() => {
      return axiosInstance.get(`coupons/v1/virtualcoupons/`, {
        params: { ...query, page: -1, codes_only: true },
        cancelToken: getCancelToken(`fetch-virtual-coupon-codes`),
      });
    });

    if (Array.isArray(response.data)) {
      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function") failCB({ error: e, payload: query });
  }

  if (typeof alwaysCB === "function") alwaysCB();
}

// #endregion [Sagas]

// #region [Action Listeners] ==========================================================================================

export const actionListener = [
  takeEvery(
    EVirtualCouponActionTypes.CREATE_VIRTUAL_COUPON,
    createVirtualCouponSaga
  ),
  takeEvery(
    EVirtualCouponActionTypes.READ_VIRTUAL_COUPON,
    readVirtualCouponSaga
  ),
  takeEvery(
    EVirtualCouponActionTypes.UPDATE_VIRTUAL_COUPON,
    updateVirtualCouponSaga
  ),
  takeEvery(
    EVirtualCouponActionTypes.DELETE_VIRTUAL_COUPON,
    deleteVirtualCouponSaga
  ),
  takeEvery(
    EVirtualCouponActionTypes.BULK_CREATE_VIRTUAL_COUPONS,
    bulkCreateVirtualCouponsSaga
  ),
  takeEvery(
    EVirtualCouponActionTypes.BULK_DELETE_VIRTUAL_COUPONS,
    bulkDeleteVirtualCouponsSaga
  ),
  takeEvery(
    EVirtualCouponActionTypes.REHYDRATE_STORE_VIRTUAL_COUPONS,
    rehydrateStoreVirtualCouponsSaga
  ),
  takeEvery(
    EVirtualCouponActionTypes.FETCH_VIRTUAL_COUPON_CODES,
    fetchVirtualCouponCodesSaga
  ),
];

// #endregion [Action Listeners]
