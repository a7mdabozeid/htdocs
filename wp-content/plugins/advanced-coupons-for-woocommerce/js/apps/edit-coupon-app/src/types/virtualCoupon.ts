export interface IVirtualCouponsStore {
  virtual_coupons: IVirtualCoupon[];
  total: number;
  fresh_count: number;
}

export interface IVirtualCouponArgs {
  id?: number;
  coupon_id?: number;
  code?: string;
  status?: string;
  user_id?: number;
  date_created?: string;
  date_expire?: string;
}

export interface IVirtualCoupon {
  key: string;
  id: number;
  coupon_id: number;
  code: string;
  main_code: string;
  coupon_code: string;
  status: string;
  user_id: number;
  user_fullname: string;
  user_email: string;
  date_created: string;
  date_expire: string;
}

export interface IBulkCreateVirtualCouponsData {
  coupon_id: number;
  count: number;
  total: number;
}
