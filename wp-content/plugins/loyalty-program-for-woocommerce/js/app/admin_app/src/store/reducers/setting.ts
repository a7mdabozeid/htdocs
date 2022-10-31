// #region [Imports] ===================================================================================================

// Types
import ISettingValue from "../../types/setting";

// Actions
import {
    ISetStoreSettingsActionPayload,
    ISetStoreSettingActionPayload,
    ESettingActionTypes,
} from "../actions/setting";

// #endregion [Imports]

// #region [Variables] ===================================================================================================

declare var acfwpElements: any;
const {
    lodash: { cloneDeep },
} = acfwpElements;

// #endregion [Variables]

// #region [Reducer] ===================================================================================================

export default (
    settings: ISettingValue[] = [],
    action: { type: string; payload: any }
) => {
    switch (action.type) {
        case ESettingActionTypes.SET_STORE_SETTINGS: {
            const { data } = action.payload as ISetStoreSettingsActionPayload;
            let clonedSettings = cloneDeep(settings);

            for (const value of data) {
                let idx = settings.findIndex((i) => i.id === value.id);
                if (clonedSettings[idx]) clonedSettings[idx] = value;
                else clonedSettings.push(value);
            }

            return clonedSettings;
        }

        case ESettingActionTypes.SET_STORE_SETTING: {
            const { data } = action.payload as ISetStoreSettingActionPayload;
            const idx = settings.findIndex((i) => i.id === data.id);

            if (idx < 0) return settings;

            const clonedSettings = cloneDeep(settings);

            clonedSettings[idx] = { ...clonedSettings[idx], ...data };

            return clonedSettings;
        }

        default:
            return settings;
    }
};

// #endregion [Reducer]
