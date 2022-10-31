// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// CSS
import "./index.scss";

// Helpers
import { getLicense, activateLicense } from "../../helpers/ajax";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface ILicense {
    key: string;
    email: string;
    is_active: string;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const License = () => {

    const { element: { useEffect, useState, Fragment }, antd, antdIcons: {CheckCircleFilled} }  = acfwpElements;
    const { Form, Input, Button, Typography, Skeleton, message } = antd;
    const { Link } = Typography;
    const [license, setLicense]:[ILicense, any] = useState({key: false});
    const [showForm, setShowForm]:[boolean, any] = useState(false);
    const [showSpinner, setShowSpinner]:[boolean, any] = useState(false);

    const [form] = Form.useForm();

    const { license_page: { 
        title, 
        desc,  
        indicator,
        license_status, 
        premium_content, 
        specs,
        formlabels,
        spinner_img
    } } = acfwAdminApp;

    useEffect( () => {

        getLicense()
        .done( (response: any) => {
            
            if ( response.status !== "success" ) return;

            setLicense({
                key: response.license_key ? response.license_key : '',
                email: response.email ? response.email : '',
                is_active: response.is_active
            });

        } )
        .always( () => setShowForm(true) );
    
    }, [setLicense, setShowForm] );

    const handleActivateLicense = (data: any) => {

        setShowSpinner(true);

        activateLicense(data)
        .done( (response: any) => {
            
            if ( response.status === "success" ) {
                message.success( response.success_msg );
                setLicense({...license, is_active: 'yes'});
            } else {

                if (['inactive', 'expired'].includes(response.license_status)) {
                    setLicense({...license, is_active: 'no'});
                }

                message.error( response.error_msg );
            }

        } )
        .always( () => setShowSpinner(false) );
    };

    return (
        <div id="license-placeholder">
            <div className="overview">
                <img className="acfw-logo" src={ acfwAdminApp.logo } alt="acfw logo" />
                <h1>{ title }</h1>
                <p>{ desc }</p>
            </div>
            <div className={`license-info ${ showSpinner ? "show" : "" }`}>

                <div className="heading">
                    <div className="left">
                        <span>{ license_status.label }</span>
                    </div>
                    <div className="right">
                        <span className={`action-button active-indicator no-hover ${license.is_active == 'yes' ? 'activated' : ''}`}>
                            {license.is_active == "yes" && <CheckCircleFilled />}
                            { license.is_active == "yes" ? indicator.active : indicator.inactive }
                        </span>
                    </div>
                </div>

                <div className="content">
                    <h2>{ premium_content.title }</h2>
                    <p>{ premium_content.text }</p>

                    <table className="license-specs">
                        <thead>
                            <tr>
                                { specs.map( (s: any) => (<th key={ s.label }>{ s.label }</th>) ) }
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                { specs.map( (s: any) => (<td key={ s.value }>{ s.value }</td>) ) }
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div className="license-form">
                    { showForm ? (
                        <Fragment>
                            <Form
                                layout="vertical"
                                form={ form }
                                initialValues={ license }
                                onFinish={ handleActivateLicense }
                            >
                                <Form.Item label={ formlabels.license_key } name="key">
                                    <Input type="password" autocomplete="off" />
                                </Form.Item>
                                <Form.Item label={ formlabels.email } name="email">
                                    <Input type="email" autocomplete="off" />
                                </Form.Item>
                                <Form.Item className={`form-action ${license.is_active == 'yes' ? 'grayed' : ''}`}>
                                    <Button type="primary" htmlType="submit">{ formlabels.button }</Button>
                                </Form.Item>
                            </Form>
                            <div className="help-row">
                                { formlabels.help.text } <Link href={ formlabels.help.link } target="_blank">{ formlabels.help.login }</Link>
                            </div>
                        </Fragment>
                    ) : (<Skeleton loading={true} active paragraph={{ rows: 1 }} />) }
                    
                </div>

                <div className="overlay">
                    <img src={ spinner_img } alt="spinner" />
                </div>
            </div>
        </div>
    );
}

export default License;

// #endregion [Component]