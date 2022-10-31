export interface IDashboardWidget {
  key: string;
  widget_name: string;
  type: string;
  title_html: string;
  description_html: string;
  tooltip_html?: string;
  page_link?: string;
  table_data?: ICouponWidgetTableData[];
  raw_data: any;
}

export interface ICouponWidgetTableData {
  id: number;
  coupon: string;
  usage_total: string;
  discount_total: string;
}
