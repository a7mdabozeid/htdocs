export default interface ICustomer {
  id: number;
  name: string;
  email?: string;
  status?: ICustomerStatus[];
  sources?: ICustomerSource[];
  history?: IHistoryEntry[];
  historyTotal?: number;
}

export interface ICustomerStatus {
  label: string;
  points: number;
  value: string;
}

export interface ICustomerSource {
  label: string;
  points: number;
}

export interface IHistoryEntry {
  id: number;
  date: string;
  activity: string;
  points: number;
  rel_link: string;
  rel_label: string;
}

export interface ICustomersQueryParams {
  page: number;
  per_page?: number;
  search?: string;
  sort_by?: string;
  sort_order?: string;
}
