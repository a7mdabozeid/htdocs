// #region [Imports] ===================================================================================================

// Components
import SidebarIcon from "./SidebarIcon";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

export interface IPluginStatus {
  key: string;
  name: string;
  campaign: string;
  status: string;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PluginStatus = (props: IPluginStatus) => {
  const {name, campaign, status} = props;
  const {
    dashboard_page: {labels}
  } = acfwAdminApp;

  let statusHtml: string;

  if ('learn_more' === status) {
    const link = `https://advancedcouponsplugin.com/pricing/?tab=premium&utm_source=acfwf&utm_medium=dashboard&utm_campaign=${campaign}`;
    statusHtml = `<a class="${status}" href="${link}" target="_blank" href="javascript:void(0);">${labels[status]}</a>`
  } else
    statusHtml = `<span class="plugin-status ${status}">${labels[status]}</span>`;

  return (
    <>
      <SidebarIcon iconKey={status} />
      <span className="plugin-name" dangerouslySetInnerHTML={{__html: `${name} (${statusHtml})`}} />
    </>
  );
}

export default PluginStatus;

// #endregion [Component]
