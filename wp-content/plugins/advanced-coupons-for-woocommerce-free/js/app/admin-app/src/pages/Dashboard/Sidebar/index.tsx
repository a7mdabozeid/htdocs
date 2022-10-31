// #region [Imports] ===================================================================================================

// Libraries
import { useEffect, useState } from "react";
import { connect } from "react-redux";
import { bindActionCreators, Dispatch } from "redux";
import { useHistory } from "react-router-dom";
import {List} from "antd";

// Actions
import { PageActions } from "../../../store/actions/page";

// Components
import ResourceLink, {IResourceItem} from "./ResourceLink";
import PluginStatus, {IPluginStatus} from "./PluginStatus";

// Helpers
import { getPathPrefix } from "../../../helpers/utils";

// Helpers
import axiosInstance from "../../../helpers/axios";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
const { setStorePage } = PageActions;
const pathPrefix = getPathPrefix();

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
  setStorePage: typeof setStorePage;
}

interface IProps {
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const Sidebar = (props: IProps) => {
  const {actions} = props;
  const history = useHistory();
  const [premiumPlugins, setPremiumPlugins]: [IPluginStatus[], any] = useState([]);
  const [loading, setLoading] = useState(true);

  const { 
    dashboard_page: {
      labels,
      resources_links,
    }
  } = acfwAdminApp;

  /**
   * Load license data on init.
   * NOTE: We are not storing this in a store as we need to fetch fresh data for this everytime the page is loaded.
   */
  useEffect(() => {
    axiosInstance.get(`coupons/v1/reports/license`)
      .then((response: any) => {
        setPremiumPlugins(response.data);
        setLoading(false);
      });
  }, []);
  
  /**
   * Handle internal redirects for the menu items.
   * 
   * @param {string} id Page ID. 
   */
  const handlePageRedirect = (id: string) => {
    history.push(`${ pathPrefix }admin.php?page=${id}`);
    actions.setStorePage({ data: id });
  };

  return (
    <div className="sidebar-inner">
      <List
        className="resources-section sidebar-section" 
        header={<h2>{`${labels.helpful_resources}:`}</h2>}
        dataSource={resources_links}
        renderItem={(item: IResourceItem) => (
          <List.Item key={item.key}>
            <ResourceLink {...item} onClick={handlePageRedirect} />
          </List.Item>
        )}
      />
      <List 
        className="plugins-section sidebar-section"
        header={<h2>{`${labels.license_activation_status}:`}</h2>}
        loading={loading}
        dataSource={premiumPlugins}
        renderItem={(item: IPluginStatus) => (
          <List.Item key={item.key}>
            <PluginStatus {...item} />
          </List.Item>
        )}
      />
      <p className="view-licenses">
        <a onClick={() => handlePageRedirect('acfw-license')}>
          {labels.view_licenses}
        </a>
        </p>
    </div>
  );
}

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators({ setStorePage }, dispatch)
})

export default connect(null, mapDispatchToProps)(Sidebar);

// #endregion [Component]
