// #region [Imports] ===================================================================================================

// Libraries
import { Row, Col, Button } from "antd";

// Components
import Logo from "../../components/Logo";

// SCSS
import "./index.scss";

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IStepProps {
  step_count: string;
  title: string;
  description: string;
  is_active: boolean;
  action_text: string;
  link: string;
  is_external: boolean;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const PluginStep = (props: IStepProps) => {

  const {
    step_count,
    title,
    description,
    is_active,
    action_text,
    link,
    is_external
  } = props;

  return (
    <div className={`plugin-step ${is_active ? '' : 'blocked'}`}>
      <div className="step-count"><span>{step_count}</span></div>
      <div className="inner-content">
        <p className="title">{title}</p>
        <p className="description">{description}</p>
        <p className="actions">
          <Button href={link} target={is_external ? '_blank' : ''}>{action_text}</Button>
        </p>
      </div>
    </div>
  );
}

const LoyaltyProgram = () => {
  const {loyalty_program: {
    title,
    description,
    plugin_image,
    features_list,
    steps_list,
  }} = acfwAdminApp;

  return (
    <div className="loyalty-program-page-upsell">
      <div className="page-header loyalty-program-header">
          <Logo hideUpgrade={true} />
        <h1>{ title }</h1>
        <p>{ description }</p>
      </div>
      <Row className="features-list" gutter={60}>
        <Col span={12} className="image">
          <img src={plugin_image.src} alt={plugin_image.alt} />
        </Col>
        <Col span={12} className="content">
          <ul>
            {features_list.map((feature: string, key: string) => <li key={key}>{feature}</li>)}
          </ul>
        </Col>
      </Row>
      <div className="action-steps">
        {steps_list.map((pluginStep: IStepProps, key: string) => <PluginStep key={key} {...pluginStep} />)}
      </div>
    </div>
  );
};

export default LoyaltyProgram;

// #endregion [Component]
