// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import { ISectionField, ISettingOption } from "../../../types/section";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;

const {
    antd: { Typography, Switch, Popover },
    antdIcons: { QuestionCircleOutlined },
} = acfwpElements;
const { Text } = Typography;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
    field: ISectionField;
    value: any;
    handleValueChange: any;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PointsCalcOptions = (props: IProps) => {
    const {
        field: { options },
        value,
        handleValueChange,
    } = props;

    /**
     * Handles when a switch input is changed.
     * @param {string} key 
     * @param {boolean} inputValue 
     */
    const onSwitchChange = (key: string, inputValue: boolean) => {
        const temp = { ...value };
        temp[key] = inputValue ? "yes" : "";
        handleValueChange({
            inputValue: temp,
            needTimeout: true
        });
    };

    return (
        <div className="point-calc-options">
            {options
                ? options.map(
                      ({
                          key,
                          default: defaultValue,
                          label,
                          tooltip,
                      }: ISettingOption) => (
                          <div className="option" key={key}>
                              <Switch
                                  checked={value[key] === "yes"}
                                  defaultChecked={defaultValue === "yes"}
                                  onChange={(inputValue: boolean) =>
                                      onSwitchChange(key, inputValue)
                                  }
                              />
                              <Text>{label}</Text>
                              <Popover
                                  placement="right"
                                  content={
                                      <div className="setting-tooltip-content">
                                          {tooltip}
                                      </div>
                                  }
                                  trigger="click"
                              >
                                  <QuestionCircleOutlined />
                              </Popover>
                          </div>
                      )
                  )
                : null}
        </div>
    );
};

export default PointsCalcOptions;

// #endregion [Component]
