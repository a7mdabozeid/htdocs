// #region [Imports] ===================================================================================================

// Libraries
import React, { useEffect, useState } from "react";
import { useLocation, useHistory } from "react-router-dom";
import { Tabs } from "antd";

// Components
import PointsBalance from "./PointsBalance";
import PointsHistory from "./PointsHistory";
import PointsRedeem from "./PointsRedeem";

// Helpers
import { getPathPrefix } from "../helpers/utils";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var lpfwMyPoints: any;

const { TabPane } = Tabs;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const App = () => {
    const [tab, setTab]: [string, any] = useState("");
    const { labels } = lpfwMyPoints;
    const urlParams = new URLSearchParams(useLocation().search);
    const history = useHistory();
    const urlTab = urlParams.get("tab");
    const pathPrefix = getPathPrefix();

    const tabs = ["balance", "history", "redeem"];

    useEffect(() => setTab(urlTab ? urlTab : "balance"), [urlTab]);

    // handle tab click event
    const handleTabClick = (key: string) => {
        setTab(key);
        history.push(`${pathPrefix}lpfw-my-points/?tab=${key}`);
    };

    return (
        <Tabs
            activeKey={tab}
            defaultActiveKey={tab}
            onTabClick={handleTabClick}
        >
            <TabPane tab={labels.points_balance} key="balance">
                <PointsBalance />
            </TabPane>
            <TabPane tab={labels.points_history} key="history">
                <PointsHistory />
            </TabPane>
            <TabPane tab={labels.redeem_points} key="redeem">
                <PointsRedeem />
            </TabPane>
        </Tabs>
    );
};

export default App;

// #endregion [Component]
