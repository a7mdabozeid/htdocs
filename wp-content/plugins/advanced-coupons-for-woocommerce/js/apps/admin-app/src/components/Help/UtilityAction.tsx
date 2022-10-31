// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Interfaces
import { IUtilityAction } from "../../types/utilities";

// helpers
import { wpAjax } from "../../helpers/ajax";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IProps {
    id: string;
    button: IUtilityAction;
    nonce: string;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const UtilityAction = (props: IProps) => {

    const { element, antd } = acfwpElements;
    const { useState } = element;
    const { Button, message } = antd;
    const { id, nonce, button: {
        text,
        action,
        type
    } } = props;

    const [loading, setLoading]: [boolean, any] = useState(false);

    const handleClick = (action: string) => {

        setLoading(true);

        wpAjax({
            action: id,
            type: action,
            nonce: nonce
        })
        .done( (response: any) => {

            if ( response.status === "success" ) {
                message.success( response.message );
            } else if (response.status === "warning") {
                message.warning(response.message);
            } else {
                message.error( response.error_msg );
            }

        } )
        .always( () => {
            setLoading(false);
        } );
    };

    return (
        <Button 
            type={type}
            onClick={ () => handleClick(action) }
            loading={ loading }
        >
            { text }
        </Button>
    );
}

export default UtilityAction;

// #endregion [Component]