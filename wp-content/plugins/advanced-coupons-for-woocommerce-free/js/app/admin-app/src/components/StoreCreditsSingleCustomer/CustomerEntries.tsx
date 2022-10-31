// #region [Imports] ===================================================================================================

// Libraries
import { useEffect, useState } from "react";
import {Card, Table, Pagination} from "antd";

// Types
import {IStoreCreditEntry} from "../../types/storeCredits";


// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
  entries: IStoreCreditEntry[];
  total: number;
  loadEntries: (page: number) => void;
  loaded: boolean;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const CustomerEntries = (props: IProps) => {
  const {entries, total, loadEntries, loaded} = props;
  const {store_credits_page: {labels}} = acfwAdminApp;
  const [page, setPage] = useState(1);

  const columns= [
    {
      title: labels.date,
      dataIndex: "date",
      key: "date",
    },
    {
      title: labels.activity,
      dataIndex: "activity",
      key: "activity",
    },
    {
      title: labels.amount,
      dataIndex: "amount",
      key: "amount",
    },
    {
      title: labels.related,
      dataIndex: "rel_label",
      key: "rel_label",
      render: (label: string, record: IStoreCreditEntry) => {

        if (!record.rel_link)
          return label;

        return (
          <a href={record.rel_link} target="_blank">{label}</a>
        )
      }
    }
  ];

  const handlePaginationClick = (value: number) => {
    setPage(value);
    loadEntries(value);
  }

  return (
    <Card className="customer-entries" title={labels.history}>
      <Table
        loading={!loaded}
        columns={columns}
        dataSource={entries}
        pagination={false}
      />
      { 0 < total && (
        <Pagination 
          defaultCurrent={page}
          current={page}
          hideOnSinglePage={true}
          disabled={!entries || !entries.length}
          total={total}
          pageSize={10}
          showSizeChanger={false}
          onChange={handlePaginationClick}
        />
      )}
    </Card>
  );
};

export default CustomerEntries;

// #endregion [Component]