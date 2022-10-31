export default interface IStoreCreditsDashboardData {
  status: IStoreCreditStatus[];
  sources: IStoreCreditSources[];
}

export interface IStoreCreditStatus {
  label: string;
  key: string;
  amount: string;
}

export interface IStoreCreditSources {
  label: string;
  key: string;
  amount: string;
}

export interface IStoreCreditEntry {
  key: string;
  id: number;
  amount: string;
  type: string;
  activity: string;
  user_id: string;
  date: string;
  rel_link: string;
  rel_label: string;
  note: string;
}

export interface IStoreCreditCustomer {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  balance_raw: number;
  balance: string;
  status: IStoreCreditStatus[];
  sources: IStoreCreditSources[];
}

export interface IStoreCreditCustomersQueryParams {
  page: number;
  per_page?: number;
  search?: string;
  sort_by?: string;
  sort_order?: string;
}
