// #region [Imports] ===================================================================================================

// Libraries
import React from "react";

// Types
import { ISectionField } from "../../../types/section";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwpElements: any;
declare var jQuery: any;
declare var ajaxurl: string;

const {
  element: { useState },
  antd: { Button, message },
} = acfwpElements;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IProps {
  field: ISectionField;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const RefetchUpdateData = (props: IProps) => {
  const {field} = props;
  const [loading, setLoading]: [boolean, any] = useState(false);

  const handleRefetchUpdateData = () => {
    setLoading(true);

    jQuery.ajax({
      method: "post",
      url: ajaxurl,
      data: {action: field.id, nonce: field?.nonce},
      dataType: "json"
    })
    .done((response: any) => {
      if ( response.status === "success" ) {
        message.success( response.message );
      } else if (response.status === "warning") {
        message.warning(response.message);
      } else {
          message.error( response.error_msg );
      }
    })
    .always(() => {
      setLoading(false);
    })
  };

  return (
    <Button type="primary" loading={loading} onClick={handleRefetchUpdateData}>
      {field.title}
    </Button>
  );
};

export default RefetchUpdateData;

// #endregion [Component]