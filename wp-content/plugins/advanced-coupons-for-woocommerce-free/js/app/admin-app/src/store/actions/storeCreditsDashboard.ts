// #region [Imports] ===================================================================================================

import IStoreCreditsDashboardData, {
  IStoreCreditStatus,
} from "../../types/storeCredits";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadStoreCreditsDashboardData {
  startPeriod: string;
  endPeriod: string;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
}

export interface ISetStoreCreditsDashboardData {
  status: IStoreCreditStatus[];
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EStoreCreditsDashboardActionTypes {
  READ_STORE_CREDITS_DASHBOARD_DATA = "READ_STORE_CREDITS_DASHBOARD_DATA",
  SET_STORE_CREDITS_DASHBOARD_DATA = "SET_STORE_CREDITS_DASHBOARD_DATA",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const StoreCreditsDashboardActions = {
  readStoreCreditsDashboardData: (payload: IReadStoreCreditsDashboardData) => ({
    type: EStoreCreditsDashboardActionTypes.READ_STORE_CREDITS_DASHBOARD_DATA,
    payload,
  }),
  setStoreCreditsDashboardData: (payload: ISetStoreCreditsDashboardData) => ({
    type: EStoreCreditsDashboardActionTypes.SET_STORE_CREDITS_DASHBOARD_DATA,
    payload,
  }),
};

// #endregion [Action Creators]
