// #region [Imports] ===================================================================================================

// Libraries
import { Table } from "antd";

// Components
import WidgetLinkIcon from "./WidgetLinkIcon";
import Tooltip from "./Tooltip";

// Types
import { IDashboardWidget, ICouponWidgetTableData } from "../../../types/dashboard";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IProps {
  widget: IDashboardWidget;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const CouponTableWidget = (props: IProps) => {
  const {widget} = props;
  const {dashboard_page: {labels, coupons_list_link}, admin_url} = acfwAdminApp;

  const columns = [
    {
      title: labels.coupon,
      dataIndex: 'coupon',
      key: 'coupon',
      render: (text: string, record: ICouponWidgetTableData) => (
        <a href={`${admin_url}post.php?post=${record.id}&action=edit`} target="_blank" rel="noreferrer">{text}</a>
      )
    },
    {
      title: labels.uses,
      dataIndex: 'usage_total',
      key: 'usage_total',
    },
    {
      title: (
        <>
          <span className="discounted-label">{labels.discounted}</span>
          {widget?.tooltip_html ? <Tooltip content={widget.tooltip_html} /> : null}
        </>        
      ),
      dataIndex: 'discount_total',
      key: 'discount_total',
    }
  ];
  
  return (
    <div className="widget coupon-table-widget">
      <div className="widget-header">
        <h3 className="widget-name" dangerouslySetInnerHTML={{ __html: widget.title_html }} />
        <a href={coupons_list_link} target="_blank" rel="noreferrer">
          <WidgetLinkIcon />
        </a>
      </div>
      
      {widget.table_data && 
        <Table 
          bordered={true}
          dataSource={widget.table_data} 
          columns={columns} 
          pagination={false} 
        />
      }
    </div>
  );
};

export default CouponTableWidget;

// #endregion [Component]
