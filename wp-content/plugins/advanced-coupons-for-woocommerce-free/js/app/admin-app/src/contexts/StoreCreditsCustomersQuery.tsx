// #region [Imports] ===================================================================================================

// Libraries
import {createContext, useReducer, FC} from "react";

// Types
import {IStoreCreditCustomersQueryParams} from "../types/storeCredits";

// #endregion [Imports]

// #region [Interfaces] ================================================================================================

interface IReducerAction {
  type: string;
  value: string|number|IStoreCreditCustomersQueryParams;
}

interface IContext {
  params: IStoreCreditCustomersQueryParams;
  dispatchParams: (value: IReducerAction) => void;
}

// #endregion [Interfaces]

// #region [Reducer] ===================================================================================================

const reducer = (state: IStoreCreditCustomersQueryParams, action: IReducerAction) => {
  switch (action.type) {
    case "SET_PAGE":
      return {...state, page: action.value as number};
    case "SET_PER_PAGE":
      return {...state, per_page: action.value as number};
    case "SET_SEARCH":
      return {...state, search: action.value as string, page: 1}; // set page back to 1 everytime search is dispatched.
    case "SET_SORT_BY":
      return {...state, sort_by: action.value as string};
    case "SET_SORT_ORDER":
      return {...state, sort_order: action.value as string}
    case "SET_QUERY":
      return action.value as IStoreCreditCustomersQueryParams;
  }

  return state;
}

// #endregion [Reducer]

// #region [Context] ===================================================================================================

export const StoreCreditsCustomersContext = createContext<IContext>({params: {page: 1}, dispatchParams: (value) => {} });

// #endregion [Context]

// #region [Component] =================================================================================================

const StoreCreditsCustomersContextProvider: FC = ({children}) => {

  const initialState: IStoreCreditCustomersQueryParams = {page: 1};
  const [params, dispatchParams]: [IStoreCreditCustomersQueryParams, any] = useReducer(reducer, initialState);
  
  return (
    <StoreCreditsCustomersContext.Provider value={{params, dispatchParams}}>
      {children}
    </StoreCreditsCustomersContext.Provider>
  );
}

export default StoreCreditsCustomersContextProvider;

// #endregion [Component]