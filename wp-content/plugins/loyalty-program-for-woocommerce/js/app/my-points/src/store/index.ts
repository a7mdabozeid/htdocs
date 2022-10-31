// #region [Imports] ===================================================================================================

// Libraries
import { createStore, combineReducers, applyMiddleware } from "redux";
import createSagaMiddleware from "redux-saga";

// Types
import IStore from "../types/store";

// Reducers
import pointsBalanceReducer from "./reducers/balance";
import userCouponsReducer from "./reducers/coupons";
import historyEntriesReducer from "./reducers/history";

// Saga
import rootSaga from "./sagas";

// #endregion [Imports]

// #region [Store] =====================================================================================================

/**
 * !Important
 * Comment this function out when releasing for production.
 */
const bindMiddleware = (middlewares: any[]) => {
    const { composeWithDevTools } = require("redux-devtools-extension");
    return composeWithDevTools(applyMiddleware(...middlewares));
};

export default function initializeStore(
    initialState: IStore | undefined = undefined
) {
    const sagaMiddleware = createSagaMiddleware();

    const store = createStore(
        combineReducers({
            balance: pointsBalanceReducer,
            coupons: userCouponsReducer,
            history: historyEntriesReducer,
        }),
        initialState,
        bindMiddleware([sagaMiddleware])
    );

    sagaMiddleware.run(rootSaga);

    return store;
}

// #endregion [Store]
