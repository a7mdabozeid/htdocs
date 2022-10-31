// #region [Imports] ===================================================================================================

// Libraries
import { connect } from "react-redux";
import { bindActionCreators, Dispatch } from "redux";
import { useHistory } from "react-router-dom";

// Components
import BigNumberWidget from "./widgets/BigNumberWidget";
import CouponTableWidget from "./widgets/CouponTableWidget";
import UpsellWidget from "./widgets/UpsellWidget";

// Actions
import { PageActions } from "../../store/actions/page";

// Types
import { IDashboardWidget } from "../../types/dashboard";

// Helpers
import { getPathPrefix } from "../../helpers/utils";

// #endregion [Imports]

// #region [Variables] =================================================================================================

const { setStorePage } = PageActions;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
  setStorePage: typeof setStorePage;
}

interface IProps {
  widgets: IDashboardWidget[];
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const ReportWidgets = (props: IProps) => {
  const {widgets, actions} = props;
  const history = useHistory();

  /**
   * Handle internal redirects for the menu items.
   * 
   * @param {string} id Page ID. 
   */
   const handlePageRedirect = (id: string) => {
    history.push(`${ getPathPrefix() }admin.php?page=${id}`);
    actions.setStorePage({ data: id });
  };

  if (!widgets) {
    return null;
  }

  return (
    <div className="report-widgets">
      {widgets.map((widget) => {
        switch (widget.type) {
          case 'big_number':
            return (<BigNumberWidget key={widget.key} widget={widget} onClickLink={handlePageRedirect} />);
          case 'table':
            return (<CouponTableWidget key={widget.key} widget={widget} />);
          case 'upsell':
            return (<UpsellWidget key={widget.key} widget={widget} />);
          case 'section_title':
            return <h2 key={widget.key} className="section-title">{widget.title_html}</h2>;
        }

        return null;
      })}
    </div>
  );
};

// export default ReportWidgets;

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators({ setStorePage }, dispatch)
})

export default connect(null, mapDispatchToProps)(ReportWidgets);

// #endregion [Component]
