// #region [Imports] ===================================================================================================

// Types
import { IStoreCreditCustomer } from "../../types/storeCredits";

// Actions
import {
  ISetStoreCreditsCustomersPayload,
  ISetStoreCreditsCustomerStatusPayload,
  EStoreCreditsCustomersActionTypes,
  ISetStoreCreditsCustomerBalancePayload,
} from "../actions/storeCreditsCustomers";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

const reducer = (
  customers: IStoreCreditCustomer[] = [],
  action: { type: string; payload: any }
) => {
  let index;
  switch (action.type) {
    case EStoreCreditsCustomersActionTypes.SET_STORE_CREDITS_CUSTOMERS:
      const { data } = action.payload as ISetStoreCreditsCustomersPayload;
      return data;

    case EStoreCreditsCustomersActionTypes.SET_STORE_CREDITS_CUSTOMER_STATUS:
      const {
        id,
        status,
        sources,
      } = action.payload as ISetStoreCreditsCustomerStatusPayload;
      index = customers.findIndex((c) => c.id === id);
      customers[index] = { ...customers[index], status, sources };
      return [...customers];

    case EStoreCreditsCustomersActionTypes.SET_STORE_CREDITS_CUSTOMER_BALANCE:
      const {
        balance,
        balance_raw,
      } = action.payload as ISetStoreCreditsCustomerBalancePayload;
      index = customers.findIndex((c) => c.id === action.payload.id);
      customers[index] = {
        ...customers[index],
        balance,
        balance_raw,
        status: [],
        sources: [],
      };
      return [...customers];

    default:
      return customers;
  }
};

export default reducer;

// #endregion [Reducer]
