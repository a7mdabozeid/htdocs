// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import { ITopCustomer } from "../../types/dashboard";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { useState, useEffect },
    router: { Link },
    antd: { Table },
    redux: { connect },
    pathPrefix,
} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
    customers: ITopCustomer[];
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const TopCustomers = (props: IProps) => {
    const { customers } = props;
    const {
        loyalty_program: { labels },
    } = acfwAdminApp;
    const [loading, setLoading]: [boolean, any] = useState(true);

    const columns = [
        {
            title: labels.customer,
            dataIndex: "name",
            key: "name",
            render: (value: string, record: ITopCustomer) => (
                <Link
                    to={`${pathPrefix}admin.php?page=acfw-loyalty-program&tab=customers&customer=${record.id}`}
                >
                    {value}
                </Link>
            ),
        },
        {
            title: labels.points,
            dataIndex: "points",
            key: "points",
        },
    ];

    /**
     * Set loading state when top customers list is empty.
     */
    useEffect(() => {
        if (Array.isArray(customers)) setLoading(false);
    }, [customers, setLoading]);

    return (
        <Table
            loading={loading}
            pagination={false}
            dataSource={customers}
            columns={columns}
        />
    );
};

const mapStateToProps = (store: any) => ({
    customers: store.dashboard?.customers,
});

export default connect(mapStateToProps, null)(TopCustomers);
