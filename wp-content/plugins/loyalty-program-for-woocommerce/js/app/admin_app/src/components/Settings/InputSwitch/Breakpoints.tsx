// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import { ISectionField } from "../../../types/section";

// Helpers
import {parsePrice} from "../../../helpers/utils";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var lpfwAdminApp: any;
declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { useEffect, useReducer, useState, Fragment },
    lodash: { isEqual },
    antd: { Table, Input, Text },
    antdIcons: { DeleteOutlined, PlusCircleOutlined },
} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IBreakpoint {
    amount: string;
    points: string;
    sanitized: number;
}

interface IProps {
    field: ISectionField;
    value: IBreakpoint[]|string;
    handleValueChange: any;
}

// #endregion [Interfaces]

// #region [Reducer] ===================================================================================================

function reducer(state: IBreakpoint[], action: any) {
    const { type, payload } = action;
    const temp = [...state];
    switch (type) {
        case "rehydrate": {
            return payload;
        }

        case "delete_row": {
            temp.splice(payload, 1);
            return temp;
        }

        case "add_row": {
            temp.splice(payload + 1, 0, {
                amount: "",
                points: "",
                sanitized: 0,
            });
            return temp;
        }

        case "set_amount": {
            const { index, value } = payload;
            temp[index] = { ...temp[index], amount: value };
            return temp;
        }

        case "set_points": {
            const { index, value } = payload;
            temp[index] = { ...temp[index], points: value };
            return temp;
        }

        default:
            return state;
    }
}

// #endregion [Reducer]

// #region [Component] =================================================================================================

const Breakpoints = (props: IProps) => {
    const { value, handleValueChange } = props;
    const {
        loyalty_program: { labels },
    } = acfwAdminApp;
    const {currencySymbol} = lpfwAdminApp;
    const [dataSource, dispatchDataSource]: [IBreakpoint[], any] = useReducer(
        reducer,
        []
    );

    /**
     * Load initial value from store to local reducer state.
     */
    useEffect(() => {
        const temp = "string" === typeof(value) ? JSON.parse(value) : value;

        // skip dispatch if value is not valid.
        if ( ! temp || !temp.length ) return;

        dispatchDataSource({ type: "rehydrate", payload: temp });
    }, []);

    /**
     * Save value to server when local state and store state is not the same excluding incomplete rows.
     */
    useEffect(() => {
        const filtered = dataSource
            .filter(({amount,points}) => amount && points)
            .sort((a, b) => { // sort based on amount from lowest to highest

                const aAmount = parsePrice(a.amount);
                const bAmount = parsePrice(b.amount);

                if (aAmount < bAmount) return -1;
                if (aAmount > bAmount) return 1;
                
                return 0;
            });

        if (filtered && filtered.length && !isEqual(props.value, filtered)) 
            handleValueChange({
                inputValue: filtered,
                needTimeout: true
            });

    }, [dataSource]);

    const columns = [
        {
            title: labels.breakpoint,
            dataIndex: "amount",
            render: (val: string, record: any, index: number) => (
                <Input
                    addonBefore={currencySymbol}
                    type="text"
                    className="wc_input_price"
                    value={val}
                    onChange={(e: any) => (
                        dispatchDataSource({
                            type: "set_amount",
                            payload: { index, value: e.target.value },
                        })
                    )}
                />
            ),
        },
        {
            title: labels.points_earned,
            dataIndex: "points",
            render: (val: string, record: any, index: number) => (
                <Input
                    type="number"
                    value={val}
                    min={1}
                    onChange={(e: any) =>
                        dispatchDataSource({
                            type: "set_points",
                            payload: { index, value: e.target.value },
                        })
                    }
                />
            ),
        },
        {
            title: "",
            dataIndex: "points",
            render: (val: string, record: any, index: number) => {
                return (
                    <div className="points-table-actions">
                        {index > 0 ? (
                            <button
                                className="delete"
                                onClick={() =>
                                    dispatchDataSource({
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
                                    dispatchDataSource({
                                        type: "add_row",
                                        payload: index,
                                    })
                                }
                            />
                        </button>
                    </div>
                );
            },
        },
    ];

    return (
        <Table
            columns={columns}
            dataSource={dataSource.length ? dataSource : [{ amount: "", points: "", sanitized: 0 }]}
            pagination={false}
            size="small"
        />
    );
};

export default Breakpoints;

// #endregion [Component]
