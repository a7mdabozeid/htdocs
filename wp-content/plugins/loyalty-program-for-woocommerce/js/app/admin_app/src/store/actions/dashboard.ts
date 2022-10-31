// #region [Imports] ===================================================================================================

// Types
import IDashboardData, { IHistoryEntry } from "../../types/dashboard";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadDashboardDataActionPayload {
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
}

export interface IReadDashboardHistoryDataActionPayload {
  page: number;
  before_date: string;
  after_date: string;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
}

export interface ISetStoreDashboardDataActionPayload {
  data: IDashboardData;
}

export interface ISetStoreDashboardHistoryDataActionPayload {
  data: IHistoryEntry[];
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EDashboardDataActionTypes {
  READ_DASHBOARD_DATA = "READ_DASHBOARD_DATA",
  READ_DASHBOARD_HISTORY_DATA = "READ_DASHBOARD_HISTORY_DATA",
  SET_DASHBOARD_DATA = "SET_DASHBOARD_DATA",
  SET_DASHBOARD_HISTORY_DATA = "SET_DASHBOARD_HISTORY_DATA",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const DashboardDataActions = {
  readDashboardData: (payload: IReadDashboardDataActionPayload) => ({
    type: EDashboardDataActionTypes.READ_DASHBOARD_DATA,
    payload,
  }),
  readDashboardHistoryData: (
    payload: IReadDashboardHistoryDataActionPayload
  ) => ({
    type: EDashboardDataActionTypes.READ_DASHBOARD_HISTORY_DATA,
    payload,
  }),
  setStoreDashboardData: (payload: ISetStoreDashboardDataActionPayload) => ({
    type: EDashboardDataActionTypes.SET_DASHBOARD_DATA,
    payload,
  }),
  setStoreDashboardHistoryData: (
    payload: ISetStoreDashboardHistoryDataActionPayload
  ) => ({
    type: EDashboardDataActionTypes.SET_DASHBOARD_HISTORY_DATA,
    payload,
  }),
};

// #endregion [Action Creators]
