// #region [Imports] ===========================================================================================

import IPointsBalance from "../../types/balance";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadStorePointsBalanceActionPayload {
    processingCB?: () => void;
    successCB?: (arg: any) => void;
    failCB?: (arg: any) => void;
}
export interface IRedeemPointsActionPayload {
    points: number;
    processingCB?: () => void;
    successCB?: (arg: any) => void;
    failCB?: (arg: any) => void;
}
export interface ISetStorePointsBalanceActionPayload {
    data: IPointsBalance;
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EPointsBalanceTypes {
    READ_POINTS_BALANCE = "READ_POINTS_BALANCE",
    SET_POINTS_BALANCE = "SET_POINTS_BALANCE",
    REDEEM_POINTS = "REDEEM_POINTS",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const PointsBalanceActions = {
    readPointsBalance: (payload: IReadStorePointsBalanceActionPayload) => ({
        type: EPointsBalanceTypes.READ_POINTS_BALANCE,
        payload,
    }),
    setPointsBalance: (payload: ISetStorePointsBalanceActionPayload) => ({
        type: EPointsBalanceTypes.SET_POINTS_BALANCE,
        payload,
    }),
    redeemPoints: (payload: IRedeemPointsActionPayload) => ({
        type: EPointsBalanceTypes.REDEEM_POINTS,
        payload,
    }),
};

// #endregion [Action Creators]
