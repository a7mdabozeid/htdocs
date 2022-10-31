// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// CSS
import "./index.scss";

// Components
import InputSwitch from "../InputSwitch";

// Types
import { ISectionField } from "../../../types/section";

// Helpers
import { validateValueByType } from "../../../helpers/utils";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { useState },
    antd: { Typography, Spin, Row, Col, Divider, Popover },
    antdIcons: { QuestionCircleOutlined, LoadingOutlined },
} = acfwpElements;
const { Text } = Typography;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
    field: ISectionField;
    order: number;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const SettingField = (props: IProps) => {
    const { field, order } = props;
    const { id, title, type, desc, desc_tip } = field;
    const { validation } = acfwAdminApp;
    const [showSpinner, setShowSpinner]: [boolean, any] = useState(false);
    const [invalidInput, setInvalidInput]: [boolean, any] = useState(false);

    const tooltip = desc_tip ? (
        <div className="setting-tooltip-content">{desc_tip}</div>
    ) : null;

    /**
     * Validate input callback to be passed as a prop.
     * 
     * @param {any} value
     * @param {string} type
     * @param {ISectionField} field 
     * @returns {boolean}
     */
    const validateInput = (value: any) => {
        let isValid = validateValueByType(value, type, field);
        setInvalidInput(!isValid);
        return isValid;
    };

    // display title field
    if ("title" === type) {
        return (
            <div className="form-heading">
                <h1>{title}</h1>
                <p>{desc}</p>
            </div>
        );
    }

    // display subtitle field
    if ("subtitle" === type) {
        return (
            <div className={`form-heading ${order > 0 ? "spacer" : ""}`}>
                <h3>{title}</h3>
            </div>
        );
    }

    return (
        <Row gutter={16} className="form-control" id={`${id}_field`} key={id}>
            {order > 0 ? <Divider /> : null}
            <Col span={8}>
                <label className="lpfw-setting-field-label">
                    <strong>{title}</strong>
                </label>
                {desc_tip ? (
                    <Popover
                        placement="right"
                        content={tooltip}
                        trigger="click"
                    >
                        <QuestionCircleOutlined className="setting-tooltip-icon" />
                    </Popover>
                ) : null}
            </Col>
            <Col className="setting-field-column" span={16}>
                <InputSwitch
                    field={field}
                    showSpinner={showSpinner}
                    setShowSpinner={setShowSpinner}
                    validateInput={validateInput}
                />
                {showSpinner ? (
                    <Spin
                        indicator={
                            <LoadingOutlined style={{ fontSize: 24 }} spin />
                        }
                    />
                ) : null}
                <div className={`invalid-input${invalidInput ? " show" : ""}`}>
                    {invalidInput ? (
                        <Text type="danger">
                            {validation[type]
                                ? validation[type]
                                : validation.default}
                        </Text>
                    ) : null}
                </div>
                {desc ? (
                    <p className="field-desc" dangerouslySetInnerHTML={{__html: desc}} />
                ) : null}
            </Col>
        </Row>
    );
};

export default SettingField;

// #endregion [Component]
