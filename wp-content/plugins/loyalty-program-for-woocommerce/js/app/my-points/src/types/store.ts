// #region [Imports] ===================================================================================================

import IPointsBalance from "./balance";
import IUserCoupons from "./coupons";
import IHistoryEntry from "./history";

// #endregion [Imports]

// #region [Types] =====================================================================================================

export default interface IStore {
    balance: IPointsBalance;
    coupons: IUserCoupons[];
    history: IHistoryEntry[];
}

// #endregion [Types]
