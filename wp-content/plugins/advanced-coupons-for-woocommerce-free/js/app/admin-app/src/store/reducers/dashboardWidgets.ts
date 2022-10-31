// #region [Imports] ===================================================================================================

// Types
import { IDashboardWidget } from "../../types/dashboard";

// Actions
import {
  ISetDashboardWidgetsData,
  EDashboardWidgetsActionTypes,
} from "../actions/dashboardWidgets";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

const reducer = (
  dashboardWidgets: IDashboardWidget[] | null = null,
  action: { type: string; payload: any }
) => {
  switch (action.type) {
    case EDashboardWidgetsActionTypes.SET_DASHBOARD_WIDGETS_DATA: {
      const { widgets } = action.payload as ISetDashboardWidgetsData;
      return widgets;
    }

    default:
      return dashboardWidgets;
  }
};

export default reducer;

// #endregion [Reducer]
