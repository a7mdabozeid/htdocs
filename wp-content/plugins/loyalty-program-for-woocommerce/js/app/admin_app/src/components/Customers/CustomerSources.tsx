// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import ICustomer from "../../types/customer";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { useState, useEffect },
    antd: { Card, Table },
} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
    customer: ICustomer;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const CustomerSources = (props: IProps) => {
    const { customer: {sources} } = props;
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
        else setLoading(true);
    }, [sources, setLoading]);

    return (
        <Card className="customer-points-sources" title={labels.points_sources}>
            <Table
                loading={loading}
                pagination={false}
                dataSource={sources}
                columns={columns}
            />
        </Card>
    );
};

export default CustomerSources;
