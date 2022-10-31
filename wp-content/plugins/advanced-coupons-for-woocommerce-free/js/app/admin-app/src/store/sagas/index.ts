// #region [Imports] ===================================================================================================

// Libraries
import { all } from "redux-saga/effects";

// Sagas
import * as section from "./section";
import * as setting from "./setting";
import * as storeCreditsDashboard from "./storeCreditsDashboard";
import * as storeCreditsCustomers from "./storeCreditsCustomers";
import * as dashboardWidgets from "./dashboardWidgets";
import * as adminNotices from "./adminNotices";

// #endregion [Imports]

// #region [Root Saga] =================================================================================================

export default function* rootSaga() {
  yield all([
    ...section.actionListener,
    ...setting.actionListener,
    ...storeCreditsCustomers.actionListener,
    ...storeCreditsDashboard.actionListener,
    ...dashboardWidgets.actionListener,
    ...adminNotices.actionListener,
  ]);
}

// #endregion [Root Saga]
