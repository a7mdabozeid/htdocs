// #region [Imports] ===================================================================================================

import React from "react";

// CSS
import "./index.scss";

// Store
import initializeStore from "./store";

// Components
import AppInit from "./components/App";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;

const {
    dom,
    router: { BrowserRouter, Route },
    pathPrefix,
    appStore: { Provider, store },
} = acfwpElements;

const lpfwStore = initializeStore();

// #endregion [Variables]

// #region [Component] =================================================================================================

document.querySelectorAll("#lpfw_admin_app").forEach((domContainer: any) => {
    dom.render(
        <Provider store={lpfwStore}>
            <BrowserRouter>
                <Route path={`${pathPrefix}admin.php`} component={AppInit} />
            </BrowserRouter>
        </Provider>,
        domContainer
    );
});

// #endregion [Component]
