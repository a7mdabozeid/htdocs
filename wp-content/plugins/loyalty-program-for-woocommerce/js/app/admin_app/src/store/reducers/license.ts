// #region [Imports] ===================================================================================================

// Types
import ILicense from "../../types/license";

// Actions
import {
    ISetLicenseActionPayload,
    ELicenseActionTypes,
} from "../actions/license";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

export default (
    license: ILicense | null = null,
    action: { type: string; payload: any }
) => {
    switch (action.type) {
        case ELicenseActionTypes.SET_LICENSE: {
            const { data } = action.payload as ISetLicenseActionPayload;
            return data;
        }

        default:
            return license;
    }
};

// #endregion [Reducer]
