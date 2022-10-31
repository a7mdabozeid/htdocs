// #region [Imports] ===================================================================================================

// Libraries
import { // @ts-ignore
	registerBlockType, // @ts-ignore
} from '@wordpress/blocks'; 

// Blocks
import redeemForm from "./blocks/redeem-form/index";

// SCSS
import "./index.scss";

// #endregion [Imports]

declare var wp: any;

// #region [RegisterBlocks] ============================================================================================

const registerBlock = (block) => {
  if (!block) {
    return;
  }

  const {name, settings} = block;
  console.log(name, settings);
  registerBlockType( name, settings);
}

/**
 * Register blocks when the DOM is ready.
 */
 wp.domReady(() => {
  const blocks = [redeemForm];
  blocks.forEach(registerBlock);
})


// #endregion [RegisterBlocks]
