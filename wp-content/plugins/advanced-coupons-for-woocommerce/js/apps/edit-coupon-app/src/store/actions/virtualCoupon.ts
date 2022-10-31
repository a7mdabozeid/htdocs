// #region [Imports] ===================================================================================================

// Types
import { IVirtualCouponArgs, IVirtualCoupon } from "../../types/virtualCoupon";
import { IVirtualCouponQuery } from "../../types/virtualCouponQuery";
import { IVirtualCouponStats } from "../../types/virtualCouponStats";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface ICreateVirtualCouponPayload {
  data: IVirtualCouponArgs;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IReadVirtualCouponPayload {
  id: number;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IUpdateVirtualCouponPayload {
  data: IVirtualCouponArgs;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IDeleteVirtualCouponPayload {
  id: number;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IBulkCreateVirtualCouponsPayload {
  coupon_id: number;
  count: number;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IBulkDeleteVirtualCouponsPayload {
  coupon_id: number;
  ids: number[];
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IRehydrateStoreVirtualCouponsPayload {
  query: IVirtualCouponQuery;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface IFetchVirtualCouponCodesPayload {
  query: IVirtualCouponQuery;
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
  alwaysCB?: () => void;
}

export interface ISetStoreVirtualCouponsPayload {
  virtualCoupons: IVirtualCoupon[];
}

export interface ISetStoreVirtualCouponPayload {
  virtualCoupon: IVirtualCoupon;
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EVirtualCouponActionTypes {
  CREATE_VIRTUAL_COUPON = "CREATE_VIRTUAL_COUPON",
  READ_VIRTUAL_COUPON = "READ_VIRTUAL_COUPON",
  UPDATE_VIRTUAL_COUPON = "UPDATE_VIRTUAL_COUPON",
  DELETE_VIRTUAL_COUPON = "DELETE_VIRTUAL_COUPON",
  BULK_CREATE_VIRTUAL_COUPONS = "BULK_CREATE_VIRTUAL_COUPONS",
  BULK_DELETE_VIRTUAL_COUPONS = "BULK_DELETE_VIRTUAL_COUPONS",
  REHYDRATE_STORE_VIRTUAL_COUPONS = "REHYDRATE_STORE_VIRTUAL_COUPONS",
  FETCH_VIRTUAL_COUPON_CODES = "FETCH_VIRTUAL_COUPON_CODES",
  SET_STORE_VIRTUAL_COUPONS = "SET_STORE_VIRTUAL_COUPONS",
  SET_STORE_VIRTUAL_COUPON = "SET_STORE_VIRTUAL_COUPON",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const VirtualCouponActions = {
  createVirtualCoupon: (payload: ICreateVirtualCouponPayload) => ({
    type: EVirtualCouponActionTypes.CREATE_VIRTUAL_COUPON,
    payload,
  }),
  readVirtualCoupon: (payload: IReadVirtualCouponPayload) => ({
    type: EVirtualCouponActionTypes.READ_VIRTUAL_COUPON,
    payload,
  }),
  updateVirtualCoupon: (payload: IUpdateVirtualCouponPayload) => ({
    type: EVirtualCouponActionTypes.UPDATE_VIRTUAL_COUPON,
    payload,
  }),
  deleteVirtualCoupon: (payload: IDeleteVirtualCouponPayload) => ({
    type: EVirtualCouponActionTypes.DELETE_VIRTUAL_COUPON,
    payload,
  }),
  bulkCreateVirtualCoupons: (payload: IBulkCreateVirtualCouponsPayload) => ({
    type: EVirtualCouponActionTypes.BULK_CREATE_VIRTUAL_COUPONS,
    payload,
  }),
  bulkDeleteVirtualCoupons: (payload: IBulkDeleteVirtualCouponsPayload) => ({
    type: EVirtualCouponActionTypes.BULK_DELETE_VIRTUAL_COUPONS,
    payload,
  }),
  rehydrateStoreVirtualCoupons: (
    payload: IRehydrateStoreVirtualCouponsPayload
  ) => ({
    type: EVirtualCouponActionTypes.REHYDRATE_STORE_VIRTUAL_COUPONS,
    payload,
  }),
  fetchVirtualCouponCodes: (payload: IFetchVirtualCouponCodesPayload) => ({
    type: EVirtualCouponActionTypes.FETCH_VIRTUAL_COUPON_CODES,
    payload,
  }),
  setStoreVirtualCoupons: (payload: ISetStoreVirtualCouponsPayload) => ({
    type: EVirtualCouponActionTypes.SET_STORE_VIRTUAL_COUPONS,
    payload,
  }),
  setStoreVirtualCoupon: (payload: ISetStoreVirtualCouponPayload) => ({
    type: EVirtualCouponActionTypes.SET_STORE_VIRTUAL_COUPON,
    payload,
  }),
};

// #endregion [Action Creators]
