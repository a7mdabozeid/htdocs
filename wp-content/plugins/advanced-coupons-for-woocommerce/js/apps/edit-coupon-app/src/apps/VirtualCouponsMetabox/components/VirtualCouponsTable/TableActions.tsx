// #region [Imports] ===================================================================================================

// Libraries
import { useEffect, useState } from "react";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import { Button, Popconfirm, message } from "antd";
import { EditOutlined, DeleteOutlined } from "@ant-design/icons";

// Actions
import {VirtualCouponActions} from "../../../../store/actions/virtualCoupon";

// Types
import { IVirtualCoupon } from "../../../../types/virtualCoupon";
import { IEditingVirtualCoupon } from "./editingReducer";

// Helpers
import {getAppLabels} from "../../../../helpers/utils";

// #endregion [Imports]


// #region [Interfaces] ================================================================================================

interface IProps {
  virtualCoupon: IVirtualCoupon;
  editData: IEditingVirtualCoupon;
  editRow: any;
  cancelEditRow: any;
  handleUpdateRow: any;
  setRefreshList: any;
  actions: typeof VirtualCouponActions;
}

// #endregion [Interfaces]


// #region [Component] =================================================================================================

const TableActions = (props: IProps) => {
  const {virtualCoupon, editData, editRow, cancelEditRow, handleUpdateRow, setRefreshList, actions} = props;
  const labels = getAppLabels();
  const [disabled, setDisabled]: [boolean, any] = useState(false);
  const [loading, setLoading]: [string, any] = useState('');

  /**
   * Delete virtual coupon action callback.
   */
  const deleteVirtualCoupon = () => {
    setDisabled(true);
    setLoading('delete');
    actions.deleteVirtualCoupon({
      id: virtualCoupon.id,
      successCB: (response) => {
        message.success(response.data.message);
        setRefreshList(true);
      },
      failCB: ({error}) => {
        message.error(error.response.data.message);
      },
      alwaysCB: () => {
        setDisabled(false);
        setLoading('');
      }
    });
  };

  /**
   * Side Effect: Set disabled state to true if editing key is not empty.
   */
  useEffect(() => {
    setDisabled("" !== editData.key)
    setLoading("");
  }, [editData]);

  return (
    <div className="vc-actions">
      {editData?.key === virtualCoupon.key ? (
          <>
            <Button 
              className="confirm-edit-vc"
              type="primary"
              size="small"
              loading={"edit" === loading}
              onClick={() => {
                setLoading("edit");
                handleUpdateRow();
              }}
            >
              {labels.edit}
            </Button>
            <Button
              className="cancel-edit-vc"
              size="small"
              onClick={cancelEditRow}
            >
              {labels.cancel}
            </Button>
          </>
      ) : (
        <>
          <Button 
            className="edit-vc" 
            size="small"
            onClick={() => editRow(virtualCoupon)}
            disabled={disabled}>
              <EditOutlined />
          </Button>
          <Popconfirm
            title={labels.delete_prompt}
            onConfirm={deleteVirtualCoupon}
            okText={labels.yes}
            cancelText={labels.cancel}
          >
            <Button 
              className="delete-vc" 
              size="small"
              disabled={disabled}
              loading={'delete' === loading}
            >
              <DeleteOutlined />
            </Button>
          </Popconfirm>
        </>
      )}
      
      
    </div>
  );
}

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators({...VirtualCouponActions}, dispatch),
});

export default connect(null, mapDispatchToProps)(TableActions);

// #endregion [Component]