// #region [Imports] ===================================================================================================

// Libraries
import { useEffect, useState } from "react";
import {Card, Table} from "antd";

// Types
import {IStoreCreditSources} from "../../types/storeCredits";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
  sources: IStoreCreditSources[];
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const CustomerSources = (props: IProps) => {
  const {sources} = props;
  const {store_credits_page: {labels}} = acfwAdminApp;
  
  const columns = [
    {
      title: labels.source,
      dataIndex: "label",
      key: "label",
    },
    {
      title: labels.amount,
      dataIndex: "amount",
      key: "amount",
    },
  ];

  return (
    <Card className="customer-sources" title={labels.sources}>
      <Table 
        loading={!sources || !sources.length}
        pagination={false}
        dataSource={sources}
        columns={columns}
      />
    </Card>
  );
}

export default CustomerSources;

// #endregion [Component]
