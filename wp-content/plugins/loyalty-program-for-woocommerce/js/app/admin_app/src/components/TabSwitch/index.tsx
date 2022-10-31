// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Contexts
import CustomersContextProvider from "../../contexts/customersQuery";

// Components
import Dashboard from "../Dashboard";
import Customers from "../Customers";
import Settings from "../Settings";
import License from "../License";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;
declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IProps {
    slug: string;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const TabSwitch = (props: IProps) => {
    const { slug } = props;

    if ("dashboard" === slug) return <Dashboard />;

    if ("customers" === slug) return (
    <CustomersContextProvider>
        <Customers />
    </CustomersContextProvider>
    );

    if ("settings" === slug) return <Settings />;

    if ("license" === slug) {
        
        const {is_multisite} = acfwAdminApp.loyalty_program.license;

        if (is_multisite) {
            window.location.href = acfwAdminApp.loyalty_program.license.license_page;
            return null;
        }

        return <License />;
    } 

    return null;
};

export default TabSwitch;

// #endregion [Component]
