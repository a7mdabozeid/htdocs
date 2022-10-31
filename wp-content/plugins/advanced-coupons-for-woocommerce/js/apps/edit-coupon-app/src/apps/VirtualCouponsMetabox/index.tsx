// #region [Imports] ===================================================================================================

// Libraries
import {useEffect, useState} from "react";
import {Button, Progress, Spin} from 'antd';
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";

// Actions
import {VirtualCouponStatsActions} from "../../store/actions/virtualCouponStats";

// Components
import GenerateVirtualCouponsForm from "./components/GenerateVirtualCouponsForm";
import VirtualCouponsModal from "./components/VirtualCouponsModal";

// Types
import IStore from "../../types/store";

// Helpers
import {getCouponId, getAppLabels} from "../../helpers/utils";

// CSS
import "./index.scss";

// #endregion [Imports]

// #region [Interfaces] ================================================================================================

interface IProps {
  used: number;
  total: number;
  actions: typeof VirtualCouponStatsActions;
}

// #endregion [Interfaces]


// #region [Component] =================================================================================================

const VirtualCouponsMetabox = (props: IProps) => {
  const {used, total, actions} = props;
  const [showModal, setShowModal]: [string, any] = useState('');
  const [loading, setLoading]: [boolean, any] = useState(true);
  const labels = getAppLabels();

  /**
   * Fetch virtual coupon stats on initial load.
   */
  useEffect(() => {
    actions.rehydrateStats({
      coupon_id: getCouponId(),
      successCB: (response) => {
        setLoading(false);
        actions.setStats(response.data);
      }
    }) // eslint-disable-next-line
  }, []);

  return (
    <>
      {total && !loading ? (
        <div className="vc-usage-stats">
          <p dangerouslySetInnerHTML={{__html: labels.status_text.replace('{status}', `<strong>${used}/${total}</strong>`)}} />
          <Progress percent={(used/total)*100} showInfo={false} />
        <Button onClick={()=> setShowModal('list')}>{labels.manage_vc}</Button>
          </div>
      ) : null}
      {loading && (
        <div className="spinner-center">
          <Spin />
        </div>
      )}
      <GenerateVirtualCouponsForm setShowModal={setShowModal} />
      <VirtualCouponsModal showModal={showModal} setShowModal={setShowModal} />
    </>
  );
}

const mapStateToProps = (store: IStore) => ({
  used: store.virtualCouponStats?.used ?? 0,
  total: store.virtualCouponStats?.total ?? 0,
});

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators({...VirtualCouponStatsActions}, dispatch),
})

export default connect(mapStateToProps, mapDispatchToProps)(VirtualCouponsMetabox);

// #endregion [Component]