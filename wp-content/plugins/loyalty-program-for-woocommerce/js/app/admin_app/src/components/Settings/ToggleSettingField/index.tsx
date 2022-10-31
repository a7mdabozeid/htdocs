// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import IStore from "../../../types/store";
import { ISectionField } from "../../../types/section";

// Components
import SettingField from "../SettingField";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;

const {
    element: { useEffect },
    redux: { bindActionCreators, connect },
} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
    field: ISectionField;
    show: boolean;
    order: number;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const ToggleSettingField = (props: IProps) => {
    const { field, show, order } = props;

    if (!show) return null;

    return <SettingField field={field} order={order} />;
};

const mapStateToProps = (store: IStore, props: any) => {
    const { toggle } = props.field;
    const index = store.settingValues.findIndex((v) => v.id === toggle);
    const setting = index >= 0 ? store.settingValues[index] : null;
    const value = setting ? setting.value : null;
    const toggleValue = props.field?.toggleValue ?? "yes";

    return { show: value === toggleValue };
};

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({}, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(ToggleSettingField);

// #endregion [Component]
