export interface IVirtualCouponQuery {
  coupon_id: number;
  page: number;
  date_format?: string;
  search?: string;
  status?: string;
  user_id?: number;
  sort_order?: string;
  sort_by?: string;
  date_created?: string;
}
