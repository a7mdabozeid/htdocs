// #region [Imports] ===================================================================================================

// Libraries
import { put, call, takeEvery } from "redux-saga/effects";

// Actions
import {
  IRehydrateStoreVirtualCouponStatsPayload,
  EVirtualCouponStatsActionTypes,
  VirtualCouponStatsActions,
} from "../actions/virtualCouponStats";

// Helpers
import axiosInstance, { getCancelToken } from "../../helpers/axios";

// Types
import { IVirtualCouponStats } from "../../types/virtualCouponStats";

// #endregion [Imports]

// #region [Sagas] =====================================================================================================

export function* rehydrateStoreVirtualCouponStats(action: {
  type: string;
  payload: IRehydrateStoreVirtualCouponStatsPayload;
}) {
  const { coupon_id, processingCB, successCB, failCB } = action.payload;

  try {
    if (typeof processingCB === "function") processingCB();

    const response: { data: IVirtualCouponStats } = yield call(() => {
      return axiosInstance.get(`coupons/v1/virtualcoupons/stats/${coupon_id}`, {
        cancelToken: getCancelToken(`rehydrate-virtual-coupon-stats`),
      });
    });

    if (response.data) {
      yield put(VirtualCouponStatsActions.setStats(response.data));

      if (typeof successCB === "function") successCB(response);
    }
  } catch (e) {
    if (typeof failCB === "function")
      failCB({ error: e, payload: { coupon_id } });
  }
}

// #endregion [Sagas]

// #region [Action Listeners] ==========================================================================================

export const actionListener = [
  takeEvery(
    EVirtualCouponStatsActionTypes.REHYDRATE_STATS,
    rehydrateStoreVirtualCouponStats
  ),
];

// #endregion [Action Listeners]
