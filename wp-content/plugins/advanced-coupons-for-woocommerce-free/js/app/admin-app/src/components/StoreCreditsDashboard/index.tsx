// #region [Imports] ===================================================================================================

// Libraries
import {useState, useEffect} from "react";
import {Card, Table, DatePicker, Select} from "antd";
// @ts-ignore
import { bindActionCreators } from "redux"; 
import { connect } from "react-redux";
import moment from "moment";

// Actions
import {StoreCreditsDashboardActions} from "../../store/actions/storeCreditsDashboard";

// Types
import {IStore} from "../../types/store";
import {IStoreCreditStatus} from "../../types/storeCredits";

// Helpers
import { axiosCancel } from "../../helpers/axios";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

const {readStoreCreditsDashboardData} = StoreCreditsDashboardActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  readDashboardData: typeof readStoreCreditsDashboardData;
}

interface IProps {
  status: IStoreCreditStatus[];
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const StoreCreditsDashboard = (props: IProps) => {

  const {status, actions} = props;
  const {store_credits_page: {labels, period_options}} = acfwAdminApp;
  const [loading, setLoading] = useState(false);
  const [periodValue, setPeriodValue] = useState('month_to_date');
  const [startPeriod, setStartPeriod] = useState(moment().startOf("month"));
  const [endPeriod, setEndPeriod] = useState(moment().startOf("day"));

  const columns = [
    {
      title: labels.statistics,
      dataIndex: "label",
      key: "label",
    },
    {
      title: labels.amount,
      dataIndex: "amount",
      key: "amount",
    }
  ];

  /**
   * Initialize loading dashboard data.
   */
  useEffect(() => {
    setLoading(true);
    axiosCancel('scdashboard');
    actions.readDashboardData({
      startPeriod: startPeriod.format("YYYY-MM-DD"),
      endPeriod: endPeriod.format("YYYY-MM-DD"),
      successCB: () => setLoading(false)
    });
  }, [startPeriod, endPeriod]);

  const handlePeriodChange = (value: string) => {
    
    setPeriodValue(value);
    switch (value) {
      case "week_to_date":
        setStartPeriod(moment().startOf("week"));
        setEndPeriod(moment().startOf("day"));
        break;
      case "month_to_date":
        setStartPeriod(moment().startOf("month"));
        setEndPeriod(moment().startOf("day"));
        break;
      case "quarter_to_date":
        setStartPeriod(moment().startOf("quarter"));
        setEndPeriod(moment().startOf("day"));
        break;
      case "year_to_date":
        setStartPeriod(moment().startOf("year"));
        setEndPeriod(moment().startOf("day"));
        break;
      case "last_week":
        setStartPeriod(moment().subtract(1, 'weeks').startOf('week'));
        setEndPeriod(moment().subtract(1, 'weeks').endOf('week'));
        break;
      case "last_month":
        setStartPeriod(moment().subtract(1, 'months').startOf('month'));
        setEndPeriod(moment().subtract(1, 'months').endOf('month'));
        break;
      case "last_quarter":
        setStartPeriod(moment().subtract(1, 'quarters').startOf('quarter'));
        setEndPeriod(moment().subtract(1, 'quarters').endOf('quarter'));
        break;
      case "last_year":
        setStartPeriod(moment().subtract(1, 'years').startOf('year'));
        setEndPeriod(moment().subtract(1, 'years').endOf('year'));
        break;
    }
  }

  const handleCustomDateRange = (values: any) => {
    setStartPeriod(values[0]);
    setEndPeriod(values[1]);
    setPeriodValue('custom');
  }

  return (
    <>
      <div className="store-credits-period-selector">
        <Select value={periodValue} onSelect={handlePeriodChange}>
          {period_options.map((period: {value: string, label: string}) => <Select.Option value={period.value}>{period.label}</Select.Option>)}
        </Select>
        <DatePicker.RangePicker value={[startPeriod, endPeriod]} onChange={handleCustomDateRange} />
      </div>
      <Card title={labels.status}>
        <Table
          loading={loading}
          pagination={false}
          dataSource={status}
          columns={columns}
        />
      </Card>
    </>
  );
};

const mapStateToProps = (store: IStore) => ({status: store.storeCreditsDashboard});

const mapDispatchToProps = (dispatch: any) => ({
  actions: bindActionCreators({readDashboardData: readStoreCreditsDashboardData}, dispatch)
});

export default connect(mapStateToProps, mapDispatchToProps)(StoreCreditsDashboard);

// #endregion [Component]
