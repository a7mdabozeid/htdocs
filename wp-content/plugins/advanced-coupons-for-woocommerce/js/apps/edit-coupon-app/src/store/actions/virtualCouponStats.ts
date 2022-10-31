// #region [Imports] ===================================================================================================

// Types
import { IVirtualCouponStats } from "../../types/virtualCouponStats";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IRehydrateStoreVirtualCouponStatsPayload {
  coupon_id: number;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EVirtualCouponStatsActionTypes {
  REHYDRATE_STATS = "REHYDRATE_VC_STATS",
  SET_STATS = "SET_VC_STATS",
  SET_USED = "SET_VC_USED",
  SET_TOTAL = "SET_VC_TOTAL",
  SET_QUERY_TOTAL = "SET_VC_QUERY_TOTAL",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const VirtualCouponStatsActions = {
  rehydrateStats: (payload: IRehydrateStoreVirtualCouponStatsPayload) => ({
    type: EVirtualCouponStatsActionTypes.REHYDRATE_STATS,
    payload,
  }),
  setStats: (payload: IVirtualCouponStats) => ({
    type: EVirtualCouponStatsActionTypes.SET_STATS,
    payload,
  }),
  setUsed: (payload: number) => ({
    type: EVirtualCouponStatsActionTypes.SET_USED,
    payload,
  }),
  setTotal: (payload: number) => ({
    type: EVirtualCouponStatsActionTypes.SET_TOTAL,
    payload,
  }),
  setQueryTotal: (payload: number) => ({
    type: EVirtualCouponStatsActionTypes.SET_QUERY_TOTAL,
    payload,
  }),
};

// #endregion [Action Creators]
