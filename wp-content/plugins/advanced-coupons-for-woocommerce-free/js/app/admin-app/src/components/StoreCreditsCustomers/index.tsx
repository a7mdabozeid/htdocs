// #region [Imports] ===================================================================================================

// Libraries
import {useState, useEffect, useContext} from "react";
import {Card, Table, Pagination, Button, Modal, Input} from "antd";
import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import { isNull } from "lodash";

// Contexts
import {StoreCreditsCustomersContext} from "../../contexts/StoreCreditsCustomersQuery";

// Actions
import {StoreCreditsCustomersActions} from "../../store/actions/storeCreditsCustomers";

// Components
import StoreCreditsSingleCustomer from "../StoreCreditsSingleCustomer";
import AdjustCustomerBalance from "./AdjustCustomerBalance";

// Types
import {IStore} from "../../types/store";
import {IStoreCreditCustomer} from "../../types/storeCredits";

// Helpers
import { axiosCancel } from "../../helpers/axios";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

const { readStoreCreditsCustomers, adjustCustomerStoreCredits } = StoreCreditsCustomersActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  readCustomers: typeof readStoreCreditsCustomers;
  adjustCustomerStoreCredits: typeof adjustCustomerStoreCredits;
}

interface IProps {
  customers: IStoreCreditCustomer[];
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const StoreCreditsCustomers = (props: IProps) => {
  const {customers, actions} = props;
  const {store_credits_page: {labels}} = acfwAdminApp;
  const {params, dispatchParams} = useContext(StoreCreditsCustomersContext);
  const [loading, setLoading] = useState(false);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState(params.search);
  const [searchTimeout, setSearchTimeout]: [any, any] = useState(null);
  const [selectedCustomer, setSelectedCustomer]: [IStoreCreditCustomer|null, any] = useState(null);
  const [adjustCustomer, setAdjustCustomer]: [IStoreCreditCustomer|null, any] = useState(null);

  const columns = [
    {
      title: labels.customer_name,
      dataIndex: "first_name",
      key: "first_name",
      render: (text: string, record: IStoreCreditCustomer) => (`${record.first_name} ${record.last_name}`)
    },
    {
      title: labels.email,
      dataIndex: "email",
      key: "email",
    },
    {
      title: labels.balance,
      dataIndex: "balance",
      key: "balance",
    },
    {
      title: "",
      dataIndex: "id",
      key: "id",
      render: (id: number, record: IStoreCreditCustomer) => {
        return (
          <>
            <Button onClick={() => setSelectedCustomer(record)}>{labels.view_stats}</Button>
            <Button onClick={() => setAdjustCustomer(record)}>{labels.adjust}</Button>
          </>
        );
      }
    }
  ];

  const handleSearch = (value: string) => {
    setSearch(value);
    if (searchTimeout) {
      axiosCancel('customer_search');
      clearTimeout(searchTimeout);
    }

    setSearchTimeout(setTimeout(() => dispatchParams({type: "SET_SEARCH", value}), 1000));
  };

  /**
   * Handle pagination click.
   */
  const handlePaginationClick = (page: number) => {
    dispatchParams({type: "SET_PAGE", value: page});
  };

  /**
   * Initialize loading customers data.
   */
  useEffect(() => {
    setLoading(true);
    actions.readCustomers({
      params,
      successCB: (response) => {
        setTotal(response.headers["x-total"]);
        setLoading(false);
      },
    });
  }, [params]);

  return (
    <>
    <Card title={labels.customers_list} >
      <div className="customer-search">
        <label>{labels.search_label}</label>
        <Input.Search 
          allowClear
          value={search} 
          onChange={(e: any) => handleSearch(e.target.value)}
        />
      </div>
      <Table
        className="customers-list-table"
        loading={loading}
        pagination={false}
        dataSource={customers}
        columns={columns}
      />
      { 0 < total && (
        <Pagination 
          defaultCurrent={params.page}
          current={params.page}
          hideOnSinglePage={true}
          disabled={loading}
          total={total}
          pageSize={params.per_page ?? 10}
          showSizeChanger={false}
          onChange={handlePaginationClick}
        />
      )}
    </Card>
    <StoreCreditsSingleCustomer 
      customer={selectedCustomer} 
      setCustomer={setSelectedCustomer} 
    />
    <Modal
      width={500}
      visible={!isNull(adjustCustomer)}
      footer={false}
      onCancel={() => setAdjustCustomer(null)}
      onOk={() => setAdjustCustomer(null)}
    >
      <AdjustCustomerBalance customer={adjustCustomer} setAdjustCustomer={setAdjustCustomer} />
    </Modal>
    </>
  );
}

const mapStateToProps = (store: IStore) => ({customers: store.storeCreditsCustomers});

const mapDispatchToProps = (dispatch: any) => ({
  actions: bindActionCreators({readCustomers: readStoreCreditsCustomers, adjustCustomerStoreCredits}, dispatch)
});


export default connect(mapStateToProps, mapDispatchToProps)(StoreCreditsCustomers);

// #endregion [Component]