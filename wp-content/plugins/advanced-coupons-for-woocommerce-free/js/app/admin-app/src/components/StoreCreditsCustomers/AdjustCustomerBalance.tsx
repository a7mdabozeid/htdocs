// #region [Imports] ===================================================================================================

// Libraries
import { useEffect, useState } from "react";
import { connect } from "react-redux";
import { bindActionCreators } from "redux";
import {Select, Input, Button, message} from "antd";

// Actions
import {StoreCreditsCustomersActions} from "../../store/actions/storeCreditsCustomers";

// Types
import {IStoreCreditCustomer} from "../../types/storeCredits";
import {IStore} from "../../types/store";

// Helpers
import {priceFormat, validatePrice, parsePrice} from "../../helpers/currency";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

const {adjustCustomerStoreCredits, setStoreCreditsCustomerBalance} = StoreCreditsCustomersActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  adjustCustomerStoreCredits: typeof adjustCustomerStoreCredits;
  setStoreCreditsCustomerBalance: typeof setStoreCreditsCustomerBalance
}

interface IProps {
  customer: IStoreCreditCustomer|null;
  setAdjustCustomer: (customer: IStoreCreditCustomer|null) => void;
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const AdjustCustomerBalance = (props: IProps) => {
  const {customer, setAdjustCustomer, actions} = props;
  const {adjust_modal, currency} = acfwAdminApp.store_credits_page;
  const [loading, setLoading] = useState(false);
  const [type, setType] = useState('increase');
  const [amount, setAmount] = useState('');
  const [newBalance, setNewBalance] = useState('');

  const handleAdjustment = () => {
    if (!customer) return;

    if (!validatePrice(amount)) {
      message.error(adjust_modal.invalid_price);
      return;
    }

    setLoading(true);
    actions.adjustCustomerStoreCredits({
      id: customer?.id,
      type,
      amount: parsePrice(amount),
      successCB: (response: any) => {
        setLoading(false);
        message.success(response.data.message);
        actions.setStoreCreditsCustomerBalance({
          id: customer?.id,
          balance: response.data.balance,
          balance_raw: response.data.balance_raw
        });
        setAdjustCustomer(null);
        setAmount('');
        setType('increase');
      },
      failCB: ({error}: any) => {
        setLoading(false);
        message.error(error.response.data.message);
      }
    });
  }

  // calculate new balance when either customer or amount values are changed.
  useEffect(() => {
    if (!customer || !amount) {
      setNewBalance('');
      return;
    }

    const newBalance = "increase" === type ? customer.balance_raw + parsePrice(amount) : customer.balance_raw - parsePrice(amount);
    setNewBalance(priceFormat(Math.max(0, newBalance)));
  }, [customer, amount, type]);

  if (!customer) return null;

  return (
    <div className="adjust-store-credits">
      <h2>{adjust_modal.title}</h2>
      <p className="description">{adjust_modal.description}</p>    
      <h3 className="current-balance">{adjust_modal.current_balance.replace('{balance}', customer.balance)}</h3>
      <div className="adjust-store-credits-form">
        <Select
          value={type}
          onSelect={(value: string) => setType(value)}
        >
          <Select.Option value="increase">{adjust_modal.increase}</Select.Option>
          <Select.Option value="decrease">{adjust_modal.decrease}</Select.Option>
        </Select>
        <Input 
          value={amount} 
          onChange={(e) => setAmount(e.target.value)}
          addonBefore={currency.symbol}
          allowClear
        />
      </div>
      {newBalance ? (
        <h3 
          className="new-balance" 
          dangerouslySetInnerHTML={
            {__html: adjust_modal.new_balance.replace('{balance}', `<span>${newBalance}</span>`) }
          } 
        />
      ): null}
      <Button 
      className="make-adjustment-btn"
        type="primary"
        size="large"
        loading={loading}
        onClick={handleAdjustment}
      >
        {adjust_modal.make_adjustment}
      </Button>
    </div>
  )
}

const mapDispatchToProps = (dispatch: any) => ({
  actions: bindActionCreators({adjustCustomerStoreCredits, setStoreCreditsCustomerBalance}, dispatch)
});

export default connect(null, mapDispatchToProps)(AdjustCustomerBalance);

// #endregion [Component]
