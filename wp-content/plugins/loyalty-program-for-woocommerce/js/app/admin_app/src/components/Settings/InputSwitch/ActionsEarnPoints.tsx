// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import IStore from "../../../types/store";
import { ISectionField, ISettingOption } from "../../../types/section";
import ISettingValue from "../../../types/setting";

// Actions
import { SettingActions } from "../../../store/actions/setting";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;

const {
    element: { useEffect },
    redux: { bindActionCreators, connect },
    antd: { Switch, Typography },
} = acfwpElements;
const { Text } = Typography;
const { setStoreSettingItems } = SettingActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
    setStoreSettingItems: typeof setStoreSettingItems;
}

interface IProps {
    field: ISectionField;
    values: ISettingValue[];
    handleValueChange: any;
    actions: IActions;
}

interface ISingleOptionProps {
    option: ISettingOption;
    key: number
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const ActionsEarnPoints = (props: IProps) => {
    const {
        field: { options },
        values,
        handleValueChange,
        actions,
    } = props;

    /**
     * Set setting items to redux state on initial load.
     */
    useEffect(() => {

        if (values.length) return;

        const temp: ISettingValue[] | undefined = options?.map(
            ({ key, value }) => ({
                id: key,
                value,
            })
        );

        if (temp) actions.setStoreSettingItems({ data: temp });
    }, []);

    /**
     * Single option component.
     * 
     * @param props 
     * @returns 
     */
    const SingleOption = (props: ISingleOptionProps) => {
        const { option } = props;
        const { key: id, label } = option;
        const index = values.findIndex((v) => v.id === id);
        const { value } = values[index];

        return (
            <div className="option">
                <Switch
                    checked={value === "yes"}
                    defaultChecked={value === "yes"}
                    onChange={
                        (inputValue: boolean) => {
                            const temp = inputValue ? "yes" : "no";
                            handleValueChange({
                                inputValue: temp,
                                needTimeout: false,
                                overrideId: id,
                                overrideTitle: label
                            });
                        }
                    }
                />
                <Text>{label}</Text>
            </div>
        );
    };

    if (values.length < 1) return null;

    return (
        <div className="actions-earn-points-options">
            {options?.map((option: ISettingOption, key: number) => (
                <SingleOption option={option} key={key} />
            ))}
        </div>
    );
};

const mapStateToProps = (store: IStore, props: any) => {
    const { options } = props.field;
    const ids = options.map((o: any) => o.key);
    const values = store.settingValues.filter((s: ISettingValue) =>
        ids.includes(s.id)
    );

    return { values: values };
};

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({ setStoreSettingItems }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(ActionsEarnPoints);

// #endregion [Component]
