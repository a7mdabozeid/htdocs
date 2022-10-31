// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// CSS
import "./index.scss";

// Components
import UtilityCard from "./UtilityCard";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

// #endregion [Variables]

// #region [Component] =================================================================================================

const HelpPremium = () => {

    const { antd: { Row } } = acfwpElements;

    // Return null component if utilities data not present.
    if ( ! acfwAdminApp.help_page.utilities )
        return null;

    const { utilities: {
        title,
        cards
    } } = acfwAdminApp.help_page;

    return (
    <div className="utility-block">
        <h1>{ title }</h1>
        <Row gutter={10}>
            { cards.map((card: any, key: number) => <UtilityCard card={card} key={key} />) }
        </Row>
    </div>
    );
}

export default HelpPremium;

// #endregion [Component]
