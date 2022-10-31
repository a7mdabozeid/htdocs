// #region [Imports] ===================================================================================================

// Libraries
import React, { useEffect, useState } from "react";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import { Skeleton } from "antd";

// Components
import UserCoupons from "../UserCoupons";
import PointsWorth from "./PointsWorth";

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

const { readPointsBalance } = PointsBalanceActions;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
    readPointsBalance: typeof readPointsBalance;
}

interface IProps {
    balance: IPointsBalance;
    actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PointsBalance = (props: IProps) => {
    const { balance, actions } = props;
    const { labels } = lpfwMyPoints;
    const [loadCoupons, setLoadCoupons]: [boolean, any] = useState(false);

    useEffect(() => {
        actions.readPointsBalance({});
    }, []);

    if (!balance) {
        return <Skeleton active />;
    }

    return (
        <>
            <div className="points-balance">
                <h3>{labels.points_balance}</h3>
                <PointsWorth balance={balance} />
            </div>
            <UserCoupons />
        </>
    );
};

const mapStateToProps = (store: IStore) => ({
    balance: store.balance,
    coupons: store.coupons,
});

const mapDispatchToProps = (dispatch: Dispatch) => ({
    actions: bindActionCreators({ readPointsBalance }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(PointsBalance);

// #endregion [Component]
