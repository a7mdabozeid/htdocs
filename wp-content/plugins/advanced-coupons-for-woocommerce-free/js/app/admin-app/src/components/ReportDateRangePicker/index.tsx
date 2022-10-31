// #region [Imports] ===================================================================================================

import {DatePicker, DatePickerProps, Select ,} from "antd";
import moment from "moment";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
  className?: string;
  periodValue: string;
  startPeriod: moment.Moment;
  endPeriod: moment.Moment;
  onPeriodChange?: (arg: string) => void;
  onCustomDateRange?: (arg: any) => void; // argument holds a tuple of Moment values.
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const ReportDangeRangePicker = (props: IProps) => {

  const {store_credits_page: {labels, period_options}} = acfwAdminApp;

  const {className, periodValue, startPeriod, endPeriod, onPeriodChange, onCustomDateRange} = props;

  return (
    <div className={`report-date-range-picker ${className}`}>
      <Select value={periodValue} onSelect={onPeriodChange}>
        {period_options.map((period: {value: string, label: string}, i: number) => <Select.Option key={i} value={period.value}>{period.label}</Select.Option>)}
      </Select>
      <DatePicker.RangePicker value={[startPeriod, endPeriod]} onChange={onCustomDateRange} />
    </div>
  );
};

export default ReportDangeRangePicker;

// #endregion [Component]