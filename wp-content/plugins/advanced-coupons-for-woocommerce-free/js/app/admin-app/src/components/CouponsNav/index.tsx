// #region [Imports] ===================================================================================================

// Libraries
import {useEffect, useState} from "react";
import { useHistory } from "react-router-dom";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";

// Actions
import { PageActions } from "../../store/actions/page";

// Types
import { IStore } from "../../types/store";
import { ISingleNotice } from "../../types/notices";

// Helpers
import { getPathPrefix } from "../../helpers/utils";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
const { setStorePage } = PageActions;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
  setStorePage: typeof setStorePage;
}

interface IProps {
  page: string;
  notices: ISingleNotice[];
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const CouponsNav = (props: IProps) => {
  const { page, notices, actions } = props;
  const [prevPage, setPrevPage] = useState('');
  const history = useHistory();
  const pathPrefix = getPathPrefix();

  const {
    coupon_nav: { toplevel, dashboard, links },
    app_pages,
  } = acfwAdminApp;

  /**
   * Handle menu click event.
   * 
   * @param {string} id Page ID/Slug
   */
  const handleMenuClick = (id: string) => {
    history.push(`${pathPrefix}admin.php?page=${id}`);
    actions.setStorePage({ data: id });
  };

  /**
   * Add page slug to the body's class list.
   */
  useEffect(() => {
    document.body.classList.add(page);
    if (prevPage) document.body.classList.remove(prevPage);
    setPrevPage(page);
  }, [page]);

  return (
    <>
      <a
        href={`${pathPrefix}edit.php?post_type=shop_coupon`}
        className="wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_acfw-admin"
      >
        <div className="wp-menu-arrow">
          <div></div>
        </div>
        <div className="wp-menu-image dashicons-before dashicons-tickets-alt">
          <br />
        </div>
        <div className="wp-menu-name">{toplevel}</div>
      </a>
      <ul className="wp-submenu wp-submenu-wrap">
        <li className="wp-submenu-head" aria-hidden="true">
          {toplevel}
        </li>
        <li key="acfw-dashboard" className={`wp-first-item ${'acfw-dashboard' === page ? 'current': ''}`}>
            <button
              className={`buttonlink dashboard-link`}
              onClick={() => handleMenuClick(`acfw-dashboard`)}
            >
              {dashboard}
              {0 < notices.length && (
                <>
                  &nbsp;
                  <span className={`update-plugins count-${notices.length}`}>
                    <span className="notices-count">{notices.length}</span>
                  </span>
                </>
              )}
            </button>
          </li>
        {links.map(({ link, text }: any, key: number) => (
          <li key={key}>
            <a href={link}>{text}</a>
          </li>
        ))}
        {app_pages.map(({ slug, label }: any) => (
          <li key={slug} className={slug === page ? `current` : ""}>
            <button
              className={`buttonlink ${slug}-link`}
              onClick={() => handleMenuClick(slug)}
            >
              {label}
            </button>
          </li>
        ))}
      </ul>
    </>
  );
};

const mapStateToProps = (store: IStore) => ({ page: store.page, notices: store.adminNotices });

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators({ setStorePage }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(CouponsNav);

// #endregion [Component]
