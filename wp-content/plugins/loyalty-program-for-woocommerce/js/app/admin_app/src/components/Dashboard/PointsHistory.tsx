// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import { IHistoryEntry } from "../../types/dashboard";

// Actions
import { DashboardDataActions } from "../../store/actions/dashboard";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;
declare var moment: any;

const {
    element: { useState, useEffect },
    router: { Link },
    antd: { Card, Select, DatePicker, Table, Pagination },
    redux: { bindActionCreators, connect },
    pathPrefix,
} = acfwpElements;

const { readDashboardHistoryData } = DashboardDataActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  readDashboardHistoryData: typeof readDashboardHistoryData;
}

interface IProps {
    entries: IHistoryEntry[];
    actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PointsHistory = (props: IProps) => {
  const { entries, actions } = props;
  const {
      loyalty_program: { labels, period_options },
  } = acfwAdminApp;
  const [page, setPage]: [number, any] = useState(1);
  const [periodValue, setPeriodValue]: [string, any] = useState('month_to_date');
  const [beforeDate, setBeforeDate]: [any ,any] = useState(moment().startOf("month"));
  const [afterDate, setAfterDate]: [any, any] = useState(moment().startOf("day"));
  const [total, setTotal]: [number, any] = useState('');
  const [loading, setLoading]: [boolean, any] = useState(true);

  const handlePeriodChange = (value: string) => {
    setPeriodValue(value);
    switch (value) {
      case "week_to_date":
        setBeforeDate(moment().startOf("week"));
        setAfterDate(moment().startOf("day"));
        break;
      case "month_to_date":
        setBeforeDate(moment().startOf("month"));
        setAfterDate(moment().startOf("day"));
        break;
      case "quarter_to_date":
        setBeforeDate(moment().startOf("quarter"));
        setAfterDate(moment().startOf("day"));
        break;
      case "year_to_date":
        setBeforeDate(moment().startOf("year"));
        setAfterDate(moment().startOf("day"));
        break;
      case "last_week":
        setBeforeDate(moment().subtract(1, 'weeks').startOf('week'));
        setAfterDate(moment().subtract(1, 'weeks').endOf('week'));
        break;
      case "last_month":
        setBeforeDate(moment().subtract(1, 'months').startOf('month'));
        setAfterDate(moment().subtract(1, 'months').endOf('month'));
        break;
      case "last_quarter":
        setBeforeDate(moment().subtract(1, 'quarters').startOf('quarter'));
        setAfterDate(moment().subtract(1, 'quarters').endOf('quarter'));
        break;
      case "last_year":
        setBeforeDate(moment().subtract(1, 'years').startOf('year'));
        setAfterDate(moment().subtract(1, 'years').endOf('year'));
        break;
    }
  }

  const handleCustomDateRange = (values: any) => {
    setBeforeDate(values[0]);
    setAfterDate(values[1]);
    setPeriodValue('custom');
  }


  useEffect(() => {
    setLoading(true);
    actions.readDashboardHistoryData({
      page,
      before_date: beforeDate.format("YYYY-MM-DD"),
      after_date: afterDate.format("YYYY-MM-DD"),
      successCB: (response) => {
        setTotal(response.headers["x-total"]);
        setLoading(false);
      }
    });
  }, [page, beforeDate, afterDate]);

  /**
   * Set loading state when sources list is empty.
   */
  useEffect(() => {
    if (entries && entries.length) setLoading(false);
  }, [entries, setLoading]);

  const columns = [
    {
      title: labels.date,
      dataIndex: "date",
      key: "date",
    },
    {
      title: labels.customer,
      dataIndex: "user_id",
      key: "user_id",
      render: (userID: number, record: IHistoryEntry) => {
        return (
          <Link
              to={`${pathPrefix}admin.php?page=acfw-loyalty-program&tab=customers&customer=${userID}`}
          >
              {record.customer_name}
          </Link>
        )
      }
    },
    {
      title: labels.activity,
      dataIndex: "activity",
      key: "activity",
    },
    {
      title: labels.points,
      dataIndex: "points",
      key: "points",
    },
    {
      title: labels.related,
      dataIndex: "rel_label",
      key: "rel_label",
      render: (label: string, record: IHistoryEntry) => {

          if (!record.rel_link)
              return label;

          return (
              <a href={record.rel_link} target="_blank">
                  {label}
              </a>
          )
      },
    },
  ];

  return (
    <Card 
      className="lpfw-dashboard-history" 
      title={labels.points_history}
      extra={
        <div className="lpfw-dashboard-period-selector">
          <Select value={periodValue} onSelect={handlePeriodChange}>
            {period_options.map((period: {value: string, label: string}) => <Select.Option value={period.value}>{period.label}</Select.Option>)}
          </Select>
          <DatePicker.RangePicker value={[beforeDate, afterDate]} onChange={handleCustomDateRange} />
        </div>
      }
    >
      <Table
        loading={loading}
        pagination={false}
        dataSource={entries}
        columns={columns}
      />
      {total > 10 && (
        <Pagination
          disabled={loading}
          current={page}
          total={total}
          onChange={setPage}
          showSizeChanger={false}
        />
      )}
    </Card>
  );
}

const mapStateToProps = (store: any) => ({ entries: store.dashboard?.history });

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({readDashboardHistoryData}, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(PointsHistory);

// #endregion [Component]