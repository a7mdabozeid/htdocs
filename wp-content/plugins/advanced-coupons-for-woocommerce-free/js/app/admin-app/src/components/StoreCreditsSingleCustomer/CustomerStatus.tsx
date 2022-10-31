// #region [Imports] ===================================================================================================

// Libraries
import { useEffect, useState } from "react";
import {Card, Table} from "antd";


// Types
import {IStoreCreditStatus} from "../../types/storeCredits";
import { IStore } from "../../types/store";
import { connect } from "react-redux";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
  status: IStoreCreditStatus[];
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const CustomerStatus = (props: IProps) => {
  const {status} = props;
  const [loading, setLoading] = useState(true);
  const {store_credits_page: {labels}} = acfwAdminApp;

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
     * Set loading state when status list is empty.
     */
  useEffect(() => {
    if (status && status.length) setLoading(false);
    else setLoading(true);
  }, [status, setLoading]);

  return (
    <Card className="customer-status" title={labels.status}>
      <Table
        loading={loading}
        pagination={false}
        dataSource={status}
        columns={columns}
      />
    </Card>
  )
}

export default CustomerStatus;

// #endregion [Component]