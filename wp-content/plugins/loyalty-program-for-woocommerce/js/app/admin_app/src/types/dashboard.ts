export default interface IDashboardData {
  status: IPointStatus[];
  sources: IPointSource[];
  customers: ITopCustomer[];
  history: IHistoryEntry[];
}

export interface IPointStatus {
  label: string;
  points: number;
  value: string;
}

export interface IPointSource {
  label: string;
  points: number;
}

export interface ITopCustomer {
  id: number;
  name: string;
  email: string;
  points: number;
}

export interface IHistoryEntry {
  id: number;
  date: string;
  activity: string;
  points: number;
  rel_link: string;
  rel_label: string;
  user_id: number;
  customer_name: string;
  customer_email: string;
}
