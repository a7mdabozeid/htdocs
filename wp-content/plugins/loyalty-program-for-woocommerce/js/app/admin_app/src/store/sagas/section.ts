// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";

// Actions
import {
    IReadSectionsActionPayload,
    IReadSectionActionPayload,
    ESectionActionTypes,
    SectionActions,
} from "../actions/section";
import { SettingActions } from "../actions/setting";

// #endregion [Imports]

// #region [Variables] ===================================================================================================

declare var acfwpElements: any;
const {
    redux: { sagaEffects },
    axiosInstance,
} = acfwpElements;
const { put, call, takeEvery } = sagaEffects;

// #endregion [Variables]

// #region [Sagas] =====================================================================================================

export function* readSectionsSaga(action: {
    type: string;
    payload: IReadSectionsActionPayload;
}): any {
    const { id, processingCB, successCB, failCB } = action.payload;
    const section = id ? id : "general";

    try {
        if (typeof processingCB === "function") processingCB();

        axiosInstance.defaults.headers["section"] = section;

        const response = yield call(() =>
            axiosInstance.get(`loyalty-program/v1/settings/sections`)
        );

        if (response && response.data) {
            const idx = section
                ? response.data.findIndex((i: any) => i.id === section)
                : 0;

            yield put(
                SettingActions.rehydrateStoreSettings({
                    data: response.data[idx],
                })
            );

            yield put(
                SectionActions.setStoreSectionItems({ data: response.data })
            );

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }
}

export function* readSectionSaga(action: {
    type: string;
    payload: IReadSectionActionPayload;
}): any {
    const { id, processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const section = id ? id : "general";

        const response = yield call(() =>
            axiosInstance.get(`loyalty-program/v1/settings/sections/${section}`)
        );

        if (response && response.data) {
            yield put(
                SettingActions.rehydrateStoreSettings({ data: response.data })
            );

            yield put(
                SectionActions.setStoreSectionItem({ data: response.data })
            );

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }
}

// #endregion [Sagas]

// #region [Action Listeners] ==========================================================================================

export const actionListener = [
    takeEvery(ESectionActionTypes.READ_SECTIONS, readSectionsSaga),
    takeEvery(ESectionActionTypes.READ_SECTION, readSectionSaga),
];

// #endregion [Action Listeners]
