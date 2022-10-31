// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Actions
import { CustomerDataActions } from "../../store/actions/customers";

// Types
import ICustomer, { IHistoryEntry } from "../../types/customer";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { useState, useEffect },
    antd: { Card, Table, Pagination },
    redux: { bindActionCreators, connect },
} = acfwpElements;
const { readCustomerHistory, setStoreHIstoryEntries } = CustomerDataActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
    readCustomerHistory: typeof readCustomerHistory;
    setStoreHIstoryEntries: typeof setStoreHIstoryEntries;
}

interface IProps {
    customer: ICustomer;
    actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const CustomerHistory = (props: IProps) => {
    const { customer, actions } = props;
    const entries = customer?.history ?? [];
    const {
        loyalty_program: { labels },
    } = acfwAdminApp;
    const [loading, setLoading]: [boolean, any] = useState(true);
    const [currentPage, setCurrentPage]: [number, any] = useState(1);
    const [total, setTotal]: [number, any] = useState(0);
    const unclaimedPoints: number = customer?.status ? customer?.status[1].points : -1;

    const columns = [
        {
            title: labels.date,
            dataIndex: "date",
            key: "date",
        },
        {
            title: labels.activity,
            dataIndex: "activity",
            key: "activity",
        },
        {
            title: labels.points,
            dataIndex: "points",
            key: "points",
        },
        {
            title: labels.related,
            dataIndex: "rel_label",
            key: "rel_label",
            render: (label: string, record: IHistoryEntry) => {

                if (!record.rel_link)
                    return label;

                return (
                    <a href={record.rel_link} target="_blank">
                        {label}
                    </a>
                )
            },
        },
    ];

    /**
     * Reload customer history table data.
     * 
     * @param page 
     * @returns 
     */
    const reloadCustomerHistory = (page: number) => {
        if (!customer?.id) return;

        setLoading(true);
        setCurrentPage(page);

        actions.readCustomerHistory({
            id: customer.id,
            page: page,
            successCB: (response) => {
                if (1 === page) setTotal(response.headers["x-total"]);
                setLoading(false);
            },
        });
    };

    /**
     * Set loading state to true when customer ID is changed.
     * This is so we don't show a "no data" when history state is cleared.
     */
    useEffect(() => {
        setLoading(true);
    }, [customer?.id]);

    /**
     * Refresh table when customer total points value has changed.
     */
    useEffect(() => {
        if (!customer?.id) return;

        reloadCustomerHistory(currentPage);
    }, [unclaimedPoints]);

    return (
        <Card title={labels.points_history}>
            <Table
                loading={loading}
                pagination={false}
                dataSource={entries}
                columns={columns}
            />
            {total ? (
                <Pagination
                    defaultCurrent={currentPage}
                    hideOnSinglePage={true}
                    disabled={loading}
                    current={currentPage}
                    total={total}
                    pageSize={10}
                    showSizeChanger={false}
                    onChange={reloadCustomerHistory}
                />
            ) : null}
        </Card>
    );
};

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators(
        { readCustomerHistory, setStoreHIstoryEntries },
        dispatch
    ),
});

export default connect(null, mapDispatchToProps)(CustomerHistory);
