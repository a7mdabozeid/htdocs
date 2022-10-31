// #region [Imports] ===================================================================================================

// Libraries
import { Input, DatePicker, Select } from "antd";
import moment from "moment";

// Components
import SelectCustomer from "./SelectCustomer";

// Types
import { IVirtualCoupon } from "../../../../types/virtualCoupon";
import { IEditingVirtualCoupon } from "./editingReducer";

// Helpers
import {getAppLabels} from "../../../../helpers/utils";

// #endregion [Imports]

// #region [Interfaces] ================================================================================================

interface IEditableCellProps extends React.HTMLAttributes<HTMLElement> {
  editing: boolean;
  dataIndex: string;
  title: any;
  record: IVirtualCoupon;
  editData: IEditingVirtualCoupon;
  index: number;
  editDispatch: any;
  children: React.ReactNode;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const EditableCell: React.FC<IEditableCellProps> = ({
  editing,
  dataIndex,
  title,
  record,
  editData,
  index,
  editDispatch,
  children,
  ...restProps
}) => {

  const labels = getAppLabels();

  /**
   * Event: update edited data state when an input value is changed.
   * 
   * @param value 
   */
  const handleInputChange = (value: any) => {
    editDispatch({type: dataIndex, value});
  }

  let inputnode = null;
  if (editing) {
    switch (dataIndex) {
      case "code":
        inputnode = <Input addonBefore={`${record.main_code}-`} size="small" value={editData.code} onChange={(e) => handleInputChange(e.target.value)} />;
        break;
      case "status":
        inputnode = (
          <Select onChange={handleInputChange} value={editData.status}>
            <Select.Option value="pending">{labels.pending}</Select.Option>
            <Select.Option value="used">{labels.used}</Select.Option>
          </Select>
        );
        break;
      case "date_created":
      case "date_expire":
        const dateFormat = `MMMM D, YYYY h:mm a`;
        const value = editData[dataIndex] ? moment(editData[dataIndex], dateFormat) : null;
        inputnode = (
          <DatePicker
            allowClear={"date_created" !== dataIndex}
            format={dateFormat}
            value={value}
            showTime
            placeholder={labels.select_date}
            onChange={(date, dateString) => {
              handleInputChange(dateString);
            }}
          />
        );
        break;
      case "user_id":
        inputnode = (
          <SelectCustomer 
            record={record} 
            editData={editData} 
            onSelect={handleInputChange}
            onClear={handleInputChange}
          />
        );
        break;
    }
  }

  return (
    <td {...restProps}>
      {editing ? (
        inputnode
      ) : (
        children
      )}
    </td>
  );
}

export default EditableCell;

// #endregion [Component]