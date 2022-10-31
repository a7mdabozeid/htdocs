// #region [Imports] ===================================================================================================

// Libraries
import { useState } from "react";
import { Select, Spin, message } from "antd";
import { IEditingVirtualCoupon } from "./editingReducer";
import { IVirtualCoupon } from "../../../../types/virtualCoupon";

// Helpers
import axiosIntance from "../../../../helpers/axios";
import {getAppLabels} from "../../../../helpers/utils";

// #endregion [Imports]

// #region [Interfaces] ================================================================================================

interface IOption {
  user_id: number;
  label: string;
}

interface ICustomer {
  user_id: number;
  user_fullname: string;
  user_email: string;
}

interface IProps {
  record: IVirtualCoupon;
  editData: IEditingVirtualCoupon;
  onSelect: any;
  onClear: any;
}

// #endregion [Interfaces]

export function getCustomerLabel(record: IVirtualCoupon|ICustomer) {
  return `${record.user_fullname} (#${record.user_id} - ${record.user_email})`;
}

// #region [Component] =================================================================================================

const SelectCustomer = (props: IProps) => {

  const { editData: {user_id}, record, onSelect, onClear } = props;
  const labels = getAppLabels();
  const initialOptions = user_id ? [{user_id, label: getCustomerLabel(record) }] : [];
  const [options, setOptions]: [IOption[], any] = useState(initialOptions);
  const [searchTimeout, setSearchTimeout]: [any, any] = useState(null);

  /**
   * Schedule customer search as a timeout to prevent duplicate searches while user still typing.
   *
   * @param value
   */
  const handleSearch = (search: string) => {
    if (searchTimeout) {
      clearTimeout(searchTimeout);
      setOptions([]);
    }

    if (search) setSearchTimeout(setTimeout(() => searchUsers(search), 1000));
  };

  /**
   * Callback to do actual customer search.
   *
   * @param search
   */
  const searchUsers = async (search: string) => {
    try {
      const response = await axiosIntance.get(`coupons/v1/searchcustomers`, {
        params: {search}
      });

      setOptions(
        response.data.map( 
          (c: ICustomer) => ({
            user_id: c.user_id,
            label: getCustomerLabel(c),
          })
        )
      );
      setSearchTimeout(null);
    } catch (error: any) {
      message.error(error.response.data.message);
    }
  };

  return (
    <Select
      showSearch
      allowClear
      filterOption={false}
      style={{width: "100%"}}
      value={user_id ? user_id : undefined}
      placeholder={labels.search_customer}
      onSearch={handleSearch}
      onSelect={onSelect}
      onClear={onClear}
      notFoundContent={searchTimeout ? <Spin size="small" /> : null}
    >
      {options.map(o => (
        <Select.Option value={o.user_id}>{o.label}</Select.Option>
      ))}
    </Select>
  );
};

export default SelectCustomer;

