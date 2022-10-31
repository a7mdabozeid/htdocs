// #region [Imports] ===========================================================================================

import IHistoryEntry from "../../types/history";

// #endregion [Imports]

// #region [Action Payloads] ===========================================================================================

export interface IReadStoreHistoryEntriesActionPayload {
    page: number | null;
    processingCB?: () => void;
    successCB?: (arg: any) => void;
    failCB?: (arg: any) => void;
}
export interface ISetStoreHistoryEntriesActionPayload {
    data: IHistoryEntry[];
}
export interface ISetStoreHistoryEntryActionPayload {
    data: IHistoryEntry;
}

// #endregion [Action Payloads]

// #region [Action Types] ==============================================================================================

export enum EHistoryEntriesTypes {
    READ_HISTORY_ENTRIES = "READ_HISTORY_ENTRIES",
    SET_HISTORY_ENTRIES = "SET_HISTORY_ENTRIES",
    SET_HISTORY_ENTRY = "SET_HISTORY_ENTRY",
}

// #endregion [Action Types]

// #region [Action Creators] ===========================================================================================

export const HistoryEntriesActions = {
    readHistoryEntries: (payload: IReadStoreHistoryEntriesActionPayload) => ({
        type: EHistoryEntriesTypes.READ_HISTORY_ENTRIES,
        payload,
    }),
    setHistoryEntries: (payload: ISetStoreHistoryEntriesActionPayload) => ({
        type: EHistoryEntriesTypes.SET_HISTORY_ENTRIES,
        payload,
    }),
    setHistoryEntry: (payload: ISetStoreHistoryEntryActionPayload) => ({
        type: EHistoryEntriesTypes.SET_HISTORY_ENTRY,
        payload,
    }),
};

// #endregion [Action Creators]
