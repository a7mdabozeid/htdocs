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

const CustomerStatus = (props: IProps) => {
    const { customer: {status} } = props;
    const {
        loyalty_program: { labels },
    } = acfwAdminApp;
    const { points_status, information, points, value } = labels;
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
        else setLoading(true);
    }, [status, setLoading]);

    return (
        <Card className="customer-points-status" title={points_status}>
            <Table
                loading={loading}
                pagination={false}
                dataSource={status}
                columns={columns}
            />
        </Card>
    );
};

export default CustomerStatus;
