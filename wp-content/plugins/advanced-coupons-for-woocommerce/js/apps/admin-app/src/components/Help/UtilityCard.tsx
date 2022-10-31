// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Components
import UtilityAction from "./UtilityAction";

// Types
import { IUtilityCard } from "../../types/utilities";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IProps {
    card: IUtilityCard;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const UtilityCard = (props: IProps) => {

    const { antd: { Col, Card } } = acfwpElements;
    const { card: {
        title,
        desc,
        nonce,
        id,
        buttons
    } } = props;

    return (
        <Col className="utility-card" span={8}>
            <Card>
                <h2>{ title }</h2>
                <p>{ desc }</p>
                <div className="actions">
                    { buttons.map( (button: any, key:number) => (
                        <UtilityAction id={id} nonce={nonce} button={button} key={key} />
                    ) ) }
                </div>
            </Card>
        </Col>
    );
}

export default UtilityCard;

// #endregion [Component]