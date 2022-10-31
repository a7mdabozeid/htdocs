// #region [Imports] ===================================================================================================

// Libraries
import {useEffect, useState} from "react";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import {Button, InputNumber} from 'antd';

// Actions
import {VirtualCouponActions} from "../../../../store/actions/virtualCoupon";
import {VirtualCouponQueryActions} from "../../../../store/actions/virtualCouponQuery";
import {VirtualCouponStatsActions} from "../../../../store/actions/virtualCouponStats";

// Helpers
import {getCouponId, getAppLabels} from "../../../../helpers/utils";


// #endregion [Imports]

// #region [Variables] =================================================================================================

const { bulkCreateVirtualCoupons } = VirtualCouponActions;
const { setTotal } = VirtualCouponStatsActions;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
  bulkCreateVirtualCoupons: typeof bulkCreateVirtualCoupons;
  setTotal: typeof setTotal;
}

interface IProps {
  setShowModal: any;
  queryActions: typeof VirtualCouponQueryActions;
  actions: IActions;
}

// #region [Component] =================================================================================================

const GenerateVirtualCouponsForm = (props: IProps) => {

  const {setShowModal, queryActions, actions} = props;
  const coupon_id = getCouponId();
  const[count, setCount]:[number, any] = useState(0);
  const[loading, setLoading]:[boolean, any] = useState(false);
  const[disabled, setDisabled]:[boolean, any] = useState(false);
  const [successMessage, setSuccessMessage]: [string, any] = useState('');
  const labels = getAppLabels();

  /**
   * Side Effect: update generate button disabled state prop everytime count state is updated.
   */
  useEffect(() => {
    setDisabled(0 >= count);
  }, [count]);

  /**
   * Event: generate virtual coupons on button click.
   */
  const generateVirtualCoupons = () => {
    setLoading(true);
    
    actions.bulkCreateVirtualCoupons({
      coupon_id,
      count,
      successCB: (response) => {
        setSuccessMessage(response.data.message);
        actions.setTotal(response.data.total);
        setCount(0);
        setLoading(false);
      }
    });
  };

  return (
    <div className="vc-generate-form">
      <div className="input-field">
        <label>{labels.number_of_codes}</label>
        <InputNumber min={0} max={10000} value={count} onChange={(value) => setCount(value)}/>
      </div>
      <div className="input-field">
        <Button 
          type="primary"
          disabled={disabled} 
          loading={loading} 
          onClick={generateVirtualCoupons}>
          {labels.generate_vc}
        </Button>
      </div>
      {'' !== successMessage ? (
        <>
        <p className="success-message">{successMessage}</p>
        <Button
          className="copy-fresh-btn"
          type="primary"
          onClick={() => {
            queryActions.setCouponId(coupon_id);
            queryActions.setDateCreated("recent");
            setShowModal('copy');
            setSuccessMessage('');
          }}
        >
          {labels.copy_new_vc}
        </Button>
        </>
      ) : null}
    </div>
  );
}

const mapDispatchToProps = (dispatch: Dispatch) => ({
  queryActions: bindActionCreators({ ...VirtualCouponQueryActions }, dispatch),
  actions: bindActionCreators({ bulkCreateVirtualCoupons, setTotal }, dispatch),
});

export default connect(null, mapDispatchToProps)(GenerateVirtualCouponsForm);

// #endregion [Component]