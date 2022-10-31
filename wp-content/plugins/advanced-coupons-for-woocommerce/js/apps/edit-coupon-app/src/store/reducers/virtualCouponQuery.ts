// #region [Imports] ===================================================================================================

// Types
import { IVirtualCouponQuery } from "../../types/virtualCouponQuery";

// Actions
import { EVirtualCouponQueryActionTypes } from "../actions/virtualCouponQuery";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

const reducer = (
  state: IVirtualCouponQuery | null = null,
  action: { type: string; payload: any }
) => {
  const clonedState = null === state ? { coupon_id: 0, page: 1 } : { ...state };
  switch (action.type) {
    case EVirtualCouponQueryActionTypes.SET_VC_QUERY: {
      const query = action.payload as IVirtualCouponQuery;
      return { ...clonedState, ...query };
    }

    case EVirtualCouponQueryActionTypes.SET_VC_QUERY_COUPON_ID: {
      clonedState["coupon_id"] = action.payload as number;
      return clonedState;
    }

    case EVirtualCouponQueryActionTypes.SET_VC_QUERY_PAGE: {
      clonedState["page"] = action.payload as number;
      return clonedState;
    }

    case EVirtualCouponQueryActionTypes.SET_VC_QUERY_DATE_FORMAT: {
      clonedState["date_format"] = action.payload as string;
      return clonedState;
    }

    case EVirtualCouponQueryActionTypes.SET_VC_QUERY_SEARCH: {
      clonedState["search"] = action.payload as string;
      return clonedState;
    }

    case EVirtualCouponQueryActionTypes.SET_VC_QUERY_STATUS: {
      clonedState["status"] = action.payload as string;
      return clonedState;
    }

    case EVirtualCouponQueryActionTypes.SET_VC_QUERY_USER_ID: {
      clonedState["user_id"] = action.payload as number;
      return clonedState;
    }

    case EVirtualCouponQueryActionTypes.SET_VC_QUERY_SORT_ORDER: {
      clonedState["sort_order"] = action.payload as string;
      return clonedState;
    }

    case EVirtualCouponQueryActionTypes.SET_VC_QUERY_SORT_BY: {
      clonedState["sort_by"] = action.payload as string;
      return clonedState;
    }

    case EVirtualCouponQueryActionTypes.SET_VC_QUERY_DATE_CREATED: {
      clonedState["date_created"] = action.payload as string;
      return clonedState;
    }
  }

  return state;
};

export default reducer;

// #endregion [Reducer]
