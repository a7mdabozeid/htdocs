// #region [Imports] ===================================================================================================

// Libraries
import {Modal} from "antd";

// Components
import VirtualCouponsTable from "../VirtualCouponsTable";
import CopyVirtualCoupons from "../CopyVirtualCoupons";

// Helpers
import { axiosCancel } from "../../../../helpers/axios";
import { getAppLabels } from "../../../../helpers/utils";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Interfaces] ================================================================================================

interface IProps {
  showModal: string;
  setShowModal: any;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const VirtualCouponsModal = (props: IProps) => {

  const {showModal, setShowModal} = props;
  const labels = getAppLabels();

  /**
   * Toggle modal content view from. 'list' for managing vcoupons, 'copy' for copying context, 
   * and blank for closing the modal.
   * 
   * @param value
   */
  const toggleModalView = (value: string) => {
    axiosCancel(`rehydrate-virtual-coupons`);
    axiosCancel(`fetch-virtual-coupon-codes`);
    setShowModal(value);
  };
  
  return (
    <Modal
      className="virtual-coupons-modal"
      title={labels.modal_title}
      visible={showModal !== ''}
      width={`90vw`}
      footer={null}
      onCancel={() => toggleModalView('')}
      onOk={() => toggleModalView('')}
    >
      {'list' === showModal ? (
        <VirtualCouponsTable toggleModalView={toggleModalView} />
      ) : null}
      {'copy' === showModal ? (
        <CopyVirtualCoupons toggleModalView={toggleModalView} />
      ) : null}
    </Modal>
  );
}

export default VirtualCouponsModal;

// #endregion [Component]
