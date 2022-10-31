// #region [Imports] ===================================================================================================

// Libraries
import {useState} from "@wordpress/element";
import ServerSideRender from '@wordpress/server-side-render';
import {
  TextControl,
  Button,
  Placeholder,
  PanelBody,
  ToolbarGroup,
  withSpokenMessages
} from "@wordpress/components";

// @ts-ignore
import { BlockControls, InspectorControls } from "@wordpress/block-editor";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var agcfwBlocksi18n: any;
const {redeemFormBlockTexts} = agcfwBlocksi18n;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface IAttributes {
  title: string;
  description: string;
  tooltip_link_text: string;
  tooltip_title: string;
  tooltip_content: string;
  input_placeholder: string;
  button_text: string;
}

interface IProps {
  attributes: IAttributes;
  name: string;
  setAttributes: (IAttributes) => void;
}

// #endregion [Interfaces]

// #region [Component] =================================================================================================

const RedeemForm = (props: IProps) => {
  const {name, attributes, setAttributes} = props;
  const {labels} = redeemFormBlockTexts;

  return (
    <>  
      <InspectorControls key="inspector">
        <PanelBody
          title={labels.main}
          initialOpen
        >
          <TextControl
            label={labels.title}
            value={attributes.title}
            onChange={ (value: string) => setAttributes({title: value})}
          />
          <TextControl
            label={labels.description}
            value={attributes.description}
            onChange={ (value: string) => setAttributes({description: value})}
          />
        </PanelBody>
        <PanelBody title={labels.tooltip_content}>
          <TextControl
            label={labels.link_text}
            value={attributes.tooltip_link_text}
            onChange={ (value: string) => setAttributes({tooltip_link_text: value})}
          />
          <TextControl
            label={labels.title}
            value={attributes.tooltip_title}
            onChange={ (value: string) => setAttributes({tooltip_title: value})}
          />
          <TextControl
            label={labels.content}
            value={attributes.tooltip_content}
            onChange={ (value: string) => setAttributes({tooltip_content: value})}
          />
        </PanelBody>
        <PanelBody title={labels.form_fields}>
          <TextControl
            label={labels.input_placeholder}
            value={attributes.input_placeholder}
            onChange={ (value: string) => setAttributes({input_placeholder: value})}
          />
          <TextControl
            label={labels.button_text}
            value={attributes.button_text}
            onChange={ (value: string) => setAttributes({button_text: value})}
          />
        </PanelBody>
      </InspectorControls>

      <ServerSideRender
        block={name}
        attributes={attributes}
        EmptyResponsePlaceholder={() => (
          <Placeholder
            label={redeemFormBlockTexts.title}
            className="agcfw-block-redeem-form"
          ><p>There was an error</p></Placeholder>
        )}
      />

      
    </>
  );
}

export default withSpokenMessages(RedeemForm);

// #endregion [Component]
