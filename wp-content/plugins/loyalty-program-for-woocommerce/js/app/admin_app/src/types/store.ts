// #region [Imports] ===================================================================================================

import IDashboardData from "./dashboard";
import { ISection } from "./section";
import ISettingValue from "./setting";
import ILicense from "./license";

// #endregion [Imports]

// #region [Types] =====================================================================================================

export default interface IStore {
    dashboard: IDashboardData;
    settingSections: ISection[];
    settingValues: ISettingValue[];
    license: ILicense | null;
}

// #endregion [Types]
