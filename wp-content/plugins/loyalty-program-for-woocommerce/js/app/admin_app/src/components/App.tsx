// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Components
import TabSwitch from "./TabSwitch";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { useState, useEffect },
    router: { useHistory, useLocation },
    antd: { Tabs },
    appStore: { store },
    pathPrefix,
} = acfwpElements;

const { TabPane } = Tabs;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface ITab {
    slug: string;
    label: string;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const AppInit = () => {
    const {
        logo,
        loyalty_program: { title, tabs },
    } = acfwAdminApp;
    const [appPage, setAppPage]: [string, any] = useState("");
    const urlParams = new URLSearchParams(useLocation().search);
    const tabParam = urlParams.get("tab");
    const currentTab = tabParam
        ? tabs.findIndex((t: any) => t.slug === tabParam)
        : 0;
    const history = useHistory();

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

    // handle tab click event
    const handleTabClick = (key: string) => {
        history.push(
            `${pathPrefix}admin.php?page=acfw-loyalty-program&tab=${tabs[key].slug}`
        );
    };

    // only show LPFW app when appPage matches.
    if (appPage !== "acfw-loyalty-program") return null;

    return (
        <div className="lpfw-app acfw-admin-app">
            <div className="page-header loyalty-program-header">
                <img className="acfw-logo" src={logo} alt="acfw logo" />
                <h1>{title}</h1>
            </div>
            <Tabs
                activeKey={currentTab + ""}
                defaultActiveKey={currentTab + ""}
                onTabClick={handleTabClick}
            >
                {tabs.map((tab: ITab, key: number) => (
                    <TabPane tab={tab.label} key={key}>
                        <TabSwitch slug={tab.slug} />
                    </TabPane>
                ))}
            </Tabs>
        </div>
    );
};

export default AppInit;

// #endregion [Component]
