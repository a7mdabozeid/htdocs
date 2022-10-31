// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import { IPointStatus } from "../../types/dashboard";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { useState, useEffect },
    antd: { Table },
    redux: { bindActionCreators, connect },
} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
    status: IPointStatus[];
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PointStatus = (props: IProps) => {
    const { status } = props;
    const {
        loyalty_program: { labels },
    } = acfwAdminApp;
    const { information, points, value } = labels;
    const [loading, setLoading]: [boolean, any] = useState(true);

    const columns = [
        {
            title: information,
            dataIndex: "label",
            key: "label",
        },
        {
            title: points,
            dataIndex: "points",
            key: "points",
        },
        {
            title: value,
            dataIndex: "value",
            key: "value",
        },
    ];

    /**
     * Set loading state when status list is empty.
     */
    useEffect(() => {
        if (status && status.length) setLoading(false);
    }, [status, setLoading]);

    return (
        <Table
            loading={loading}
            pagination={false}
            dataSource={status}
            columns={columns}
        />
    );
};

const mapStateToProps = (store: any) => ({ status: store.dashboard?.status });

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({}, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(PointStatus);
