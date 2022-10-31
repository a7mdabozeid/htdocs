// #region [Imports] ===================================================================================================

// Sagas
import { all } from "redux-saga/effects";
import * as virtualCoupon from "./virtualCoupon";
import * as virtualCouponStats from "./virtualCouponStats";

// #endregion [Imports]

// #region [Root Saga] =================================================================================================

export default function* rootSaga() {
  yield all([
    ...virtualCoupon.actionListener,
    ...virtualCouponStats.actionListener,
  ]);
}

// #endregion [Root Saga]
