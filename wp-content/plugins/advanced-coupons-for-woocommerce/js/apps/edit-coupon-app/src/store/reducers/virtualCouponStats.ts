// #region [Imports] ===================================================================================================

// Types
import { IVirtualCouponStats } from "../../types/virtualCouponStats";

// Actions
import { EVirtualCouponStatsActionTypes } from "../actions/virtualCouponStats";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

const reducer = (
  state: IVirtualCouponStats | null = null,
  action: { type: string; payload: any }
) => {
  const clonedState =
    null === state ? { used: 0, total: 0, queryTotal: -1 } : { ...state };
  switch (action.type) {
    case EVirtualCouponStatsActionTypes.SET_STATS: {
      const stats = action.payload as IVirtualCouponStats;
      return { ...clonedState, ...stats };
    }

    case EVirtualCouponStatsActionTypes.SET_USED: {
      clonedState["used"] = action.payload as number;
      return clonedState;
    }

    case EVirtualCouponStatsActionTypes.SET_TOTAL: {
      clonedState["total"] = action.payload as number;
      return clonedState;
    }

    case EVirtualCouponStatsActionTypes.SET_QUERY_TOTAL: {
      clonedState["queryTotal"] = action.payload as number;
      return clonedState;
    }
  }

  return state;
};

export default reducer;
