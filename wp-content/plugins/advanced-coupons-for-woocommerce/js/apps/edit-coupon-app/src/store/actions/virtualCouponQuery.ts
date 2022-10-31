// #region [Imports] ===================================================================================================

// Types
import { IVirtualCouponQuery } from "../../types/virtualCouponQuery";

// #endregion [Imports]

// #region [Action Types] ==============================================================================================

export enum EVirtualCouponQueryActionTypes {
  SET_VC_QUERY = "SET_VC_QUERY",
  SET_VC_QUERY_COUPON_ID = "SET_VC_QUERY_COUPON_ID",
  SET_VC_QUERY_PAGE = "SET_VC_QUERY_PAGE",
  SET_VC_QUERY_DATE_FORMAT = "SET_VC_QUERY_DATE_FORMAT",
  SET_VC_QUERY_SEARCH = "SET_VC_QUERY_SEARCH",
  SET_VC_QUERY_STATUS = "SET_VC_QUERY_STATUS",
  SET_VC_QUERY_USER_ID = "SET_VC_QUERY_USER_ID",
  SET_VC_QUERY_SORT_ORDER = "SET_VC_QUERY_SORT_ORDER",
  SET_VC_QUERY_SORT_BY = "SET_VC_QUERY_SORT_BY",
  SET_VC_QUERY_DATE_CREATED = "SET_VC_QUERY_DATE_CREATED",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const VirtualCouponQueryActions = {
  setQuery: (payload: IVirtualCouponQuery) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY,
    payload,
  }),
  setCouponId: (payload: number) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY_COUPON_ID,
    payload,
  }),
  setPage: (payload: number) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY_PAGE,
    payload,
  }),
  setDateFormat: (payload: string) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY_DATE_FORMAT,
    payload,
  }),
  setSearch: (payload: string) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY_SEARCH,
    payload,
  }),
  setStatus: (payload: string) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY_STATUS,
    payload,
  }),
  setUserId: (payload: string) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY_USER_ID,
    payload,
  }),
  setSortOrder: (payload: string) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY_SORT_ORDER,
    payload,
  }),
  setSortBy: (payload: string) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY_SORT_BY,
    payload,
  }),
  setDateCreated: (payload: string) => ({
    type: EVirtualCouponQueryActionTypes.SET_VC_QUERY_DATE_CREATED,
    payload,
  }),
};

// #endregion [Action Creators]
