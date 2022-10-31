// #region [Imports] ===================================================================================================

import React from "react";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;
const { 
  element: { useEffect, useState },
  antdIcons: {
    SettingOutlined,
    DollarCircleOutlined,
    TrophyOutlined,
    UserOutlined,
    MedicineBoxOutlined,
    InfoCircleOutlined
  }
 } = acfwpElements;
// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
  section: string;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const MenuIcon = (props: IProps) => {
  const { section } = props;

  switch (section) {
    case "general":
      return <SettingOutlined />;

    case "points_earning":
      return <DollarCircleOutlined />;

    case "messages":
      return <InfoCircleOutlined />;

    case "redemption_expiry":
      return <TrophyOutlined />;

    case "role_restrictions":
      return <UserOutlined />;

    case "help":
      return <MedicineBoxOutlined />;
  }

  return null;
}

export default MenuIcon;

// #endregion [Component]
