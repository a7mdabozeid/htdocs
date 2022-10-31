// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

import { ISectionField } from "../../../types/section";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { Fragment, useEffect, useReducer },
    lodash: { isEqual },
    antd: { Table, DatePicker, Input },
    antdIcons: { DeleteOutlined, PlusCircleOutlined },
    moment,
} = acfwpElements;
const { RangePicker } = DatePicker;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IOrderPeriod {
    sdate: string;
    stime: string;
    edate: string;
    etime: string;
    points: string;
}

interface IDateRangePoint {
    dateRange: any | null;
    points: string;
}

interface IProps {
    field: ISectionField;
    value: IOrderPeriod[];
    handleValueChange: any;
}

// #endregion [Interfaces]

// #region [Reducer] ===================================================================================================

function reducer(state: IDateRangePoint[], action: any) {
    const { type, payload } = action;
    const temp = [...state];
    switch (type) {
        case "rehydrate":
            return payload;

        case "delete_row": {
            temp.splice(payload, 1);
            return temp;
        }

        case "add_row": {
            temp.splice(payload + 1, 0, {
                dateRange: null,
                points: "",
            });
            return temp;
        }

        case "set_data": {
            const { index, value } = payload;
            temp[index] = { ...value };
            return temp;
        }

        default:
            return state;
    }
}

// #endregion [Reducer]

// #region [Component] =================================================================================================

const OrderPeriod = (props: IProps) => {
    const { value, handleValueChange } = props;
    const {
        loyalty_program: { labels },
    } = acfwAdminApp;
    const [data, dispatchData]: [IDateRangePoint[], any] = useReducer(
        reducer,
        []
    );

    /**
     * load initial value from store to local reducer state.
     */
    useEffect(() => {
        const temp = "string" === typeof(value) ? JSON.parse(value) : value;

        // skip dispatch if value is not valid.
        if ( ! temp || !temp.length ) return;

        dispatchData({
            type: "rehydrate",
            payload: temp.map(orderPeriodToDateRangeData),
        });
    }, []);

    /**
     * Save value to server when local state and store state is not the same excluding incomplete rows.
     */
    useEffect(() => {
        const valid = data
            .sort((a, b) => { // sort by start timestamp from lowest to highest
                
                if (a?.dateRange && b?.dateRange) {
                    const aTimestamp = a?.dateRange[0].unix() ?? 0;
                    const bTimestamp = b?.dateRange[0].unix() ?? 0;

                    if (aTimestamp < bTimestamp) return -1;
                    if (aTimestamp > bTimestamp) return 1;
                }
                
                return 0;
            })
            .map(dateRangeDataToOrderPeriod)
            .filter(
                (d) => d.sdate && d.stime && d.edate && d.etime && d.points
            );

        if (valid && valid.length && !isEqual(props.value, valid))
            handleValueChange({
                inputValue: valid,
                needTimeout: true
            });
    }, [data]);

    /**
     * Convert order period data (scheme from server) to date range (moments array) and points data.
     * 
     * @param {IOrderPeriod} orderPeriod 
     * @returns {IDateRangePoint}
     */
    const orderPeriodToDateRangeData = (orderPeriod: IOrderPeriod) => {
        const { sdate, stime, edate, etime, points } = orderPeriod;

        return {
            dateRange: [
                moment(`${sdate} ${stime}`, "MM/DD/YYYY hh:mm a"),
                moment(`${edate} ${etime}`, "MM/DD/YYYY hh:mm a"),
            ],
            points: points,
        };
    };

    /**
     * Convert date range (moments array) and points data to order period data (schema from server).
     * 
     * @param {IDateRangePoint} dateRangePoint 
     * @returns {IOrderPeriod}
     */
    const dateRangeDataToOrderPeriod = (dateRangePoint: IDateRangePoint) => {
        const { dateRange, points } = dateRangePoint;

        const startDate = dateRange ? dateRange[0] : null;
        const endDate = dateRange ? dateRange[1] : null;

        return {
            sdate: startDate?.format("MM/DD/YYYY") ?? "",
            stime: startDate?.format("hh:mm a") ?? "",
            edate: endDate?.format("MM/DD/YYYY") ?? "",
            etime: endDate?.format("hh:mm a") ?? "",
            points: points,
        };
    };

    const columns = [
        {
            title: labels.date_range,
            dataIndex: "dateRange",
            render: (val: string, record: IDateRangePoint, index: number) => (
                <RangePicker
                    format="YYYY/MM/DD hh:mm a"
                    showTime={true}
                    value={val}
                    onChange={(dates: any[]) =>
                        dispatchData({
                            type: "set_data",
                            payload: {
                                index: index,
                                value: { ...record, dateRange: dates },
                            },
                        })
                    }
                />
            ),
        },
        {
            title: labels.points_earned,
            dataIndex: "points",
            render: (val: string, record: IDateRangePoint, index: number) => (
                <Input
                    className="points"
                    type="number"
                    value={val}
                    min={1}
                    onChange={(e: any) =>
                        dispatchData({
                            type: "set_data",
                            payload: {
                                index: index,
                                value: { ...record, points: e.target.value },
                            },
                        })
                    }
                />
            ),
        },
        {
            title: "",
            dataIndex: "points",
            render: (val: string, record: any, index: number) => (
                <div className="points-table-actions">
                    {index > 0 ? (
                        <button
                            className="delete"
                            onClick={() =>
                                dispatchData({
                                    type: "delete_row",
                                    payload: index,
                                })
                            }
                        >
                            <DeleteOutlined />
                        </button>
                    ) : null}
                    <button className="add">
                        <PlusCircleOutlined
                            onClick={() =>
                                dispatchData({
                                    type: "add_row",
                                    payload: index,
                                })
                            }
                        />
                    </button>
                </div>
            ),
        },
    ];

    return (
        <Table
            className="order-points-table"
            columns={columns}
            dataSource={
                data && data.length
                    ? data
                    : [
                          {
                              dateRange: null,
                              points: "",
                          },
                      ]
            }
            pagination={false}
            size="small"
        />
    );
};

export default OrderPeriod;

// #endregion [Component]
