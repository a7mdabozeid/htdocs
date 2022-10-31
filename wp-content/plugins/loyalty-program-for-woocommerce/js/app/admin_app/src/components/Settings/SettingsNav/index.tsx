// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Components
import MenuIcon from "./MenuIcon";

// Types
import IStore from "../../../types/store";
import { ISection } from "../../../types/section";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;
const {
    element: { useEffect, useState },
    router: { Link },
    redux: { bindActionCreators, connect },
    antd: { Skeleton, Menu },
    pathPrefix,
} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
    sections: ISection[];
    currentSection: string | null;
}

// #region [Component] =================================================================================================

const SettingsNav = (props: IProps) => {
    const { sections, currentSection } = props;
    const defaultKey: string = currentSection ? currentSection : "general";

    if (sections.length < 1) {
        return (
            <div className="settings-nav-skeleton">
                <Skeleton active paragraph={false} />
                <Skeleton active paragraph={false} />
                <Skeleton active paragraph={false} />
                <Skeleton active paragraph={false} />
                <Skeleton active paragraph={false} />
            </div>
        );
    }

    return (
        <Menu className="acfw-settings-nav" defaultSelectedKeys={[defaultKey]}>
            {sections.map(({ id, title }) => (
                <Menu.Item key={id}>
                    <Link
                        to={`${pathPrefix}admin.php?page=acfw-loyalty-program&tab=settings&section=${id}`}
                    >
                        <MenuIcon section={id} />
                        {title}
                    </Link>
                </Menu.Item>
            ))}
        </Menu>
    );
};

const mapStateToProps = (store: IStore) => ({
    sections: store.settingSections,
});

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({}, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(SettingsNav);

// #endregion [Component]
