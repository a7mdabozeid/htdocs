// Types
import { ISectionField } from "../types/section";

declare var lpfwAdminApp: any;
declare var acfwpElements: any;

const { validateURL } = acfwpElements;

/**
 * Validate price input.
 *
 * @param {string} value
 * @returns {boolean}
 */
export function validatePrice(value: string): boolean {
    const { decimalPoint } = lpfwAdminApp;
    const regex = new RegExp(`[^-0-9%\\${decimalPoint}]+`, "gi");
    const decimalRegex = new RegExp(`[^\\${decimalPoint}"]`, "gi");
    let newvalue = value.replace(regex, "");

    // Check if newvalue have more than one decimal point.
    if (1 < newvalue.replace(decimalRegex, "").length) {
        newvalue = newvalue.replace(decimalRegex, "");
    }

    const floatVal = parseFloat(newvalue.replace(decimalPoint, "."));

    return value === newvalue && floatVal > 0.0;
}

/**
 * Parse string as price value (float).
 *
 * @param {string} value
 * @returns {number}
 */
export function parsePrice(value: string): number {
    const { decimalPoint } = lpfwAdminApp;
    return parseFloat(value.replace(decimalPoint, "."));
}

/**
 * Validate field value based on type.
 *
 * @param {any} value
 * @param {string} type
 * @param {ISectionField} field
 * @returns {boolean}
 */
export function validateValueByType(
    value: any,
    type: string,
    field: ISectionField
) {
    let isValid = true;

    switch (type) {
        case "number":
            const min = field?.min ?? 0;
            isValid = parseInt(value) >= min;
            break;

        case "url":
            isValid = value && validateURL(value + "");
            break;

        case "price":
            isValid = value && validatePrice(value + "");
            break;

        case "breakpoints":
            isValid = value.reduce(
                (c: boolean, { amount }: any) => validatePrice(amount) && c,
                true
            );
            break;

        case "order_period":
            isValid = value.reduce(
                (c: boolean, { points }: any) => parseInt(points) > 0 && c,
                true
            );
            break;
    }

    return isValid;
}
