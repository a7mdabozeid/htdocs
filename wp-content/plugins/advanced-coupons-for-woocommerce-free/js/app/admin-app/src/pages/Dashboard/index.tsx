// #region [Imports] ===================================================================================================

// Libraries
import { useEffect, useState } from "react";
import {Row, Col} from "antd";
import { bindActionCreators } from "redux"; 
import { connect } from "react-redux";
import moment from "moment";

// Components
import Logo from "../../components/Logo";
import QuickCreate from "./QuickCreate";
import Notices from "./Notices";
import ReportWidgets from "./ReportWidgets";
import ReportDangeRangePicker from "../../components/ReportDateRangePicker";
import ReportWidgetsSkeleton from "./ReportWidgetsSkeleton";
import Sidebar from "./Sidebar";

// Actions
import {DashboardWidgetsActions} from "../../store/actions/dashboardWidgets";

// Types
import {IStore} from "../../types/store";
import { IDashboardWidget } from "../../types/dashboard";

// Helpers
import { axiosCancel } from "../../helpers/axios";
import { getPathPrefix, getDateRangeMomentValues } from "../../helpers/utils";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

const {readDashboardWidgetsData} = DashboardWidgetsActions;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
  readDashboardWidgetsData: typeof readDashboardWidgetsData;
}

interface IProps {
  widgets: IDashboardWidget[];
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const Dashboard = (props: IProps) => {
  const {widgets, actions} = props;
  const [periodValue, setPeriodValue] = useState('month_to_date');
  const [startPeriod, setStartPeriod] = useState(moment().startOf("month"));
  const [endPeriod, setEndPeriod] = useState(moment().startOf("day"));
  const [loading, setLoading] = useState(false);

  const { 
    dashboard_page: {
      title,
    }
  } = acfwAdminApp;

  /**
   * Initial load of widgets data.
   * Refresh data when period range value(s) are changed.
   */
  useEffect(() => {
    setLoading(true);
    axiosCancel('dashboardwidgets');
    actions.readDashboardWidgetsData({
      startPeriod: startPeriod.format("YYYY-MM-DD"),
      endPeriod: endPeriod.format("YYYY-MM-DD"),
      successCB: () => setLoading(false)
    })
  }, [startPeriod, endPeriod]);

  /**
   * Handle period selector value change.
   * 
   * @param {string} value 
   * @returns 
   */
  const handlePeriodChange = (value: string) => {

    if (value === periodValue) return;

    const [start, end] = getDateRangeMomentValues(value);
    setPeriodValue(value);
    setStartPeriod(start);
    setEndPeriod(end);
  }

  /**
   * Handle custom date range values change.
   * 
   * @param {[Moment, Moment]} values 
   */
  const handleCustomDateRange = (values: any) => {
    if (!values) {
      return;
    }

    setStartPeriod(values[0] ?? '');
    setEndPeriod(values[1] ?? '');
    setPeriodValue('custom');
  }

  return (
    <div className="dashboard-page">
      <div className="page-header dashboard-header">
        <Logo />
        <h1>{ title }</h1>
      </div>
      <Row gutter={16}>
        <Col className="dashboard-content" span={18}>
          <Notices />
          <QuickCreate />
          <ReportDangeRangePicker
            periodValue={periodValue}
            startPeriod={startPeriod}
            endPeriod={endPeriod}
            onPeriodChange={handlePeriodChange}
            onCustomDateRange={handleCustomDateRange}
          />
          {loading ? <ReportWidgetsSkeleton /> : <ReportWidgets widgets={widgets} />}
        </Col>
        <Col className="dashboard-sidebar" span={6}>
          <Sidebar />
        </Col>
      </Row>
    </div>
  );
};

const mapStateToProps = (store: IStore) => ({widgets: store.dashboardWidgets});

const mapDispatchToProps = (dispatch: any) => ({
  actions: bindActionCreators({readDashboardWidgetsData}, dispatch)
});

export default connect(mapStateToProps, mapDispatchToProps)(Dashboard);

// #endregion [Component]