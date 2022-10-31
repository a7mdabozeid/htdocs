// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import IPointsBalance from "../../types/balance";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var lpfwMyPoints: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IProps {
    balance: IPointsBalance;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PointsWorth = (props: IProps) => {
    const { balance } = props;
    const { labels, points_expiry_note } = lpfwMyPoints;

    const { points, worth, expiry } = balance;

    return (
        <p>
            <span
                className="points-worth-text"
                dangerouslySetInnerHTML={{
                    __html: labels.points_worth
                        .replace("{p}", points.toLocaleString())
                        .replace("{w}", worth),
                }}
            />
            <br />
            {0 < points && expiry ? (
                <span 
                    className="points-expiry-text" 
                    style={ {fontSize: 13} }
                >
                    <em>{ points_expiry_note.replace('{date_expire}', expiry) }</em>
                </span>
            ): null}
        </p>
    );
};

export default PointsWorth;

// #endregion [Component]
