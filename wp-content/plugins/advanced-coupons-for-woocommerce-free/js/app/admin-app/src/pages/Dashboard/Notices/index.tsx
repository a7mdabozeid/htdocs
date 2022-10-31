// #region [Imports] ===================================================================================================

// Libraries
import { useEffect, useState, useRef } from "react";
import { bindActionCreators } from "redux"; 
import { connect } from "react-redux";
import { Button, Divider } from "antd";
import { isNull } from "lodash";

// Components
import SingleNotice from "./SingleNotice";

// Actions
import { AdminNoticesActions } from "../../../store/actions/adminNotices";

// Types
import { IStore } from "../../../types/store";
import { ISingleNotice } from "../../../types/notices";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var jQuery: any;
declare var ajaxurl: any;

const { readAdminNotices, dismissAdminNotice } = AdminNoticesActions;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
  readAdminNotices: typeof readAdminNotices;
  dismissAdminNotice: typeof dismissAdminNotice;
}

interface IProps {
  notices: ISingleNotice[];
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const Notices = (props: IProps) => {
  const {notices, actions} = props;
  const [displayCount, setDisplayCount] = useState(1);
  const [listHeight, setListHeight] = useState(0);
  const listRef = useRef<HTMLDivElement>(null);

  const { 
    dashboard_page: {
      labels
    }
  } = acfwAdminApp;

  const handleDismiss = (noticeSlug: string, response: string = 'yes') => {
    actions.dismissAdminNotice({slug: noticeSlug});

    jQuery.ajax({
      method: "post",
        url: ajaxurl,
        data: {action: 'acfw_dismiss_admin_notice', notice: noticeSlug, response: response},
        dataType: "json"
    });
  }

  /**
   * Load notices data on init.
   */
   useEffect(() => {
    actions.readAdminNotices({});
  }, []);

  useEffect(() => {
    if (notices.length && !isNull(listRef)) {
      // @ts-ignore
      setListHeight(listRef.current.clientHeight);
    }
  });

  if (0 >= notices.length) {
    return null;
  }

  return (
    <div className="dashboard-notices">
      <h2>{labels.notices}</h2>
      <div className={`list-overflow`} style={{height: 0 < listHeight ? listHeight + 5 : 'auto'}}>
        <div className="notices-list" ref={listRef}>
          {notices.slice(0, displayCount).map((notice) => <SingleNotice key={notice.slug} notice={notice} onDismiss={handleDismiss} />)}
        </div>
      </div>
      {1 < notices.length && (
        <Divider>
          <Button 
            className="toggle-view-all"
            size="small"
            type="primary"
            onClick={() => setDisplayCount(1 >= displayCount ? notices.length : 1)}
            ghost
          >
            {1 >= displayCount ?labels.view_all : labels.hide}
          </Button>
        </Divider>
      )}
    </div>
  );
};

const mapStateToProps = (store: IStore) => ({notices: store.adminNotices});

const mapDispatchToProps = (dispatch: any) => ({
  actions: bindActionCreators({readAdminNotices, dismissAdminNotice}, dispatch)
});

export default connect(mapStateToProps, mapDispatchToProps)(Notices);

// export default Notices;

// #endregion [Component]