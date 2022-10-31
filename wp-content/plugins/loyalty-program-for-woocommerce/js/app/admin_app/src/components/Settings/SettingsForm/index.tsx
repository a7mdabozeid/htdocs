// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Components
import SettingField from "../SettingField";
import ToggleSettingField from "../ToggleSettingField";

// Types
import IStore from "../../../types/store";
import { ISection } from "../../../types/section";
import ISettingValue from "../../../types/setting";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;

const {
    element: { Fragment },
    antd: { Skeleton, Divider },
    redux: { bindActionCreators, connect },
} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
    sections: ISection[];
    settingValues: ISettingValue[];
    currentSection: string | null;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const SettingsForm = (props: IProps) => {
    const { sections, settingValues, currentSection } = props;

    const idx = currentSection
        ? sections.findIndex((i) => i.id === currentSection)
        : 0;
    const sectionID = sections.length ? sections[idx].id : "";
    const sectionFields = sections.length ? sections[idx].fields : [];

    if (sectionFields.length < 1 || settingValues.length < 1) {
        const dummy = [0, 1, 2];
        return (
            <Fragment>
                <Skeleton loading={true} active paragraph={{ rows: 1 }} />
                {dummy.map((key: number) => (
                    <Fragment>
                        <Divider />
                        <Skeleton
                            loading={true}
                            active
                            paragraph={{ rows: 2 }}
                            title={false}
                        />
                    </Fragment>
                ))}
            </Fragment>
        );
    }

    return (
        <div className={`settings-form ${sectionID}-form`}>
            {sectionFields.map((field, order: number) => (
                <Fragment key={field.id}>
                    {field.toggle ? (
                        <ToggleSettingField field={field} order={order} />
                    ) : (
                        <SettingField field={field} order={order} />
                    )}
                </Fragment>
            ))}
        </div>
    );
};

const mapStateToProps = (store: IStore) => ({
    sections: store.settingSections,
    settingValues: store.settingValues,
});

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({}, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(SettingsForm);

// #endregion [Component]
