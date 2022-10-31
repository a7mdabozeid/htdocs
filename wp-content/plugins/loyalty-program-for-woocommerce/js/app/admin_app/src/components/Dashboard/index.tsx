// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// CSS
import "./index.scss";

// Components
import PointsStatus from "./PointsStatus";
import PointsSources from "./PointsSources";
import TopCustomers from "./TopCustomers";
import PointsHistory from "./PointsHistory";

// Types
import IDashboardData from "../../types/dashboard";

// Actions
import { DashboardDataActions } from "../../store/actions/dashboard";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { Fragment, useEffect },
    redux: { bindActionCreators, connect },
    antd: { Row, Col, Card },
} = acfwpElements;

const { readDashboardData } = DashboardDataActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
    readDashboardData: typeof readDashboardData;
}

interface IProps {
    dashboard: IDashboardData|null;
    actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const Dashboard = (props: IProps) => {
    const { dashboard, actions } = props;
    const {
        loyalty_program: {
            labels: { points_status, points_sources, top_customers, points_history },
        },
    } = acfwAdminApp;

    /**
     * Initialize loading dashboard data.
     */
    useEffect(() => {
        if (!dashboard) actions.readDashboardData({});
    }, []);

    return (
        <Fragment>
            <Row className="lpfw-dashboard" gutter={20}>
                <Col span={8}>
                    <Card title={points_status}>
                        <PointsStatus />
                    </Card>
                </Col>
                <Col span={8}>
                    <Card title={points_sources}>
                        <PointsSources />
                    </Card>
                </Col>
                <Col span={8}>
                    <Card title={top_customers}>
                        <TopCustomers />
                    </Card>
                </Col>
                <Col span={24}>
                    <PointsHistory />
                </Col>
            </Row>
        </Fragment>
    );
};

const mapStateToProps = (store: any) => ({ dashboard: store.dashboard });

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({ readDashboardData }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(Dashboard);

// #endregion [Component]
