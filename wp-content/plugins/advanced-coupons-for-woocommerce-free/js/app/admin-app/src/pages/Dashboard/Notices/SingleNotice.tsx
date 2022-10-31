// #region [Imports] ===================================================================================================

// Libraries
import { Button, Card } from "antd";
import { CloseOutlined } from "@ant-design/icons";

// Types
import { ISingleNotice } from "../../../types/notices";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces]=================================================================================================

interface IProps {
  notice: ISingleNotice;
  onDismiss: (slug: string, response?: string) => void;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const SingleNotice = (props: IProps) => {
  const {notice: {slug, content, type, actions, is_dismissable}, onDismiss} = props;

  const { 
    dashboard_page: {
      labels
    }
  } = acfwAdminApp;

  return (
    <Card className={`single-notice notice-${type} ${slug}-notice`}>
      <div className="notice-content">
        {content.map(((text, i) => <p key={i} dangerouslySetInnerHTML={{__html: text}} />))}
      </div>
      <div className="notice-actions">
        {actions.map(((action, i) => (
          <Button 
            key={action.key} type={0 === i ? 'primary' : 'default'} 
            href={action.link.replace(/&amp;/g, '&')} 
            target={action?.is_external ? '_blank' : '' }
          >
            {action.text}
          </Button>)))}
          {is_dismissable && (
            <Button onClick={() => onDismiss(slug)}>
              {labels.dismiss}
            </Button>
          )}
      </div>
      {is_dismissable && (
        <Button 
          className="dismiss-notice-icon" 
          icon={<CloseOutlined />} 
          size="small" 
          type="text"
          shape="circle"
          onClick={() => onDismiss(slug)}
        />
      )}
    </Card>
  );
};

export default SingleNotice;

// #endregion [Component]