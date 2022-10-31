// #region [Imports] ===================================================================================================

// Libraries
import { Card } from "antd";

// Types
import { IDashboardWidget } from "../../../types/dashboard";

// #endregion [Imports]

// #region [Interfaces]=================================================================================================

interface IProps {
  widget: IDashboardWidget;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const UpsellWidget = (props: IProps) => {
  const {widget} = props;

  return (
    <Card className="widget upsell-widget">
      <p className="title widget-name" dangerouslySetInnerHTML={{__html: widget.title_html}} />
      <p className="description" dangerouslySetInnerHTML={{__html: widget.description_html}} />
    </Card>
  );
};

export default UpsellWidget;

// #endregion [Component]
