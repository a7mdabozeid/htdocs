// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Components
import SettingsNav from "./SettingsNav";
import SettingsForm from "./SettingsForm";

// Actions
import { SectionActions } from "../../store/actions/section";

// Types
import IStore from "../../types/store";
import { ISection } from "../../types/section";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { Fragment, useEffect },
    router: { useLocation },
    antd: { Row, Col },
    redux: { bindActionCreators, connect },
} = acfwpElements;

const { readSections, readSection } = SectionActions;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IActions {
    readSections: typeof readSections;
    readSection: typeof readSection;
}

interface IProps {
    sections: ISection[];
    actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const Settings = (props: IProps) => {
    const { sections, actions } = props;
    const {
        loyalty_program: { tabs },
    } = acfwAdminApp;
    const urlParams = new URLSearchParams(useLocation().search);
    const currentSection = urlParams.get("section") || "general";
    const tabParam = urlParams.get("tab");
    const index = tabs.findIndex((t: any) => t.slug === "settings");
    const { label, desc } = tabs[index];

    /**
     * fetch all sections on first load.
     */
    useEffect(() => {
        if (sections.length > 0) return;
        actions.readSections({
            id: currentSection ? currentSection : "general",
        });
    }, []);

    /**
     * fetch fields of section (on menu click).
     */
    useEffect(() => {
        if (sections.length < 1) return;

        const idx = currentSection
            ? sections.findIndex((i) => i.id === currentSection)
            : 0;

        if (sections[idx].fields.length < 1)
            actions.readSection({ id: currentSection });
    }, [sections, actions, currentSection]);

    /**
     * Ensures that the settings navigation is re-rendered when tabs are switched.
     */
    if (tabParam !== "settings") return null;

    return (
        <Fragment>
            <div className="tab-header">
                <h1>{label}</h1>
                <p>{desc}</p>
            </div>
            <Row className="settings-content">
                <Col span={6}>
                    <SettingsNav currentSection={currentSection} />
                </Col>
                <Col span={18} className="content-column">
                    <SettingsForm currentSection={currentSection} />
                </Col>
            </Row>
        </Fragment>
    );
};

const mapStateToProps = (store: IStore) => ({
    sections: store.settingSections,
});

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({ readSections, readSection }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(Settings);
