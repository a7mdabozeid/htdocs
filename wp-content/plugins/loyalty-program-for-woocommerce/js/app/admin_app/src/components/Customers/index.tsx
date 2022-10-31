// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// CSS
import "./index.scss";

// Components
import CustomersList from "./CustomersList";
import SingleCustomer from "./SingleCustomer";

// Actions
import { CustomerDataActions } from "../../store/actions/customers";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;

const {
    router: { useLocation },
} = acfwpElements;

const { readCustomerStatus, readCustomerHistory } = CustomerDataActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const Customers = () => {
    const urlParams = new URLSearchParams(useLocation().search);
    const customerID = urlParams.get("customer");

    if (customerID) {
        return <SingleCustomer customerID={parseInt(customerID)} />
    }

    return (
        <CustomersList />
    );
};


export default Customers;

// #endregion [Component]
