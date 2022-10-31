// #region [Imports] ===================================================================================================

// Libraries
import React from "react";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import {useHistory } from "react-router-dom";

// SCSS
import "./index.scss";

// Actions
import { PageActions } from "../../store/actions/page";

// Types
import { IStore } from "../../types/store";

// Helpers
import { getPathPrefix } from "../../helpers/utils";

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
  hideUpgrade?: boolean;
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const Logo = (props: IProps) => {

  const {hideUpgrade, actions} = props;
  const {app_pages, premium_page} = acfwAdminApp;
  const history = useHistory();
  const [premiumPage] = app_pages.filter((p: any) => 'acfw-premium' === p.slug );

  const handleUpgradeClick = () => {
    history.push(`${ pathPrefix }admin.php?page=acfw-premium`);
    actions.setStorePage({ data: 'acfw-premium' });
  };

  return (
    <div className="acfw-logo-div">
      
      {premiumPage ? (
        <>
          <a href="https://advancedcouponsplugin.com/pricing/?utm_source=acfwf&amp;utm_medium=upsell&amp;utm_campaign=logo" target="_blank" rel="noreferrer">
            <img className="acfw-logo" src={ acfwAdminApp.logo } alt="acfw logo" />
          </a>
          {!hideUpgrade && (
            <button 
            className="acfw-header-upgrade-btn"
            onClick={() => handleUpgradeClick()}
          >
            {premium_page.upgrade}
          </button>
          )}
        </>
      ) : 
      <img className="acfw-logo" src={ acfwAdminApp.logo } alt="acfw logo" />}
    </div>
  );
}

const mapStateToProps = (store: IStore) => ({ sections: store.sections });

const mapDispatchToProps = (dispatch: Dispatch) => ({
    actions: bindActionCreators({ setStorePage }, dispatch)
})

export default connect(mapStateToProps, mapDispatchToProps)(Logo);

// #endregion [Component]