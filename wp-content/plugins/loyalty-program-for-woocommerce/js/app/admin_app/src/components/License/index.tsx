// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// CSS
import "./index.scss";

// Types
import ILicense from "../../types/license";

// Actions
import { LicenseActions } from "../../store/actions/license";

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var acfwpElements: any;

const {
    element: { Fragment, useEffect, useState },
    redux: { bindActionCreators, connect },
    antd: { Form, Input, Button, Typography, Skeleton, message },
} = acfwpElements;
const { Link } = Typography;

const { readLicenseData, activateLicenseData } = LicenseActions;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IActions {
  readLicenseData: typeof readLicenseData;
  activateLicenseData: typeof activateLicenseData;
}

interface IFormData {
  key: string;
  email: string;
}

interface IProps {
  license: ILicense | null;
  actions: IActions;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const License = (props: IProps) => {
  const {license, actions} = props;
  const labels = acfwAdminApp.loyalty_program.license;

  const [showForm, setShowForm]:[boolean, any] = useState(false);
  const [loading, setLoading]:[boolean, any] = useState(false);
  const [form] = Form.useForm();

  /**
   * Initializing loading plugin license data.
   */
  useEffect(() => {
    if (!license) actions.readLicenseData({alwaysCB: () => setShowForm(true)});
  }, []);

  /**
   * Handle form submission for activating plugin license.
   * 
   * @param {IFormData} param0 
   */
  const handleActivateLicense = ({key, email}: IFormData) => {

    setLoading(true);

    actions.activateLicenseData({
      license_key: key, 
      email,
      successCB: (response) => {
        if ( response.data.status === "success" )
          message.success( response.data.success_msg );
        else
          message.error( response.data.error_msg );
      },
      alwaysCB: () => setLoading(false)
    });

  };

  return (
    <div id="license-placeholder">
      <div className="license-info">

        <div className="heading">
          <div className="left">
            <span>{labels.license_status}</span>
          </div>
          <div className="right">
            <span className="action-button active-indicator no-hover">
              { license?.is_active === "yes" ? labels.activated : labels.not_activated }
            </span>
          </div>
        </div>

        <div className="content">
          <p>{ labels.description }</p>
          <table className="license-specs">
            <thead>
              <tr>
                <th>{ labels.version_label }</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{ labels.version_value }</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div className="license-form">
          {showForm ? (
            <Fragment>
              <Form
                layout="vertical"
                form={ form }
                initialValues={{
                  key : license?.key ? license.key : '',
                  email : license?.email ? license.email : '',
                }}
                onFinish={ handleActivateLicense }
              >
                <Form.Item label={ labels.license_key } name="key">
                  <Input type="password" autocomplete="off" required />
                </Form.Item>
                <Form.Item label={ labels.license_email } name="email">
                  <Input type="email" autocomplete="off" required />
                </Form.Item>
                <Form.Item className="form-action">
                  <Button type="primary" htmlType="submit" loading={loading}>{ labels.activate_btn }</Button>
                </Form.Item>
              </Form>
              <div className="help-row">
                  { labels.help.text } <Link href={ labels.help.link } target="_blank">{ labels.help.login }</Link>
              </div>
            </Fragment>
          ) : (<Skeleton loading={true} active paragraph={{ rows: 1 }} />)}
        </div>

      </div>
    </div>
  );
};

const mapStateToProps = (store: any) => ({ license: store.license });

const mapDispatchToProps = (dispatch: any) => ({
    actions: bindActionCreators({ readLicenseData, activateLicenseData }, dispatch),
});

export default connect(mapStateToProps, mapDispatchToProps)(License);

// #endregion [Component]