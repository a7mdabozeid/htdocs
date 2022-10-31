// #region [Imports] ===================================================================================================

// Libs
import cloneDeep from "lodash/cloneDeep";

// Types
import IHistoryEntry from "../../types/history";

// Actions
import {
    ISetStoreHistoryEntriesActionPayload,
    ISetStoreHistoryEntryActionPayload,
    EHistoryEntriesTypes,
} from "../actions/history";

// #endregion [Imports]

// #region [Reducer] ===================================================================================================

export default (
    historyEntries: IHistoryEntry[] = [],
    action: { type: string; payload: any }
) => {
    switch (action.type) {
        case EHistoryEntriesTypes.SET_HISTORY_ENTRIES: {
            const {
                data,
            } = action.payload as ISetStoreHistoryEntriesActionPayload;
            return data;
        }

        case EHistoryEntriesTypes.SET_HISTORY_ENTRY: {
            const {
                data,
            } = action.payload as ISetStoreHistoryEntryActionPayload;
            const index = historyEntries.findIndex((c) => c.id === data.id);

            if (index < 0) return [data, ...historyEntries];

            const clonedEntries = cloneDeep(historyEntries);
            clonedEntries[index] = { ...clonedEntries[index], ...data };

            return clonedEntries;
        }

        default:
            return historyEntries;
    }
};

// #endregion [Reducer]
