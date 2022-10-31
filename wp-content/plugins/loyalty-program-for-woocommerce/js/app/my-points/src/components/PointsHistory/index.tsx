// #region [Imports] ===================================================================================================

// Libraries
import React, { useEffect, useState } from "react";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import { Table, Pagination } from "antd";
import {defaults} from "lodash";

// Actions
import { HistoryEntriesActions } from "../../store/actions/history";

// Types
import IStore from "../../types/store";
import IHistoryEntry from "../../types/history";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var lpfwMyPoints: any;

const { readHistoryEntries } = HistoryEntriesActions;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
    readHistoryEntries: typeof readHistoryEntries;
}

interface IProps {
    entries: IHistoryEntry[];
    actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PointsHistory = (props: IProps) => {
    const { entries, actions } = defaults(props, {entries: []});
    const { labels } = lpfwMyPoints;
    const [loading, setLoading]: [boolean, any] = useState(true);
    const [currentPage, setCurrentPage]: [number, any] = useState(1);
    const [total, setTotal]: [number, any] = useState(0);

    const columns = [
        {
            title: `${labels.date}`,
            dataIndex: "date",
            key: "date",
        },
        {
            title: `${labels.activity}`,
            dataIndex: "activity",
            key: "activity",
        },
        {
            title: `${labels.points}`,
            dataIndex: "points",
            key: "points",
            render: (points: number) => points.toLocaleString(),
        },
        {
            title: `${labels.related}`,
            dataIndex: "rel_label",
            key: "rel_label",
            render: (label: string, record: IHistoryEntry) => (
                <>
                    {record.rel_link ? (
                        <a href={record.rel_link} target="_blank">
                            {label}
                        </a>
                    ) : (
                        <span>{label}</span>
                    )}
                </>
            ),
        },
    ];

    useEffect(() => {
        if (entries.length) return;
        actions.readHistoryEntries({
            page: 1,
            successCB: (response) => {
                setTotal(response.headers["x-total"]);
                setLoading(false);
            },
        });
    }, []);

    /**
     * Handle pagination click event.
     *
     * @param page
     */
    const handlePagination = (page: number) => {
        setCurrentPage(page);
        setLoading(true);
        actions.readHistoryEntries({
            page: page,
            successCB: () => setLoading(false),
        });
    };

    return (
        <div className="user-points-history">
            <h3>{labels.points_history}</h3>
            <Table
                loading={loading}
                pagination={false}
                dataSource={entries}
                columns={columns}
            />
            {total && !loading ? (
                <Pagination
                    defaultCurrent={currentPage}
                    hideOnSinglePage={true}
                    current={currentPage}
                    total={total}
                    pageSize={10}
                    showSizeChanger={false}
                    onChange={handlePagination}
                />
            ) : null}
        </div>
    );
};

const mapStateToProps = (store: IStore) => ({ entries: store.history });

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({ readHistoryEntries }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(PointsHistory);

// #endregion [Component]
