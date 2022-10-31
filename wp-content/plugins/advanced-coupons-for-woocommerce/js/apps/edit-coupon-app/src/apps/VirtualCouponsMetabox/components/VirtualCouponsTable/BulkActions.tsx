// #region [Imports] ===================================================================================================

// Libraries
import { useState } from "react";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import { Button, Popconfirm, message } from "antd";
import { DeleteOutlined } from "@ant-design/icons";

// Actions
import { VirtualCouponActions } from "../../../../store/actions/virtualCoupon";
import { VirtualCouponQueryActions } from "../../../../store/actions/virtualCouponQuery";
import { VirtualCouponStatsActions } from "../../../../store/actions/virtualCouponStats";

// Helpers
import { getCouponId, getAppLabels } from "../../../../helpers/utils";

// Types
import IStore from "../../../../types/store";
import { IVirtualCouponQuery } from "../../../../types/virtualCouponQuery";

// #endregion [Imports]

// #region [Variables] =================================================================================================

const { bulkDeleteVirtualCoupons } = VirtualCouponActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  bulkDeleteVirtualCoupons: typeof bulkDeleteVirtualCoupons;
}

interface IProps {
  selected: number[];
  query: IVirtualCouponQuery;
  queryActions: typeof VirtualCouponQueryActions;
  statsActions: typeof VirtualCouponStatsActions;
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const BulkActions = (props: IProps) => {
  const { selected, query, queryActions, statsActions, actions } = props;
  const labels = getAppLabels();
  const [loading, setLoading]: [boolean, any] = useState(false);

  /**
   * Event: Bulk delete virtual coupons after prompt confirm.
   */
  const handleBulkDelete = () => {
    setLoading(true);
    actions.bulkDeleteVirtualCoupons({
      coupon_id: getCouponId(),
      ids: selected,
      successCB: (response) => {
        setLoading(false);
        message.success(response.data.message);
        statsActions.setTotal(response.data.total);

        const totalPages = Math.ceil(response.data.total/10);
        queryActions.setPage(query.page <= totalPages ? query.page : 1);
      },
      failCB: (error) => {
        message.error(error.response.data.message);
        setLoading(false);
      }
    });
  }
  
  if (!selected.length) return null;

  return (
    <Popconfirm
      title={labels.bulk_delete_prompt.replace('{count}', selected.length)}
      onConfirm={handleBulkDelete}
      okText={labels.yes}
      cancelText={labels.cancel}
    >
      <Button
        danger
        loading={loading}
      >
        <DeleteOutlined />
        {labels.bulk_delete}
      </Button>
    </Popconfirm>
    
  );
};

const mapStateToProps = (store: IStore) => ({
  query: store.virtualCouponQuery,
});
const mapDispatchToProps = (dispatch: Dispatch) => ({
  queryActions: bindActionCreators({...VirtualCouponQueryActions}, dispatch),
  statsActions: bindActionCreators({...VirtualCouponStatsActions}, dispatch),
  actions: bindActionCreators({bulkDeleteVirtualCoupons}, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(BulkActions);