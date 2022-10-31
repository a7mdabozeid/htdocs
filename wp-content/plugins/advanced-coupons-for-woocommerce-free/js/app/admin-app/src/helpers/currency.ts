// #region [Imports] ===================================================================================================

import { isNull } from "lodash";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;

// #endregion [Variables]

// #region [Interfaces] ================================================================================================

interface INumberFormatProps {
  precision: number | null;
  decimalSeparator: string;
  thousandSeparator: string;
}

// #endregion [Interfaces]

// #region [Functions] =================================================================================================

export function numberFormat(
  {
    precision = null,
    decimalSeparator = ".",
    thousandSeparator = ",",
  }: INumberFormatProps,
  number: number
) {
  if (isNull(precision)) {
    const [, decimals] = number.toString().split(".");
    precision = decimals ? decimals.length : 0;
  }

  let formatted = number.toFixed(precision);
  let [whole, decimal] = formatted.split(decimalSeparator);
  const regex = /(\d+)(\d{3})/;
  while (regex.test(whole)) {
    whole = whole.replace(regex, "$1" + thousandSeparator + "$2");
  }
  return `${whole}${decimalSeparator}${decimal}`;
}

export function priceFormat(number: number, useCode = false) {
  const { currency } = acfwAdminApp.store_credits_page;
  const currencySettings = {
    precision: currency.decimals,
    decimalSeparator: currency.decimal_separator,
    thousandSeparator: currency.thousand_separator,
  };

  const formattedNumber = numberFormat(currencySettings, number);

  if ("" === formattedNumber) {
    return formattedNumber;
  }

  return `${currency.symbol}${formattedNumber}`;
}

/**
 * Validate price input.
 *
 * @param {string} value
 * @returns {boolean}
 */
export function validatePrice(value: string): boolean {
  const { currency } = acfwAdminApp.store_credits_page;
  const regex = new RegExp(`[^-0-9%\\${currency.decimal_separator}]+`, "gi");
  const decimalRegex = new RegExp(`[^\\${currency.decimal_separator}"]`, "gi");
  let newvalue = value.replace(regex, "");

  // Check if newvalue have more than one decimal point.
  if (1 < newvalue.replace(decimalRegex, "").length) {
    newvalue = newvalue.replace(decimalRegex, "");
  }

  const floatVal = parseFloat(
    newvalue.replace(currency.decimal_separator, ".")
  );

  return value === newvalue && floatVal > 0.0;
}

/**
 * Parse string as price value (float).
 *
 * @param {string} value
 * @returns {number}
 */
export function parsePrice(value: string): number {
  const { currency } = acfwAdminApp.store_credits_page;
  return parseFloat(value.replace(currency.decimal_separator, "."));
}

// #endregion [Functions]
