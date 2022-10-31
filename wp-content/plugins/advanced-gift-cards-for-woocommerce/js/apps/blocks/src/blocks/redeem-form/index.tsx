// #region [Imports] ===================================================================================================

// Components
import RedeemForm from "./block";

// SCSS
import "./index.scss";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var agcfwBlocksi18n: any;
const {redeemFormBlockTexts} = agcfwBlocksi18n;

// #endregion [Variables]

// #region [BlockData] =================================================================================================

export default {
  name: 'acfw/gift-card-redeem-form',

  settings: {
    title: redeemFormBlockTexts.title,
    icon: "tickets-alt",
    category: "advancedcoupons",
    keywords: ["gift", "card", "advanced", "redeem"],
    description: redeemFormBlockTexts.description,
    supports: {
      align: ['wide', 'full'],
      html: false
    },

    example: {
      attributes: {
        isPreview: true,
      }
    },
  
    attributes: {
  
      title: {
        type: 'string',
        default: redeemFormBlockTexts.defaults.title,
      },
  
      description: {
        type: 'string',
        default: redeemFormBlockTexts.defaults.description,
      },

      tooltip_link_text: {
        type: 'string',
        default: redeemFormBlockTexts.defaults.tooltip_link_text,
      },

      tooltip_title: {
        type: 'string',
        default: redeemFormBlockTexts.defaults.tooltip_title,
      },

      tooltip_content: {
        type: 'string',
        default: redeemFormBlockTexts.defaults.tooltip_content,
      },

      input_placeholder: {
        type: 'string',
        default: redeemFormBlockTexts.defaults.input_placeholder,
      },

      button_text: {
        type: 'string',
        default: redeemFormBlockTexts.defaults.button_text,
      },
    },
  
    edit(props) {
      return <RedeemForm {...props} />;
    },
  
    /**
     * Save nothing; rendered by server.
     */
    save() {
      return null;
    }
  },
}

// #endregion [BlockData]