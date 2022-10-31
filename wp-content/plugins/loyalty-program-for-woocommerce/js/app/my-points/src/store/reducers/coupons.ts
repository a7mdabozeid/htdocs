// #region [Imports] ===================================================================================================

// Libs
import cloneDeep from "lodash/cloneDeep";

// Types
import IUserCoupon from "../../types/coupons";

// Actions
import {
    ISetStoreUserCouponActionPayload,
    ISetStoreUserCouponsActionPayload,
    EUserCouponsTypes,
} from "../actions/coupons";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

export default (
    coupons: IUserCoupon[] = [],
    action: { type: string; payload: any }
) => {
    switch (action.type) {
        case EUserCouponsTypes.SET_USER_COUPONS: {
            const {
                data,
            } = action.payload as ISetStoreUserCouponsActionPayload;
            return data;
        }

        case EUserCouponsTypes.SET_USER_COUPON: {
            const { data } = action.payload as ISetStoreUserCouponActionPayload;
            const index = coupons.findIndex((c) => c.id === data.id);

            if (index < 0) return [data, ...coupons];

            const clonedCoupons = cloneDeep(coupons);
            clonedCoupons[index] = { ...clonedCoupons[index], ...data };

            return clonedCoupons;
        }

        default:
            return coupons;
    }
};

// #endregion [Reducer]
