// #region [Imports] ===================================================================================================

import { ISection } from "./section";
import { ISettingValue } from "./settings";
import IStoreCreditsDashboardData, {
  IStoreCreditCustomer,
  IStoreCreditStatus,
} from "./storeCredits";
import { IDashboardWidget } from "./dashboard";
import { ISingleNotice } from "./notices";

// #endregion [Imports]

// #region [Types] =====================================================================================================

export interface IStore {
  sections: ISection[];
  settingValues: ISettingValue[];
  page: string;
  storeCreditsDashboard: IStoreCreditStatus[];
  storeCreditsCustomers: IStoreCreditCustomer[];
  dashboardWidgets: IDashboardWidget[];
  adminNotices: ISingleNotice[];
}

// #endregion [Types]
