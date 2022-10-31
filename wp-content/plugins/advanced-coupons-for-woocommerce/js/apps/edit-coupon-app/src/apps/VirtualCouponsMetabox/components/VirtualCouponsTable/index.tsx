// #region [Imports] ===================================================================================================

// Libraries
import { useState, useEffect, useReducer } from "react";
import { Row, Col, Table, Button, Pagination, message} from "antd";
import { CopyOutlined } from "@ant-design/icons";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";

// Actions
import { VirtualCouponActions } from "../../../../store/actions/virtualCoupon";
import { VirtualCouponQueryActions } from "../../../../store/actions/virtualCouponQuery";
import { VirtualCouponStatsActions } from "../../../../store/actions/virtualCouponStats";

// Components
import FilterVirtualCoupons from "../FilterVirtualCoupons";
import BulkActions from "./BulkActions";
import TableActions from "./TableActions";
import EditableCell from "./EditableCell";

// State Reducers
import editingReducer, {IEditingVirtualCoupon} from "./editingReducer";

// Types
import IStore from "../../../../types/store";
import {IVirtualCoupon} from "../../../../types/virtualCoupon";
import {IVirtualCouponQuery} from "../../../../types/virtualCouponQuery";

// Helpers
import {getCouponId, getAppLabels} from "../../../../helpers/utils";
import {getCustomerLabel} from "./SelectCustomer";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

const { updateVirtualCoupon, rehydrateStoreVirtualCoupons } = VirtualCouponActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  rehydrateStoreVirtualCoupons: typeof rehydrateStoreVirtualCoupons;
  updateVirtualCoupon: typeof updateVirtualCoupon;
}

interface IProps {
  toggleModalView: any;
  virtualCoupons: IVirtualCoupon[];
  query: IVirtualCouponQuery;
  total: number;
  queryActions: typeof VirtualCouponQueryActions;
  statsActions: typeof VirtualCouponStatsActions;
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const VirtualCouponsTable = (props: IProps) => {
  const {toggleModalView, virtualCoupons, query, total, queryActions, statsActions, actions} = props;
  const currentPage = query?.page ?? 0;
  const labels = getAppLabels();
  const [loading, setLoading]: [boolean, any] = useState(true);
  const [refreshList, setRefreshList]: [boolean, any] = useState(false);
  const [totalText, setTotalText]: [string, any] = useState('');
  const [selected, setSelected]: [number[], any] = useState([]);
  const [editData, editDispatch]: [IEditingVirtualCoupon, any] = useReducer(editingReducer, {
    key: "",
    id: 0,
    code: "",
    status: "pending",
    date_created: "",
    date_expire: "",
    user_id: 0,
  });

  /**
   * Callback action to edit a row.
   * 
   * @param record 
   */
  const editRow = ({key, id, code, status, date_created, date_expire, user_id}: IVirtualCoupon) =>{
    editDispatch({type: "all", value: {key, id, code, status, date_created, date_expire, user_id} });
  };

  /**
   * Cancel editing a row.
   */
  const cancelEditRow = () => {
    editDispatch({type: "reset"});
  }

  /**
   * Event: Update virtual coupon.
   */
  const handleUpdateRow = () => {
    actions.updateVirtualCoupon({
      data: editData,
      successCB: (response) => {
        message.success(response.data.message);
      },
      failCB: ({error}) => {
        message.error(error.response.data.message);
      },
      alwaysCB: () => {
        cancelEditRow();
      }
    });
  };

  /**
   * Table columns.
   */
  const columns = [
    {
      title: labels.vc_code,
      dataIndex: "code",
      key: "code",
      className: "vc-coupon-code",
      editable: true,
      sorter: true,
      render: (text: string, record: IVirtualCoupon) => (`${record.main_code}-${record.code}`)
    },
    {
      title: labels.coupon,
      dataIndex: "main_code",
      className: "vc-main-code",
      key: "main_code",
    },
    {
      title: labels.usage_status,
      dataIndex: "status",
      className: "vc-status",
      key: "status",
      editable: true,
      sorter: true,
    },
    {
      title: labels.date_created,
      dataIndex: "date_created",
      key: "date_created",
      className: "vc-date-created",
      editable: true,
      sorter: true,
    },
    {
      title: labels.date_expire,
      dataIndex: "date_expire",
      className: "vc-date-expire",
      key: "date_expire",
      editable: true,
      sorter: true,
    },
    {
      title: labels.owner,
      dataIndex: "user_id",
      key: "user_id",
      className: "vc-user",
      editable: true,
      sorter: true,
      render: (user_id: string, record: IVirtualCoupon) => {
        if (!record.user_id) return '';
        return (
          <span>{getCustomerLabel(record)}</span>
        )
      }
    },
    {
      title: "",
      dataIndex: "id",
      key: "actions",
      className: "vc-actions",
      render: (text: string, record: IVirtualCoupon, index: number) => (
        <TableActions 
          virtualCoupon={record} 
          editData={editData}
          cancelEditRow={cancelEditRow}
          editRow={editRow}
          handleUpdateRow={handleUpdateRow}
          setRefreshList={setRefreshList} 
        />
      ),
    },
  ];

  /**
   * Pass state and props to editable components.
   * This allows editing the editable cells.
   */
  const mergedColumn = columns.map(col => {
    if(!col.editable) return col;

    return {
      ...col,
      onCell: (record: IVirtualCoupon) => ({
        record,
        editData,
        dataIndex: col.dataIndex,
        title: col.title,
        editing: record.key === editData.key,
        editDispatch
      }),
    }
  });

  /**
   * Table's row selection property data.
   * Handle's updating state of selected virtual coupons.
   */
  const rowSelection = {
    onChange: (selectedKeys: React.Key[], selectedRows: IVirtualCoupon[]) => {
      setSelected(selectedRows.map(r => r.id));
    },
    getCheckboxProps: (record: IVirtualCoupon) => ({
      name: record.code,
    }),
  };

  /**
   * Side Effect: Set initial query context when modal is displayed.
   */
  useEffect(() => {
    if (!virtualCoupons.length) {
      queryActions.setQuery({
        coupon_id: getCouponId(),
        page: 1,
      });
    } else {
      setLoading(false);
    } // eslint-disable-next-line
  }, []);

  /**
   * Side Effect: Refresh list of virtual coupons displayed when state is set to true.
   */
  useEffect(() => {
    if(query?.coupon_id && refreshList) {
      setLoading(true);
      actions.rehydrateStoreVirtualCoupons(
        {
          query,
          successCB: (response) => {
            statsActions.setQueryTotal(parseInt(response.headers["x-total"]));
            setTotalText(response.headers["x-total-text"]);
          },
          alwaysCB: () => {
            setLoading(false);
            setRefreshList(false);
          }
        }
      );
    } // eslint-disable-next-line
  }, [refreshList]);

  /**
   * Side Effect: set refresh boolean to true when query data is changed.
   */
  useEffect(() => {
    setRefreshList(true);
  }, [query]);

  return (
    <>
      <Row gutter={16}>
        <Col className="filter-col" md={24} lg={14} xxl={12}>
          <FilterVirtualCoupons />
        </Col>
        <Col className="bulkactions-col" md={18} lg={5} xxl={9}>
          <BulkActions selected={selected} />
        </Col>
        <Col className="nav-col"  md={6} lg={5} xxl={3}>
          <Button
            className="copy-vc-button"
            type="primary"
            onClick={() => toggleModalView('copy')}
          >
            <CopyOutlined />
            {labels.copy_vc}
          </Button>
        </Col>
      </Row>
      <Table 
        className="virtual-coupons-table"
        components={{
          body: {
            cell: EditableCell,
          }
        }}
        rowSelection={{
          type: "checkbox",
          ...rowSelection
        }}
        loading={loading} 
        dataSource={virtualCoupons} 
        columns={mergedColumn}
        pagination={false}
        onChange={(pagination, filters, sorter) => {
          // @ts-ignore
          queryActions.setSortBy(sorter.field);
          // @ts-ignore
          queryActions.setSortOrder("descend" === sorter.order ? "desc" : "asc");
        }}
      />
      {total ? (
        <>
          <Pagination
            defaultCurrent={currentPage}
            hideOnSinglePage={true}
            current={currentPage}
            total={total}
            pageSize={10}
            showSizeChanger={false}
            onChange={(page: number) => {
              queryActions.setPage(page);
              cancelEditRow();
            }}
          />
          <div className="total-text">{totalText}</div>
        </>
      ) : null}
    </>
  );
};

const mapStateToProps = (store: IStore) => ({
  virtualCoupons: store.virtualCoupons,
  total: store.virtualCouponStats?.queryTotal ?? -1,
  query: store.virtualCouponQuery,
});
const mapDispatchToProps = (dispatch: Dispatch) => ({
  queryActions: bindActionCreators({...VirtualCouponQueryActions}, dispatch),
  statsActions: bindActionCreators({...VirtualCouponStatsActions}, dispatch),
  actions: bindActionCreators({rehydrateStoreVirtualCoupons, updateVirtualCoupon}, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(VirtualCouponsTable);

// #endregion [Component]
