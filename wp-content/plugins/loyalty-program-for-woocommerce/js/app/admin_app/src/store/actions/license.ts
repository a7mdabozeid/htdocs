// #region [Imports] ===================================================================================================

// Types
import ILicense from "../../types/license";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadLicenseActionPayload {
    processingCB?: () => void;
    successCB?: (arg: any) => void;
    failCB?: (arg: any) => void;
    alwaysCB?: () => void;
}

export interface IActivateLicenseActionPayload {
    license_key: string;
    email: string;
    processingCB?: () => void;
    successCB?: (arg: any) => void;
    failCB?: (arg: any) => void;
    alwaysCB?: () => void;
}

export interface ISetLicenseActionPayload {
    data: ILicense;
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum ELicenseActionTypes {
    READ_LICENSE = "READ_LICENSE",
    ACTIVATE_LICENSE = "ACTIVATE_LICENSE",
    SET_LICENSE = "SET_LICENSE",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const LicenseActions = {
    readLicenseData: (payload: IReadLicenseActionPayload) => ({
        type: ELicenseActionTypes.READ_LICENSE,
        payload,
    }),
    activateLicenseData: (payload: IActivateLicenseActionPayload) => ({
        type: ELicenseActionTypes.ACTIVATE_LICENSE,
        payload,
    }),
    setLicenseData: (payload: ISetLicenseActionPayload) => ({
        type: ELicenseActionTypes.SET_LICENSE,
        payload,
    }),
};
