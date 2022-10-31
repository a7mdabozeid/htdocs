// #region [Imports] ===================================================================================================

// Types
import ICustomer from "../../types/customer";

// Actions
import {
  ISetCustomersActionPayload,
  ISetCustomerStatusActionPayload,
  ISetCustomerSourcesActionPayload,
  ISetHistoryEntriesActionPayload,
  ISetHistoryEntryActionPayload,
  ECustomerDataActionTypes,
  ISetSingleCustomerActionPayload,
} from "../actions/customers";

// #endregion [Imports]

interface IAction {
  type: string;
  payload: any;
}

// #region [Variables] ===================================================================================================

declare var acfwpElements: any;
const {
  lodash: { cloneDeep },
} = acfwpElements;

// #endregion [Variables]

// #region [Reducer] ===================================================================================================

export default (customers: ICustomer[] = [], action: IAction) => {
  let index;
  switch (action.type) {
    case ECustomerDataActionTypes.SET_STORE_CUSTOMERS: {
      const { data } = action.payload as ISetCustomersActionPayload;
      return data;
    }

    case ECustomerDataActionTypes.SET_SINGLE_CUSTOMER: {
      const { data } = action.payload as ISetSingleCustomerActionPayload;
      index = customers.findIndex((c) => c.id === data.id);

      if (index >= 0) {
        customers[index] = data;
        return [...customers];
      } else {
        return [data, ...customers];
      }
    }

    case ECustomerDataActionTypes.SET_STORE_CUSTOMER_STATUS: {
      const { id, data } = action.payload as ISetCustomerStatusActionPayload;
      index = customers.findIndex((c) => c.id === id);
      customers[index] = { ...customers[index], status: data };
      return [...customers];
    }

    case ECustomerDataActionTypes.SET_STORE_CUSTOMER_SOURCES: {
      const { id, data } = action.payload as ISetCustomerSourcesActionPayload;
      index = customers.findIndex((c) => c.id === id);
      customers[index] = { ...customers[index], sources: data };
      return [...customers];
    }

    case ECustomerDataActionTypes.SET_STORE_HISTORY_ENTRIES: {
      const { id, data } = action.payload as ISetHistoryEntriesActionPayload;
      index = customers.findIndex((c) => c.id === id);
      customers[index] = { ...customers[index], history: data };
      return [...customers];
    }

    case ECustomerDataActionTypes.SET_STORE_HISTORY_ENTRY: {
      const { id, data } = action.payload as ISetHistoryEntryActionPayload;
      index = customers.findIndex((c) => c.id === id);
      const prevHistory = customers[index]?.history ?? [];
      customers[index] = {
        ...customers[index],
        history: [...prevHistory, data],
      };
      return [...customers];
    }

    default:
      return customers;
  }
};

// #endregion [Reducer]
