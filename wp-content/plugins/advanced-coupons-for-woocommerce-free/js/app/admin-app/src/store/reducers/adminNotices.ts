// #region [Imports] ===================================================================================================

import { ISingleNotice } from "../../types/notices";

import {
  ISetAdminNoticesData,
  EAdminNoticesActionTypes,
  IDismissAdminNoticeData,
} from "../actions/adminNotices";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

const reducer = (
  noticesState: ISingleNotice[] = [],
  action: { type: string; payload: any }
) => {
  switch (action.type) {
    case EAdminNoticesActionTypes.SET_ADMIN_NOTICES: {
      const { notices } = action.payload as ISetAdminNoticesData;
      return notices;
    }

    case EAdminNoticesActionTypes.DISMISS_ADMIN_NOTICE: {
      const { slug } = action.payload as IDismissAdminNoticeData;
      const newNotices = noticesState.filter((n) => n.slug !== slug);
      return newNotices;
    }

    default:
      return noticesState;
  }
};

export default reducer;

// #endregion [Reducer]
