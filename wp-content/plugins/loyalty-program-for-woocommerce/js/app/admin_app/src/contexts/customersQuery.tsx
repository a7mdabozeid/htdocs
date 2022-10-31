// #region [Imports] ===================================================================================================

// Types
import {ICustomersQueryParams} from "../types/customer";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;
const {element: {createContext, useReducer}} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IReducerAction {
  type: string;
  value: string|number|ICustomersQueryParams;
}

// #endregion [Interfaces]

// #region [Reducer] ===================================================================================================

const reducer = (state: ICustomersQueryParams, action: IReducerAction) => {
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
      return action.value as ICustomersQueryParams;
  }

  return state;
}

// #endregion [Reducer]

// #region [Context] ===================================================================================================

export const CustomersQueryContext = createContext({params: {page: 1}, dispatchParams: (value: string|number|ICustomersQueryParams) => {}});

// #endregion [Context]

// #region [Component] =================================================================================================

const CustomersContextProvider: any = ({children}: any) => {

  const initialState: ICustomersQueryParams = {page: 1};
  const [params, dispatchParams]: [ICustomersQueryParams, any] = useReducer(reducer, initialState);

  return (
    <CustomersQueryContext.Provider value={{params, dispatchParams}}>
      {children}
    </CustomersQueryContext.Provider>
  );
};

export default CustomersContextProvider;

// #endregion [Component]