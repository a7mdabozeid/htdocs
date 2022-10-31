export interface ISingleNotice {
  slug: string;
  id: string;
  is_dismissable: boolean;
  type: string;
  content: string[];
  actions: INoticeAction[];
}

export interface INoticeAction {
  key: string;
  link: string;
  text: string;
  is_external?: boolean;
}
