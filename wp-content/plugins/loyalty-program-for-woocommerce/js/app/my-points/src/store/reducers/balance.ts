// #region [Imports] ===================================================================================================

// Actions
import {
    ISetStorePointsBalanceActionPayload,
    EPointsBalanceTypes,
} from "../actions/balance";

// Types
import IPointsBalance from "../../types/balance";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

export default (
    balance: IPointsBalance | null = null,
    action: { type: string; payload: any }
) => {
    switch (action.type) {
        case EPointsBalanceTypes.SET_POINTS_BALANCE: {
            const {
                data,
            } = action.payload as ISetStorePointsBalanceActionPayload;
            return data;
        }

        default:
            return balance;
    }
};

// #endregion [Reducer]
