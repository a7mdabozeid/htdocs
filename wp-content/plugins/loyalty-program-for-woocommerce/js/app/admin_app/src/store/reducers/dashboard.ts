// #region [Imports] ===================================================================================================

// Types
import IDashboardData from "../../types/dashboard";

// Actions
import {
  ISetStoreDashboardDataActionPayload,
  EDashboardDataActionTypes,
  ISetStoreDashboardHistoryDataActionPayload,
} from "../actions/dashboard";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

export default (
  dashboardData: IDashboardData | null = null,
  action: { type: string; payload: any }
) => {
  switch (action.type) {
    case EDashboardDataActionTypes.SET_DASHBOARD_DATA: {
      const { data } = action.payload as ISetStoreDashboardDataActionPayload;
      return { ...dashboardData, ...data };
    }

    case EDashboardDataActionTypes.SET_DASHBOARD_HISTORY_DATA: {
      const {
        data,
      } = action.payload as ISetStoreDashboardHistoryDataActionPayload;
      return { ...dashboardData, history: data };
    }

    default:
      return dashboardData;
  }
};

// #endregion [Reducer]
