// #region [Imports] ===================================================================================================

// Types
import { IVirtualCoupon } from "../../types/virtualCoupon";

// Actions
import {
  ISetStoreVirtualCouponsPayload,
  ISetStoreVirtualCouponPayload,
  EVirtualCouponActionTypes,
} from "../actions/virtualCoupon";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

const reducer = (
  virtualCouponsState: IVirtualCoupon[] = [],
  action: { type: string; payload: any }
) => {
  switch (action.type) {
    case EVirtualCouponActionTypes.SET_STORE_VIRTUAL_COUPONS: {
      const { virtualCoupons } =
        action.payload as ISetStoreVirtualCouponsPayload;
      return virtualCoupons;
    }

    case EVirtualCouponActionTypes.SET_STORE_VIRTUAL_COUPON: {
      const { virtualCoupon } = action.payload as ISetStoreVirtualCouponPayload;
      const clonedVirtualCoupons = [...virtualCouponsState];

      const index = clonedVirtualCoupons.findIndex(
        (v) => v.id === virtualCoupon.id
      );
      if (index > -1) clonedVirtualCoupons[index] = virtualCoupon;
      else clonedVirtualCoupons.unshift(virtualCoupon);

      return clonedVirtualCoupons;
    }

    default:
      return virtualCouponsState;
  }
};

export default reducer;

// #endregion [Reducer]
