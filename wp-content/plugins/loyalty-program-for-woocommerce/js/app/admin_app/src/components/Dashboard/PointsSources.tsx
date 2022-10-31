// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import { IPointSource } from "../../types/dashboard";

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
    sources: IPointSource[];
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PointsSources = (props: IProps) => {
    const { sources } = props;
    const {
        loyalty_program: { labels },
    } = acfwAdminApp;
    const [loading, setLoading]: [boolean, any] = useState(true);

    const columns = [
        {
            title: labels.source,
            dataIndex: "label",
            key: "label",
        },
        {
            title: labels.points,
            dataIndex: "points",
            key: "points",
        },
    ];

    /**
     * Set loading state when sources list is empty.
     */
    useEffect(() => {
        if (sources && sources.length) setLoading(false);
    }, [sources, setLoading]);

    return (
        <Table
            loading={loading}
            pagination={false}
            dataSource={sources}
            columns={columns}
        />
    );
};

const mapStateToProps = (store: any) => ({ sources: store.dashboard?.sources });

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({}, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(PointsSources);
