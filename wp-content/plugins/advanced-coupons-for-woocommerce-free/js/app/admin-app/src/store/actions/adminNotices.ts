// #region [Imports] ===================================================================================================

import { ISingleNotice } from "../../types/notices";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadAdminNoticesData {
  processingCB?: () => void;
  successCB?: (arg: any) => void;
  failCB?: (arg: any) => void;
}

export interface ISetAdminNoticesData {
  notices: ISingleNotice[];
}

export interface IDismissAdminNoticeData {
  slug: string;
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EAdminNoticesActionTypes {
  READ_ADMIN_NOTICES = "READ_ADMIN_NOTICES",
  SET_ADMIN_NOTICES = "SET_ADMIN_NOTICES",
  DISMISS_ADMIN_NOTICE = "DISMISS_ADMIN_NOTICE",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const AdminNoticesActions = {
  readAdminNotices: (payload: IReadAdminNoticesData) => ({
    type: EAdminNoticesActionTypes.READ_ADMIN_NOTICES,
    payload,
  }),
  setAdminNotices: (payload: ISetAdminNoticesData) => ({
    type: EAdminNoticesActionTypes.SET_ADMIN_NOTICES,
    payload,
  }),
  dismissAdminNotice: (payload: IDismissAdminNoticeData) => ({
    type: EAdminNoticesActionTypes.DISMISS_ADMIN_NOTICE,
    payload,
  }),
};

// #endregion [Action Creators]
