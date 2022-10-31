// #region [Imports] ===================================================================================================

// Libraries
import { createStore, combineReducers, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";

// Reducers
import virtualCouponReducer from "./reducers/virtualCoupon";
import virtualCouponQueryReducer from "./reducers/virtualCouponQuery";
import virtualCouponStatsReducer from "./reducers/virtualCouponStats";

// Sagas
import rootSaga from "./sagas";

// Types
import IStore from "../types/store";

// #region [Variables] =================================================================================================

/**
 * !Important
 * Comment this function out when releasing for production.
 */
const bindMiddleware = (middlewares: any[]) => {
  // const { composeWithDevTools } = require("redux-devtools-extension");
  // return composeWithDevTools(applyMiddleware(...middlewares));
  return applyMiddleware(...middlewares);
};

export default function initializeStore(
  initialState: IStore | undefined = undefined
) {
  const sagaMiddleware = createSagaMiddleware();

  const store = createStore(
    combineReducers({
      virtualCoupons: virtualCouponReducer,
      virtualCouponQuery: virtualCouponQueryReducer,
      virtualCouponStats: virtualCouponStatsReducer,
    }),
    initialState,
    bindMiddleware([sagaMiddleware])
  );

  sagaMiddleware.run(rootSaga);

  return store;
}
