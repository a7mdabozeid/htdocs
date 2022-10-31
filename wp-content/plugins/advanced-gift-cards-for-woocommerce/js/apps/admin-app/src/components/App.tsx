// #region [Imports] ===================================================================================================

// Components
import License from "./License";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;

const {
  element: { useState, useEffect },
  appStore: { store },
} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================
// #endregion [Interfaces]

const AppInit = () => {
  const [appPage, setAppPage]: [string, any] = useState("");

  // set current page state value from ACFW store to local state.
  useEffect(() => {
    if (!appPage) setAppPage(store.getState().page);
}, []);

  // detect page state change from ACFW redux store.
  const handlePageChange = () => {
    const pageValue = store.getState().page;

    if (appPage !== pageValue) setAppPage(pageValue);
};

  // subscribe to any state value changes from ACFW redux store.
  store.subscribe(handlePageChange);

  if ("acfw-license" === appPage) {
    return <License />;
  }

  return null;
};

export default AppInit;

// #region [Component] =================================================================================================
