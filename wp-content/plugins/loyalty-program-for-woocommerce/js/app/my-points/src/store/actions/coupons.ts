// #region [Imports] ===========================================================================================

import IUserCoupon from "../../types/coupons";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadStoreUserCouponsActionPayload {
    page: number | null;
    processingCB?: () => void;
    successCB?: (arg: any) => void;
    failCB?: (arg: any) => void;
}
export interface ISetStoreUserCouponsActionPayload {
    data: IUserCoupon[];
}
export interface ISetStoreUserCouponActionPayload {
    data: IUserCoupon;
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EUserCouponsTypes {
    READ_USER_COUPONS = "READ_USER_COUPONS",
    SET_USER_COUPONS = "SET_USER_COUPONS",
    SET_USER_COUPON = "SET_USER_COUPON",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const UserCouponsActions = {
    readUserCoupons: (payload: IReadStoreUserCouponsActionPayload) => ({
        type: EUserCouponsTypes.READ_USER_COUPONS,
        payload,
    }),
    setUserCoupons: (payload: ISetStoreUserCouponsActionPayload) => ({
        type: EUserCouponsTypes.SET_USER_COUPONS,
        payload,
    }),
    setUserCoupon: (payload: ISetStoreUserCouponActionPayload) => ({
        type: EUserCouponsTypes.SET_USER_COUPON,
        payload,
    }),
};

// #endregion [Action Creators]
