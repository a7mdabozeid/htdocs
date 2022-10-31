// #region [Imports] ===================================================================================================

// Contexts
import { CustomersQueryContext } from "../../contexts/customersQuery";

// Actions
import {CustomerDataActions} from "../../store/actions/customers";

// Types
import ICustomer from "../../types/customer";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
  element: { useState, useEffect, useContext },
  antd: { Card, Table, Pagination, Input },
  redux: { bindActionCreators, connect },
  router: { useHistory },
  pathPrefix
} = acfwpElements;

const { readCustomers } = CustomerDataActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  readCustomers: typeof readCustomers;
}

interface IProps {
  customers: ICustomer[];
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const CustomersList = (props: IProps) => {
  const {customers, actions} = props;
  const {
    loyalty_program: { labels },
  } = acfwAdminApp;
  const {params, dispatchParams} = useContext(CustomersQueryContext);
  const [loading, setLoading] = useState(false);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState(params.search);
  const [searchTimeout, setSearchTimeout]: [any, any] = useState(null);
  const history = useHistory();

  const columns = [
    {
      title: labels.customer_name,
      dataIndex: "name",
      key: "name",
    },
    {
      title: labels.email,
      dataIndex: "email",
      key: "email",
    },
    {
      title: labels.points,
      dataIndex: "points",
      key: "points",
    },
    {
      title: labels.points_expiry,
      dataIndex: "expiry",
      key: "expiry",
    },
  ];

  /**
   * Handle search event.
   */
  const handleSearch = (value: string) => {
    setSearch(value);
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }

    setSearchTimeout(setTimeout(() => dispatchParams({type: "SET_SEARCH", value}), 1000));
  }

  /**
   * Handle pagination click.
   */
   const handlePaginationClick = (page: number) => {
    dispatchParams({type: "SET_PAGE", value: page});
  };

  const handleViewCustomer = (customer: ICustomer) => {
    history.push(`${pathPrefix}admin.php?page=acfw-loyalty-program&tab=customers&customer=${customer.id}`);
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
    <Card title={`Customers List`} className="lpfw-customers-list">
      <div className="customer-search">
        <label>{labels.search_customers}</label>
        <Input.Search 
          allowClear
          value={search}
          onChange={(e: any) => handleSearch(e.target.value)}
          placeholder={labels.name_or_email}
        />
      </div>
      <Table 
        className="customers-list-table"
        loading={loading}
        pagination={false}
        dataSource={customers}
        columns={columns}
        onRow={(record: ICustomer) => ({
          onClick: () => handleViewCustomer(record)
        })}
      />
      {0 < total && (
        <Pagination
          defaultContent={params.page}
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
  );
}

const mapStateToProps = (store: any) => ({ customers: store.customers });

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({ readCustomers }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(CustomersList);

// #endregion [Component]
