// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// CSS
import "./index.scss";

// Components
import AdjustPoints from "./AdjustPoints";
import CustomerStatus from "./CustomerStatus";
import CustomerSources from "./CustomerSources";
import CustomerHistory from "./CustomerHistory";

// Actions
import { CustomerDataActions } from "../../store/actions/customers";

// Types
import ICustomer from "../../types/customer";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { Fragment, useEffect },
    redux: { bindActionCreators, connect },
    antd: { Descriptions, Row, Col, Skeleton, Button },
    antdIcons: {LeftOutlined},
    router: {useHistory}
} = acfwpElements;

const { readSingleCustomer, readCustomerStatus, readCustomerHistory } = CustomerDataActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  readSingleCustomer: typeof readSingleCustomer;
  readCustomerStatus: typeof readCustomerStatus;
  readCustomerHistory: typeof readCustomerHistory;
}

interface IProps {
  customerID: number;
  customers: ICustomer[];
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const SingleCustomers = (props: IProps) => {
    const { customerID, customers, actions } = props;
    const {
      loyalty_program: { labels },
    } = acfwAdminApp;
    const index = customers.findIndex(c => c.id === customerID);
    const customer: ICustomer | null = customers[index] ?? null;
    const history = useHistory();

    const handleGoBack = () => {
      history.goBack();
    };

    useEffect(() => {
      if (customer && customer.id) {
        actions.readCustomerStatus({id: customer.id});
        actions.readCustomerHistory({id: customer.id});

      } else {
        actions.readSingleCustomer({id: customerID, successCB: () => {
          actions.readCustomerStatus({id: customerID});
          actions.readCustomerHistory({id: customerID});
        }});
      }
    }, []);

    return (
      <div className="lpfw-single-customer">
        <Button className="go-back-customers" icon={<LeftOutlined />} onClick={handleGoBack}>Go back</Button>
        {customer ? (
          <Fragment>
            <Descriptions title={labels.customer_info} bordered>
              <Descriptions.Item label={labels.customer_name}>{customer.name}</Descriptions.Item>
              <Descriptions.Item label={labels.email}>{customer.email}</Descriptions.Item>
            </Descriptions>
            <AdjustPoints customer={customer} />
            <Row gutter={16}>
              <Col span={12}>
                <CustomerStatus customer={customer} />
              </Col>
              <Col span={12}>
                <CustomerSources customer={customer} />
              </Col>
            </Row>
            <CustomerHistory customer={customer} />
          </Fragment>
        ) : <Skeleton />}
      </div>
    );
};

const mapStateToProps = (store: any) => ({
  customers: store.customers,
});

const mapDispatchToProps = (dispatch: any) => ({
  actions: bindActionCreators(
    { readSingleCustomer, readCustomerStatus, readCustomerHistory },
    dispatch
  ),
});

export default connect(mapStateToProps, mapDispatchToProps)(SingleCustomers);

// #endregion [Component]
