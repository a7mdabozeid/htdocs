// #region [Imports] ===================================================================================================

// Libraries
import React from "react";
import ReactDOM from 'react-dom';
import { Provider } from "react-redux";

// Components
import VirtualCouponsMetabox from "./apps/VirtualCouponsMetabox";

// Store
import initializeStore from "./store";

//CSS
import "./antd.css"; // custom antd CSS.
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

// Initialize redux store.
const store = initializeStore();

// #endregion [Variables]

// #region [Renders] ===================================================================================================

const virtualCouponsAppRoot = document.querySelector('#acfw-virtual-coupon #virtual-coupons-app');

if (virtualCouponsAppRoot) {
  ReactDOM.render(
    <Provider store={store}>
      <VirtualCouponsMetabox />
    </Provider>,
    virtualCouponsAppRoot
  );
}

// #endregion [Renders]

