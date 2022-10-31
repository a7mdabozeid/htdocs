// #region [Imports] ===================================================================================================

// Libraries
import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import { Row, Col, Form, InputNumber, Button, Skeleton, message } from "antd";

// CSS
import "./index.scss";

// Components
import PointsWorth from "../PointsBalance/PointsWorth";

// Actions
import { PointsBalanceActions } from "../../store/actions/balance";

// Types
import IStore from "../../types/store";
import IPointsBalance from "../../types/balance";

// Helpers
import { getPathPrefix } from "../../helpers/utils";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var lpfwMyPoints: any;

const { readPointsBalance, redeemPoints } = PointsBalanceActions;
const pathPrefix = getPathPrefix();

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
    readPointsBalance: typeof readPointsBalance;
    redeemPoints: typeof redeemPoints;
}

interface IProps {
    balance: IPointsBalance;
    actions: IActions;
}

interface IFieldValues {
    points: number;
    worth: number;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PointsRedeem = (props: IProps) => {
    const { balance, actions } = props;
    const {
        labels,
        currency_ratio,
        redeem_ratio,
        currency_symbol,
        coupon_expire_period,
        minimum_points_redeem,
        maximum_points_redeem,
        decimals,
    } = lpfwMyPoints;
    const [pointsValue, setPointsValue]: [number|string, any] = useState(0);
    const [worthValue, setWorthValue]: [number|string, any] = useState(0);
    const [loading, setLoading]: [boolean, any] = useState(false);
    const worthStep = (1/redeem_ratio) * currency_ratio;

    useEffect(() => {
        actions.readPointsBalance({});
    }, []);

    if (!balance) {
        return <Skeleton active />;
    }

    /**
     * When maximum points allowed for each coupon redemption setting value is greater than 0, then the maximum points
     * is either the customers points balance or the value in the setting, whichever has a lower value.
     * When the setting value is set to 0, maximum will just be set to customer's total points balance.
     */
    const maxPoints = 0 < maximum_points_redeem ? Math.min(balance.points, maximum_points_redeem) : balance.points;

    /**
     * Calculate amount worth based on the provided points value.
     * 
     * @param value
     * @returns 
     */
    const calculateAmountFromPoints = (value: string | number | undefined) => {
        if (typeof value === "undefined") return;
        value = typeof value === "string" ? parseInt(value) : value;
        if (value < minimum_points_redeem) return;

        let points: number =
            typeof value === "string" ? parseInt(value) : value;

        points = Math.min(points, balance.points);
        let temp = (points / redeem_ratio) * currency_ratio;

        if ( isNaN(temp) ) {
            temp = 0;
            points = 0;
        }

        setWorthValue(temp.toFixed(decimals));
        setPointsValue(parseInt(points.toString()));
    };

    /**
     * Calculate points value based on the set amount.
     * 
     * @param value
     * @returns 
     */
    const calculatePointsFromAmount = (value: string | number | undefined) => {
        if (typeof value === "undefined") return;
        let worth = typeof value === "string" ? parseFloat(value) : value;
        if (worth < 0) return;

        let temp: number = Math.floor((worth / currency_ratio) * redeem_ratio);

        temp = Math.min(temp, balance.points);
        worth = (temp / redeem_ratio) * currency_ratio;

        if ( isNaN( temp ) ) {
            worth = 0;
            temp = 0;
        }

        setPointsValue(parseInt(temp.toString()));
        setWorthValue(worth.toFixed(decimals));
    };

    const redeemPoints = () => {
        setLoading(true);
        actions.redeemPoints({
            points: pointsValue,
            successCB: (response: any) => {
                setWorthValue(0.0);
                setPointsValue(0);
                setLoading(false);
                message.success( response.data.message );
            },
            failCB: ({error}) => {
                setLoading(false);
                message.error( error.response.data.message );
            }
        });
    };

    return (
        <div className="points-redeem">
            <h3>{labels.redeem_points}</h3>
            <p>{labels.redeem_desc}</p>
            <PointsWorth balance={balance} />
            <Row className="form-wrap" gutter={10}>
                <Col span={11}>
                    <label>{labels.enter_points}:</label>
                    <InputNumber
                        min={minimum_points_redeem}
                        max={maxPoints}
                        step={1}
                        value={pointsValue}
                        onChange={calculateAmountFromPoints}
                    />
                </Col>
                <Col span={1}>
                    <span className="equal-sign">{`=`}</span>
                </Col>
                <Col span={11}>
                    <label>
                        {labels.enter_amount} ({currency_symbol}):
                    </label>
                    <InputNumber
                        min={0}
                        step={worthStep.toFixed(decimals)}
                        value={worthValue}
                        onChange={calculatePointsFromAmount}
                    />
                </Col>
                <Col span={24}>
                    <Button
                        loading={loading}
                        type="primary"
                        size="large"
                        onClick={redeemPoints}
                        disabled={!pointsValue || minimum_points_redeem > pointsValue}
                    >
                        {labels.redeem_button}
                    </Button>
                    <span className="view-redeemed-link">
                        <Link to={`${pathPrefix}lpfw-my-points/?tab=balance`}>
                            {labels.view_redeemed}
                        </Link>
                    </span>
                </Col>
            </Row>
            {0 < coupon_expire_period ? (
                <div
                    className="expiry-note"
                    dangerouslySetInnerHTML={{ __html: labels.additional_info.replace('{date_expire}', balance.expiry) }}
                />
            ) : null}
        </div>
    );
};

const mapStateToProps = (store: IStore) => ({ balance: store.balance });

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({ readPointsBalance, redeemPoints }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(PointsRedeem);

// #endregion [Component]
