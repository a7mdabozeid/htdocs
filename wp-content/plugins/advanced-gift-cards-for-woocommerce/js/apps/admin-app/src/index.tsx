// #region [Imports] ===================================================================================================

// Components
import AppInit from "./components/App";

// CSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;

const {
    dom,
    router: { BrowserRouter, Route },
    pathPrefix,
    appStore: { Provider, store },
} = acfwpElements;

// #endregion [Variables]

// #region [Component] =================================================================================================

document.querySelectorAll("#agcfw_admin_app").forEach((domContainer: any) => {
    dom.render(
      <BrowserRouter>
          <Route path={`${pathPrefix}admin.php`} component={AppInit} />
      </BrowserRouter>,
      domContainer
    );
});

// #endregion [Component]
