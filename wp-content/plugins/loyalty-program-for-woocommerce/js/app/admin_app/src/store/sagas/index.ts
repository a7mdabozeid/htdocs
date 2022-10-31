// #region [Imports] ===================================================================================================

// Sagas
import * as dashboard from "./dashboard";
import * as section from "./section";
import * as setting from "./setting";
import * as customer from "./customers";
import * as license from "./license";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;
const {
  redux: { sagaEffects },
} = acfwpElements;

const { all } = sagaEffects;

// #endregion [Variables]

// #region [Root Saga] =================================================================================================

export default function* rootSaga() {
  yield all([
    ...dashboard.actionListener,
    ...section.actionListener,
    ...setting.actionListener,
    ...customer.actionListener,
    ...license.actionListener,
  ]);
}

// #endregion [Root Saga]
