// #region [Imports] ===================================================================================================

// Types
import ICustomer, {
  ICustomerStatus,
  ICustomerSource,
  IHistoryEntry,
  ICustomersQueryParams,
} from "../../types/customer";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadCustomersActionPayload {
  params: ICustomersQueryParams;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IReadSingleCustomerActionPayload {
  id: number;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IReadCustomerStatusActionPayload {
  id: number;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IReadCustomerHistoryActionPayload {
  id: number;
  page?: number | null;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IAdjustCustomerPointsActionPayload {
  id: number;
  type: string;
  points: number | string;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface ISetCustomersActionPayload {
  data: ICustomer[];
}

export interface ISetSingleCustomerActionPayload {
  data: ICustomer;
}

export interface ISetCustomerStatusActionPayload {
  id: number;
  data: ICustomerStatus[];
}

export interface ISetCustomerSourcesActionPayload {
  id: number;
  data: ICustomerSource[];
}

export interface ISetHistoryEntriesActionPayload {
  id: number;
  data: IHistoryEntry[];
}

export interface ISetHistoryEntryActionPayload {
  id: number;
  data: IHistoryEntry;
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum ECustomerDataActionTypes {
  READ_CUSTOMERS = "READ_CUSTOMERS",
  READ_SINGLE_CUSTOMER = "READ_SINGLE_CUSTOMER",
  READ_CUSTOMER_STATUS = "READ_CUSTOMER_STATUS",
  READ_CUSTOMER_HISTORY = "READ_CUSTOMER_HISTORY",
  ADJUST_CUSTOMER_POINTS = "ADJUST_CUSTOMER_POINTS",
  SET_STORE_CUSTOMERS = "SET_STORE_CUSTOMERS",
  SET_SINGLE_CUSTOMER = "SET_SINGLE_CUSTOMER",
  SET_STORE_CUSTOMER_STATUS = "SET_STORE_CUSTOMER_STATUS",
  SET_STORE_CUSTOMER_SOURCES = "SET_STORE_CUSTOMER_SOURCES",
  SET_STORE_HISTORY_ENTRIES = "SET_STORE_HISTORY_ENTRIES",
  SET_STORE_HISTORY_ENTRY = "SET_STORE_HISTORY_ENTRY",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const CustomerDataActions = {
  readCustomers: (payload: IReadCustomersActionPayload) => ({
    type: ECustomerDataActionTypes.READ_CUSTOMERS,
    payload,
  }),
  readSingleCustomer: (payload: IReadSingleCustomerActionPayload) => ({
    type: ECustomerDataActionTypes.READ_SINGLE_CUSTOMER,
    payload,
  }),
  readCustomerStatus: (payload: IReadCustomerStatusActionPayload) => ({
    type: ECustomerDataActionTypes.READ_CUSTOMER_STATUS,
    payload,
  }),
  readCustomerHistory: (payload: IReadCustomerHistoryActionPayload) => ({
    type: ECustomerDataActionTypes.READ_CUSTOMER_HISTORY,
    payload,
  }),
  adjustCustomerPoints: (payload: IAdjustCustomerPointsActionPayload) => ({
    type: ECustomerDataActionTypes.ADJUST_CUSTOMER_POINTS,
    payload,
  }),
  setStoreCustomers: (payload: ISetCustomersActionPayload) => ({
    type: ECustomerDataActionTypes.SET_STORE_CUSTOMERS,
    payload,
  }),
  setStoreSingleCustomer: (payload: ISetSingleCustomerActionPayload) => ({
    type: ECustomerDataActionTypes.SET_SINGLE_CUSTOMER,
    payload,
  }),
  setStoreCustomerStatus: (payload: ISetCustomerStatusActionPayload) => ({
    type: ECustomerDataActionTypes.SET_STORE_CUSTOMER_STATUS,
    payload,
  }),
  setStoreCustomerSources: (payload: ISetCustomerSourcesActionPayload) => ({
    type: ECustomerDataActionTypes.SET_STORE_CUSTOMER_SOURCES,
    payload,
  }),
  setStoreHIstoryEntries: (payload: ISetHistoryEntriesActionPayload) => ({
    type: ECustomerDataActionTypes.SET_STORE_HISTORY_ENTRIES,
    payload,
  }),
  setStoreHistoryEntry: (payload: ISetHistoryEntryActionPayload) => ({
    type: ECustomerDataActionTypes.SET_STORE_HISTORY_ENTRY,
    payload,
  }),
};

// #endregion [Action Creators]
