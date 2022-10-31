// #region [Imports] ===================================================================================================

// Libraries
import { useState, useEffect } from "react"; 
import { bindActionCreators, Dispatch } from "redux";
import { Row, Col, Input, Tag, Button, Skeleton, message } from "antd";
import { LeftOutlined, CopyOutlined, DownloadOutlined } from "@ant-design/icons";
import { connect } from "react-redux";
import CsvDownloader from "react-csv-downloader";
// @ts-ignore
import { CopyToClipboard } from "react-copy-to-clipboard";

// Actions
import {VirtualCouponActions} from "../../../../store/actions/virtualCoupon";

// Types
import IStore from "../../../../types/store";
import {IVirtualCouponQuery} from "../../../../types/virtualCouponQuery";
import { IVirtualCouponStats } from "../../../../types/virtualCouponStats";

// Helpers
import { getMainCouponCode, getAppLabels } from "../../../../helpers/utils";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

const { fetchVirtualCouponCodes } = VirtualCouponActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
  fetchVirtualCouponCodes: typeof fetchVirtualCouponCodes;
}

interface IProps {
  toggleModalView: any;
  query: IVirtualCouponQuery;
  stats: IVirtualCouponStats;
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const CopyVirtualCoupons = (props: IProps) => {
  const { toggleModalView, query, actions } = props;
  const labels = getAppLabels();
  const [codes, setCodes]:[string[], any] = useState([]);
  const [loading, setLoading]: [boolean, any] = useState(true);

  /**
   * Refresh codes list call back.
   */
  const refreshCodesList = () => {
    actions.fetchVirtualCouponCodes({
      query,
      successCB: (response) => {
        setCodes(response.data);
      },
      alwaysCB: () => {
        setLoading(false);
      }
    });
  };

  /**
   * Side effect: update codes list when query state store is updated.
   */
  useEffect(() => {
    setLoading(true);
    refreshCodesList();
  }, [query]);

  return (
    <>
      <Row gutter={16}>
        <Col span={3}>
          <Button
            className="manage-vc-button"
            onClick={() => toggleModalView('list')}
          >
            <LeftOutlined />
            {labels.manage_vc}
          </Button>
        </Col>
        <Col span={9}>
          <div className="copy-coupon-filter-tags">
            <Tag><strong>{`${labels.coupon}:`}</strong> {getMainCouponCode()}</Tag>
            {codes.length > 0 && <Tag><strong>{`Total:`}</strong> {codes.length}</Tag>}
            { query?.search && (<Tag><strong>{labels.search}:</strong> {query?.search}</Tag>) }
            { query?.status && (<Tag><strong>{labels.status}:</strong> {query?.status}</Tag>) }
            { "recent" === query?.date_created && (<Tag>{labels.recent_generated}</Tag>) }
          </div>
        </Col>
      </Row>
      
      
      <div className="copy-virtual-coupons">
        {loading ? (
          <Skeleton 
            active
            loading
          />
        ) : (
        <>
          <div className="copy-actions">
            <CopyToClipboard 
              text={codes.join("\n")} 
              onCopy={() => message.success(labels.copy_success)}
            >
              <Button type="primary">
                <CopyOutlined />
                {labels.copy_all}
              </Button>
            </CopyToClipboard>
            <CsvDownloader
              datas={codes.map(c => ({cell1: c}))}
              columns={[{id: 'cell1', displayName: labels.vc_code }]}
              filename="coupons"
              extension=".csv"
            >
              <Button type="primary">
                <DownloadOutlined />
                {labels.download_as_csv}
              </Button>
            </CsvDownloader>
          </div>
          <Input.TextArea 
            value={codes.join("\n")}
            rows={20}
            readOnly
          />
        </>
        )}
      </div>
  </>
  );
}

const mapStateToProps = (store: IStore) => ({
  stats: store.virtualCouponStats,
  query: store.virtualCouponQuery,
});

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators({fetchVirtualCouponCodes}, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(CopyVirtualCoupons);

// #endregion [Component]