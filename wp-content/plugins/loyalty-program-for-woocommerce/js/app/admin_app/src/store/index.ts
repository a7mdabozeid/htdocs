// #region [Imports] ===================================================================================================

// Reducers
import dashboardReducer from "./reducers/dashboard";
import settingSectionsReducer from "./reducers/section";
import settingValuesReducer from "./reducers/setting";
import customersReducer from "./reducers/customers";
import licenseReducer from "./reducers/license";

// Sagas
import rootSaga from "./sagas";

// Types
import IStore from "../types/store";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;
const {
  redux: {
    sagaEffects,
    createStore,
    combineReducers,
    applyMiddleware,
    createSagaMiddleware,
  },
} = acfwpElements;

const { all } = sagaEffects;

// #endregion [Variables]

// #region [Store] =====================================================================================================

/**
 * !Important
 * Comment this function out when releasing for production.
 */
const bindMiddleware = (middlewares: any[]) => {
  return applyMiddleware(...middlewares);
};

export default function initializeStore(
  initialState: IStore | undefined = undefined
) {
  const sagaMiddleware = createSagaMiddleware();

  const store = createStore(
    combineReducers({
      dashboard: dashboardReducer,
      settingSections: settingSectionsReducer,
      settingValues: settingValuesReducer,
      customers: customersReducer,
      license: licenseReducer,
    }),
    initialState,
    bindMiddleware([sagaMiddleware])
  );

  sagaMiddleware.run(rootSaga);

  return store;
}

// #endregion [Store]
