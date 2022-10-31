import { IVirtualCoupon } from "./virtualCoupon";
import { IVirtualCouponQuery } from "./virtualCouponQuery";
import { IVirtualCouponStats } from "./virtualCouponStats";

// #region [Types] =====================================================================================================

export default interface IStore {
  virtualCoupons: IVirtualCoupon[];
  virtualCouponQuery: IVirtualCouponQuery;
  virtualCouponStats: IVirtualCouponStats;
}

export interface IGenericData {
  message: string;
  data: IVirtualCoupon;
}

export interface IGenericResponse {
  data: IGenericData;
}

export interface IBulkData {
  message: string;
  count: number;
  total: number;
}

export interface IBulkResponse {
  data: IBulkData;
}

// #endregion [Types]
