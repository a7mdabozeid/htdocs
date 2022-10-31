// #region [Imports] ===================================================================================================

// Types
import {
  IStoreCreditCustomer,
  IStoreCreditSources,
  IStoreCreditStatus,
  IStoreCreditCustomersQueryParams,
} from "../../types/storeCredits";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadStoreCreditsCustomersPayload {
  params: IStoreCreditCustomersQueryParams;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
}

export interface IReadStoreCreditsCustomerStatusPayload {
  id: number;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
}

export interface IReadStoreCreditsCustomerEntriesPayload {
  id: number;
  page: number;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
}

export interface IAdjustCustomerStoreCreditsPayload {
  id: number;
  type: string;
  amount: number | string;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
}

export interface ISetStoreCreditsCustomersPayload {
  data: IStoreCreditCustomer[];
}

export interface ISetStoreCreditsCustomerStatusPayload {
  id: number;
  status: IStoreCreditStatus[];
  sources: IStoreCreditSources[];
}

export interface ISetStoreCreditsCustomerBalancePayload {
  id: number;
  balance: string;
  balance_raw: number;
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EStoreCreditsCustomersActionTypes {
  READ_STORE_CREDITS_CUSTOMERS = "READ_STORE_CREDITS_CUSTOMERS",
  READ_STORE_CREDITS_CUSTOMER_STATUS = "READ_STORE_CREDITS_CUSTOMER_STATUS",
  READ_STORE_CREDITS_CUSTOMER_ENTRIES = "READ_STORE_CREDITS_CUSTOMER_ENTRIES",
  ADJUST_CUSTOMER_STORE_CREDITS = "ADJUST_CUSTOMER_STORE_CREDITS",
  SET_STORE_CREDITS_CUSTOMERS = "SET_STORE_CREDITS_CUSTOMERS",
  SET_STORE_CREDITS_CUSTOMER_STATUS = "SET_STORE_CREDITS_CUSTOMER_STATUS",
  SET_STORE_CREDITS_CUSTOMER_BALANCE = "SET_STORE_CREDITS_CUSTOMER_BALANCE",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const StoreCreditsCustomersActions = {
  readStoreCreditsCustomers: (payload: IReadStoreCreditsCustomersPayload) => ({
    type: EStoreCreditsCustomersActionTypes.READ_STORE_CREDITS_CUSTOMERS,
    payload,
  }),
  readStoreCreditsCustomerStatus: (
    payload: IReadStoreCreditsCustomerStatusPayload
  ) => ({
    type: EStoreCreditsCustomersActionTypes.READ_STORE_CREDITS_CUSTOMER_STATUS,
    payload,
  }),
  readStoreCreditsCustomerEntries: (
    payload: IReadStoreCreditsCustomerEntriesPayload
  ) => ({
    type: EStoreCreditsCustomersActionTypes.READ_STORE_CREDITS_CUSTOMER_ENTRIES,
    payload,
  }),
  adjustCustomerStoreCredits: (
    payload: IAdjustCustomerStoreCreditsPayload
  ) => ({
    type: EStoreCreditsCustomersActionTypes.ADJUST_CUSTOMER_STORE_CREDITS,
    payload,
  }),
  setStoreCreditsCustomers: (payload: ISetStoreCreditsCustomersPayload) => ({
    type: EStoreCreditsCustomersActionTypes.SET_STORE_CREDITS_CUSTOMERS,
    payload,
  }),
  setStoreCreditsCustomerStatus: (
    payload: ISetStoreCreditsCustomerStatusPayload
  ) => ({
    type: EStoreCreditsCustomersActionTypes.SET_STORE_CREDITS_CUSTOMER_STATUS,
    payload,
  }),
  setStoreCreditsCustomerBalance: (
    payload: ISetStoreCreditsCustomerBalancePayload
  ) => ({
    type: EStoreCreditsCustomersActionTypes.SET_STORE_CREDITS_CUSTOMER_BALANCE,
    payload,
  }),
};

// #endregion [Action Creators]
