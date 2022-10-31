// #region [Imports] ===================================================================================================

// Libraries
import "cross-fetch/polyfill";
import axios from "axios";

// helpers
import axiosInstance, { getCancelToken } from "../../helpers/axios";

// Types
import ISettingValue from "../../types/setting";
import IAxiosResponse from "../../types/axios";

// Actions
import {
    ICreateSettingActionPayload,
    IUpdateSettingActionPayload,
    IDeleteSettingActionPayload,
    IReadSettingsActionPayload,
    IRehydrateStoreSettingsActionPayload,
    IRehydrateStoreSettingActionPayload,
    ESettingActionTypes,
    SettingActions,
} from "../actions/setting";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;
const {
    redux: { sagaEffects },
} = acfwpElements;
const { put, call, takeEvery } = sagaEffects;

// #endregion [Variables]

// #region [Sagas] =====================================================================================================

export function* createSettingSaga(action: {
    type: string;
    payload: ICreateSettingActionPayload;
}): any {
    const { data, processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const response: IAxiosResponse = yield call(() =>
            axiosInstance.post(`loyalty-program/v1/setting/sections`, data)
        );

        if (response && response.data) {
            yield put(SettingActions.rehydrateStoreSettings({}));

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e, payload: data });
    }
}

export function* updateSettingSaga(action: {
    type: string;
    payload: IUpdateSettingActionPayload;
}): any {
    const { data, processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        if (!data.id)
            throw new Error("Can't update setting. No setting id provided.");

        const response: IAxiosResponse = yield call(() => {
            const { id, ...updateData } = data;

            return axiosInstance.put(
                `loyalty-program/v1/settings/${id}`,
                updateData,
                {
                    cancelToken: getCancelToken(id),
                }
            );
        });

        if (response && response.data) {
            yield put(
                SettingActions.rehydrateStoreSetting({
                    id: response.data.id,
                    data: response.data,
                })
            );

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (axios.isCancel(e)) {
            return;
        }

        if (typeof failCB === "function") failCB({ error: e, payload: data });
    }
}

export function* deleteSettingSaga(action: {
    type: string;
    payload: IDeleteSettingActionPayload;
}): any {
    const { id, processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const response: IAxiosResponse = yield call(() =>
            axiosInstance.delete(`loyalty-program/v1/settings/${id}`)
        );

        if (response && response.data) {
            yield put(SettingActions.rehydrateStoreSettings({}));

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }
}

export function* readSettingSaga(action: {
    type: string;
    payload: IReadSettingsActionPayload;
}): any {
    const { processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        const response: IAxiosResponse = yield call(() =>
            axiosInstance.get(`loyalty-program/v1/settings/`)
        );

        if (response && response.data) {
            yield put(
                SettingActions.rehydrateStoreSettings({ data: response.data })
            );

            if (typeof successCB === "function") successCB(response);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }
}

export function* rehydrateStoreSettingsSaga(action: {
    type: string;
    payload: IRehydrateStoreSettingsActionPayload;
}): any {
    const { data, processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        if (data) {
            let values: ISettingValue[] = data.fields
                .filter((f) => f.type !== "title" && f.type !== "sectionend")
                .map((f) => ({ id: f.id, value: f.value }));

            yield put(SettingActions.setStoreSettingItems({ data: values }));

            if (typeof successCB === "function") successCB(values);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }
}

export function* rehydrateStoreSettingSaga(action: {
    type: string;
    payload: IRehydrateStoreSettingActionPayload;
}): any {
    const { id, data, processingCB, successCB, failCB } = action.payload;

    try {
        if (typeof processingCB === "function") processingCB();

        if (!data) {
            const response: IAxiosResponse = yield call(() =>
                axiosInstance.get(`loyalty-program/v1/settings/${id}`)
            );

            if (response && response.data) {
                yield put(
                    SettingActions.setStoreSettingItem({ data: response.data })
                );

                if (typeof successCB === "function") successCB(response);
            }
        } else {
            yield put(SettingActions.setStoreSettingItem({ data }));

            if (typeof successCB === "function") successCB(data);
        }
    } catch (e) {
        if (typeof failCB === "function") failCB({ error: e });
    }
}

// #endregion [Sagas]

// #region [Action Listeners] ==========================================================================================

export const actionListener = [
    takeEvery(ESettingActionTypes.CREATE_SETTING, createSettingSaga),
    takeEvery(ESettingActionTypes.UPDATE_SETTING, updateSettingSaga),
    takeEvery(ESettingActionTypes.DELETE_SETTING, deleteSettingSaga),
    takeEvery(ESettingActionTypes.READ_SETTINGS, readSettingSaga),
    takeEvery(
        ESettingActionTypes.REHYDRATE_STORE_SETTINGS,
        rehydrateStoreSettingsSaga
    ),
    takeEvery(
        ESettingActionTypes.REHYDRATE_STORE_SETTING,
        rehydrateStoreSettingSaga
    ),
];

// #endregion [Action Listeners]
