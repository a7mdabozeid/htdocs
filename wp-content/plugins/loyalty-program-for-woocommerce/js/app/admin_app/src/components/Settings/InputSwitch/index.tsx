// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Components
import PointsCalcOptions from "./PointsCalcOptions";
import ActionsEarnPoints from "./ActionsEarnPoints";
import Breakpoints from "./Breakpoints";
import OrderPeriod from "./OrderPeriod";
import RefetchUpdateData from "./RefetchUpdateData";

// Helpers 
import {axiosCancel} from "../../../helpers/axios";

// Types
import IStore from "../../../types/store";
import { ISectionField } from "../../../types/section";

// Actions
import { SettingActions } from "../../../store/actions/setting";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var lpfwAdminApp: any;
declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { Fragment, useState },
    antd: { Input, Switch, Select, message },
    redux: { bindActionCreators, connect },
    lodash: {defaults}
} = acfwpElements;

const { updateSetting, setStoreSettingItem } = SettingActions;
const { action_notices } = acfwAdminApp;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
    updateSetting: typeof updateSetting;
    setStoreSettingItem: typeof setStoreSettingItem;
}

interface IProps {
    field: ISectionField;
    showSpinner: boolean;
    setShowSpinner: any;
    validateInput: any;
    value: any;
    actions: IActions;
}

interface IHandleValueChange {
    inputValue: unknown;
    needTimeout?: boolean;
    overrideId?: string;
    overrideTitle?: string;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const InputSwitch = (props: IProps) => {
    const {
        field,
        showSpinner,
        setShowSpinner,
        validateInput,
        value: savedValue,
        actions,
    } = props;
    const { id, type, placeholder, default: defaultValue } = field;
    const {currencySymbol} = lpfwAdminApp;
    const [saveTimeout, setSaveTimeout]: [any, any] = useState(null);
    const value =
        savedValue !== undefined && savedValue !== false
            ? savedValue
            : defaultValue;

    /**
     * Handle value change for all input types.
     * This function will validate the input values first and will schedule update to the database.
     * When a user does additional changes to the input's value state, the previously scheduled action and axios request
     * will be cancelled and be replaced with a new scheduled action.
     * 
     * @param {IHandleValueChange} args
     */
    const handleValueChange = (args: IHandleValueChange) => {

        const {inputValue, needTimeout, overrideId, overrideTitle} = defaults(args, {
            needTimeout: false,
            overrideID: '',
            overrideTitle: ''
        });

        const settingId = overrideId ? overrideId : id;
        const fieldTitle = overrideTitle ? overrideTitle : field.title;

        // set state early to prevent rerenders.
        actions.setStoreSettingItem({
            data: { id: settingId, value: inputValue },
        });

        // if value is changed after change already submitted, then cancel previous axios instance.
        if (showSpinner) {
            axiosCancel(settingId);
            if (needTimeout) setShowSpinner(false);
        }

        /**
         * Update value blcok made as a function so it can be either scheduled or run directly.
         */
        const updateValue = () => {
            // validate value
            if (!validateInput(inputValue)) return;

            // input types which value needs to be serialized as JSON before saving to db.
            const serializeTypes = ['breakpoints', 'order_period'];

            // update setting value via api
            actions.updateSetting({
                data: { 
                    id: settingId, 
                    value: serializeTypes.indexOf(type) > -1 ? JSON.stringify(inputValue) : inputValue, 
                    type: type 
                },
                processingCB: () => setShowSpinner(true),
                successCB: () => {
                    message.success(
                        <Fragment>
                            <strong>{fieldTitle}</strong>{" "}
                            {action_notices.success}
                        </Fragment>
                    );
                    setShowSpinner(false);
                },
                failCB: () => {
                    message.error(
                        <Fragment>
                            <strong>{fieldTitle}</strong> {action_notices.fail}
                        </Fragment>
                    );
                    setShowSpinner(false);
                },
            });
        };

        // we add timeout for fields that requires users to update value by typing.
        if (needTimeout) {
            // clear timeout when user is still editing
            if (saveTimeout) {
                clearTimeout(saveTimeout);
                setSaveTimeout(null);
            }

            // set 1 second delay before updating value.
            setSaveTimeout(setTimeout(updateValue, 1000));
        } else updateValue();
    };

    if ("checkbox" === type || "module" === type) {
        return (
            <Switch
                key={id}
                checked={value === "yes"}
                defaultChecked={value === "yes"}
                onChange={(inputValue: any) =>
                    handleValueChange({
                        inputValue: inputValue ? "yes" : ""
                    })
                }
            />
        );
    }

    if ("textarea" === type) {
        return (
            <Input.TextArea
                key={id}
                rows={3}
                placeholder={placeholder}
                defaultValue={value}
                onChange={(event: any) =>
                    handleValueChange({
                        inputValue: event.target.value,
                        needTimeout: true
                    })
                }
            />
        );
    }

    if ("select" === type) {
        const { options } = field;
        return (
            <Select
                key={id}
                defaultValue={value ? value : null}
                style={{ width: `50%` }}
                placeholder={placeholder}
                allowClear={field?.allow_clear ?? true}
                onSelect={(value: any) => handleValueChange({inputValue: value})}
                onClear={() => handleValueChange({inputValue: null})}
            >
                {options
                    ? options.map(({ key, label }) => (
                          <Select.Option
                              key={key.toString()}
                              value={key.toString()}
                          >
                              {label}
                          </Select.Option>
                      ))
                    : null}
            </Select>
        );
    }

    if ("multiselect" === type) {
        const { options } = field;
        return (
            <Select
                key={id}
                mode="multiple"
                defaultValue={value}
                style={{ width: `100%` }}
                placeholder={placeholder}
                onChange={(value: any) =>
                    handleValueChange({
                        inputValue: value,
                        needTimeout: true
                    })
                }
            >
                {options
                    ? options.map(({ key, label }) => (
                          <Select.Option
                              key={key.toString()}
                              value={key.toString()}
                          >
                              {label}
                          </Select.Option>
                      ))
                    : null}
            </Select>
        );
    }

    if("number" === type)
        return (
            <Input
                key={id}
                type={type}
                name={id}
                placeholder={placeholder}
                defaultValue={value}
                min={field?.min}
                max={field?.max}
                step={field?.step}
                onChange={(event: any) =>
                    handleValueChange({
                        inputValue: event.target.value,
                        needTimeout: true
                    })
                }
            />
        );

    if (["text", "url"].indexOf(type) > -1)
        return (
            <Input
                key={id}
                type={type}
                name={id}
                placeholder={placeholder}
                defaultValue={value}
                onChange={(event: any) =>
                    handleValueChange({
                        inputValue: event.target.value,
                        needTimeout: true
                    })
                }
            />
        );

    if ("price" === type)
        return (
            <Input
                addonBefore={currencySymbol}
                type="text"
                className="wc_input_price"
                name={id}
                placeholder={placeholder}
                defaultValue={value}
                onChange={(event: any) =>
                    handleValueChange({
                        inputValue: event.target.value,
                        needTimeout: true
                    })
                }
            />
        );

    if ("points_calculation" === type)
        return (
            <PointsCalcOptions
                field={field}
                value={value}
                handleValueChange={handleValueChange}
            />
        );

    if ("actions_earn_points" === type)
        return (
            <ActionsEarnPoints
                field={field}
                handleValueChange={handleValueChange}
            />
        );

    if ("breakpoints" === type)
        return (
            <Breakpoints
                field={field}
                value={value}
                handleValueChange={handleValueChange}
            />
        );

    if ("order_period" === type)
        return (
            <OrderPeriod
                field={field}
                value={value}
                handleValueChange={handleValueChange}
            />
        );

    if ("refetch_update_data" === type)
        return (
            <RefetchUpdateData field={field} />
        );

    return null;
};

const mapStateToProps = (store: IStore, props: any) => {
    const { id } = props.field;
    const index = store.settingValues.findIndex((i: any) => i.id === id);
    const value = index > -1 ? store.settingValues[index].value : "";

    return { value: value };
};

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators(
        { updateSetting, setStoreSettingItem },
        dispatch
    ),
});

export default connect(mapStateToProps, mapDispatchToProps)(InputSwitch);

// #endregion [Component]
