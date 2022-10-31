// #region [Imports] ===================================================================================================

// Types
import { IDashboardWidget } from "../../types/dashboard";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadDashboardWidgetsData {
  startPeriod: string;
  endPeriod: string;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
}

export interface ISetDashboardWidgetsData {
  widgets: IDashboardWidget[];
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EDashboardWidgetsActionTypes {
  READ_DASHBOARD_WIDGETS_DATA = "READ_DASHBOARD_WIDGETS_DATA",
  SET_DASHBOARD_WIDGETS_DATA = "SET_DASHBOARD_WIDGETS_DATA",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const DashboardWidgetsActions = {
  readDashboardWidgetsData: (payload: IReadDashboardWidgetsData) => ({
    type: EDashboardWidgetsActionTypes.READ_DASHBOARD_WIDGETS_DATA,
    payload,
  }),
  setDashboardWidgetsData: (payload: ISetDashboardWidgetsData) => ({
    type: EDashboardWidgetsActionTypes.SET_DASHBOARD_WIDGETS_DATA,
    payload,
  }),
};

// #endregion [Action Creators]
