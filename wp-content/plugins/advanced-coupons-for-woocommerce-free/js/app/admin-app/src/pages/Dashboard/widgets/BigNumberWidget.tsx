// #region [Imports] ===================================================================================================

// Libraries
import { Card, Popover } from "antd";
import { ExclamationCircleFilled } from "@ant-design/icons";

// Components
import WidgetLinkIcon from "./WidgetLinkIcon";
import Tooltip from "./Tooltip";

// Types
import { IDashboardWidget } from "../../../types/dashboard";

// #endregion [Imports]

// #region [Interfaces]=================================================================================================

interface IProps {
  widget: IDashboardWidget;
  onClickLink: (arg: string) => void;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const BigNumberWidget = (props: IProps) => {
  const {widget, onClickLink} = props;
  const pageKey = widget.page_link ?? false;

  return (
    <Card className="widget big-number-widget">
      <p className="title">
        <span className="widget-value" dangerouslySetInnerHTML={{__html: widget.title_html}} />
        { widget?.tooltip_html ? <Tooltip content={widget.tooltip_html} /> : null}
      </p>
      <p className="description">
        <span className="widget-name" dangerouslySetInnerHTML={{__html: widget.description_html}} />
        {pageKey ? (
          <a onClick={() => onClickLink(pageKey)}  href="javascript:void(0);"><WidgetLinkIcon /></a>
        ) : null}
      </p>
    </Card>
  );
};

export default BigNumberWidget;

// #endregion [Component]
