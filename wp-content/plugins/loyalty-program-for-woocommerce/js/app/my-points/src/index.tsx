// #region [Imports] ===================================================================================================

// Libraries
import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import { BrowserRouter, Route } from "react-router-dom";
import {ConfigProvider} from "antd";

// Store
import initializeStore from "./store";

// CSS
import "./antd.scss";
import "./index.scss";

// Components
import App from "./components/App";

// Helpers
import { getPathPrefix } from "./helpers/utils";
import { getAntdLocale } from "./helpers/antd";

// #endregion [Imports]

// #region [Variables] =================================================================================================

// Initialize redux store.
const store = initializeStore();

const pathPrefix = getPathPrefix();

// #endregion [Variables]

// #region [Component] =================================================================================================

document
    .querySelectorAll("#lpfw_my_points_app")
    .forEach((domContainer: any) => {
        ReactDOM.render(
            <Provider store={store}>
                <ConfigProvider locale={getAntdLocale()}>
                    <BrowserRouter>
                        <Route
                            path={`${pathPrefix}lpfw-my-points/`}
                            component={App}
                        />
                    </BrowserRouter>
                </ConfigProvider>
            </Provider>,
            domContainer
        );
    });

// #endregion [Component]
