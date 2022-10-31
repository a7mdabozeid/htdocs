// #region [Imports] ===================================================================================================

// Libraries
import { useEffect, useState } from "react";
import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import {Row, Col, Divider, Modal} from "antd";
import { isNull, set } from "lodash";

// Components
import CustomerStatus from "./CustomerStatus";
import CustomerSources from "./CustomerSources";
import CustomerEntries from "./CustomerEntries";

// Types
import {IStoreCreditCustomer, IStoreCreditSources, IStoreCreditStatus, IStoreCreditEntry} from "../../types/storeCredits";

// Actions
import {StoreCreditsCustomersActions} from "../../store/actions/storeCreditsCustomers";

// #endregion [Imports]

// #region [Variables] =================================================================================================

const {readStoreCreditsCustomerStatus, readStoreCreditsCustomerEntries} = StoreCreditsCustomersActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  readStoreCreditsCustomerStatus: typeof readStoreCreditsCustomerStatus;
  readStoreCreditsCustomerEntries: typeof readStoreCreditsCustomerEntries;
}

interface IProps {
  customer: IStoreCreditCustomer|null;
  setCustomer: (customer: IStoreCreditCustomer|null) => void;
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const StoreCreditsSingleCustomer = (props: IProps) => {
  const {customer, setCustomer, actions} = props;
  const [status, setStatus]: [IStoreCreditStatus[], any] = useState(customer?.status ?? []);
  const [sources, setSources]: [IStoreCreditSources[], any] = useState(customer?.sources ?? []);
  const [entries, setEntries]: [IStoreCreditEntry[], any] = useState([]);
  const [totalEntries, setTotalEntries] = useState(0);
  const [entriesLoaded, setEntriesLoaded] = useState(false); // used for initial loading state.

  const loadEntries = (page: number = 1) => {
    
    if (!customer?.id) return;

    setEntriesLoaded(false);

    actions.readStoreCreditsCustomerEntries({
      id: customer?.id,
      page,
      successCB: (response) => {
        setEntries(response.data);
        setTotalEntries(response.headers["x-total"]);
        setEntriesLoaded(true);
      }
    });
  }

  const closeModal = () => {
    setCustomer(null);
    setSources([]);
    setEntries([]);
  };

  useEffect(() => {
    if (!customer?.id) {
      setStatus([]);
      setSources([]);
      setEntries([]);
      setTotalEntries(0);
      return;
    }

    // if (sources.length && status.length) return;

    actions.readStoreCreditsCustomerStatus({
      id: customer?.id,
      successCB: (response: any) => {
        setStatus(response.data.status);
        setSources(response.data.sources);
      }
    });

    loadEntries();

  }, [customer]);

  if (!customer) return null;

  return (
    <Modal
      width={1100}
      visible={!isNull(customer)}
      footer={false}
      onCancel={() => closeModal()}
      onOk={() => closeModal()}
    >
      <h2>{`${customer.first_name} ${customer.last_name} Stats`}</h2>
      <Row gutter={16}>
        <Col span={12}>
          <CustomerStatus status={status} />
        </Col>
        <Col span={12}>
          <CustomerSources sources={sources} />
        </Col>
      </Row>
      <Divider />
      <CustomerEntries 
        entries={entries} 
        total={totalEntries}
        loadEntries={loadEntries}
        loaded={entriesLoaded}
      />
    </Modal>
  )
}

const mapDispatchToProps = (dispatch: any) => ({
  actions: bindActionCreators({readStoreCreditsCustomerStatus, readStoreCreditsCustomerEntries}, dispatch)
});

export default connect(null, mapDispatchToProps)(StoreCreditsSingleCustomer);

// #endregion [Component]
