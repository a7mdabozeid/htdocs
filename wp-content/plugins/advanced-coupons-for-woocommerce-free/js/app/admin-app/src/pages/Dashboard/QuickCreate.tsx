// #region [Imports] ===================================================================================================

import {Button} from "antd";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Component] =================================================================================================

const QuickCreate = () => {

  const { 
    admin_url,
    dashboard_page: {
      create_coupon,
    }
  } = acfwAdminApp;

  return (
    <div className="quick-create-coupon">
      <span className="label">{`${create_coupon.label}:`}</span>
      <Button type="primary" href={`${admin_url}post-new.php?post_type=shop_coupon&type=percent`} target="_blank" size="small" ghost>{create_coupon.percentage}</Button>
      <Button type="primary" href={`${admin_url}post-new.php?post_type=shop_coupon&type=fixed_cart`} target="_blank" size="small" ghost>{create_coupon.fixed}</Button>
      <Button type="primary" href={`${admin_url}post-new.php?post_type=shop_coupon&type=acfw_bogo`} target="_blank" size="small" ghost>{create_coupon.bogo}</Button>
    </div>
  );
};

export default QuickCreate;

// #endregion [Component]