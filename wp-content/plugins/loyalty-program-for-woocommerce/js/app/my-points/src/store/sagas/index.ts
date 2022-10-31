// #region [Imports] ===================================================================================================

// Libraries
import { all } from "redux-saga/effects";

// Sagas
import * as balance from "./balance";
import * as coupons from "./coupons";
import * as history from "./history";

// #endregion [Imports]

// #region [Root Saga] =================================================================================================

export default function* rootSaga() {
    yield all([
        ...balance.actionListeners,
        ...coupons.actionListeners,
        ...history.actionListeners,
    ]);
}

// #endregion [Root Saga]
