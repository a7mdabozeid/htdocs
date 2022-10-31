// #region [Imports] ===================================================================================================

// Types
import { ISection } from "../../types/section";

// Actions
import {
    ISetStoreSectionsActionPayload,
    ISetStoreSectionActionPayload,
    ESectionActionTypes,
} from "../actions/section";

// #endregion [Imports]

// #region [Variables] ===================================================================================================

declare var acfwpElements: any;
const {
    lodash: { cloneDeep },
} = acfwpElements;

// #endregion [Variables]

// #region [Reducer] ===================================================================================================

export default (
    sections: ISection[] = [],
    action: { type: string; payload: any }
) => {
    switch (action.type) {
        case ESectionActionTypes.SET_STORE_SECTIONS: {
            const { data } = action.payload as ISetStoreSectionsActionPayload;
            return data;
        }

        case ESectionActionTypes.SET_STORE_SECTION: {
            const { data } = action.payload as ISetStoreSectionActionPayload;
            const idx = sections.findIndex((i) => i.id === data.id);

            if (idx < 0) return sections;

            const clonedSections = cloneDeep(sections);

            clonedSections[idx] = { ...clonedSections[idx], ...data };

            return clonedSections;
        }

        default:
            return sections;
    }
};

// #endregion [Reducer]
