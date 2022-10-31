// #region [Imports] ===================================================================================================

// Components
import SidebarIcon from "./SidebarIcon";

// #endregion [Imports]

// #region [Variables] =================================================================================================

// #region [Interfaces]=================================================================================================

export interface IResourceItem {
  key: string;
  slug: string;
  label: string;
  link: string;
  onClick?: (id: string) => void;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const ResourceLink = (props: IResourceItem) => {
  const {slug, label, link, onClick} = props;

  if (link.includes('http')) {
    return(
      <a href={link} target="_blank" rel="noreferrer">
        <SidebarIcon iconKey={slug} />
        {label}
      </a>
    );
  }

  return (
    <a onClick={() => onClick && onClick(link)} href="javascript:void(0);">
      <SidebarIcon iconKey={slug} />
      {label}
    </a>
  );
}

export default ResourceLink;

// #endregion [Component]
