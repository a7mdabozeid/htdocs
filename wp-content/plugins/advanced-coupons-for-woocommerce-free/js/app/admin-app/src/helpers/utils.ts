// #region [Imports] ===================================================================================================

import moment from "moment";

// #endregion [Imports]

// #region [Variables] =================================================================================================

declare var acfwAdminApp: any;
declare var location: any;

// #endregion [Variables]

// #region [Functions] ==================================================================================================

export const getPathPrefix = function () {
  return acfwAdminApp.admin_url.replace(location.origin, "");
};

export const validateURL = (str: string) => {
  const pattern = new RegExp(
    "^(https?:\\/\\/)?" + // protocol
      "((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|" + // domain name
      "((\\d{1,3}\\.){3}\\d{1,3}))" + // OR ip (v4) address
      "(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // port and path
      "(\\?[;&a-z\\d%_.~+=-]*)?" + // query string
      "(\\#[-a-z\\d_]*)?$",
    "i"
  ); // fragment locator
  return !!pattern.test(str);
};

export const getDateRangeMomentValues = (value: string) => {
  let startPeriod: moment.Moment = moment().startOf("month");
  let endPeriod: moment.Moment = moment().startOf("day");

  switch (value) {
    case "week_to_date":
      startPeriod = moment().startOf("week");
      endPeriod = moment().startOf("day");
      break;
    case "month_to_date":
      startPeriod = moment().startOf("month");
      endPeriod = moment().startOf("day");
      break;
    case "quarter_to_date":
      startPeriod = moment().startOf("quarter");
      endPeriod = moment().startOf("day");
      break;
    case "year_to_date":
      startPeriod = moment().startOf("year");
      endPeriod = moment().startOf("day");
      break;
    case "last_week":
      startPeriod = moment().subtract(1, "weeks").startOf("week");
      endPeriod = moment().subtract(1, "weeks").endOf("week");
      break;
    case "last_month":
      startPeriod = moment().subtract(1, "months").startOf("month");
      endPeriod = moment().subtract(1, "months").endOf("month");
      break;
    case "last_quarter":
      startPeriod = moment().subtract(1, "quarters").startOf("quarter");
      endPeriod = moment().subtract(1, "quarters").endOf("quarter");
      break;
    case "last_year":
      startPeriod = moment().subtract(1, "years").startOf("year");
      endPeriod = moment().subtract(1, "years").endOf("year");
      break;
  }

  return [startPeriod, endPeriod];
};

// #endregion [Functions]
