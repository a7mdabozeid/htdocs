// #region [Imports] ===================================================================================================

// Libraries
import { 
  ThunderboltOutlined, 
  ReadOutlined, 
  SettingOutlined, 
  QuestionCircleOutlined, 
  CheckSquareOutlined, 
  ApiOutlined, 
  IssuesCloseOutlined, 
  WarningOutlined 
} from "@ant-design/icons";

// #endregion [Imports]

// #region [Interfaces]=================================================================================================

export interface IProps {
  iconKey: string;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const SidebarIcon = (props: IProps) => {
  const {iconKey} = props;

  if ('getting_started'=== iconKey) 
    return <ThunderboltOutlined />;

  if ('documentation'=== iconKey)
    return <ReadOutlined />;

  if ('settings'=== iconKey)
    return <SettingOutlined />;

  if ('support'=== iconKey)
    return <QuestionCircleOutlined />;

  if ('active'=== iconKey)
    return <CheckSquareOutlined />;

  if ('inactive'=== iconKey)
    return <IssuesCloseOutlined />;

  if ('expired'=== iconKey)
    return <WarningOutlined />;

  if ('learn_more'=== iconKey)
    return <ApiOutlined />;

  return null;
}

export default SidebarIcon;

// #endregion [Component]
