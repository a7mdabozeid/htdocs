// #region [Imports] ===================================================================================================

// Library
import {Card, Skeleton} from "antd";

// #endregion [Imports]

// #region [Component] =================================================================================================

const ReportWidgetsSkeleton = () => {

  return (
    <div className="report-widgets">
      { [...Array(8)].map((_, key) => (
        <Card key={key} className="widget widget-skeleton">
          <Skeleton active />
        </Card>
      ))}
    </div>
  );
};

export default ReportWidgetsSkeleton;

// #endregion [Component]
